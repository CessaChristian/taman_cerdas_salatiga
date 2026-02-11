<?php
/**
 * Security helpers:
 * - Hardened session bootstrap
 * - CSRF token generation/validation
 */

if (!function_exists('app_bootstrap_session')) {
    function app_bootstrap_session(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $isHttps = (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
            (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)
        );

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_secure', $isHttps ? '1' : '0');

        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_start();

        app_send_security_headers();
    }

    function csrf_token(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            app_bootstrap_session();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    function csrf_validate(): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            app_bootstrap_session();
        }

        $token = $_POST['csrf_token'] ?? '';
        $stored = $_SESSION['csrf_token'] ?? '';

        return is_string($token) && is_string($stored) && $token !== '' && hash_equals($stored, $token);
    }

    function csrf_validate_or_abort(string $redirect): void
    {
        if (!csrf_validate()) {
            header('Location: ' . $redirect);
            exit();
        }
    }

    function app_send_security_headers(): void
    {
        if (headers_sent()) {
            return;
        }

        $isHttps = (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
            (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)
        );

        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
        header('Cross-Origin-Resource-Policy: same-origin');
        header('Cross-Origin-Opener-Policy: same-origin-allow-popups');

        if ($isHttps) {
            header('Strict-Transport-Security: max-age=31536000');
        }
    }

    function app_client_ip(): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        return is_string($ip) && $ip !== '' ? $ip : 'unknown';
    }

    function app_login_rate_limit_file(): string
    {
        return rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'taman_cerdas_login_rate_limit.json';
    }

    function app_login_rate_limit_key(string $username): string
    {
        $normalizedUser = strtolower(trim($username));
        return hash('sha256', app_client_ip() . '|' . $normalizedUser);
    }

    function app_login_rate_limit_read_storage($fh): array
    {
        rewind($fh);
        $raw = stream_get_contents($fh);
        if (!is_string($raw) || $raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    function app_login_rate_limit_write_storage($fh, array $data): void
    {
        rewind($fh);
        ftruncate($fh, 0);
        fwrite($fh, json_encode($data));
        fflush($fh);
    }

    function app_login_rate_limit_check(string $username): array
    {
        $path = app_login_rate_limit_file();
        $fh = fopen($path, 'c+');
        if ($fh === false) {
            return ['allowed' => true, 'retry_after' => 0];
        }

        $maxAttempts = 5;
        $windowSeconds = 15 * 60;
        $lockSeconds = 15 * 60;
        $now = time();
        $key = app_login_rate_limit_key($username);

        flock($fh, LOCK_EX);
        $store = app_login_rate_limit_read_storage($fh);

        foreach ($store as $k => $entry) {
            if (!is_array($entry)) {
                unset($store[$k]);
                continue;
            }
            $lockedUntil = (int) ($entry['locked_until'] ?? 0);
            $fails = array_values(array_filter((array) ($entry['fails'] ?? []), function ($ts) use ($now, $windowSeconds) {
                return is_int($ts) && ($now - $ts) <= $windowSeconds;
            }));
            if ($lockedUntil <= $now && count($fails) === 0) {
                unset($store[$k]);
                continue;
            }
            $store[$k]['fails'] = $fails;
        }

        $entry = $store[$key] ?? ['fails' => [], 'locked_until' => 0];
        $lockedUntil = (int) ($entry['locked_until'] ?? 0);
        $fails = array_values(array_filter((array) ($entry['fails'] ?? []), function ($ts) use ($now, $windowSeconds) {
            return is_int($ts) && ($now - $ts) <= $windowSeconds;
        }));

        $allowed = true;
        $retryAfter = 0;

        if ($lockedUntil > $now) {
            $allowed = false;
            $retryAfter = $lockedUntil - $now;
        } elseif (count($fails) >= $maxAttempts) {
            $lockedUntil = $now + $lockSeconds;
            $entry['locked_until'] = $lockedUntil;
            $entry['fails'] = $fails;
            $store[$key] = $entry;
            $allowed = false;
            $retryAfter = $lockSeconds;
        } else {
            $entry['fails'] = $fails;
            $entry['locked_until'] = $lockedUntil;
            $store[$key] = $entry;
        }

        app_login_rate_limit_write_storage($fh, $store);
        flock($fh, LOCK_UN);
        fclose($fh);

        return ['allowed' => $allowed, 'retry_after' => $retryAfter];
    }

    function app_login_rate_limit_record_failure(string $username): void
    {
        $path = app_login_rate_limit_file();
        $fh = fopen($path, 'c+');
        if ($fh === false) {
            return;
        }

        $maxAttempts = 5;
        $windowSeconds = 15 * 60;
        $lockSeconds = 15 * 60;
        $now = time();
        $key = app_login_rate_limit_key($username);

        flock($fh, LOCK_EX);
        $store = app_login_rate_limit_read_storage($fh);
        $entry = $store[$key] ?? ['fails' => [], 'locked_until' => 0];

        $fails = array_values(array_filter((array) ($entry['fails'] ?? []), function ($ts) use ($now, $windowSeconds) {
            return is_int($ts) && ($now - $ts) <= $windowSeconds;
        }));
        $fails[] = $now;

        $entry['fails'] = $fails;
        if (count($fails) >= $maxAttempts) {
            $entry['locked_until'] = $now + $lockSeconds;
        }

        $store[$key] = $entry;
        app_login_rate_limit_write_storage($fh, $store);
        flock($fh, LOCK_UN);
        fclose($fh);
    }

    function app_login_rate_limit_clear(string $username): void
    {
        $path = app_login_rate_limit_file();
        $fh = fopen($path, 'c+');
        if ($fh === false) {
            return;
        }

        $key = app_login_rate_limit_key($username);

        flock($fh, LOCK_EX);
        $store = app_login_rate_limit_read_storage($fh);
        unset($store[$key]);
        app_login_rate_limit_write_storage($fh, $store);
        flock($fh, LOCK_UN);
        fclose($fh);
    }
}

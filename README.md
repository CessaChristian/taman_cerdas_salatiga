# Taman Cerdas

Sistem informasi dan reservasi fasilitas **Taman Cerdas Salatiga** berbasis web. Aplikasi ini memungkinkan masyarakat untuk melihat informasi fasilitas, melakukan reservasi tempat, mengunggah bukti pembayaran, serta berdiskusi melalui forum komunitas.

## Fitur Utama

- **Reservasi Fasilitas** — Booking Pendopo, Ruang Baca, dan Taman Bermain dengan pengecekan ketersediaan real-time
- **Sistem Pembayaran** — Upload bukti transfer/QRIS dengan batas waktu 24 jam dan auto-cancel
- **Panel Admin** — Dashboard statistik, approval/reject reservasi, lihat bukti pembayaran
- **Forum Komunitas** — Buat post, reply bersarang (nested), edit & hapus
- **Autentikasi** — Login/register dengan sliding panel, 2 level user (user & admin)
- **Responsive Design** — Optimasi tampilan untuk desktop, tablet, dan mobile

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | PHP 8.1+ |
| Database | MySQL / MariaDB 10.4+ |
| Frontend | Bootstrap 5.3, Bootstrap Icons, Vanilla JS |
| Styling | Custom CSS (component-based) |
| Server | Apache (XAMPP / Laragon / LAMP) |

## Struktur Project

```
taman_cerdas_php/
├── admin/                  # Panel admin (dashboard, kelola reservasi)
├── assets/
│   ├── css/                # Stylesheet per halaman & komponen
│   └── images/             # Foto fasilitas
├── forum/                  # Modul forum (post, reply, edit)
├── includes/
│   ├── components/         # Komponen reusable (gallery, facilities, dll)
│   ├── header.php          # Header publik
│   ├── footer.php          # Footer publik
│   ├── admin_header.php    # Header admin
│   ├── auto_cancel.php     # Auto-cancel reservasi expired
│   └── database.sample.php # Template konfigurasi database
├── reservasi/              # Modul reservasi (CRUD, upload bukti, cek ketersediaan)
├── user/                   # Panel user (profil, daftar reservasi)
├── index.php               # Homepage
├── about.php               # Halaman tentang
├── event.php               # Info event & fasilitas
├── login.php               # Halaman login/register
├── tr_rpl.sql              # Database schema
└── README.md
```

## Instalasi

### Prasyarat

- PHP 8.1 atau lebih baru
- MySQL / MariaDB
- Apache web server (XAMPP, Laragon, atau LAMP stack)

### Langkah Setup

1. **Clone repository**

   ```bash
   git clone https://github.com/username/taman-cerdas-php.git
   ```

2. **Pindahkan ke direktori web server**

   ```bash
   # XAMPP
   cp -r taman-cerdas-php/ /opt/lampp/htdocs/taman_cerdas_php

   # Laragon
   cp -r taman-cerdas-php/ C:/laragon/www/taman_cerdas_php
   ```

3. **Buat database**

   Import file `tr_rpl.sql` melalui phpMyAdmin atau terminal:

   ```bash
   mysql -u root -p < tr_rpl.sql
   ```

4. **Konfigurasi database**

   Salin template konfigurasi dan sesuaikan kredensial:

   ```bash
   cp includes/database.sample.php includes/database.php
   ```

   Edit `includes/database.php` sesuai environment lokal.

5. **Jalankan aplikasi**

   Buka browser dan akses:

   ```
   http://localhost/taman_cerdas_php
   ```

## Database Schema

```
user            data_user         reservasi
┌──────────┐   ┌────────────┐   ┌─────────────────┐
│ username  │──<│ username   │   │ id              │
│ password  │   │ nama       │   │ username        │>── user
│ level     │   │ email      │   │ nama_penyewa    │
└──────────┘   └────────────┘   │ tanggal_mulai   │
      │                          │ tanggal_selesai  │
      │         post             │ status           │
      │        ┌────────────┐   │ pendopo          │
      └───────<│ username   │   │ ruang_baca       │
               │ title      │   │ taman_bermain    │
               │ content    │   │ total_bayar      │
               │ created_at │   │ bukti_transfer   │
               └────────────┘   └─────────────────┘
                     │
                replies
               ┌────────────────┐
               │ post_id        │>── post
               │ username       │>── user
               │ content        │
               │ parent_id      │
               │ reply_to_user  │
               └────────────────┘
```

## Screenshot

> Tambahkan screenshot aplikasi di sini untuk melengkapi portfolio.

| Halaman | Preview |
|---------|---------|
| Homepage | _screenshot_ |
| Reservasi | _screenshot_ |
| Forum | _screenshot_ |
| Admin Panel | _screenshot_ |

## Lisensi

Project ini dibuat untuk keperluan akademik dan portfolio.

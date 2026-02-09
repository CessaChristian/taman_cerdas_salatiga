<footer class="footer-section">
    <div class="container">
        <div class="footer-main">
            <div class="footer-brand">
                <a href="<?php echo $base_path ?? './'; ?>index.php" class="footer-logo">
                    <span class="brand-icon">🌳</span>
                    <span>Taman Cerdas</span>
                </a>
                <p>Tempat di mana rekreasi, edukasi, dan alam berpadu harmonis untuk pengalaman tak terlupakan bagi seluruh keluarga.</p>
                <div class="footer-social">
                    <a href="#" class="social-link"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="social-link"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="social-link"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="social-link"><i class="bi bi-youtube"></i></a>
                </div>
            </div>

            <div class="footer-links">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="<?php echo $base_path ?? './'; ?>index.php">Home</a></li>
                    <li><a href="<?php echo $base_path ?? './'; ?>about.php">About Us</a></li>
                    <li><a href="<?php echo $base_path ?? './'; ?>event.php">Event</a></li>
                    <li><a href="<?php echo $base_path ?? './'; ?>forum/index.php">Forum</a></li>
                </ul>
            </div>

            <div class="footer-links">
                <h4>Fasilitas</h4>
                <ul>
                    <li><a href="<?php echo $base_path ?? './'; ?>reservasi/reservasi.php">Pendopo</a></li>
                    <li><a href="<?php echo $base_path ?? './'; ?>reservasi/reservasi.php">Ruang Baca</a></li>
                    <li><a href="<?php echo $base_path ?? './'; ?>reservasi/reservasi.php">Taman Bermain</a></li>
                    <li><a href="<?php echo $base_path ?? './'; ?>reservasi/reservasi.php">Reservasi</a></li>
                </ul>
            </div>

            <div class="footer-contact">
                <h4>Kontak</h4>
                <div class="contact-item">
                    <i class="bi bi-geo-alt-fill"></i>
                    <span>Sidorejo Lor, Kec. Sidorejo, Kota Salatiga, Jawa Tengah</span>
                </div>
                <div class="contact-item">
                    <i class="bi bi-telephone-fill"></i>
                    <span>+62 812-3456-7890</span>
                </div>
                <div class="contact-item">
                    <i class="bi bi-envelope-fill"></i>
                    <span>info@tamancerdas.id</span>
                </div>
                <div class="contact-item">
                    <i class="bi bi-clock-fill"></i>
                    <span>Senin - Minggu: 08.00 - 17.00 WIB</span>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date("Y"); ?> Taman Cerdas Salatiga. All Rights Reserved.</p>
            <div class="footer-bottom-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

<style>
/* ===================================================
   FOOTER STYLES
   =================================================== */

.footer-section {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    color: #94a3b8;
    padding-top: 80px;
}

.footer-main {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1.5fr;
    gap: 40px;
    padding-bottom: 60px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.footer-brand .footer-logo {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    color: white;
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 16px;
}

.footer-brand .brand-icon {
    font-size: 1.8rem;
}

.footer-brand p {
    line-height: 1.8;
    margin-bottom: 24px;
}

.footer-social {
    display: flex;
    gap: 12px;
}

.social-link {
    width: 42px;
    height: 42px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
}

.social-link:hover {
    background: #2563eb;
    transform: translateY(-3px);
    color: white;
}

.footer-links h4,
.footer-contact h4 {
    color: white;
    font-weight: 600;
    margin-bottom: 24px;
    font-size: 1.1rem;
}

.footer-links ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links ul li {
    margin-bottom: 12px;
}

.footer-links ul li a {
    color: #94a3b8;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
}

.footer-links ul li a:hover {
    color: white;
    padding-left: 8px;
}

.contact-item {
    display: flex;
    gap: 12px;
    margin-bottom: 16px;
    align-items: flex-start;
}

.contact-item i {
    color: #2563eb;
    font-size: 1rem;
    margin-top: 4px;
}

.contact-item span {
    line-height: 1.6;
}

.footer-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 30px 0;
}

.footer-bottom p {
    margin: 0;
}

.footer-bottom-links {
    display: flex;
    gap: 24px;
}

.footer-bottom-links a {
    color: #94a3b8;
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer-bottom-links a:hover {
    color: white;
}

/* Footer Responsive */
@media (max-width: 1024px) {
    .footer-main {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .footer-main {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .footer-social {
        justify-content: center;
    }

    .contact-item {
        justify-content: center;
    }

    .footer-bottom {
        flex-direction: column;
        gap: 16px;
        text-align: center;
    }
}
</style>

<!-- Bootstrap JS + Popper -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>

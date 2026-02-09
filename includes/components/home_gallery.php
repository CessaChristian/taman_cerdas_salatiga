<!-- Galeri Foto Section -->
<section id="galeri" class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Galeri Taman Cerdas</h2>
            <p class="text-muted">Abadikan momen berharga Anda di setiap sudut taman kami yang indah.</p>
        </div>
        
        <div class="gallery-vertical-scroll">
            <div class="row g-4">
                <?php
                    // Daftar gambar yang terkurasi untuk galeri homepage
                    $curated_images = [
                        'foto_taman-cerdas_01122023-002934.png',
                        'gardu_pandang.jpg',
                        'Mushola.jpg',
                        'Toilet.jpg',
                        'Taman.jpg',
                        'Pendopo.jpg',
                        'Ruang_baca.jpg',
                        'umkm.jpg' // Contoh gambar lain jika ada
                    ];

                    foreach ($curated_images as $image_file) {
                        echo '<div class="col-lg-4 col-md-6 mb-4">';
                        echo '<div class="gallery-item">';
                        echo '<img src="assets/images/' . htmlspecialchars($image_file) . '" class="img-fluid" alt="Galeri Taman Cerdas">';
                        echo '</div>';
                        echo '</div>';
                    }
                ?>
            </div>
        </div>
    </div>
</section>

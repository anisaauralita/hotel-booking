<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Ambil daftar kamar yang tersedia
$sql = "SELECT * FROM rooms WHERE status='tersedia' LIMIT 6";
$result = $conn->query($sql);
$rooms = $result->fetch_all(MYSQLI_ASSOC);
?>

<?php include 'includes/header.php'; ?>

<!-- CSS Kustom -->
<style>
    :root {
        --primary: #0f3460;
        --secondary: #16213e;
        --accent: #0ea5e9;
        --light: #f8fafc;
        --dark: #1e293b;
        --success: #10b981;
        --warning: #fbbf24;
        --info: #3b82f6;
        --danger: #ef4444;
        --grey-light: #e2e8f0;
        --grey-dark: #64748b;
    }

    body {
        font-family: 'Montserrat', sans-serif;
        color: var(--dark);
        background-color: #f8fafc;
        overflow-x: hidden;
    }
    
    .hero-section {
        background: linear-gradient(rgba(51, 104, 168, 0.6), rgba(67, 69, 73, 0.7)), 
                    url('assets/images/oke.png');
        background-size: cover;
        background-position: center center;
        background-attachment: fixed;
        height: 85vh;
        display: flex;
        align-items: center;
        position: relative;
        margin-bottom: 6rem;
        clip-path: polygon(0 0, 100% 0, 100% 90%, 0 100%);
        overflow: hidden;
    }
    
    /* Elemen background bergerak (parallax) */
    .hero-parallax {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 0;
    }
    
    .hero-parallax span {
        position: absolute;
        display: block;
        width: 20px;
        height: 20px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 50%;
        animation: float 15s linear infinite;
    }
    
    .hero-content {
        position: relative;
        z-index: 2;
        max-width: 800px;
        animation: fadeInUp 1s ease;
    }
     @keyframes float {
        0% {
            transform: translateY(0) rotate(0deg);
            opacity: 0;
            border-radius: 50%;
        }
        10% {
            opacity: 1;
        }
        90% {
            opacity: 1;
        }
        100% {
            transform: translateY(-1000px) rotate(720deg);
            opacity: 0;
        }
    }
    
    /* Animasi untuk teks hero yang muncul */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .hero-tagline {
        font-weight: 300;
        letter-spacing: 3px;
        border-left: 3px solid var(--accent);
        padding-left: 1rem;
        text-transform: uppercase;
    }
    
    .hero-title {
        font-weight: 800;
        font-size: 4rem;
        margin-bottom: 1.5rem;
        line-height: 1.1;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    
    .btn-reservation {
        background: linear-gradient(45deg, var(--accent), #38bdf8);
        color: white;
        border: none;
        padding: 0.9rem 2.8rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        border-radius: 50px;
        box-shadow: 0 10px 25px rgba(14, 165, 233, 0.4);
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        position: relative;
        overflow: hidden;
        z-index: 1;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% {
            box-shadow: 0 10px 25px rgba(14, 165, 233, 0.4);
        }
        50% {
            box-shadow: 0 15px 30px rgba(14, 165, 233, 0.6);
        }
        100% {
            box-shadow: 0 10px 25px rgba(14, 165, 233, 0.4);
        }
    }
    
    .btn-reservation:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 0%;
        height: 100%;
        background: linear-gradient(45deg, #0284c7, var(--accent));
        transition: all 0.4s ease;
        z-index: -1;
        border-radius: 50px;
    }
    
    .btn-reservation:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 25px rgba(14, 165, 233, 0.5);
    }
    
    .btn-reservation:hover:before {
        width: 100%;
    }
    
    .search-availability {
        background: white;
        border-radius: 16px;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.1);
        padding: 2.5rem;
        margin-top: -120px;
        position: relative;
        z-index: 10;
        border-top: 5px solid var(--accent);
    }
    
    .search-availability label {
        font-weight: 600;
        color: var(--secondary);
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
        display: block;
    }
    
    .search-availability .form-control,
    .search-availability .form-select {
        border: 2px solid var(--grey-light);
        padding: 0.8rem 1rem;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .search-availability .form-control:focus,
    .search-availability .form-select:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
    }
    
    .section-header {
        margin-bottom: 4rem;
        text-align: center;
        position: relative;
    }
    
    .section-title {
        font-size: 2.4rem;
        font-weight: 800;
        color: var(--primary);
        margin-bottom: 1rem;
        position: relative;
        display: inline-block;
    }
    
    .section-title:after {
        content: '';
        position: absolute;
        width: 30%;
        height: 4px;
        background: var(--accent);
        bottom: -10px;
        left: 35%;
        border-radius: 2px;
    }
    
    .section-subtitle {
        color: var(--grey-dark);
        font-weight: 400;
        max-width: 700px;
        margin: 1.5rem auto 0;
        line-height: 1.7;
    }
    
    .room-card {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        margin-bottom: 30px;
        transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
        background: white;
        height: 100%;
    }
    
    .room-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.15);
    }
    
    .room-image-container {
        position: relative;
        overflow: hidden;
        height: 260px;
    }
    
    .room-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.8s ease;
    }
    
    .room-card:hover .room-image {
        transform: scale(1.08);
    }
    
    .room-price-badge {
        position: absolute;
        bottom: 20px;
        right: 20px;
        background: linear-gradient(45deg, var(--accent), #38bdf8);
        color: white;
        padding: 0.6rem 1.2rem;
        border-radius: 50px;
        font-weight: 700;
        box-shadow: 0 5px 15px rgba(14, 165, 233, 0.3);
        font-size: 1.1rem;
    }
    
    .room-body {
        padding: 2rem;
    }
    
    .room-type {
        color: var(--accent);
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 0.8rem;
        display: inline-block;
        background: rgba(14, 165, 233, 0.1);
        padding: 0.3rem 1rem;
        border-radius: 30px;
    }
    
    .room-title {
        font-size: 1.6rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: var(--secondary);
    }
    
    .room-features {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 1.8rem;
        gap: 1.2rem;
    }
    
    .feature-item {
        display: flex;
        align-items: center;
        color: var(--grey-dark);
        font-size: 0.9rem;
        background: var(--light);
        padding: 0.5rem 1rem;
        border-radius: 30px;
        transition: all 0.3s ease;
    }
    
    .feature-item:hover {
        background: rgba(14, 165, 233, 0.1);
        color: var(--accent);
    }
    
    .feature-icon {
        color: var(--accent);
        margin-right: 0.5rem;
        font-size: 1rem;
    }
    
    .room-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid var(--grey-light);
        padding-top: 1.5rem;
        margin-top: 0.5rem;
    }
    
    .btn-details {
        background-color: transparent;
        color: var(--primary);
        border: 2px solid var(--primary);
        padding: 0.7rem 1.8rem;
        font-weight: 600;
        border-radius: 50px;
        transition: all 0.3s ease;
    }
    
    .btn-details:hover {
        background-color: var(--primary);
        color: white;
    }
    
    .view-all-container {
        text-align: center;
        margin: 4rem 0 6rem;
    }
    
    .btn-view-all {
        background: linear-gradient(45deg, var(--primary), #1e3a8a);
        color: white;
        border: none;
        padding: 0.9rem 2.8rem;
        font-weight: 600;
        border-radius: 50px;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px rgba(15, 52, 96, 0.2);
    }
    
    .btn-view-all:hover {
        background: linear-gradient(45deg, #1e3a8a, var(--primary));
        transform: translateY(-3px);
        box-shadow: 0 15px 25px rgba(15, 52, 96, 0.3);
    }
    
    .hotel-features {
        background-color: #f0f5ff;
        padding: 7rem 0;
        margin-top: 3rem;
        position: relative;
        overflow: hidden;
    }
    
    .hotel-features:before {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        background: rgba(14, 165, 233, 0.05);
        border-radius: 50%;
        top: -100px;
        left: -100px;
    }
    
    .hotel-features:after {
        content: '';
        position: absolute;
        width: 200px;
        height: 200px;
        background: rgba(14, 165, 233, 0.05);
        border-radius: 50%;
        bottom: -50px;
        right: -50px;
    }
    
    .feature-card {
        text-align: center;
        padding: 3rem 2rem;
        border-radius: 16px;
        background-color: white;
        box-shadow: 0 15px 35px rgba(15, 23, 42, 0.05);
        height: 100%;
        transition: all 0.4s ease;
        position: relative;
        z-index: 1;
        overflow: hidden;
    }
    
    .feature-card:before {
        content: '';
        position: absolute;
        width: 100%;
        height: 0;
        background: linear-gradient(180deg, rgba(14, 165, 233, 0.05) 0%, rgba(14, 165, 233, 0) 100%);
        left: 0;
        bottom: 0;
        transition: all 0.5s ease;
        z-index: -1;
    }
    
    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.1);
    }
    
    .feature-card:hover:before {
        height: 100%;
    }
    
    .feature-icon-large {
        font-size: 2.2rem;
        color: white;
        background: linear-gradient(45deg, var(--accent), #38bdf8);
        width: 90px;
        height: 90px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin: 0 auto 2rem;
        box-shadow: 0 10px 20px rgba(14, 165, 233, 0.2);
        transition: all 0.3s ease;
    }
    
    .feature-card:hover .feature-icon-large {
        transform: rotateY(180deg);
    }
    
    .feature-title {
        font-weight: 700;
        font-size: 1.3rem;
        margin-bottom: 1.2rem;
        color: var(--primary);
    }
    
    .feature-description {
        color: var(--grey-dark);
        font-size: 0.95rem;
        line-height: 1.7;
    }
    
    .rating {
        display: flex;
        color: var(--warning);
        margin-bottom: 0.8rem;
        font-size: 1rem;
    }
    
    .testimonials-section {
        background-color: var(--light);
        padding: 5rem 0;
    }
    
    .cta-section {
        background: linear-gradient(rgba(15, 52, 96, 0.9), rgba(22, 33, 62, 0.9)), 
                    url('../assets/images/hotel-luxury.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        padding: 7rem 0;
        color: white;
        text-align: center;
        position: relative;
    }
    
    .cta-section:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 10px;
        background: linear-gradient(90deg, var(--accent), #38bdf8);
    }
    
    .cta-title {
        font-size: 2.8rem;
        font-weight: 800;
        margin-bottom: 1.5rem;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    
    .cta-text {
        max-width: 700px;
        margin: 0 auto 2.5rem;
        font-size: 1.15rem;
        opacity: 0.9;
        line-height: 1.7;
    }
    
    .newsletter-form .form-control {
        height: 54px;
        border-radius: 50px 0 0 50px;
        padding: 0 1.5rem;
        font-size: 1rem;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .newsletter-form .btn {
        border-radius: 0 50px 50px 0;
        padding: 0 2rem;
        height: 54px;
        font-weight: 600;
        background: linear-gradient(45deg, var(--accent), #38bdf8);
        box-shadow: 0 5px 15px rgba(14, 165, 233, 0.3);
    }
    
    .newsletter-form .btn:hover {
        background: linear-gradient(45deg, #38bdf8, var(--accent));
    }
    
    /* Animasi scroll reveal */
    .reveal {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.8s ease;
    }
    
    .reveal.active {
        opacity: 1;
        transform: translateY(0);
    }
    
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.8rem;
        }
        
        .search-availability {
            margin-top: 0;
            border-radius: 0;
        }
        
        .hero-section {
            height: 100vh;
            background-attachment: scroll;
        }
        
        .cta-title {
            font-size: 2.2rem;
        }
        
        .section-title {
            font-size: 2rem;
        }
        
        .feature-card {
            margin-bottom: 2rem;
        }
    }
</style>

<!-- Hero Section -->
<section class="hero-section">
    <!-- Elemen parallax untuk animasi background -->
    <div class="hero-parallax" id="parallax-container"></div>
    
    <div class="container">
        <div class="hero-content" style="padding: 3rem;">
            <p class="hero-tagline text-white mb-3">PENGALAMAN MENGINAP EKSKLUSIF</p>
            <h1 class="hero-title text-white">Temukan Kemewahan dalam Kenyamanan</h1>
            <p class="lead text-white mb-4 opacity-90">Nikmati pengalaman menginap tak terlupakan dengan layanan premium dan fasilitas modern</p>
            <a href="search.php" class="btn btn-reservation">Pesan Kamar Sekarang</a>
        </div>
    </div>
</section>

<div class="container">
    <!-- Search Availability Form -->
    <div class="search-availability mb-5 reveal">
        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Check-in</label>
                    <input type="date" class="form-control" min="<?= date('Y-m-d') ?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Check-out</label>
                    <input type="date" class="form-control" min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Tamu</label>
                    <select class="form-select">
                        <option value="1">1 Tamu</option>
                        <option value="2">2 Tamu</option>
                        <option value="3">3 Tamu</option>
                        <option value="4">4 Tamu</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <button class="btn btn-reservation">Cek Ketersediaan</button>
        </div>
    </div>

    <!-- Available Rooms Section -->
    <div class="section-header reveal">
        <h2 class="section-title">Kamar Tersedia</h2>
        <p class="section-subtitle">Pilih kamar yang sesuai dengan kebutuhan Anda. Semua kamar kami dilengkapi dengan fasilitas modern untuk kenyamanan maksimal.</p>
    </div>

    <div class="row">
        <?php foreach ($rooms as $room): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="room-card reveal">
                    <div class="room-image-container">
                        <img src="<?= BASE_URL ?>assets/images/rooms/<?= $room['foto'] ?>" 
                         class="card-img-top" 
                         alt="<?= $room['tipe'] ?>"
                         style="object-fit: cover;">
                        <div class="room-price-badge">
                            Rp <?= number_format($room['harga'], 0, ',', '.') ?> / malam
                        </div>
                    </div>
                    <div class="room-body">
                        <div class="room-type"><?= ucfirst($room['tipe']) ?></div>
                        <h3 class="room-title">Kamar <?= $room['nomor_kamar'] ?></h3>
                        
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        
                        <div class="room-features">
                            <div class="feature-item">
                                <i class="fas fa-wifi feature-icon"></i> WiFi Gratis
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-bath feature-icon"></i> Kamar Mandi
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-snowflake feature-icon"></i> AC
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-tv feature-icon"></i> TV LED
                            </div>
                        </div>
                        
                        <div class="room-actions">
                            <a href="room-detail.php?id=<?= $room['id'] ?>" class="btn btn-details">
                                Lihat Detail
                            </a>
                            <a href="<?= BASE_URL ?>user/reservations/book.php?room_id=<?= $room['id'] ?>" class="btn btn-reservation">
                            Pesan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="view-all-container reveal">
        <a href="search.php" class="btn btn-view-all">
            Lihat Semua Kamar <i class="fas fa-arrow-right ms-2"></i>
        </a>
    </div>
</div>

<!-- Hotel Features Section -->
<section class="hotel-features">
    <div class="container">
        <div class="section-header reveal">
            <h2 class="section-title">Fasilitas Unggulan</h2>
            <p class="section-subtitle">Nikmati berbagai fasilitas eksklusif yang kami sediakan untuk memastikan kenyamanan Anda selama menginap</p>
        </div>
        
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-card reveal">
                    <div class="feature-icon-large">
                        <i class="fas fa-concierge-bell"></i>
                    </div>
                    <h4 class="feature-title">Layanan 24 Jam</h4>
                    <p class="feature-description">Tim layanan kami siap membantu setiap kebutuhan Anda sepanjang hari</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-card reveal">
                    <div class="feature-icon-large">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h4 class="feature-title">Restoran Premium</h4>
                    <p class="feature-description">Nikmati sajian kuliner terbaik dari chef profesional kami</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-card reveal">
                    <div class="feature-icon-large">
                        <i class="fas fa-swimming-pool"></i>
                    </div>
                    <h4 class="feature-title">Kolam Renang</h4>
                    <p class="feature-description">Bersantai dan nikmati kolam renang dengan pemandangan kota</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-card reveal">
                    <div class="feature-icon-large">
                        <i class="fas fa-spa"></i>
                    </div>
                    <h4 class="feature-title">Spa & Wellness</h4>
                    <p class="feature-description">Rilekskan tubuh dan pikiran dengan layanan spa eksklusif</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="cta-section">
    <div class="container">
        <h2 class="cta-title reveal">Dapatkan Penawaran Spesial</h2>
        <p class="cta-text reveal">Berlangganan newsletter kami untuk mendapatkan penawaran eksklusif dan diskon spesial untuk kunjungan berikutnya</p>
        <div class="row justify-content-center">
            <div class="col-md-6 reveal">
                <div class="input-group newsletter-form">
                    <input type="email" class="form-control" placeholder="Alamat Email Anda">
                    <button class="btn" type="button">Berlangganan</button>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<!-- Font Montserrat dari Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<!-- Font Awesome untuk Icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<!-- Script untuk animasi scroll reveal -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Animasi scroll reveal
    window.addEventListener('scroll', reveal);
    reveal();
    
    function reveal() {
        var reveals = document.querySelectorAll('.reveal');
        
        for(var i = 0; i < reveals.length; i++) {
            var windowHeight = window.innerHeight;
            var revealTop = reveals[i].getBoundingClientRect().top;
            var revealPoint = 150;
            
            if(revealTop < windowHeight - revealPoint) {
                reveals[i].classList.add('active');
            }
        }
    }
    
    // Membuat elemen-elemen parallax floating di background
    const container = document.getElementById('parallax-container');
    const count = 20; // Jumlah elemen floating
    
    for (let i = 0; i < count; i++) {
        const span = document.createElement('span');
        const size = Math.random() * 30 + 10; // Ukuran acak
        
        span.style.width = size + 'px';
        span.style.height = size + 'px';
        span.style.top = Math.random() * 100 + '%';
        span.style.left = Math.random() * 100 + '%';
        span.style.opacity = Math.random() * 0.3;
        span.style.animationDelay = Math.random() * 10 + 's';
        span.style.animationDuration = Math.random() * 20 + 15 + 's';
        
        container.appendChild(span);
    }
    
    // Parallax effect saat menggulir - background bergerak ke atas
    const heroSection = document.querySelector('.hero-section');
    
    // Set posisi awal background
    heroSection.style.backgroundPositionY = '0px';
    
    window.addEventListener('scroll', function() {
        const scrollPosition = window.pageYOffset;
        
        // Menggeser background ke atas saat scroll ke bawah
        if (scrollPosition <= heroSection.offsetHeight) {
            // Gunakan transform untuk performa yang lebih baik
            const yPos = -scrollPosition * 0.3;
            heroSection.style.backgroundPositionY = yPos + 'px';
        }
    });
    
    // Efek hover untuk tombol
    const reservationBtn = document.querySelector('.btn-reservation');
    
    reservationBtn.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px)';
    });
    
    reservationBtn.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
        window.addEventListener('scroll', function() {
        const heroSection = document.querySelector('.hero-section');
        const scrollPosition = window.pageYOffset;
        
        // Menggeser background ke atas saat scroll ke bawah
        if (scrollPosition <= heroSection.offsetHeight) {
            heroSection.style.backgroundPositionY = -scrollPosition * 0.3 + 'px';
        }
    });
});

</script>
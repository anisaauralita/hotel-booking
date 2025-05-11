<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$type = isset($_GET['type']) ? sanitize($_GET['type']) : '';
$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 10000000;

$sql = "SELECT * FROM rooms WHERE status='tersedia'";
if (!empty($search)) {
    $sql .= " AND (nomor_kamar LIKE '%$search%' OR tipe LIKE '%$search%')";
}
if (!empty($type)) {
    $sql .= " AND tipe='$type'";
}
$sql .= " AND harga BETWEEN $min_price AND $max_price";

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
    
    .search-header {
        background: linear-gradient(rgba(51, 104, 168, 0.8), rgba(67, 69, 73, 0.9)), 
                    url('assets/images/oke.png');
        background-size: cover;
        background-position: center top;
        padding: 6rem 0 8rem;
        margin-bottom: -4rem;
        position: relative;
        clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
    }
    
    .search-title {
        font-weight: 800;
        font-size: 3rem;
        color: white;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        margin-bottom: 1rem;
    }
    
    .search-subtitle {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1.1rem;
        max-width: 700px;
        margin: 0 auto;
    }
    
    .search-form-container {
        background: white;
        border-radius: 16px;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.1);
        padding: 2.5rem;
        position: relative;
        z-index: 10;
        border-top: 5px solid var(--accent);
    }
    
    .search-form-container .form-control,
    .search-form-container .form-select {
        border: 2px solid var(--grey-light);
        padding: 0.8rem 1rem;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }
    
    .search-form-container .form-control:focus,
    .search-form-container .form-select:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
    }
    
    .search-form-container .input-group-text {
        background-color: var(--grey-light);
        border: 2px solid var(--grey-light);
        font-weight: 600;
        color: var(--primary);
    }
    
    .search-btn {
        background: linear-gradient(45deg, var(--accent), #38bdf8);
        color: white;
        border: none;
        padding: 0.8rem 2rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        border-radius: 50px;
        box-shadow: 0 10px 25px rgba(14, 165, 233, 0.4);
        transition: all 0.3s ease;
        width: 100%;
    }
    
    .search-btn:hover {
        background: linear-gradient(45deg, #38bdf8, var(--accent));
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(14, 165, 233, 0.5);
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
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.15);
    }
    
    .room-image-container {
        position: relative;
        overflow: hidden;
        height: 250px; /* Fix height for all images */
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
    
    .btn-book {
        background: linear-gradient(45deg, var(--accent), #38bdf8);
        color: white;
        border: none;
        padding: 0.7rem 1.8rem;
        font-weight: 600;
        border-radius: 50px;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(14, 165, 233, 0.2);
    }
    
    .btn-book:hover {
        background: linear-gradient(45deg, #38bdf8, var(--accent));
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(14, 165, 233, 0.3);
    }
    
    .rating {
        display: flex;
        color: var(--warning);
        margin-bottom: 0.8rem;
        font-size: 1rem;
    }
    
    .search-results-header {
        margin: 3rem 0 2rem;
        text-align: center;
    }
    
    .search-results-title {
        font-weight: 800;
        color: var(--primary);
        font-size: 2.2rem;
        margin-bottom: 1rem;
        position: relative;
        display: inline-block;
    }
    
    .search-results-title:after {
        content: '';
        position: absolute;
        width: 30%;
        height: 4px;
        background: var(--accent);
        bottom: -10px;
        left: 35%;
        border-radius: 2px;
    }
    
    .search-results-count {
        color: var(--grey-dark);
        font-size: 1.1rem;
    }
    
    .no-results {
        background: #fff;
        border-radius: 16px;
        padding: 3rem;
        text-align: center;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    }
    
    .no-results i {
        font-size: 4rem;
        color: var(--grey-dark);
        margin-bottom: 1.5rem;
        opacity: 0.5;
    }
    
    .no-results h3 {
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 1rem;
    }
    
    .no-results p {
        color: var(--grey-dark);
        margin-bottom: 2rem;
    }
    
    @media (max-width: 768px) {
        .search-title {
            font-size: 2.2rem;
        }
        
        .search-header {
            padding: 4rem 0 6rem;
        }
        
        .search-form-container {
            margin-top: -2rem;
            padding: 1.5rem;
        }
        
        .room-image-container {
            height: 200px;
        }
    }
</style>

<!-- Search Header -->
<section class="search-header text-center">
    <div class="container">
        <h1 class="search-title">Temukan Kamar Ideal Anda</h1>
        <p class="search-subtitle">Pilih dari berbagai tipe kamar yang kami tawarkan untuk pengalaman menginap terbaik Anda</p>
    </div>
</section>

<div class="container">
    <!-- Search Form -->
    <div class="search-form-container mb-5">
        <form method="GET" action="search.php">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold">Cari Kamar</label>
                    <input type="text" name="search" class="form-control" placeholder="Nomor kamar atau tipe..." value="<?= $search ?>">
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold">Tipe Kamar</label>
                    <select name="type" class="form-select">
                        <option value="">Semua Tipe</option>
                        <option value="single" <?= $type == 'single' ? 'selected' : '' ?>>Single</option>
                        <option value="double" <?= $type == 'double' ? 'selected' : '' ?>>Double</option>
                        <option value="suite" <?= $type == 'suite' ? 'selected' : '' ?>>Suite</option>
                    </select>
                </div>
                <div class="col-lg-4 col-md-8">
                    <label class="form-label fw-semibold">Rentang Harga</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="min_price" class="form-control" placeholder="Min" value="<?= $min_price ?>">
                        <span class="input-group-text">-</span>
                        <input type="number" name="max_price" class="form-control" placeholder="Max" value="<?= $max_price ?>">
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search me-2"></i> Cari
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Search Results -->
    <div class="search-results-header">
        <h2 class="search-results-title">Hasil Pencarian</h2>
        <p class="search-results-count"><?= count($rooms) ?> kamar ditemukan</p>
    </div>

    <div class="row">
        <?php if (count($rooms) > 0): ?>
            <?php foreach ($rooms as $room): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="room-card">
                        <div class="room-image-container">
                            <img src="<?= BASE_URL ?>assets/images/rooms/<?= $room['foto'] ?>" 
                                 class="room-image" 
                                 alt="<?= $room['tipe'] ?>">
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
                                <a href="<?= BASE_URL ?>user/reservations/book.php?room_id=<?= $room['id'] ?>" class="btn btn-book">
                                    Pesan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <h3>Tidak ada kamar yang ditemukan</h3>
                    <p>Coba ubah kriteria pencarian Anda untuk menemukan kamar yang sesuai.</p>
                    <a href="search.php" class="btn btn-book">Reset Pencarian</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Font Montserrat dari Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<!-- Font Awesome untuk Icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<?php include 'includes/footer.php'; ?>

<!-- Script untuk animasi -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Animasi scroll reveal
    function reveal() {
        var reveals = document.querySelectorAll('.room-card');
        
        for(var i = 0; i < reveals.length; i++) {
            var windowHeight = window.innerHeight;
            var revealTop = reveals[i].getBoundingClientRect().top;
            var revealPoint = 150;
            
            if(revealTop < windowHeight - revealPoint) {
                reveals[i].style.opacity = '1';
                reveals[i].style.transform = 'translateY(0)';
            }
        }
    }
    
    // Set initial state
    var cards = document.querySelectorAll('.room-card');
    cards.forEach(function(card, index) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.5s ease ' + (index * 0.1) + 's';
    });
    
    // Trigger once on load
    setTimeout(reveal, 300);
    
    // Trigger on scroll
    window.addEventListener('scroll', reveal);
});
</script>
<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$room_id = sanitize($_GET['id']);
$sql = "SELECT * FROM rooms WHERE id='$room_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit();
}

$room = $result->fetch_assoc();
?>

<?php include 'includes/header.php'; ?>

<style>
    :root {
        --primary: #0f3460;
        --secondary: #16213e;
        --accent: #0ea5e9;
        --light: #f8fafc;
        --dark: #1e293b;
        --success: #10b981;
        --danger: #ef4444;
    }

    body {
        font-family: 'Montserrat', sans-serif;
        background-color: #f8fafc;
    }

    .room-detail-container {
        padding: 3rem 0;
    }

    .room-image-container {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.1);
        height: 500px;
    }

    .room-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.8s ease;
    }

    .room-image-container:hover .room-image {
        transform: scale(1.05);
    }

    .room-price-badge {
        position: absolute;
        bottom: 20px;
        right: 20px;
        background: linear-gradient(45deg, var(--accent), #38bdf8);
        color: white;
        padding: 0.8rem 1.5rem;
        border-radius: 50px;
        font-weight: 700;
        box-shadow: 0 5px 15px rgba(14, 165, 233, 0.3);
        font-size: 1.1rem;
    }

    .room-info-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        height: 100%;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    }

    .room-title {
        font-size: 2rem;
        font-weight: 800;
        color: var(--primary);
        margin-bottom: 1.5rem;
        position: relative;
    }

    .room-title:after {
        content: '';
        position: absolute;
        width: 60px;
        height: 4px;
        background: var(--accent);
        bottom: -10px;
        left: 0;
        border-radius: 2px;
    }

    .room-meta {
        margin-bottom: 2rem;
    }

    .room-status {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .status-available {
        background-color: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }

    .status-unavailable {
        background-color: rgba(239, 68, 68, 0.1);
        color: var(--danger);
    }

    .btn-booking {
        border-radius: 50px;
        padding: 0.8rem 2rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        margin-top: 1rem;
    }

    .btn-primary.btn-booking {
        background: linear-gradient(45deg, var(--accent), #38bdf8);
        border: none;
        box-shadow: 0 10px 25px rgba(14, 165, 233, 0.4);
    }

    .btn-primary.btn-booking:hover {
        background: linear-gradient(45deg, #38bdf8, var(--accent));
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(14, 165, 233, 0.5);
    }

    .btn-secondary.btn-booking {
        background-color: var(--dark);
        border: none;
    }

    .features-section {
        margin-top: 2.5rem;
    }

    .features-title {
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 1.5rem;
        position: relative;
    }

    .features-title:after {
        content: '';
        position: absolute;
        width: 40px;
        height: 3px;
        background: var(--accent);
        bottom: -8px;
        left: 0;
        border-radius: 3px;
    }

    .room-features {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .feature-item {
        display: flex;
        align-items: center;
        color: var(--dark);
        font-size: 0.95rem;
        background: var(--light);
        padding: 0.8rem 1rem;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .feature-item:hover {
        background: rgba(14, 165, 233, 0.1);
        color: var(--accent);
        transform: translateY(-2px);
    }

    .feature-icon {
        color: var(--accent);
        margin-right: 0.8rem;
        font-size: 1.1rem;
    }

    /* Tombol Kembali */
    .btn-back {
        display: inline-block;
        margin-top: 2rem;
        padding: 0.7rem 1.5rem;
        background: white;
        color: var(--primary);
        border: 2px solid var(--primary);
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(15, 52, 96, 0.1);
    }

    .btn-back:hover {
        background: var(--primary);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 52, 96, 0.2);
    }

    .btn-back i {
        margin-right: 0.5rem;
    }

    @media (max-width: 768px) {
        .room-image-container {
            height: 350px;
            margin-bottom: 1.5rem;
        }
        
        .room-features {
            grid-template-columns: 1fr;
        }
        
        .btn-back {
            width: 100%;
            text-align: center;
        }
    }
</style>

<div class="container room-detail-container">
    <div class="row">
        <div class="col-lg-6">
            <div class="room-image-container">
                <img src="<?= BASE_URL ?>assets/images/rooms/<?= $room['foto'] ?>" 
                     class="room-image" 
                     alt="<?= htmlspecialchars($room['tipe']) ?>"
                     onerror="this.src='<?= BASE_URL ?>assets/images/rooms/default.jpg'">
                <div class="room-price-badge">
                    Rp <?= number_format($room['harga'], 0, ',', '.') ?> / malam
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="room-info-card">
                <h1 class="room-title">Kamar <?= $room['nomor_kamar'] ?> - <?= ucfirst($room['tipe']) ?></h1>
                
                <div class="room-meta">
                    <span class="room-status <?= $room['status'] == 'tersedia' ? 'status-available' : 'status-unavailable' ?>">
                        <i class="fas fa-circle me-1"></i> <?= ucfirst($room['status']) ?>
                    </span>
                </div>
                
                <?php if ($room['status'] == 'tersedia'): ?>
                    <?php if (isLoggedIn() && !isAdmin()): ?>
                        <a href="user/reservations/book.php?room_id=<?= $room['id'] ?>" class="btn btn-primary btn-booking">
                            <i class="fas fa-calendar-check me-2"></i> Pesan Sekarang
                        </a>
                    <?php elseif (!isLoggedIn()): ?>
                        <a href="auth/login.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn btn-primary btn-booking">
                            <i class="fas fa-sign-in-alt me-2"></i> Login untuk Memesan
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <button class="btn btn-secondary btn-booking" disabled>
                        <i class="fas fa-times-circle me-2"></i> Kamar Tidak Tersedia
                    </button>
                <?php endif; ?>
                
                <div class="features-section">
                    <h4 class="features-title">Fasilitas Kamar</h4>
                    <div class="room-features">
                        <div class="feature-item">
                            <i class="fas fa-wifi feature-icon"></i> WiFi Gratis
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-snowflake feature-icon"></i> AC
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-tv feature-icon"></i> TV LED
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-bath feature-icon"></i> Kamar Mandi
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-bed feature-icon"></i> Tempat Tidur Nyaman
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-phone feature-icon"></i> Telepon
                        </div>
                    </div>
                </div>
                
                <!-- Tombol Kembali -->
                <a href="<?= BASE_URL ?>search.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Kembali ke Pencarian
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Font Awesome -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<?php include 'includes/footer.php'; ?>
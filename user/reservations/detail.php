<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

if (!isLoggedIn() || isAdmin()) {
    header("Location: ../../../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: ../dashboard.php");
    exit();
}

$reservation_id = sanitize($_GET['id']);
$user_id = $_SESSION['user_id'];

// Ambil data reservasi
$sql = "SELECT 
            res.*, 
            r.nomor_kamar, 
            r.tipe, 
            r.harga, 
            r.foto,
            u.nama as user_name,
            u.email as user_email
        FROM reservations res
        JOIN rooms r ON res.room_id = r.id
        JOIN users u ON res.user_id = u.id
        WHERE res.id='$reservation_id' AND res.user_id='$user_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: ../dashboard.php");
    exit();
}

$reservation = $result->fetch_assoc();

// Hitung total harga
$check_in = new DateTime($reservation['check_in']);
$check_out = new DateTime($reservation['check_out']);
$nights = $check_out->diff($check_in)->days;
$total = $nights * $reservation['harga'];
?>

<?php include '../../includes/header.php'; ?>

<style>
    :root {
        --primary: #0f3460;
        --secondary: #16213e;
        --accent: #0ea5e9;
        --light: #f8fafc;
        --dark: #1e293b;
        --success: #10b981;
        --warning: #fbbf24;
        --danger: #ef4444;
    }

    body {
        font-family: 'Montserrat', sans-serif;
        background-color: #f8fafc;
    }

    .detail-container {
        padding-top: 3rem;
        padding-bottom: 3rem;
    }

    .reservation-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.1);
        overflow: hidden;
    }

    .card-header {
        background: linear-gradient(45deg, var(--primary), var(--secondary));
        color: white;
        padding: 1.5rem;
        border-bottom: none;
    }

    .card-header h3 {
        font-weight: 700;
        margin: 0;
    }

    .card-body {
        padding: 2rem;
    }

    .room-image {
        width: 100%;
        height: 300px;
        object-fit: cover;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-bottom: 1.5rem;
    }

    .price-badge {
        background: linear-gradient(45deg, var(--accent), #38bdf8);
        color: white;
        padding: 0.6rem 1.2rem;
        border-radius: 50px;
        font-weight: 700;
        display: inline-block;
        margin-bottom: 1.5rem;
        box-shadow: 0 5px 15px rgba(14, 165, 233, 0.3);
    }

    .section-title {
        color: var(--primary);
        font-weight: 700;
        margin-bottom: 1.5rem;
        position: relative;
        padding-bottom: 0.5rem;
    }

    .section-title:after {
        content: '';
        position: absolute;
        width: 50px;
        height: 3px;
        background: var(--accent);
        bottom: 0;
        left: 0;
        border-radius: 3px;
    }

    .info-label {
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.3rem;
    }

    .info-value {
        color: var(--secondary);
        margin-bottom: 1rem;
    }

    .status-badge {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
    }

    .btn {
        border-radius: 50px;
        padding: 0.7rem 1.8rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-danger {
        background: linear-gradient(45deg, var(--danger), #f87171);
        border: none;
        box-shadow: 0 5px 15px rgba(239, 68, 68, 0.2);
    }

    .btn-danger:hover {
        background: linear-gradient(45deg, #f87171, var(--danger));
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(239, 68, 68, 0.3);
    }

    .btn-secondary {
        background: var(--dark);
        border: none;
    }

    .btn-secondary:hover {
        background: #334155;
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(30, 41, 59, 0.3);
    }

    .feature-icon {
        color: var(--accent);
        margin-right: 0.5rem;
    }

    @media (max-width: 768px) {
        .room-image {
            height: 200px;
        }
    }
</style>

<div class="container detail-container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="reservation-card card">
                <div class="card-header">
                    <h3>Detail Reservasi #<?= $reservation['id'] ?></h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <h5 class="section-title">Informasi Kamar</h5>
                            <img src="<?= BASE_URL ?>assets/images/rooms/<?= $reservation['foto'] ?>" 
                                 class="room-image" 
                                 alt="Kamar <?= $reservation['nomor_kamar'] ?>">
                            
                            <div class="price-badge">
                                Rp <?= number_format($reservation['harga'], 0, ',', '.') ?> / malam
                            </div>
                            
                            <div class="mb-3">
                                <p class="info-label">Tipe Kamar</p>
                                <p class="info-value"><?= ucfirst($reservation['tipe']) ?></p>
                            </div>
                            
                            <div class="mb-3">
                                <p class="info-label">Nomor Kamar</p>
                                <p class="info-value"><?= $reservation['nomor_kamar'] ?></p>
                            </div>
                            
                            <div class="room-features">
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="feature-item">
                                        <i class="fas fa-wifi feature-icon"></i> WiFi Gratis
                                    </span>
                                    <span class="feature-item">
                                        <i class="fas fa-snowflake feature-icon"></i> AC
                                    </span>
                                    <span class="feature-item">
                                        <i class="fas fa-tv feature-icon"></i> TV LED
                                    </span>
                                    <span class="feature-item">
                                        <i class="fas fa-bath feature-icon"></i> Kamar Mandi
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6 mb-4">
                            <h5 class="section-title">Detail Reservasi</h5>
                            
                            <div class="mb-3">
                                <p class="info-label">Status</p>
                                <span class="status-badge 
                                    <?= $reservation['status'] == 'confirmed' ? 'bg-success' : 
                                       ($reservation['status'] == 'canceled' ? 'bg-danger' : 'bg-warning') ?>">
                                    <?= ucfirst($reservation['status']) ?>
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <p class="info-label">Tanggal Check In</p>
                                <p class="info-value"><?= date('d M Y', strtotime($reservation['check_in'])) ?></p>
                            </div>
                            
                            <div class="mb-3">
                                <p class="info-label">Tanggal Check Out</p>
                                <p class="info-value"><?= date('d M Y', strtotime($reservation['check_out'])) ?></p>
                            </div>
                            
                            <div class="mb-3">
                                <p class="info-label">Durasi Menginap</p>
                                <p class="info-value"><?= $nights ?> malam</p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="info-label">Total Harga</p>
                                <p class="info-value" style="font-size: 1.2rem; font-weight: 700; color: var(--accent);">
                                    Rp <?= number_format($total, 0, ',', '.') ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <h5 class="section-title">Informasi Pemesan</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <p class="info-label">Nama Lengkap</p>
                                    <p class="info-value"><?= $reservation['user_name'] ?></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p class="info-label">Email</p>
                                    <p class="info-value"><?= $reservation['user_email'] ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <div>
                            <?php if ($reservation['status'] == 'pending'): ?>
                                <a href="cancel.php?id=<?= $reservation['id'] ?>" 
                                   class="btn btn-danger me-2" 
                                   onclick="return confirm('Yakin ingin membatalkan reservasi?')">
                                    <i class="fas fa-times me-1"></i> Batalkan Reservasi
                                </a>
                            <?php endif; ?>
                        </div>
                        <a href="../dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Font Awesome -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<?php include '../../includes/footer.php'; ?>
<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isAdmin()) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$reservation_id = sanitize($_GET['id']);
$sql = "SELECT 
            res.*,
            u.nama as user_name,
            u.email as user_email,
            r.nomor_kamar,
            r.tipe,
            r.harga,
            r.foto
        FROM reservations res
        JOIN users u ON res.user_id = u.id
        JOIN rooms r ON res.room_id = r.id
        WHERE res.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $reservation_id);
$stmt->execute();
$result = $stmt->get_result();
$reservation = $result->fetch_assoc();

if (!$reservation) {
    header("Location: index.php");
    exit();
}
?>

<?php include '../includes/header.php'; ?>

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

    .reservation-detail-container {
        padding: 3rem 0;
    }

    .reservation-image-container {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.1);
        height: 500px;
    }

    .reservation-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.8s ease;
    }

    .reservation-image-container:hover .reservation-image {
        transform: scale(1.05);
    }

    .reservation-price-badge {
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

    .reservation-info-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        height: 100%;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    }

    .reservation-title {
        font-size: 2rem;
        font-weight: 800;
        color: var(--primary);
        margin-bottom: 1.5rem;
        position: relative;
    }

    .reservation-title:after {
        content: '';
        position: absolute;
        width: 60px;
        height: 4px;
        background: var(--accent);
        bottom: -10px;
        left: 0;
        border-radius: 2px;
    }

    .reservation-meta {
        margin-bottom: 2rem;
    }

    .reservation-status {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .status-confirmed {
        background-color: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }

    .status-pending {
        background-color: rgba(251, 191, 36, 0.1);
        color: var(--warning);
    }

    .status-canceled {
        background-color: rgba(239, 68, 68, 0.1);
        color: var(--danger);
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .info-item {
        background: var(--light);
        padding: 1rem;
        border-radius: 8px;
    }

    .info-label {
        font-weight: 600;
        color: var(--primary);
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .info-value {
        font-size: 1.1rem;
    }

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
        .reservation-image-container {
            height: 350px;
            margin-bottom: 1.5rem;
        }
        
        .info-grid {
            grid-template-columns: 1fr;
        }
        
        .btn-back {
            width: 100%;
            text-align: center;
        }
    }
</style>

<div class="container reservation-detail-container">
    <div class="row">
        <div class="col-lg-6">
            <div class="reservation-image-container">
                <img src="<?= BASE_URL ?>assets/images/rooms/<?= $reservation['foto'] ?>" 
                     class="reservation-image" 
                     alt="Kamar <?= $reservation['nomor_kamar'] ?>"
                     onerror="this.src='<?= BASE_URL ?>assets/images/rooms/default.jpg'">
                <div class="reservation-price-badge">
                    Rp <?= number_format($reservation['harga'], 0, ',', '.') ?>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="reservation-info-card">
                <h1 class="reservation-title">Reservasi #<?= $reservation['id'] ?></h1>
                
                <div class="reservation-meta">
                    <span class="reservation-status <?= 'status-' . strtolower($reservation['status']) ?>">
                        <i class="fas fa-circle me-1"></i> <?= ucfirst($reservation['status']) ?>
                    </span>
                </div>
                
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Pelanggan</div>
                        <div class="info-value"><?= $reservation['user_name'] ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?= $reservation['user_email'] ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Kamar</div>
                        <div class="info-value"><?= $reservation['nomor_kamar'] ?> (<?= ucfirst($reservation['tipe']) ?>)</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Harga</div>
                        <div class="info-value">Rp <?= number_format($reservation['harga'], 0, ',', '.') ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Check In</div>
                        <div class="info-value"><?= date('d M Y', strtotime($reservation['check_in'])) ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Check Out</div>
                        <div class="info-value"><?= date('d M Y', strtotime($reservation['check_out'])) ?></div>
                    </div>
                </div>
                
                <?php if (!empty($reservation['catatan'])): ?>
                    <div class="info-item">
                        <div class="info-label">Catatan Khusus</div>
                        <div class="info-value"><?= nl2br($reservation['catatan']) ?></div>
                    </div>
                <?php endif; ?>
                
                <!-- Tombol Kembali -->
                <a href="dashboard.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Font Awesome -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<?php include '../includes/footer.php'; ?>
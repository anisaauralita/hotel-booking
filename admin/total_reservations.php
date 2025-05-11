<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isAdmin()) {
    header("Location: ../index.php");
    exit();
}

// Hitung total reservasi per status
$sql = "SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'canceled' THEN 1 ELSE 0 END) as canceled
        FROM reservations";
$result = $conn->query($sql);
$counts = $result->fetch_assoc();

// Ambil semua reservasi
$reservations_sql = "SELECT 
                        res.id, 
                        u.nama as user_name,
                        r.nomor_kamar,
                        r.tipe,
                        r.harga,
                        res.check_in,
                        res.check_out,
                        res.status,
                        res.created_at
                    FROM reservations res
                    JOIN users u ON res.user_id = u.id
                    JOIN rooms r ON res.room_id = r.id
                    ORDER BY res.created_at DESC";
$reservations_result = $conn->query($reservations_sql);
$reservations = $reservations_result->fetch_all(MYSQLI_ASSOC);
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
        --warning: #fbbf24;
        --danger: #ef4444;
    }

    .reservations-header {
        background: linear-gradient(rgba(15, 52, 96, 0.9), rgba(22, 33, 62, 0.9));
        padding: 3rem 0;
        color: white;
        margin-bottom: 2rem;
        border-radius: 0 0 20px 20px;
    }

    .stat-card {
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        border: none;
        margin-bottom: 2rem;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(15, 23, 42, 0.1);
    }

    .stat-card.primary {
        background: linear-gradient(45deg, var(--primary), #1e3a8a);
    }

    .stat-card.success {
        background: linear-gradient(45deg, var(--success), #34d399);
    }

    .stat-card.warning {
        background: linear-gradient(45deg, var(--warning), #fbbf24);
    }

    .stat-card.danger {
        background: linear-gradient(45deg, var(--danger), #ef4444);
    }

    .stat-card .card-body {
        padding: 1.5rem;
        color: white;
    }

    .stat-card .card-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        opacity: 0.9;
    }

    .stat-card .card-text {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0;
    }

    .reservations-table {
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    }

    .reservations-table .table th {
        background-color: var(--primary);
        color: white;
        font-weight: 600;
        padding: 1rem;
    }

    .reservations-table .table td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--light);
    }

    .badge-status {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .badge-confirmed {
        background-color: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }

    .badge-pending {
        background-color: rgba(251, 191, 36, 0.1);
        color: var(--warning);
    }

    .badge-canceled {
        background-color: rgba(239, 68, 68, 0.1);
        color: var(--danger);
    }

    .btn-action {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .btn-detail {
        background-color: var(--accent);
        color: white;
        border: 2px solid var(--accent);
    }

    .btn-detail:hover {
        background-color: transparent;
        color: var(--accent);
    }

    .empty-state {
        padding: 3rem;
        text-align: center;
    }

    .empty-state i {
        font-size: 3rem;
        color: var(--dark);
        opacity: 0.5;
        margin-bottom: 1rem;
    }

    @media (max-width: 768px) {
        .stat-card .card-text {
            font-size: 1.5rem;
        }
        
        .reservations-header {
            padding: 2rem 0;
        }
    }

    table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: center; /* Menjaga agar teks sejajar di tengah */
}

th {
    background-color: #004080; /* Warna header */
    color: white;
}

.tipe-suite {
    background-color: blue;
    color: white;
    border: none;
    padding: 5px 10px;
}

.tipe-double {
    background-color: green;
    color: white;
    border: none;
    padding: 5px 10px;
}

.status-ter {
    color: green; 
}

.status-dipesan {
    color: red; 
}

img {
    width: 50px; /* Mengatur ukuran gambar */
    height: auto;
}
</style>

<!-- Header Section -->
<section class="reservations-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1>Total Reservasi</h1>
                <p class="mb-0">Ringkasan semua reservasi sistem</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="reports.php" class="btn btn-light">
                    <i class="fas fa-chart-pie me-2"></i> Lihat Laporan
                </a>
            </div>
        </div>
    </div>
</section>

<div class="container">
    <!-- Stat Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-4">
            <div class="stat-card primary">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Reservasi</h5>
                    <p class="card-text"><?= $counts['total'] ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="stat-card success">
                <div class="card-body text-center">
                    <h5 class="card-title">Confirmed</h5>
                    <p class="card-text"><?= $counts['confirmed'] ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="stat-card warning">
                <div class="card-body text-center">
                    <h5 class="card-title">Pending</h5>
                    <p class="card-text"><?= $counts['pending'] ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="stat-card danger">
                <div class="card-body text-center">
                    <h5 class="card-title">Canceled</h5>
                    <p class="card-text"><?= $counts['canceled'] ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Reservations Table -->
    <div class="card reservations-table">
        <div class="card-header bg-white">
            <h4 class="mb-0">Daftar Reservasi</h4>
            <p class="text-muted mb-0">Semua reservasi yang tercatat dalam sistem</p>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pelanggan</th>
                            <th>Kamar</th>
                            <th>Tipe</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $reservation): ?>
                            <tr>
                                <td>#<?= $reservation['id'] ?></td>
                                <td><?= $reservation['user_name'] ?></td>
                                <td><?= $reservation['nomor_kamar'] ?></td>
                                <td><?= ucfirst($reservation['tipe']) ?></td>
                                <td>
                                    <?= date('d M Y', strtotime($reservation['check_in'])) ?><br>
                                    <small>s/d <?= date('d M Y', strtotime($reservation['check_out'])) ?></small>
                                </td>
                                <td>
                                    <span class="badge-status badge-<?= strtolower($reservation['status']) ?>">
                                        <?= ucfirst($reservation['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="detail_reservation.php?id=<?= $reservation['id'] ?>" 
                                       class="btn btn-action btn-detail">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($reservations)): ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="fas fa-calendar-times"></i>
                                        <h5>Tidak ada reservasi</h5>
                                        <p class="text-muted">Belum ada reservasi yang tercatat</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<!-- Font Awesome -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
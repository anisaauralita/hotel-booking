<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

if (!isLoggedIn() || isAdmin()) {
    header("Location: ../../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Filter parameter
$status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$date_from = isset($_GET['date_from']) ? sanitize($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? sanitize($_GET['date_to']) : '';

// Query dasar
$sql = "SELECT 
            res.id,
            r.nomor_kamar,
            r.tipe,
            r.harga,
            r.foto,
            res.check_in,
            res.check_out,
            res.status,
            res.created_at
        FROM reservations res
        JOIN rooms r ON res.room_id = r.id
        WHERE res.user_id = '$user_id'";

// Tambahkan filter
if (!empty($status)) {
    $sql .= " AND res.status = '$status'";
}

if (!empty($date_from)) {
    $sql .= " AND res.check_in >= '$date_from'";
}

if (!empty($date_to)) {
    $sql .= " AND res.check_out <= '$date_to'";
}

$sql .= " ORDER BY res.check_in DESC";

$result = $conn->query($sql);
$reservations = $result->fetch_all(MYSQLI_ASSOC);
?>

<?php include '../../includes/header.php'; ?>

<style>
    :root {
        --primary: #0f3460;
        --secondary: #16213e;
        --accent: #0ea5e9;
        --light: #f8fafc;
        --dark: #1e293b;
    }

    body {
        font-family: 'Montserrat', sans-serif;
        background-color: #f8fafc;
    }

    .container {
        padding-top: 2rem;
        padding-bottom: 3rem;
    }

    h2 {
        color: var(--primary);
        font-weight: 800;
        margin-bottom: 1.5rem;
        position: relative;
    }

    h2:after {
        content: '';
        position: absolute;
        width: 60px;
        height: 4px;
        background: var(--accent);
        bottom: -10px;
        left: 0;
        border-radius: 2px;
    }

    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        margin-bottom: 30px;
    }

    .card-header {
        background-color: var(--primary);
        color: white;
        padding: 1.2rem 1.5rem;
        border-bottom: none;
    }

    .card-header h5 {
        margin: 0;
        font-weight: 700;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        background-color: var(--light);
        color: var(--primary);
        font-weight: 700;
        border-bottom: 2px solid var(--grey-light);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(14, 165, 233, 0.05);
    }

    .badge {
        font-size: 0.85rem;
        padding: 0.5rem 0.8rem;
        border-radius: 50px;
        font-weight: 600;
    }

    .bg-success {
        background-color: #10b981 !important;
    }

    .bg-warning {
        background-color: #fbbf24 !important;
        color: var(--dark) !important;
    }

    .bg-danger {
        background-color: #ef4444 !important;
    }

    .btn {
        border-radius: 50px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-info {
        background-color: var(--accent);
        border-color: var(--accent);
    }

    .btn-info:hover {
        background-color: #0d94d7;
        border-color: #0d94d7;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(14, 165, 233, 0.3);
    }

    .btn-danger {
        background-color: #ef4444;
        border-color: #ef4444;
    }

    .btn-danger:hover {
        background-color: #dc2626;
        border-color: #dc2626;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(239, 68, 68, 0.3);
    }

    .alert {
        border-radius: 12px;
    }

    .alert-success {
        background-color: rgba(16, 185, 129, 0.1);
        border-color: rgba(16, 185, 129, 0.3);
        color: #10b981;
    }

    .alert-info {
        background-color: rgba(14, 165, 233, 0.1);
        border-color: rgba(14, 165, 233, 0.3);
        color: var(--accent);
    }

    .alert-link {
        color: var(--accent);
        font-weight: 600;
        text-decoration: underline;
    }

    .alert-link:hover {
        color: #0d94d7;
    }

    .form-control, .form-select {
        border: 2px solid #e2e8f0;
        padding: 0.7rem 1rem;
        border-radius: 8px;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
    }

    .btn-primary {
        background-color: var(--accent);
        border-color: var(--accent);
    }

    .btn-primary:hover {
        background-color: #0d94d7;
        border-color: #0d94d7;
    }

    .btn-secondary {
        background-color: var(--dark);
        border-color: var(--dark);
    }

    .btn-secondary:hover {
        background-color: #1e293b;
        border-color: #1e293b;
    }

    .room-image {
        width: 80px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }

    .price-cell {
        font-weight: 700;
        color: var(--primary);
    }

    .action-cell {
        white-space: nowrap;
    }
</style>

<div class="container">
    <h2 class="my-4">Daftar Reservasi Saya</h2>
    
    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filter Reservasi</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Semua Status</option>
                            <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="confirmed" <?= $status == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                            <option value="canceled" <?= $status == 'canceled' ? 'selected' : '' ?>>Canceled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Dari Tanggal</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="<?= $date_from ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Sampai Tanggal</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="<?= $date_to ?>">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-sync-alt me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Reservations List -->
    <div class="card">
        <div class="card-body">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success mb-4"><?= $_SESSION['success'] ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <?php if (count($reservations) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Kamar</th>
                                <th>Tipe</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Tanggal Pesan</th>
                                <th class="action-cell">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations as $reservation): ?>
                                <?php
                                $check_in = new DateTime($reservation['check_in']);
                                $check_out = new DateTime($reservation['check_out']);
                                $nights = $check_out->diff($check_in)->days;
                                $total = $nights * $reservation['harga'];
                                ?>
                                <tr>
                                    <td><?= $reservation['id'] ?></td>
                                    <td>
                                        <img src="<?= BASE_URL ?>assets/images/rooms/<?= $reservation['foto'] ?>" 
                                             class="room-image me-2" 
                                             alt="Kamar <?= $reservation['nomor_kamar'] ?>">
                                        Kamar <?= $reservation['nomor_kamar'] ?>
                                    </td>
                                    <td><?= ucfirst($reservation['tipe']) ?></td>
                                    <td><?= date('d M Y', strtotime($reservation['check_in'])) ?></td>
                                    <td><?= date('d M Y', strtotime($reservation['check_out'])) ?></td>
                                    <td class="price-cell">Rp <?= number_format($total, 0, ',', '.') ?></td>
                                    <td>
                                        <span class="badge 
                                            <?= $reservation['status'] == 'confirmed' ? 'bg-success' : 
                                               ($reservation['status'] == 'canceled' ? 'bg-danger' : 'bg-warning') ?>">
                                            <?= ucfirst($reservation['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d M Y H:i', strtotime($reservation['created_at'])) ?></td>
                                    <td class="action-cell">
                                        <a href="detail.php?id=<?= $reservation['id'] ?>" class="btn btn-sm btn-info me-1" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($reservation['status'] == 'pending'): ?>
                                            <a href="cancel.php?id=<?= $reservation['id'] ?>" class="btn btn-sm btn-danger" title="Batalkan" onclick="return confirm('Yakin ingin membatalkan reservasi?')">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Tidak ada reservasi yang ditemukan.
                    <a href="../../search.php" class="alert-link">Cari kamar sekarang</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Font Awesome untuk Icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<?php include '../../includes/footer.php'; ?>
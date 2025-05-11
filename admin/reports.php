<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isAdmin()) {
    header("Location: ../index.php");
    exit();
}

// Default filter: bulan ini
$start_date = date('Y-m-01');
$end_date = date('Y-m-t');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $start_date = sanitize($_POST['start_date']);
    $end_date = sanitize($_POST['end_date']);
}

// Query untuk laporan
$sql = "SELECT 
            COUNT(*) as total_reservations,
            SUM(r.harga) as total_income,
            AVG(r.harga) as avg_income,
            (SELECT COUNT(*) FROM rooms) as total_rooms,
            (SELECT COUNT(*) FROM rooms WHERE status='tersedia') as available_rooms
        FROM reservations res
        JOIN rooms r ON res.room_id = r.id
        WHERE res.check_in BETWEEN '$start_date' AND '$end_date'";
$report_result = $conn->query($sql);
$report = $report_result->fetch_assoc();

// Query untuk daftar reservasi
$reservations_sql = "SELECT 
                        res.id, 
                        u.nama as user_name, 
                        r.nomor_kamar, 
                        r.tipe, 
                        r.harga, 
                        res.check_in, 
                        res.check_out, 
                        res.status
                    FROM reservations res
                    JOIN users u ON res.user_id = u.id
                    JOIN rooms r ON res.room_id = r.id
                    WHERE res.check_in BETWEEN '$start_date' AND '$end_date'
                    ORDER BY res.check_in DESC";
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
    --grey-light: #e2e8f0;
    --grey-dark: #64748b;
}

body {
    font-family: 'Montserrat', sans-serif;
    background-color: var(--light);
    color: var(--dark);
}

.report-management {
    padding: 2rem 0;
}

.report-header {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    margin-bottom: 2rem;
    border-left: 4px solid var(--accent);
}

.report-header h2 {
    color: var(--primary);
    font-weight: 700;
}

.date-filter {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    margin-bottom: 2rem;
}

.stat-card {
    border: none;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    transition: all 0.3s ease;
    height: 100%;
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

.stat-card.info {
    background: linear-gradient(45deg, var(--accent), #38bdf8);
}

.stat-card .card-title {
    font-size: 1rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    opacity: 0.9;
    margin-bottom: 0.5rem;
}

.stat-card .card-text {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 0;
}

.report-table {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    transition: all 0.3s ease;
}

.report-table:hover {
    box-shadow: 0 15px 35px rgba(15, 23, 42, 0.1);
}

.report-table .table {
    margin-bottom: 0;
}

.report-table .table thead th {
    background-color: var(--primary);
    color: white;
    font-weight: 600;
    padding: 1rem;
    border-bottom: none;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 0.85rem;
}

.report-table .table tbody td {
    padding: 1rem;
    vertical-align: middle;
    border-bottom: 1px solid var(--grey-light);
}

.report-table .table tbody tr:last-child td {
    border-bottom: none;
}

.report-table .table tbody tr:hover {
    background-color: rgba(14, 165, 233, 0.05);
}

.badge {
    padding: 0.5em 0.75em;
    font-weight: 600;
    border-radius: 8px;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}

.btn-primary {
    background: linear-gradient(45deg, var(--accent), #38bdf8);
    border: none;
    padding: 0.6rem 1.5rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(14, 165, 233, 0.3);
}

.btn-primary:hover {
    background: linear-gradient(45deg, #0284c7, var(--accent));
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(14, 165, 233, 0.4);
}

.btn-outline-secondary {
    border: 2px solid var(--grey-light);
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    border-color: var(--accent);
    color: var(--accent);
    background: rgba(14, 165, 233, 0.05);
}

.empty-state {
    padding: 3rem;
    text-align: center;
}

.empty-state i {
    font-size: 3rem;
    color: var(--grey-dark);
    margin-bottom: 1rem;
}

/* Animation classes */
.reveal {
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.6s ease;
}

.reveal.active {
    opacity: 1;
    transform: translateY(0);
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

<div class="report-management">
  <div class="container">
    <div class="report-header reveal">
      <h2 class="mb-0">Laporan Reservasi</h2>
      <p class="text-muted mb-0">Analisis data reservasi periode <?= date('d M Y', strtotime($start_date)) ?> - <?= date('d M Y', strtotime($end_date)) ?></p>
    </div>

    <!-- Date Filter -->
    <div class="date-filter reveal">
      <form method="POST" action="">
        <div class="row align-items-end">
          <div class="col-md-4">
            <label for="start_date" class="form-label">Tanggal Mulai</label>
            <input type="date" class="form-control" id="start_date" name="start_date" value="<?= $start_date ?>" required>
          </div>
          <div class="col-md-4">
            <label for="end_date" class="form-label">Tanggal Akhir</label>
            <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $end_date ?>" required>
          </div>
          <div class="col-md-4">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-filter me-2"></i> Filter
            </button>
            <a href="reports.php" class="btn btn-outline-secondary ms-2">
              <i class="fas fa-sync-alt me-2"></i> Reset
            </a>
          </div>
        </div>
      </form>
    </div>

    <!-- Stat Cards -->
    <div class="row mb-4">
      <div class="col-md-4 mb-4 reveal">
        <div class="stat-card primary text-white">
          <div class="card-body">
            <h5 class="card-title">Total Reservasi</h5>
            <p class="card-text"><?= $report['total_reservations'] ?? 0 ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4 reveal">
        <div class="stat-card success text-white">
          <div class="card-body">
            <h5 class="card-title">Total Pendapatan</h5>
            <p class="card-text">Rp <?= number_format($report['total_income'] ?? 0, 0, ',', '.') ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4 reveal">
        <div class="stat-card info text-white">
          <div class="card-body">
            <h5 class="card-title">Tingkat Okupansi</h5>
            <p class="card-text">
              <?= $report['total_rooms'] > 0 ? round(($report['total_reservations'] / $report['total_rooms']) * 100, 2) : 0 ?>%
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Reservations Table -->
    <div class="card report-table reveal">
      <div class="card-header bg-white border-0">
        <h4 class="mb-0">Detail Reservasi</h4>
        <p class="text-muted mb-0">Daftar reservasi periode <?= date('d M Y', strtotime($start_date)) ?> - <?= date('d M Y', strtotime($end_date)) ?></p>
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
                <th>Harga</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($reservations as $reservation): ?>
                <tr class="reveal">
                  <td>#<?= $reservation['id'] ?></td>
                  <td><?= $reservation['user_name'] ?></td>
                  <td><?= $reservation['nomor_kamar'] ?></td>
                  <td>
                    <span class="badge bg-primary">
                      <?= ucfirst($reservation['tipe']) ?>
                    </span>
                  </td>
                  <td>Rp <?= number_format($reservation['harga'], 0, ',', '.') ?></td>
                  <td><?= date('d M Y', strtotime($reservation['check_in'])) ?></td>
                  <td><?= date('d M Y', strtotime($reservation['check_out'])) ?></td>
                  <td>
                    <span class="badge bg-<?= $reservation['status'] == 'confirmed' ? 'success' : 'warning' ?>">
                      <?= $reservation['status'] ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (count($reservations) == 0): ?>
                <tr>
                  <td colspan="8" class="text-center py-4">
                    <div class="empty-state">
                      <i class="fas fa-calendar-times"></i>
                      <h5>Tidak ada data reservasi</h5>
                      <p class="text-muted">Tidak ditemukan reservasi pada periode ini</p>
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
</div>

<?php include '../includes/footer.php'; ?>

<!-- Font Awesome -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<script>
// Scroll reveal animation
document.addEventListener('DOMContentLoaded', function() {
    window.addEventListener('scroll', reveal);
    reveal();
    
    function reveal() {
        var reveals = document.querySelectorAll('.reveal');
        
        for(var i = 0; i < reveals.length; i++) {
            var windowHeight = window.innerHeight;
            var revealTop = reveals[i].getBoundingClientRect().top;
            var revealPoint = 100;
            
            if(revealTop < windowHeight - revealPoint) {
                reveals[i].classList.add('active');
            }
        }
    }
});
</script>
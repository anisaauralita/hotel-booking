<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isAdmin()) {
    header("Location: ../index.php");
    exit();
}

// Hitung total kamar
$sql_rooms = "SELECT COUNT(*) as total FROM rooms";
$result_rooms = $conn->query($sql_rooms);
$total_rooms = $result_rooms->fetch_assoc()['total'];

// Hitung total reservasi
$sql_reservations = "SELECT COUNT(*) as total FROM reservations";
$result_reservations = $conn->query($sql_reservations);
$total_reservations = $result_reservations->fetch_assoc()['total'];

// Hitung total pendapatan
$sql_income = "SELECT COALESCE(SUM(r.harga), 0) as total 
               FROM reservations res
               JOIN rooms r ON res.room_id = r.id
               WHERE res.status = 'confirmed'";
$result_income = $conn->query($sql_income);
$total_income = $result_income->fetch_assoc()['total'];

// Ambil 5 reservasi terbaru
$sql = "SELECT res.id, u.nama, r.nomor_kamar, r.tipe, r.foto, res.check_in, res.check_out, res.status 
        FROM reservations res
        JOIN users u ON res.user_id = u.id
        JOIN rooms r ON res.room_id = r.id
        ORDER BY res.id DESC LIMIT 5";
$result = $conn->query($sql);
$recent_reservations = $result->fetch_all(MYSQLI_ASSOC);
?>

<?php include '../includes/header.php'; ?>

<style>
    /* Gunakan variabel warna yang sama dengan halaman index */
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

    .dashboard-header {
        background: linear-gradient(rgba(15, 52, 96, 0.9), rgba(22, 33, 62, 0.9)), 
                    url('../assets/images/oke.png');
        background-size: cover;
        background-position: center;
        padding: 5rem 3rem;
        color: white;
        margin-bottom: 3rem;
        position: relative;
    }

    .dashboard-header:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 10px;
        background: linear-gradient(90deg, var(--accent), #38bdf8);
    }

    .dashboard-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .dashboard-header p {
        font-size: 1.05rem;
        font-weight: 500;
        opacity: 0.95;
    }

    .section-title {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--primary);
        margin-bottom: 1.5rem;
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
        left: 0;
        border-radius: 2px;
    }

    .stat-card {
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
        height: 100%;
        color: white;
        position: relative;
        overflow: hidden;
        border: none;
    }

    .stat-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.15);
    }

    .stat-card.primary {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
    }

    .stat-card.success {
        background: linear-gradient(135deg, var(--success), #059669);
    }

    .stat-card.info {
        background: linear-gradient(135deg, var(--accent), #0284c7);
    }

    .stat-card .card-body {
        padding: 2rem;
        position: relative;
        z-index: 2;
    }

    .stat-card h5 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        opacity: 0.9;
    }

    .stat-card .card-text {
        font-size: 2.2rem;
        font-weight: 800;
        margin-bottom: 1.5rem;
    }

    .stat-card .card-link {
        display: inline-flex;
        align-items: center;
        color: white !important;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .stat-card .card-link:hover {
        text-decoration: underline;
        transform: translateX(5px);
    }

    .stat-card .card-link i {
        margin-left: 0.5rem;
        transition: all 0.3s ease;
    }

    .stat-card:before {
        content: '';
        position: absolute;
        width: 100%;
        height: 0;
        background: rgba(255, 255, 255, 0.1);
        bottom: 0;
        left: 0;
        transition: all 0.5s ease;
        z-index: 1;
    }

    .stat-card:hover:before {
        height: 100%;
    }

    .room-image {
        width: 80px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }

    .table-responsive {
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    }

    .table th {
        background-color: var(--primary);
        color: white;
        font-weight: 600;
        padding: 1rem;
    }

    .table td {
        padding: 1rem;
        vertical-align: middle;
    }

    .table tr:nth-child(even) {
        background-color: #f8fafc;
    }

    .badge {
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 50px;
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
        transition: all 0.3s ease;
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
        .dashboard-header {
            padding: 3rem 1.5rem;
        }
        
        .stat-card .card-body {
            padding: 1.5rem;
        }
        
        .stat-card .card-text {
            font-size: 1.8rem;
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

<!-- Header Dashboard -->
<section class="dashboard-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="text-white mb-3">Admin Dashboard</h1>
                <p class="text-white mb-0">Ringkasan aktivitas sistem dan manajemen hotel</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="reports.php" class="btn btn-view-all">Lihat Laporan</a>
            </div>
        </div>
    </div>
</section>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <!-- Statistik -->
            <div class="row mb-5 reveal">
                <div class="col-md-4 mb-4">
                    <div class="stat-card primary">
                        <div class="card-body">
                            <h5>Total Kamar</h5>
                            <p class="card-text"><?= $total_rooms ?></p>
                            <a href="rooms/index.php" class="card-link">
                                Kelola Kamar <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="stat-card success">
                        <div class="card-body">
                            <h5>Total Reservasi</h5>
                            <p class="card-text"><?= $total_reservations ?></p>
                            <a href="total_reservations.php" class="card-link">
                                Lihat Reservasi <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="stat-card info">
    <div class="card-body">
        <h5>Total Pendapatan</h5>
        <p class="card-text">
            <?php
            if(isset($total_income) && $total_income > 0) {
                echo 'Rp ' . number_format($total_income, 0, ',', '.');
            } else {
                echo '0';
            }
            ?>
        </p>
        <a href="reports.php" class="card-link">
            <i class="fas fa-chart-line me-1"></i> Detail Laporan
        </a>
    </div>
</div>
                </div>
            </div>

            <!-- Reservasi Terbaru -->
            <div class="dashboard-section">
                <h2 class="section-title reveal">Reservasi Terbaru</h2>

                <?php if (count($recent_reservations) > 0): ?>
                    <div class="table-responsive reveal">
                        <table class="table">
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
                                <?php foreach ($recent_reservations as $reservation): ?>
                                    <tr>
                                        <td>#<?= $reservation['id'] ?></td>
                                        <td><?= $reservation['nama'] ?></td>
                                        <td>
                                            <img src="<?= BASE_URL ?>assets/images/rooms/<?= $reservation['foto'] ?>" 
                                                 alt="Kamar <?= $reservation['nomor_kamar'] ?>" 
                                                 class="room-image me-2">
                                            <?= $reservation['nomor_kamar'] ?>
                                        </td>
                                        <td><?= ucfirst($reservation['tipe']) ?></td>
                                        <td>
                                            <?= date('d M Y', strtotime($reservation['check_in'])) ?><br>
                                            <small>s/d <?= date('d M Y', strtotime($reservation['check_out'])) ?></small>
                                        </td>
                                        <td>
                                            <?php 
                                            $badge_class = '';
                                            if ($reservation['status'] == 'confirmed') {
                                                $badge_class = 'badge-confirmed';
                                            } elseif ($reservation['status'] == 'pending') {
                                                $badge_class = 'badge-pending';
                                            } else {
                                                $badge_class = 'badge-canceled';
                                            }
                                            ?>
                                            <span class="badge <?= $badge_class ?>">
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
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info reveal">
                        <i class="fas fa-info-circle me-2"></i> Tidak ada reservasi terbaru.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<!-- Font Awesome untuk Icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

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
    
    // Efek hover untuk stat card
    const statCards = document.querySelectorAll('.stat-card');
    
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
            this.style.boxShadow = '0 20px 40px rgba(15, 23, 42, 0.15)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 10px 30px rgba(15, 23, 42, 0.05)';
        });
    });
});
</script>
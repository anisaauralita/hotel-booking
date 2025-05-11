<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn() || isAdmin()) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT 
            res.id, 
            r.nomor_kamar, 
            r.tipe, 
            r.harga, 
            r.foto, 
            res.check_in, 
            res.check_out, 
            res.status
        FROM reservations res
        JOIN rooms r ON res.room_id = r.id
        WHERE res.user_id = '$user_id'
        ORDER BY res.check_in DESC";
$result = $conn->query($sql);
$reservations = $result->fetch_all(MYSQLI_ASSOC);

$user_sql = "SELECT nama, email FROM users WHERE id = '$user_id'";
$user_result = $conn->query($user_sql);
$user_data = $user_result->fetch_assoc();
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
        --info: #3b82f6;
        --danger: #ef4444;
        --grey-light: #e2e8f0;
        --grey-dark: #64748b;
    }

    body {
        font-family: 'Montserrat', sans-serif;
        color: var(--dark);
        background-color: var(--light);
    }

    .dashboard-header {
    background: linear-gradient(rgba(15, 52, 96, 0.9), rgba(22, 33, 62, 0.9)), 
                url('../assets/images/oke.png');
    background-size: cover;
    background-position: center;
    padding: 5rem 3rem; /* Added horizontal padding */
    color: white;
    margin-bottom: 3rem;
    position: relative;
    /* Removed clip-path to make it straight */
}

.dashboard-header .container {
    padding-left: 2rem;
    padding-right: 2rem;
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

/* Update the media query for mobile */
@media (max-width: 768px) {
    .dashboard-header {
        padding: 3rem 1.5rem;
    }
    
    .dashboard-header .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
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

    .room-image {
        width: 100px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        transition: transform 0.3s ease;
    }

    .room-image:hover {
        transform: scale(1.1);
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

    .btn-cancel {
        background-color: var(--danger);
        color: white;
        border: 2px solid var(--danger);
    }

    .btn-cancel:hover {
        background-color: transparent;
        color: var(--danger);
    }

    .no-reservation {
        background-color: white;
        border-radius: 16px;
        padding: 3rem;
        text-align: center;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    }

    .no-reservation-icon {
        font-size: 4rem;
        color: var(--accent);
        margin-bottom: 1.5rem;
    }

    .btn-book {
        background: linear-gradient(45deg, var(--accent), #38bdf8);
        color: white;
        border: none;
        padding: 0.9rem 2.8rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        border-radius: 50px;
        box-shadow: 0 10px 25px rgba(14, 165, 233, 0.4);
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    .btn-book:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 25px rgba(14, 165, 233, 0.5);
        color: white;
    }

    .total-price {
        font-weight: 700;
        color: var(--primary);
    }

    .dashboard-section {
        margin-bottom: 4rem;
    }

    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    }

    .alert {
        border-radius: 8px;
    }

    .alert-info {
        background-color: rgba(14, 165, 233, 0.1);
        border-color: rgba(14, 165, 233, 0.2);
        color: var(--accent);
    }

    .alert-success {
        background-color: rgba(16, 185, 129, 0.1);
        border-color: rgba(16, 185, 129, 0.2);
        color: var(--success);
    }

    @media (max-width: 768px) {
        .section-title {
            font-size: 1.5rem;
        }
        
        .dashboard-header {
            clip-path: none;
            padding: 3rem 0;
        }
        
        .dashboard-header h1 {
            font-size: 2rem;
        }
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
</style>

<!-- Header Dashboard -->
<section class="dashboard-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="text-white mb-3">Dashboard Pengguna</h1>
                <p class="text-white mb-0">
                    <i class="fas fa-user-circle me-1"></i> <strong><?= $user_data['nama'] ?></strong><br>
                    <i class="fas fa-envelope me-1"></i> <?= $user_data['email'] ?>
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="../search.php" class="btn btn-book">Pesan Kamar Baru</a>
            </div>
        </div>
    </div>
</section>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Reservasi Saya -->
            <div class="dashboard-section reveal">
                <h2 class="section-title">Reservasi Saya</h2>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success reveal"><?= $_SESSION['success'] ?></div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (count($reservations) > 0): ?>
                    <div class="table-responsive reveal">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Kamar</th>
                                    <th>Tipe</th>
                                    <th>Tanggal</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservations as $reservation): ?>
                                    <tr>
                                        <td>
                                            <img src="<?= BASE_URL ?>assets/images/rooms/<?= $reservation['foto'] ?>" 
                                                 alt="Kamar <?= $reservation['nomor_kamar'] ?>" 
                                                 class="room-image">
                                            <span class="ms-2">Kamar <?= $reservation['nomor_kamar'] ?></span>
                                        </td>
                                        <td><?= ucfirst($reservation['tipe']) ?></td>
                                        <td>
                                            <?= date('d M Y', strtotime($reservation['check_in'])) ?><br>
                                            <small>s/d <?= date('d M Y', strtotime($reservation['check_out'])) ?></small>
                                        </td>
                                        <td class="total-price">
                                            <?php
                                            $check_in = new DateTime($reservation['check_in']);
                                            $check_out = new DateTime($reservation['check_out']);
                                            $nights = $check_out->diff($check_in)->days;
                                            $total = $nights * $reservation['harga'];
                                            ?>
                                            Rp <?= number_format($total, 0, ',', '.') ?>
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
                                            <a href="reservations/detail.php?id=<?= $reservation['id'] ?>" class="btn btn-action btn-detail me-2">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                            <?php if ($reservation['status'] == 'pending'): ?>
                                                <a href="reservations/cancel.php?id=<?= $reservation['id'] ?>" 
                                                   class="btn btn-action btn-cancel" 
                                                   onclick="return confirm('Yakin ingin membatalkan reservasi?')">
                                                    <i class="fas fa-times"></i> Batal
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="no-reservation reveal">
                        <div class="no-reservation-icon">
                            <i class="fas fa-calendar-times"></i>
                        </div>
                        <h4 class="mb-3">Anda belum memiliki reservasi</h4>
                        <p class="mb-4">Mulai pesan kamar sekarang dan nikmati pengalaman menginap yang tak terlupakan</p>
                        <a href="../search.php" class="btn btn-book">Pesan Kamar Sekarang</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Riwayat Aktivitas -->
            <div class="dashboard-section reveal">
                <h2 class="section-title">Aktivitas Terkini</h2>
                <div class="card">
                    <div class="card-body">
                        <div class="alert alert-info reveal">
                            <i class="fas fa-info-circle me-2"></i> Fitur riwayat aktivitas akan segera hadir.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<!-- Font Montserrat dari Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
    
    // Efek hover untuk tombol
    const actionButtons = document.querySelectorAll('.btn-action');
    const bookButton = document.querySelector('.btn-book');
    
    actionButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
            this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.1)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });
    
    bookButton.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-3px)';
        this.style.boxShadow = '0 15px 25px rgba(14, 165, 233, 0.5)';
    });
    
    bookButton.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
        this.style.boxShadow = '0 10px 25px rgba(14, 165, 233, 0.4)';
    });
});
</script>
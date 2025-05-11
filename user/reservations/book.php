<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

if (!isLoggedIn()) {
    header("Location: ../../../index.php");
    exit();
}

if (!isset($_GET['room_id'])) {
    header("Location: ../../../search.php");
    exit();
}

$room_id = sanitize($_GET['room_id']);
$user_id = $_SESSION['user_id'];

// Cek ketersediaan kamar
$sql = "SELECT * FROM rooms WHERE id='$room_id' AND status='tersedia'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    $_SESSION['error'] = "Kamar tidak tersedia atau sudah dipesan.";
    header("Location: ../../../search.php");
    exit();
}

$room = $result->fetch_assoc();
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $check_in = sanitize($_POST['check_in']);
    $check_out = sanitize($_POST['check_out']);
    
    // Validasi tanggal
    if (strtotime($check_in) >= strtotime($check_out)) {
        $error = "Tanggal check out harus setelah tanggal check in.";
    } else {
        // Cek apakah kamar sudah dipesan di tanggal tersebut
        $sql = "SELECT id FROM reservations 
                WHERE room_id='$room_id' 
                AND (
                    (check_in <= '$check_in' AND check_out >= '$check_in') OR
                    (check_in <= '$check_out' AND check_out >= '$check_out') OR
                    (check_in >= '$check_in' AND check_out <= '$check_out')
                )";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $error = "Kamar sudah dipesan di tanggal tersebut.";
        } else {
            // Buat reservasi
            $sql = "INSERT INTO reservations (user_id, room_id, check_in, check_out, status) 
                    VALUES ('$user_id', '$room_id', '$check_in', '$check_out', 'pending')";
            
            if ($conn->query($sql)) {
                // Update status kamar
                $conn->query("UPDATE rooms SET status='dipesan' WHERE id='$room_id'");
                
                $_SESSION['success'] = "Reservasi berhasil dibuat!";
                header("Location: ../dashboard.php");
                exit();
            } else {
                $error = "Terjadi kesalahan: " . $conn->error;
            }
        }
    }
}
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

    .booking-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        background: linear-gradient(rgba(15, 52, 96, 0.8), rgba(22, 33, 62, 0.8)), 
                    url('../../assets/images/oke.png');
        background-size: cover;
        background-position: center;
        padding: 2rem 0;
    }

    .booking-card {
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
        border: none;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2);
        background: white;
        transition: all 0.3s ease;
    }

    .booking-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 25px 60px rgba(15, 23, 42, 0.3);
    }

    .booking-header {
        background: linear-gradient(45deg, var(--primary), var(--secondary));
        color: white;
        padding: 2rem;
        text-align: center;
    }

    .booking-title {
        font-weight: 700;
        font-size: 1.8rem;
        margin: 0;
    }

    .booking-body {
        padding: 2.5rem;
    }

    .form-label {
        font-weight: 600;
        color: var(--secondary);
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control {
        width: 100%;
        padding: 0.8rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
    }

    .form-control:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
        outline: none;
    }

    .btn-booking {
        width: 100%;
        padding: 0.9rem;
        background: linear-gradient(45deg, var(--accent), #38bdf8);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px rgba(14, 165, 233, 0.2);
    }

    .btn-booking:hover {
        background: linear-gradient(45deg, #38bdf8, var(--accent));
        transform: translateY(-2px);
        box-shadow: 0 15px 25px rgba(14, 165, 233, 0.3);
    }

    .price-display {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--secondary);
        margin-bottom: 1rem;
    }

    .total-price {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--accent);
    }

    .room-info {
        background: rgba(14, 165, 233, 0.1);
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }

    .room-name {
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 0.5rem;
    }

    .alert-danger {
        background-color: #fee2e2;
        color: #b91c1c;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        border: 1px solid #fca5a5;
    }

    .btn-back {
        display: block;
        text-align: center;
        margin-top: 1.5rem;
        color: var(--accent);
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        color: var(--primary);
        text-decoration: underline;
    }
</style>

<div class="booking-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="booking-card">
                    <div class="booking-header">
                        <h2 class="booking-title">Pesan Kamar</h2>
                    </div>
                    <div class="booking-body">
                        <?php if ($error): ?>
                            <div class="alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <div class="room-info">
                            <p class="room-name">Kamar <?= $room['nomor_kamar'] ?> - <?= ucfirst($room['tipe']) ?></p>
                            <p class="price-display">Harga per malam: <span class="total-price">Rp <?= number_format($room['harga'], 0, ',', '.') ?></span></p>
                        </div>
                        
                        <form method="POST" action="">
                            <div>
                                <label for="check_in" class="form-label">Tanggal Check In</label>
                                <input type="date" class="form-control" id="check_in" name="check_in" min="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div>
                                <label for="check_out" class="form-label">Tanggal Check Out</label>
                                <input type="date" class="form-control" id="check_out" name="check_out" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                            </div>
                            <div class="price-display">
                                <p>Total Harga: <span class="total-price" id="total-price-display">Rp 0</span></p>
                                <input type="hidden" id="price-per-night" value="<?= $room['harga'] ?>">
                            </div>
                            <button type="submit" class="btn-booking">Pesan Sekarang</button>
                        </form>
                        
                        <a href="<?= BASE_URL ?>room-detail.php?id=<?= $room['id'] ?>" class="btn-back">
                            <i class="fas fa-arrow-left"></i> Kembali ke Detail Kamar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    const pricePerNight = document.getElementById('price-per-night').value;
    const totalPriceDisplay = document.getElementById('total-price-display');
    
    function calculateTotal() {
        if (checkInInput.value && checkOutInput.value) {
            const checkIn = new Date(checkInInput.value);
            const checkOut = new Date(checkOutInput.value);
            const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
            const total = nights * pricePerNight;
            totalPriceDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID');
        }
    }
    
    checkInInput.addEventListener('change', function() {
        if (this.value) {
            const minCheckOut = new Date(this.value);
            minCheckOut.setDate(minCheckOut.getDate() + 1);
            checkOutInput.min = minCheckOut.toISOString().split('T')[0];
            
            if (checkOutInput.value && new Date(checkOutInput.value) <= new Date(this.value)) {
                checkOutInput.value = '';
            }
        }
        calculateTotal();
    });
    
    checkOutInput.addEventListener('change', calculateTotal);
    
    // Set min date for check in (today)
    checkInInput.min = new Date().toISOString().split('T')[0];
});
</script>

<!-- Font Awesome -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<?php include '../../includes/footer.php'; ?>
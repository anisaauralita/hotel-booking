<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (isLoggedIn()) {
    header("Location: ../index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password !== $confirm_password) {
        $error = "Password tidak sama!";
    } else {
        $email_check = sanitize($email);
        $sql = "SELECT id FROM users WHERE email='$email_check'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            if (register($nama, $email, $password)) {
                $success = "Pendaftaran berhasil! Silakan login.";
            } else {
                $error = "Terjadi kesalahan saat mendaftar.";
            }
        }
    }
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
        --danger: #ef4444;
        --success: #10b981;
    }

    .register-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        background: linear-gradient(rgba(15, 52, 96, 0.8), rgba(22, 33, 62, 0.8)), 
                    url('../assets/images/oke.png');
        background-size: cover;
        background-position: center;
        padding: 2rem 0;
    }

    .register-card {
        width: 100%;
        max-width: 550px;
        margin: 0 auto;
        border: none;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2);
        background: white;
        transition: all 0.3s ease;
    }

    .register-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 25px 60px rgba(15, 23, 42, 0.3);
    }

    .register-header {
        background: linear-gradient(45deg, var(--primary), var(--secondary));
        color: white;
        padding: 2rem;
        text-align: center;
    }

    .register-title {
        font-weight: 700;
        font-size: 1.8rem;
        margin: 0;
    }

    .register-body {
        padding: 2rem 2.5rem;
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
        margin-bottom: 1.2rem;
    }

    .form-control:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
        outline: none;
    }

    .btn-register {
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
        margin-top: 0.5rem;
    }

    .btn-register:hover {
        background: linear-gradient(45deg, #38bdf8, var(--accent));
        transform: translateY(-2px);
        box-shadow: 0 15px 25px rgba(14, 165, 233, 0.3);
    }

    .register-footer {
        text-align: center;
        margin-top: 1.5rem;
        color: #64748b;
    }

    .register-link {
        color: var(--accent);
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .register-link:hover {
        color: var(--primary);
        text-decoration: underline;
    }

    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        border: 1px solid transparent;
    }

    .alert-danger {
        background-color: #fee2e2;
        color: #b91c1c;
        border-color: #fca5a5;
    }

    .alert-success {
        background-color: #d1fae5;
        color: #065f46;
        border-color: #6ee7b7;
    }

    .password-hint {
        font-size: 0.8rem;
        color: #64748b;
        margin-top: -0.8rem;
        margin-bottom: 1rem;
    }
</style>

<div class="register-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="register-card">
                    <div class="register-header">
                        <h2 class="register-title">Daftar Akun Baru</h2>
                    </div>
                    <div class="register-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div>
                                <label for="nama" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama" name="nama" required>
                            </div>
                            <div>
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div>
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <p class="password-hint">Minimal 8 karakter, kombinasi huruf dan angka</p>
                            </div>
                            <div>
                                <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" class="btn-register">Daftar Sekarang</button>
                        </form>

                        <div class="register-footer">
                            Sudah punya akun? <a href="login.php" class="register-link">Login disini</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
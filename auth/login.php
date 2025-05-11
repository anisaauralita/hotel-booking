<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

// Inisialisasi variabel error
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']);

if (isLoggedIn()) {
    header("Location: " . BASE_URL . (isAdmin() ? 'admin/dashboard.php' : 'index.php'));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    if (login($email, $password)) {
        $redirect = isAdmin() ? 'admin/dashboard.php' : 'index.php';
        header("Location: " . BASE_URL . $redirect);
        exit();
    } else {
        $_SESSION['error'] = "Email atau password salah";
        header("Location: " . BASE_URL . "auth/login.php");
        exit();
    }
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<style>
    :root {
        --primary: #0f3460;
        --secondary: #16213e;
        --accent: #0ea5e9;
        --light: #f8fafc;
        --dark: #1e293b;
    }

    .login-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        background: linear-gradient(rgba(15, 52, 96, 0.8), rgba(22, 33, 62, 0.8)), 
                    url('../assets/images/oke.png');
        background-size: cover;
        background-position: center;
        padding: 2rem 0;
    }

    .login-card {
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
        border: none;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2);
        background: white;
        transition: all 0.3s ease;
    }

    .login-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 25px 60px rgba(15, 23, 42, 0.3);
    }

    .login-header {
        background: linear-gradient(45deg, var(--primary), var(--secondary));
        color: white;
        padding: 2rem;
        text-align: center;
    }

    .login-title {
        font-weight: 700;
        font-size: 1.8rem;
        margin: 0;
    }

    .login-body {
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

    .btn-login {
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

    .btn-login:hover {
        background: linear-gradient(45deg, #38bdf8, var(--accent));
        transform: translateY(-2px);
        box-shadow: 0 15px 25px rgba(14, 165, 233, 0.3);
    }

    .login-footer {
        text-align: center;
        margin-top: 1.5rem;
        color: #64748b;
    }

    .login-link {
        color: var(--accent);
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .login-link:hover {
        color: var(--primary);
        text-decoration: underline;
    }

    .alert-danger {
        background-color: #fee2e2;
        color: #b91c1c;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        border: 1px solid #fca5a5;
    }
</style>

<div class="login-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-card">
                    <div class="login-header">
                        <h2 class="login-title">Login</h2>
                    </div>
                    <div class="login-body">
                        <?php if ($error): ?>
                            <div class="alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div>
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div>
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn-login">Login</button>
                        </form>

                        <div class="login-footer">
                            Belum punya akun? <a href="<?= BASE_URL ?>auth/register.php" class="login-link">Daftar di sini</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn() || isAdmin()) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Ambil data user
$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Update profile
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $nama = sanitize($_POST['nama']);
    $email = sanitize($_POST['email']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi email unik
    if ($email != $user['email']) {
        $check_sql = "SELECT id FROM users WHERE email='$email'";
        if ($conn->query($check_sql)->num_rows > 0) {
            $error = "Email sudah digunakan oleh user lain";
        }
    }
    
    if (empty($error)) {
        // Update data dasar
        $sql = "UPDATE users SET nama='$nama', email='$email' WHERE id='$user_id'";
        
        // Update password jika diisi
        if (!empty($current_password) && !empty($new_password)) {
            if (password_verify($current_password, $user['password'])) {
                if ($new_password == $confirm_password) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $sql = "UPDATE users SET nama='$nama', email='$email', password='$hashed_password' WHERE id='$user_id'";
                } else {
                    $error = "Password baru tidak sama dengan konfirmasi password";
                }
            } else {
                $error = "Password saat ini salah";
            }
        }
        
        if (empty($error) && $conn->query($sql)) {
            $_SESSION['user_name'] = $nama;
            $success = "Profile berhasil diperbarui";
            // Refresh data user
            $result = $conn->query("SELECT * FROM users WHERE id='$user_id'");
            $user = $result->fetch_assoc();
        } else if (empty($error)) {
            $error = "Terjadi kesalahan: " . $conn->error;
        }
    }
}

?>

<?php include '../includes/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3>Profile Saya</h3>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama" name="nama" value="<?= $user['nama'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= $user['email'] ?>" required>
                    </div>
                    
                    <h5 class="mt-4">Ganti Password</h5>
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Password Saat Ini</label>
                        <input type="password" class="form-control" id="current_password" name="current_password">
                        <small class="text-muted">Kosongkan jika tidak ingin mengganti password</small>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="new_password" name="new_password">
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                    </div>
                    
                    <button type="submit" name="update_profile" class="btn btn-primary">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
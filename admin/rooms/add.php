<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

if (!isAdmin()) {
    header("Location: ../../index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nomor_kamar = sanitize($_POST['nomor_kamar']);
    $tipe = sanitize($_POST['tipe']);
    $harga = (int)$_POST['harga'];
    $status = sanitize($_POST['status']);
    
    // Handle file upload
    $foto = 'default.jpg';
    if (isset($_FILES['foto'])) {
        $target_dir = __DIR__ . "/../../assets/images/rooms/";

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        
        $file_ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (!in_array($file_ext, $allowed_ext)) {
            $error = "Format file tidak didukung. Gunakan JPG, PNG, atau WEBP";
        }
        elseif ($_FILES['foto']['size'] > 2097152) {
            $error = "Ukuran file terlalu besar (max 2MB)";
        }
        elseif (!getimagesize($_FILES['foto']['tmp_name'])) {
            $error = "File bukan gambar valid";
        } else {
            $foto = 'room_' . uniqid() . '.' . $file_ext;
            $target_file = $target_dir . $foto;
            
            if (!move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
                $error = "Gagal mengupload file";
            }
        }
    }   
    
    if (empty($error)) {
        $sql = "INSERT INTO rooms (nomor_kamar, tipe, harga, status, foto) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiss", $nomor_kamar, $tipe, $harga, $status, $foto);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Kamar berhasil ditambahkan!";
            header("Location: index.php");
            exit();
        } else {
            $error = "Terjadi kesalahan: " . $stmt->error;
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

.room-management {
    padding: 2rem 0;
    min-height: calc(100vh - 56px);
}

.room-form-container {
    background: white;
    padding: 2rem;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    transition: all 0.3s ease;
    border-left: 4px solid var(--accent);
}

.room-form-container:hover {
    box-shadow: 0 15px 35px rgba(15, 23, 42, 0.1);
}

.form-header {
    margin-bottom: 2rem;
    border-bottom: 1px solid var(--grey-light);
    padding-bottom: 1rem;
}

.form-header h3 {
    color: var(--primary);
    font-weight: 700;
}

.form-label {
    font-weight: 600;
    color: var(--secondary);
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border: 2px solid var(--grey-light);
    padding: 0.8rem 1rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
}

.input-group-text {
    background-color: var(--light);
    border: 2px solid var(--grey-light);
    font-weight: 600;
}

.btn-primary {
    background: linear-gradient(45deg, var(--accent), #38bdf8);
    border: none;
    padding: 0.8rem 2rem;
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

.image-preview {
    width: 100%;
    max-width: 300px;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    display: none;
    margin: 1rem auto;
}

.current-image {
    width: 100%;
    max-width: 300px;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    margin: 0.5rem 0;
}

.alert {
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
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
</style>

<div class="room-management">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="room-form-container reveal">
          <div class="form-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Tambah Kamar Baru</h3>
            <a href="index.php" class="btn btn-outline-secondary">
              <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
          </div>

          <?php if ($error): ?>
            <div class="alert alert-danger reveal"><?= $error ?></div>
          <?php endif; ?>

          <form method="POST" action="" enctype="multipart/form-data">
            <div class="row">
              <div class="col-md-6 mb-3 reveal">
                <label for="nomor_kamar" class="form-label">Nomor Kamar</label>
                <input type="text" class="form-control" id="nomor_kamar" name="nomor_kamar" required>
              </div>
              <div class="col-md-6 mb-3 reveal">
                <label for="tipe" class="form-label">Tipe Kamar</label>
                <select class="form-select" id="tipe" name="tipe" required>
                  <option value="single">Single</option>
                  <option value="double">Double</option>
                  <option value="suite">Suite</option>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3 reveal">
                <label for="harga" class="form-label">Harga per Malam</label>
                <div class="input-group">
                  <span class="input-group-text">Rp</span>
                  <input type="number" class="form-control" id="harga" name="harga" required>
                </div>
              </div>
              <div class="col-md-6 mb-3 reveal">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                  <option value="tersedia">Tersedia</option>
                  <option value="dipesan">Dipesan</option>
                </select>
              </div>
            </div>

            <div class="mb-4 reveal">
              <label for="foto" class="form-label">Foto Kamar</label>
              <input type="file" class="form-control" id="foto" name="foto" accept="image/*" onchange="previewImage(this)">
              <div class="mt-3 text-center">
                <img id="imagePreview" class="image-preview" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="Preview">
              </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end reveal">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i> Simpan Kamar
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function previewImage(input) {
  const preview = document.getElementById('imagePreview');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      preview.src = e.target.result;
      preview.style.display = 'block';
    }
    reader.readAsDataURL(input.files[0]);
  }
}

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

<?php include '../../includes/footer.php'; ?>
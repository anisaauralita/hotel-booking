<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';

if (!isAdmin()) {
    header("Location: " . BASE_URL . "/index.php");
    exit();
}

// Get all rooms with pagination
$per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $per_page;

$sql = "SELECT * FROM rooms ORDER BY id DESC LIMIT $offset, $per_page";
$result = $conn->query($sql);
$rooms = $result->fetch_all(MYSQLI_ASSOC);

// Count total rooms
$total_result = $conn->query("SELECT COUNT(*) FROM rooms");
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $per_page);
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>

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
}

.room-header {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-left: 4px solid var(--accent);
}

.room-table {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    transition: all 0.3s ease;
}

.room-table:hover {
    box-shadow: 0 20px 40px rgba(15, 23, 42, 0.15);
}

.room-table .table {
    margin-bottom: 0;
}

.room-table .table thead th {
    background-color: var(--primary);
    color: white;
    font-weight: 600;
    padding: 1rem;
    border-bottom: none;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 0.85rem;
}

.room-table .table tbody td {
    padding: 1rem;
    vertical-align: middle;
    border-bottom: 1px solid var(--grey-light);
}

.room-table .table tbody tr:last-child td {
    border-bottom: none;
}

.room-table .table tbody tr:hover {
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

.btn-danger {
    background: linear-gradient(45deg, var(--danger), #f87171);
    border: none;
    box-shadow: 0 5px 15px rgba(239, 68, 68, 0.3);
}

.btn-danger:hover {
    background: linear-gradient(45deg, #dc2626, var(--danger));
}

.btn-action {
    width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
}

.pagination {
    justify-content: center;
}

.pagination .page-item.active .page-link {
    background-color: var(--primary);
    border-color: var(--primary);
}

.pagination .page-link {
    color: var(--primary);
    font-weight: 600;
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

.room-table img {
    width: 80px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    display: block;
    margin: 0 auto;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.room-table img:hover {
    transform: scale(1.05);
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

<div class="room-management">
  <div class="container">
    <div class="room-header reveal">
      <div>
        <h2 class="mb-0" style="color: var(--primary); font-weight: 700;">Manajemen Kamar</h2>
        <p class="text-muted mb-0">Total <?= $total_rows ?> kamar terdaftar</p>
      </div>
      <div>
        <a href="<?= BASE_URL ?>admin/rooms/add.php" class="btn btn-primary">
          <i class="fas fa-plus me-2"></i> Tambah Kamar
        </a>
        <button class="btn btn-outline-secondary ms-2" data-bs-toggle="modal" data-bs-target="#filterModal">
          <i class="fas fa-filter me-2"></i> Filter
        </button>
      </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
      <div class="alert alert-success alert-dismissible fade show reveal">
        <?= $_SESSION['success'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="card room-table reveal">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th width="5%">ID</th>
                <th width="15%">Nomor Kamar</th>
                <th width="15%">Tipe</th>
                <th width="20%">Harga</th>
                <th width="15%">Status</th>
                <th width="20%">Foto</th>
                <th width="10%">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($rooms)): ?>
                <?php foreach ($rooms as $room): ?>
                  <tr class="reveal">
                    <td>#<?= $room['id'] ?></td>
                    <td><?= htmlspecialchars($room['nomor_kamar']) ?></td>
                    <td>
                      <span class="badge bg-primary">
                        <?= ucfirst($room['tipe']) ?>
                      </span>
                    </td>
                    <td>Rp <?= number_format($room['harga'], 0, ',', '.') ?></td>
                    <td>
                      <span class="badge bg-<?= $room['status'] == 'tersedia' ? 'success' : 'danger' ?>">
                        <?= ucfirst($room['status']) ?>
                      </span>
                    </td>
                    <td>
                      <?php if (!empty($room['foto'])): ?>
                        <img src="<?= BASE_URL ?>assets/images/rooms/<?= $room['foto'] ?>" alt="Kamar <?= $room['id'] ?>">
                      <?php else: ?>
                        <span class="text-muted">No Image</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <div class="d-flex gap-2">
                        <a href="<?= BASE_URL ?>admin/rooms/edit.php?id=<?= $room['id'] ?>" class="btn btn-sm btn-primary btn-action" title="Edit">
                          <i class="fas fa-edit"></i>
                        </a>
                        <a href="<?= BASE_URL ?>admin/rooms/delete.php?id=<?= $room['id'] ?>" 
                          class="btn btn-sm btn-danger btn-action" 
                          title="Hapus"
                          onclick="return confirm('Yakin ingin menghapus kamar ini?')">
                          <i class="fas fa-trash"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7">
                    <div class="empty-state">
                      <i class="fas fa-hotel"></i>
                      <h5>Tidak ada data kamar</h5>
                      <p class="text-muted">Belum ada kamar yang terdaftar</p>
                      <a href="<?= BASE_URL ?>admin/rooms/add.php" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-2"></i>Tambah Kamar Pertama
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <?php if ($total_pages > 1): ?>
        <nav class="px-3 py-2 border-top">
          <ul class="pagination mb-0">
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= $page-1 ?>">Sebelumnya</a>
            </li>
            
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
              <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>
            
            <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= $page+1 ?>">Selanjutnya</a>
            </li>
          </ul>
        </nav>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Filter Kamar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form method="get" action="">
          <div class="mb-3">
            <label class="form-label">Tipe Kamar</label>
            <select class="form-select" name="type">
              <option value="">Semua Tipe</option>
              <option value="single">Single</option>
              <option value="double">Double</option>
              <option value="suite">Suite</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" name="status">
              <option value="">Semua Status</option>
              <option value="tersedia">Tersedia</option>
              <option value="dipesan">Dipesan</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Harga</label>
            <div class="input-group">
              <span class="input-group-text">Rp</span>
              <input type="number" class="form-control" placeholder="Minimum" name="min_price">
              <span class="input-group-text">-</span>
              <input type="number" class="form-control" placeholder="Maksimum" name="max_price">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-primary">Terapkan Filter</button>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<!-- Font Awesome -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<script>
// Enable tooltips and animations
document.addEventListener('DOMContentLoaded', function() {
    // Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Scroll reveal animation
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
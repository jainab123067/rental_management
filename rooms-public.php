<?php
$pageTitle = "Daftar Kamar";
require_once 'includes/header-landing.php';

// Filter
$minPrice = $_GET['min_price'] ?? 0;
$maxPrice = $_GET['max_price'] ?? 10000000;
$type = $_GET['type'] ?? '';

$query = "SELECT * FROM rooms WHERE status='Available' AND price BETWEEN $minPrice AND $maxPrice";
if (!empty($type)) {
    $query .= " AND type='$type'";
}
$query .= " ORDER BY price ASC";

$rooms = mysqli_query($conn, $query);
$totalRooms = mysqli_num_rows($rooms);
?>

<!-- Page Header -->
<section style="background: linear-gradient(135deg, #667eea, #764ba2); padding: 150px 0 80px; color: white;">
    <div class="container text-center">
        <h1 style="font-size: 3rem; font-weight: 700; margin-bottom: 15px;">Pilihan Kamar</h1>
        <p style="font-size: 1.2rem; opacity: 0.9;">Temukan kamar yang sesuai dengan kebutuhan dan budget Anda</p>
    </div>
</section>

<!-- Filter & Rooms -->
<section style="padding: 80px 0;">
    <div class="container">
        <!-- Filter -->
        <div class="card border-0 shadow-sm mb-5" style="border-radius: 20px;">
            <div class="card-body p-4">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Harga Minimum</label>
                        <input type="number" name="min_price" class="form-control" value="<?= $minPrice ?>" placeholder="Rp 0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Harga Maksimum</label>
                        <input type="number" name="max_price" class="form-control" value="<?= $maxPrice ?>" placeholder="Rp 10.000.000">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tipe Kamar</label>
                        <select name="type" class="form-select">
                            <option value="">Semua Tipe</option>
                            <option value="Single" <?= $type == 'Single' ? 'selected' : '' ?>>Single</option>
                            <option value="Double" <?= $type == 'Double' ? 'selected' : '' ?>>Double</option>
                            <option value="Suite" <?= $type == 'Suite' ? 'selected' : '' ?>>Suite</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100" style="background: linear-gradient(135deg, #667eea, #764ba2); border: none; padding: 12px;">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Results Info -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <p class="mb-0 text-muted">Menampilkan <strong><?= $totalRooms ?></strong> kamar tersedia</p>
            <a href="rooms-public.php" class="btn btn-outline-secondary">
                <i class="fas fa-redo me-2"></i>Reset Filter
            </a>
        </div>
        
        <!-- Rooms Grid -->
        <?php if ($totalRooms > 0): ?>
        <div class="row">
            <?php while ($room = mysqli_fetch_assoc($rooms)): 
                $facilities = !empty($room['facilities']) ? explode(',', $room['facilities']) : [];
            ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="room-card h-100">
                    <div class="room-image">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <div class="room-body">
                        <h3 class="room-title"><?= htmlspecialchars($room['room_name']) ?></h3>
                        <div class="room-price">
                            Rp <?= number_format($room['price'], 0, ',', '.') ?>
                            <small>/bulan</small>
                        </div>
                        <p class="text-muted mb-3">
                            <i class="fas fa-tag me-2"></i><?= $room['type'] ?>
                            <span class="mx-2">|</span>
                            <i class="fas fa-hashtag me-2"></i><?= htmlspecialchars($room['room_number']) ?>
                        </p>
                        <?php if (!empty($facilities)): ?>
                        <ul class="room-features">
                            <?php foreach (array_slice($facilities, 0, 4) as $facility): ?>
                            <li><i class="fas fa-check"></i><?= trim($facility) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                        <div class="d-grid gap-2">
                            <a href="room-detail.php?id=<?= $room['id'] ?>" class="btn btn-room">
                                <i class="fas fa-info-circle me-2"></i>Lihat Detail
                            </a>
                            <a href="booking-online.php?room_id=<?= $room['id'] ?>" class="btn" style="background: #28a745; color: white;">
                                <i class="fas fa-calendar-check me-2"></i>Booking Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-door-open fa-5x text-muted mb-4"></i>
            <h4 class="text-muted">Maaf, tidak ada kamar yang sesuai dengan filter Anda</h4>
            <a href="rooms-public.php" class="btn btn-primary mt-3" style="background: linear-gradient(135deg, #667eea, #764ba2); border: none;">
                <i class="fas fa-redo me-2"></i>Reset Filter
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer-landing.php'; ?>
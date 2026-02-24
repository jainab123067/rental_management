<?php
require_once 'includes/header-landing.php';

$room_id = $_GET['id'] ?? 0;
$room = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM rooms WHERE id=$room_id AND status='Available'"));

if (!$room) {
    header("Location: rooms.php");
    exit();
}

$facilities = !empty($room['facilities']) ? explode(',', $room['facilities']) : [];
?>

<!-- Page Header -->
<section style="background: linear-gradient(135deg, #667eea, #764ba2); padding: 150px 0 80px; color: white;">
    <div class="container">
        <a href="rooms.php" class="btn btn-outline-light mb-4">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Kamar
        </a>
        <h1 style="font-size: 3rem; font-weight: 700; margin-bottom: 15px;"><?= htmlspecialchars($room['room_name']) ?></h1>
        <p style="font-size: 1.2rem; opacity: 0.9;">
            <i class="fas fa-hashtag me-2"></i><?= htmlspecialchars($room['room_number']) ?>
            <span class="mx-3">|</span>
            <i class="fas fa-tag me-2"></i><?= $room['type'] ?>
            <span class="mx-3">|</span>
            <i class="fas fa-check-circle me-2"></i>Tersedia
        </p>
    </div>
</section>

<!-- Room Detail -->
<section style="padding: 80px 0;">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <!-- Room Image -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; overflow: hidden;">
                    <div style="height: 400px; background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-door-open fa-5x" style="color: white;"></i>
                    </div>
                </div>
                
                <!-- Description -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                    <div class="card-body p-4">
                        <h4 class="mb-3"><i class="fas fa-info-circle me-2"></i>Deskripsi</h4>
                        <p class="text-muted" style="line-height: 1.8;"><?= nl2br(htmlspecialchars($room['description'] ?? 'Tidak ada deskripsi')) ?></p>
                    </div>
                </div>
                
                <!-- Facilities -->
                <?php if (!empty($facilities)): ?>
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                    <div class="card-body p-4">
                        <h4 class="mb-3"><i class="fas fa-star me-2"></i>Fasilitas</h4>
                        <div class="row">
                            <?php foreach ($facilities as $facility): ?>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle me-3" style="color: #28a745; font-size: 1.2rem;"></i>
                                    <span><?= trim($facility) ?></span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm" style="border-radius: 20px; position: sticky; top: 100px;">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h2 style="color: #667eea; font-weight: 700;">
                                Rp <?= number_format($room['price'], 0, ',', '.') ?>
                            </h2>
                            <p class="text-muted mb-0">per bulan</p>
                        </div>
                        
                        <div class="d-grid gap-2 mb-4">
                            <a href="booking-online.php?room_id=<?= $room['id'] ?>" class="btn btn-lg" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none;">
                                <i class="fas fa-calendar-check me-2"></i>Booking Sekarang
                            </a>
                            <a href="https://wa.me/6281234567890?text=Halo, saya tertarik dengan kamar <?= urlencode($room['room_name']) ?>" target="_blank" class="btn btn-lg btn-whatsapp">
                                <i class="fab fa-whatsapp me-2"></i>Chat via WhatsApp
                            </a>
                        </div>
                        
                        <hr>
                        
                        <div class="mb-3">
                            <h6 class="mb-2"><i class="fas fa-user me-2"></i>Hubungi Kami</h6>
                            <p class="text-muted mb-1"><i class="fas fa-phone me-2"></i>+62 812-3456-7890</p>
                            <p class="text-muted mb-0"><i class="fas fa-envelope me-2"></i>info@rentalkost.com</p>
                        </div>
                        
                        <div class="alert alert-info mb-0" style="border-radius: 10px;">
                            <small><i class="fas fa-info-circle me-2"></i>Kamar ini tersedia dan siap untuk di-booking</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer-landing.php'; ?>
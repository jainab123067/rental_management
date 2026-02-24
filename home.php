<?php
$pageTitle = "Kost Nyaman & Strategis";
require_once 'includes/header-landing.php';

// Get stats
$totalRooms = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM rooms WHERE status='Available'"))['count'];
$avgPrice = mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(price) as avg FROM rooms WHERE status='Available'"))['avg'];
$totalTenants = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM tenants"))['count'];
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-content">
                <h1>Temukan Kost Nyaman & Strategis di Sini!</h1>
                <p>Kost berkualitas dengan fasilitas lengkap, lokasi strategis, dan harga terjangkau. Cocok untuk mahasiswa dan profesional.</p>
                <div>
                    <a href="rooms-public.php" class="btn btn-hero btn-hero-primary">
                        <i class="fas fa-door-open me-2"></i>Lihat Kamar
                    </a>
                    <a href="#kontak" class="btn btn-hero btn-hero-outline">
                        <i class="fas fa-phone me-2"></i>Hubungi Kami
                    </a>
                </div>
            </div>
            <div class="col-lg-6 hero-image">
                <i class="fas fa-building fa-10x" style="color: rgba(255,255,255,0.3);"></i>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <div class="stat-number"><?= $totalRooms ?></div>
                    <div class="stat-label">Kamar Tersedia</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number"><?= $totalTenants ?></div>
                    <div class="stat-label">Penghuni Puas</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-tag"></i>
                    </div>
                    <div class="stat-number">Rp <?= number_format($avgPrice ?? 0, 0, ',', '.') ?></div>
                    <div class="stat-label">Harga Mulai</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-number">4.9</div>
                    <div class="stat-label">Rating</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Rooms Section -->
<section class="rooms-section">
    <div class="container">
        <div class="section-title">
            <h2>Pilih Kamar Impian Anda</h2>
            <p>Berbagai pilihan kamar dengan fasilitas lengkap dan harga terjangkau</p>
        </div>
        
        <div class="row">
            <?php
            $rooms = mysqli_query($conn, "SELECT * FROM rooms WHERE status='Available' ORDER BY price ASC LIMIT 6");
            while ($room = mysqli_fetch_assoc($rooms)):
                $facilities = !empty($room['facilities']) ? explode(',', $room['facilities']) : [];
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="room-card">
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
                            <?php foreach (array_slice($facilities, 0, 3) as $facility): ?>
                            <li><i class="fas fa-check"></i><?= trim($facility) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                        <a href="room-detail.php?id=<?= $room['id'] ?>" class="btn btn-room">
                            <i class="fas fa-info-circle me-2"></i>Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
        <div class="text-center mt-5">
            <a href="rooms-public.php" class="btn btn-lg" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 15px 40px; border-radius: 50px; font-weight: 600;">
                <i class="fas fa-th me-2"></i>Lihat Semua Kamar
            </a>
        </div>
    </div>
</section>

<!-- Facilities Section -->
<section class="facilities-section" id="fasilitas">
    <div class="container">
        <div class="section-title">
            <h2>Fasilitas Lengkap</h2>
            <p>Nikmati berbagai fasilitas untuk kenyamanan Anda</p>
        </div>
        
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="facility-card">
                    <div class="facility-icon">
                        <i class="fas fa-wifi"></i>
                    </div>
                    <h5>WiFi Cepat</h5>
                    <p class="text-muted mb-0">Internet gratis 24/7</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="facility-card">
                    <div class="facility-icon">
                        <i class="fas fa-snowflake"></i>
                    </div>
                    <h5>AC</h5>
                    <p class="text-muted mb-0">Kamar ber-AC nyaman</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="facility-card">
                    <div class="facility-icon">
                        <i class="fas fa-shower"></i>
                    </div>
                    <h5>Kamar Mandi Dalam</h5>
                    <p class="text-muted mb-0">Private & bersih</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="facility-card">
                    <div class="facility-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h5>Keamanan 24 Jam</h5>
                    <p class="text-muted mb-0">CCTV & security</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="facility-card">
                    <div class="facility-icon">
                        <i class="fas fa-parking"></i>
                    </div>
                    <h5>Area Parkir</h5>
                    <p class="text-muted mb-0">Luas & aman</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="facility-card">
                    <div class="facility-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h5>Dapur Bersama</h5>
                    <p class="text-muted mb-0">Lengkap & bersih</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="facility-card">
                    <div class="facility-icon">
                        <i class="fas fa-tv"></i>
                    </div>
                    <h5>Ruang TV</h5>
                    <p class="text-muted mb-0">Untuk bersantai</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="facility-card">
                    <div class="facility-icon">
                        <i class="fas fa-broom"></i>
                    </div>
                    <h5>Laundry</h5>
                    <p class="text-muted mb-0">Layanan tersedia</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section" id="kontak">
    <div class="container">
        <div class="section-title">
            <h2>Hubungi Kami</h2>
            <p>Tertarik? Segera hubungi kami untuk informasi lebih lanjut</p>
        </div>
        
        <div class="row">
            <div class="col-lg-6">
                <div class="contact-card">
                    <h4 class="mb-4">Informasi Kontak</h4>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h6>Alamat</h6>
                            <p class="text-muted mb-0">Jl. Trans Lembata</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <h6>Telepon/WhatsApp</h6>
                            <p class="text-muted mb-0">+62 813-3965-1112</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h6>Email</h6>
                            <p class="text-muted mb-0">jainablembata@gmail.com</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h6>Jam Operasional</h6>
                            <p class="text-muted mb-0">Senin - Minggu: 08.00 - 20.00</p>
                        </div>
                    </div>
                    
                    <a href="https://wa.me/6281339651112" target="_blank" class="btn btn-whatsapp mt-3">
                        <i class="fab fa-whatsapp"></i> Chat WhatsApp
                    </a>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="contact-card">
                    <h4 class="mb-4">Kirim Pesan</h4>
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" placeholder="Masukkan nama Anda">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" placeholder="email@example.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="tel" class="form-control" placeholder="0812-3456-7890">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pesan</label>
                            <textarea class="form-control" rows="4" placeholder="Tulis pesan Anda..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" style="background: linear-gradient(135deg, #667eea, #764ba2); border: none; padding: 12px;">
                            <i class="fas fa-paper-plane me-2"></i>Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer-landing.php'; ?>
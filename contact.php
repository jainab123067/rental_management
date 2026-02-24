<?php
$pageTitle = "Hubungi Kami";
require_once 'includes/header-landing.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $message = sanitize($_POST['message']);
    
    // Send email or save to database (optional)
    // For now, just show success message
    $success = "Terima kasih! Pesan Anda telah terkirim. Kami akan menghubungi Anda segera.";
}
?>

<!-- Page Header -->
<section style="background: linear-gradient(135deg, #667eea, #764ba2); padding: 150px 0 80px; color: white;">
    <div class="container text-center">
        <h1 style="font-size: 3rem; font-weight: 700; margin-bottom: 15px;">Hubungi Kami</h1>
        <p style="font-size: 1.2rem; opacity: 0.9;">Punya pertanyaan? Kami siap membantu Anda</p>
    </div>
</section>

<!-- Contact Section -->
<section style="padding: 80px 0;">
    <div class="container">
        <?php if ($success): ?>
        <div class="alert alert-success" style="border-radius: 15px; padding: 30px;">
            <i class="fas fa-check-circle fa-3x mb-3"></i>
            <h4>✅ Pesan Terkirim!</h4>
            <p><?= $success ?></p>
            <a href="contact.php" class="btn btn-primary mt-3" style="background: linear-gradient(135deg, #667eea, #764ba2); border: none;">
                <i class="fas fa-redo me-2"></i>Kirim Pesan Lain
            </a>
        </div>
        <?php else: ?>
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="contact-card h-100" style="border-radius: 20px;">
                    <h4 class="mb-4"><i class="fas fa-info-circle me-2"></i>Informasi Kontak</h4>
                    
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
                            <p class="text-muted mb-0">jainablembata@gamil.com</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h6>Jam Operasional</h6>
                            <p class="text-muted mb-0">Senin - Minggu: 08.00 - 20.00 WIB</p>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="https://wa.me/6281339651112" target="_blank" class="btn btn-whatsapp">
                            <i class="fab fa-whatsapp me-2"></i>Chat WhatsApp
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="contact-card" style="border-radius: 20px;">
                    <h4 class="mb-4"><i class="fas fa-paper-plane me-2"></i>Kirim Pesan</h4>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="tel" name="phone" class="form-control" placeholder="0812-3456-7890">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pesan <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control" rows="5" required placeholder="Tulis pesan Anda..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" style="background: linear-gradient(135deg, #667eea, #764ba2); border: none; padding: 12px;">
                            <i class="fas fa-paper-plane me-2"></i>Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Map (Optional) -->
        <div class="mt-5">
            <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                <div style="height: 400px; background: #e9ecef; display: flex; align-items: center; justify-content: center;">
                    <div class="text-center text-muted">
                        <i class="fas fa-map-marked-alt fa-4x mb-3"></i>
                        <h5>Google Maps</h5>
                        <p>Embed Google Maps di sini</p>
                        <small>Contoh: &lt;iframe src="https://www.google.com/maps/embed?..."&gt;&lt;/iframe&gt;</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer-landing.php'; ?>
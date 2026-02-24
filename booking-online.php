<?php
require_once 'includes/header-landing.php';

$success = '';
$error = '';

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $id_card = sanitize($_POST['id_card']);
    $room_id = (int)$_POST['room_id'];
    $check_in = sanitize($_POST['check_in']);
    $duration = (int)$_POST['duration'];
    $notes = sanitize($_POST['notes']);
    
    // Get room info
    $room = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM rooms WHERE id=$room_id AND status='Available'"));
    
    if ($room) {
        // Calculate total price
        $total_price = $room['price'] * $duration;
        
        // Start transaction
        mysqli_begin_transaction($conn);
        
        try {
            // 1. Insert tenant
            $query_tenant = "INSERT INTO tenants (full_name, email, phone, id_card, address, emergency_contact, emergency_name) 
                            VALUES ('$full_name', '$email', '$phone', '$id_card', '-', '-', '-')";
            mysqli_query($conn, $query_tenant);
            $tenant_id = mysqli_insert_id($conn);
            
            // 2. Insert booking
            $check_out = date('Y-m-d', strtotime("$check_in + $duration months"));
            $query_booking = "INSERT INTO bookings (tenant_id, room_id, check_in, check_out, duration, total_price, status, notes) 
                             VALUES ($tenant_id, $room_id, '$check_in', '$check_out', $duration, $total_price, 'Pending', '$notes')";
            mysqli_query($conn, $query_booking);
            $booking_id = mysqli_insert_id($conn);
            
            mysqli_commit($conn);
            
            $success = "Booking berhasil! Kode booking Anda: <strong>BOOK-$booking_id</strong>. Kami akan menghubungi Anda segera untuk konfirmasi.";
            
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = "Terjadi kesalahan saat memproses booking. Silakan coba lagi atau hubungi kami via WhatsApp.";
        }
    } else {
        $error = "Maaf, kamar ini sudah tidak tersedia.";
    }
}

$room_id = $_GET['room_id'] ?? 0;
$room = null;
if ($room_id) {
    $room = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM rooms WHERE id=$room_id AND status='Available'"));
}
?>

<!-- Page Header -->
<section style="background: linear-gradient(135deg, #667eea, #764ba2); padding: 150px 0 80px; color: white;">
    <div class="container text-center">
        <h1 style="font-size: 3rem; font-weight: 700; margin-bottom: 15px;">Booking Online</h1>
        <p style="font-size: 1.2rem; opacity: 0.9;">Isi formulir di bawah untuk melakukan pemesanan kamar</p>
    </div>
</section>

<!-- Booking Form -->
<section style="padding: 80px 0;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if ($success): ?>
                <div class="alert alert-success" style="border-radius: 15px; padding: 30px;">
                    <i class="fas fa-check-circle fa-3x mb-3"></i>
                    <h4>✅ Booking Berhasil!</h4>
                    <p><?= $success ?></p>
                    <div class="mt-4">
                        <a href="index.php" class="btn btn-primary me-2" style="background: linear-gradient(135deg, #667eea, #764ba2); border: none;">
                            <i class="fas fa-home me-2"></i>Kembali ke Beranda
                        </a>
                        <a href="https://wa.me/6281234567890" target="_blank" class="btn btn-whatsapp">
                            <i class="fab fa-whatsapp me-2"></i>Hubungi via WhatsApp
                        </a>
                    </div>
                </div>
                <?php elseif ($error): ?>
                <div class="alert alert-danger" style="border-radius: 15px; padding: 30px;">
                    <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                    <h4>❌ Booking Gagal</h4>
                    <p><?= $error ?></p>
                    <a href="rooms.php" class="btn btn-primary mt-3" style="background: linear-gradient(135deg, #667eea, #764ba2); border: none;">
                        <i class="fas fa-door-open me-2"></i>Lihat Kamar Lain
                    </a>
                </div>
                <?php else: ?>
                
                <?php if ($room): ?>
                <!-- Room Summary -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; background: linear-gradient(135deg, #667eea, #764ba2); color: white;">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="mb-2"><i class="fas fa-door-open me-2"></i><?= htmlspecialchars($room['room_name']) ?></h4>
                                <p class="mb-0 opacity-75">
                                    <?= htmlspecialchars($room['room_number']) ?> | <?= $room['type'] ?> | 
                                    Rp <?= number_format($room['price'], 0, ',', '.') ?>/bulan
                                </p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <span class="badge bg-white text-primary" style="font-size: 1rem;">Tersedia</span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Form -->
                <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                    <div class="card-body p-4 p-md-5">
                        <form method="POST">
                            <h5 class="mb-4"><i class="fas fa-user me-2"></i>Data Penyewa</h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="full_name" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">No. Telepon/WA <span class="text-danger">*</span></label>
                                    <input type="tel" name="phone" class="form-control" placeholder="0812-3456-7890" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">No. KTP/ID Card <span class="text-danger">*</span></label>
                                    <input type="text" name="id_card" class="form-control" required>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <h5 class="mb-4"><i class="fas fa-calendar me-2"></i>Detail Booking</h5>
                            
                            <input type="hidden" name="room_id" value="<?= $room_id ?>">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Check-In <span class="text-danger">*</span></label>
                                    <input type="date" name="check_in" class="form-control" min="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Durasi (Bulan) <span class="text-danger">*</span></label>
                                    <select name="duration" class="form-select" required>
                                        <option value="1">1 Bulan</option>
                                        <option value="3">3 Bulan</option>
                                        <option value="6" selected>6 Bulan</option>
                                        <option value="12">12 Bulan</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Catatan Tambahan</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Pertanyaan atau permintaan khusus..."></textarea>
                            </div>
                            
                            <div class="alert alert-info" style="border-radius: 10px;">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Info:</strong> Booking ini berstatus <strong>Pending</strong>. Admin kami akan menghubungi Anda untuk konfirmasi dan pembayaran.
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-lg" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; padding: 15px;">
                                    <i class="fas fa-paper-plane me-2"></i>Kirim Booking
                                </button>
                                <a href="rooms.php" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer-landing.php'; ?>
<?php
/**
 * Helper Functions untuk Sistem Rental
 */

// Format Rupiah
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Format Tanggal Indonesia
function formatTanggal($tanggal) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $pecahkan = explode('-', $tanggal);
    return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
}

// Kirim WhatsApp
function sendWhatsApp($phone, $message) {
    // Format phone: 6281234567890
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (substr($phone, 0, 1) == '0') {
        $phone = '62' . substr($phone, 1);
    }
    
    $url = "https://wa.me/" . $phone . "?text=" . urlencode($message);
    return $url;
}

// Generate Random String
function generateRandomString($length = 10) {
    return bin2hex(random_bytes($length / 2));
}

// Upload File
function uploadFile($file, $folder = 'rooms') {
    $uploadDir = BASE_URL . 'uploads/' . $folder . '/';
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Tipe file tidak diperbolehkan'];
    }
    
    if ($file['size'] > 5000000) { // 5MB
        return ['success' => false, 'message' => 'Ukuran file terlalu besar (max 5MB)'];
    }
    
    $fileName = time() . '_' . basename($file['name']);
    $targetPath = $uploadDir . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'filename' => $fileName];
    }
    
    return ['success' => false, 'message' => 'Gagal upload file'];
}

// Delete File
function deleteFile($folder, $filename) {
    $filePath = BASE_URL . 'uploads/' . $folder . '/' . $filename;
    if (file_exists($filePath)) {
        return unlink($filePath);
    }
    return false;
}

// Hitung Selisih Hari
function selisihHari($tanggal1, $tanggal2) {
    $date1 = new DateTime($tanggal1);
    $date2 = new DateTime($tanggal2);
    $interval = $date1->diff($date2);
    return $interval->days;
}

// Cek Pembayaran Jatuh Tempo
function getPembayaranJatuhTempo($conn, $hari = 7) {
    $query = "SELECT p.*, t.full_name, t.phone, b.check_in 
              FROM payments p 
              JOIN bookings b ON p.booking_id = b.id 
              JOIN tenants t ON b.tenant_id = t.id 
              WHERE p.status = 'Pending' 
              AND p.payment_date <= DATE_ADD(CURDATE(), INTERVAL $hari DAY)
              ORDER BY p.payment_date ASC";
    return mysqli_query($conn, $query);
}

// Get Booking Bulan Ini
function getBookingBulanIni($conn) {
    $query = "SELECT b.*, t.full_name, r.room_number 
              FROM bookings b 
              JOIN tenants t ON b.tenant_id = t.id 
              JOIN rooms r ON b.room_id = r.id 
              WHERE MONTH(b.check_in) = MONTH(CURRENT_DATE()) 
              AND YEAR(b.check_in) = YEAR(CURRENT_DATE())
              ORDER BY b.check_in ASC";
    return mysqli_query($conn, $query);
}

// Send Email (jika pakai SMTP)
function sendEmail($to, $subject, $message) {
    // Implementasi dengan PHPMailer atau mail()
    // Untuk sekarang pakai mail() basic
    $headers = "From: no-reply@rentalkost.com\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    return mail($to, $subject, $message, $headers);
}
?>
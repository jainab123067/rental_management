<?php
/**
 * Cron Job untuk Reminder Pembayaran
 * Jalankan setiap hari via Task Scheduler
 */

require_once '../config/database.php';

// Get pending payments due in 7 days
$payments = getPembayaranJatuhTempo($conn, 7);

$message = "🔔 *REMINDER PEMBAYARAN*\n\n";
$message .= "Halo! Kami ingin mengingatkan bahwa pembayaran sewa Anda akan jatuh tempo dalam waktu dekat.\n\n";
$message .= "Silakan segera melakukan pembayaran untuk menghindari keterlambatan.\n\n";
$message .= "Terima kasih,\nRental Management";

$count = 0;
while ($p = mysqli_fetch_assoc($payments)):
    $phone = $p['phone'];
    $name = $p['full_name'];
    $amount = $p['amount'];
    $dueDate = formatTanggal($p['payment_date']);
    
    $personalMessage = str_replace(
        ["[NAME]", "[AMOUNT]", "[DATE]"],
        [$name, formatRupiah($amount), $dueDate],
        $message
    );
    
    // Generate WhatsApp link
    $waLink = sendWhatsApp($phone, $personalMessage);
    
    // Log reminder (optional: save to database)
    error_log("Reminder sent to $name ($phone) - Due: $dueDate");
    
    $count++;
endwhile;

echo "✅ $count reminder(s) processed on " . date('Y-m-d H:i:s');
?>
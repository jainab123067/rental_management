<?php
// File: buat_password.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

$password_baru = 'admin123';
$hash_baru = password_hash($password_baru, PASSWORD_DEFAULT);

echo "<h2>🔐 Password Hash Generator</h2>";
echo "<div style='background:#f0f0f0; padding:20px; border-radius:10px; font-family:monospace;'>";
echo "<strong>Password:</strong> admin123<br><br>";
echo "<strong>Hash Baru:</strong><br>";
echo "<code style='word-break:break-all; background:#fff; padding:10px; display:block; margin:10px 0;'>$hash_baru</code><br>";
echo "</div>";

// Langsung update ke database
echo "<h3>📝 Menjalankan Update...</h3>";

$query = "UPDATE users SET password = '$hash_baru' WHERE username = 'admin'";
if (mysqli_query($conn, $query)) {
    echo "✅ <strong>SUCCESS!</strong> Password admin sudah di-update dengan hash baru.<br>";
    
    // Test langsung
    echo "<h3>🧪 Testing Password...</h3>";
    
    // Ambil hash yang baru disimpan
    $result = mysqli_query($conn, "SELECT password FROM users WHERE username = 'admin'");
    $user = mysqli_fetch_assoc($result);
    $hash_dari_db = $user['password'];
    
    // Test verify
    $test = password_verify('admin123', $hash_dari_db);
    
    if ($test) {
        echo "✅ <strong style='color:green; font-size:20px;'>PASSWORD VERIFICATION BERHASIL!</strong><br>";
        echo "Sekarang Anda bisa login dengan:<br>";
        echo "<strong>Username:</strong> admin<br>";
        echo "<strong>Password:</strong> admin123<br><br>";
        echo "<a href='login.php' style='background:#667eea; color:white; padding:10px 20px; text-decoration:none; border-radius:5px; display:inline-block;'>👉 Login Sekarang</a>";
    } else {
        echo "❌ <strong style='color:red;'>Masih gagal! Hash di database:</strong><br>";
        echo "<code>$hash_dari_db</code>";
    }
} else {
    echo "❌ Error: " . mysqli_error($conn);
}

echo "<br><br><hr>";
echo "<h4>Info Sistem:</h4>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Password Hash Algorithm: PASSWORD_DEFAULT<br>";
?>
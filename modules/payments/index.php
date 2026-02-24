<?php
$action = $_GET['action'] ?? 'list';

// Handle Save
if ($action == 'save' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $booking_id = sanitize($_POST['booking_id']);
    $amount = sanitize($_POST['amount']);
    $payment_date = sanitize($_POST['payment_date']);
    $payment_method = sanitize($_POST['payment_method']);
    $payment_type = sanitize($_POST['payment_type']);
    $status = sanitize($_POST['status']);
    $receipt_number = sanitize($_POST['receipt_number']);
    $notes = sanitize($_POST['notes']);
    
    if ($id) {
        $query = "UPDATE payments SET booking_id='$booking_id', amount='$amount', payment_date='$payment_date', 
                  payment_method='$payment_method', payment_type='$payment_type', status='$status', 
                  receipt_number='$receipt_number', notes='$notes' WHERE id=$id";
        showAlert('Data pembayaran berhasil diupdate!');
    } else {
        if (empty($receipt_number)) {
            $receipt_number = 'PAY-' . date('Ymd') . '-' . rand(1000, 9999);
        }
        $query = "INSERT INTO payments (booking_id, amount, payment_date, payment_method, payment_type, status, receipt_number, notes) 
                  VALUES ('$booking_id', '$amount', '$payment_date', '$payment_method', '$payment_type', '$status', '$receipt_number', '$notes')";
        showAlert('Data pembayaran berhasil ditambahkan!');
    }
    mysqli_query($conn, $query);
    redirect(BASE_URL . '?page=payments');
}

// Handle Delete
if ($action == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    mysqli_query($conn, "DELETE FROM payments WHERE id=$id");
    showAlert('Data pembayaran berhasil dihapus!');
    redirect(BASE_URL . '?page=payments');
}

$edit_data = null;
if ($action == 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM payments WHERE id=$id");
    $edit_data = mysqli_fetch_assoc($result);
}
?>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i> Data Pembayaran</h4>
        
        <!-- PERBAIKAN 1: Tombol untuk form DAN edit -->
        <?php if ($action != 'form' && $action != 'edit'): ?>
            <a href="?page=payments&action=form" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Pembayaran
            </a>
        <?php else: ?>
            <a href="?page=payments" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        <?php endif; ?>
    </div>

    <!-- PERBAIKAN 2: Tampilkan form untuk action form ATAU edit -->
    <?php if ($action == 'form' || $action == 'edit'): ?>
        <form method="POST" action="?page=payments&action=save">
            <?php if ($edit_data): ?>
                <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Booking <span class="text-danger">*</span></label>
                    <select name="booking_id" class="form-select" required>
                        <option value="">Pilih Booking</option>
                        <?php
                        $bookings = mysqli_query($conn, "SELECT b.id, t.full_name, r.room_number, b.status 
                            FROM bookings b 
                            JOIN tenants t ON b.tenant_id = t.id 
                            JOIN rooms r ON b.room_id = r.id 
                            ORDER BY b.created_at DESC");
                        while ($b = mysqli_fetch_assoc($bookings)):
                            $selected = ($edit_data['booking_id'] ?? '') == $b['id'] ? 'selected' : '';
                        ?>
                            <option value="<?= $b['id'] ?>" <?= $selected ?>>
                                <?= htmlspecialchars($b['full_name']) ?> - Kamar <?= $b['room_number'] ?> 
                                (<?= $b['status'] ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nomor Kwitansi</label>
                    <input type="text" name="receipt_number" class="form-control" 
                           value="<?= $edit_data['receipt_number'] ?? 'PAY-' . date('Ymd') . '-' . rand(1000, 9999) ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Jumlah (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="amount" class="form-control" 
                           value="<?= $edit_data['amount'] ?? '' ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tanggal Pembayaran <span class="text-danger">*</span></label>
                    <input type="date" name="payment_date" class="form-control" 
                           value="<?= $edit_data['payment_date'] ?? date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Metode Pembayaran</label>
                    <select name="payment_method" class="form-select">
                        <option value="Cash" <?= ($edit_data['payment_method'] ?? '') == 'Cash' ? 'selected' : '' ?>>Cash/Tunai</option>
                        <option value="Transfer" <?= ($edit_data['payment_method'] ?? '') == 'Transfer' ? 'selected' : '' ?>>Transfer Bank</option>
                        <option value="E-Wallet" <?= ($edit_data['payment_method'] ?? '') == 'E-Wallet' ? 'selected' : '' ?>>E-Wallet</option>
                    </select>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jenis Pembayaran</label>
                    <select name="payment_type" class="form-select">
                        <option value="Monthly Rent" <?= ($edit_data['payment_type'] ?? '') == 'Monthly Rent' ? 'selected' : '' ?>>Sewa Bulanan</option>
                        <option value="Deposit" <?= ($edit_data['payment_type'] ?? '') == 'Deposit' ? 'selected' : '' ?>>Deposit/Uang Muka</option>
                        <option value="Additional" <?= ($edit_data['payment_type'] ?? '') == 'Additional' ? 'selected' : '' ?>>Tambahan</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="Pending" <?= ($edit_data['status'] ?? '') == 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Paid" <?= ($edit_data['status'] ?? '') == 'Paid' ? 'selected' : '' ?>>Lunas</option>
                        <option value="Failed" <?= ($edit_data['status'] ?? '') == 'Failed' ? 'selected' : '' ?>>Gagal</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Catatan</label>
                <textarea name="notes" class="form-control" rows="2"><?= $edit_data['notes'] ?? '' ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan
            </button>
        </form>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No. Kwitansi</th>
                        <th>Penyewa</th>
                        <th>Kamar</th>
                        <th>Jumlah</th>
                        <th>Tanggal</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $payments = mysqli_query($conn, "SELECT p.*, t.full_name, r.room_number 
                        FROM payments p 
                        JOIN bookings b ON p.booking_id = b.id 
                        JOIN tenants t ON b.tenant_id = t.id 
                        JOIN rooms r ON b.room_id = r.id 
                        ORDER BY p.payment_date DESC");
                    $no = 1;
                    while ($payment = mysqli_fetch_assoc($payments)):
                        $badgeClass = $payment['status'] == 'Paid' ? 'success' : ($payment['status'] == 'Pending' ? 'warning' : 'danger');
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong><?= htmlspecialchars($payment['receipt_number']) ?></strong></td>
                        <td><?= htmlspecialchars($payment['full_name']) ?></td>
                        <td><?= htmlspecialchars($payment['room_number']) ?></td>
                        <td>Rp <?= number_format($payment['amount'], 0, ',', '.') ?></td>
                        <td><?= date('d/m/Y', strtotime($payment['payment_date'])) ?></td>
                        <td><?= $payment['payment_method'] ?></td>
                        <td><span class="badge bg-<?= $badgeClass ?>"><?= $payment['status'] ?></span></td>
                        <td>
                            <a href="?page=payments&action=edit&id=<?= $payment['id'] ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?page=payments&action=delete&id=<?= $payment['id'] ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Yakin ingin menghapus?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php
$action = $_GET['action'] ?? 'list';

// Handle Save
if ($action == 'save' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $tenant_id = sanitize($_POST['tenant_id']);
    $room_id = sanitize($_POST['room_id']);
    $check_in = sanitize($_POST['check_in']);
    $check_out = sanitize($_POST['check_out']);
    $duration = sanitize($_POST['duration']);
    $total_price = sanitize($_POST['total_price']);
    $status = sanitize($_POST['status']);
    $notes = sanitize($_POST['notes']);
    
    if ($id) {
        // Get old room_id to update status
        $old_booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT room_id, status FROM bookings WHERE id=$id"));
        
        $query = "UPDATE bookings SET tenant_id='$tenant_id', room_id='$room_id', check_in='$check_in', 
                  check_out='$check_out', duration='$duration', total_price='$total_price', status='$status', 
                  notes='$notes' WHERE id=$id";
        
        if (mysqli_query($conn, $query)) {
            // Update room status if needed
            if ($status == 'Confirmed' || $status == 'Checked In') {
                mysqli_query($conn, "UPDATE rooms SET status='Occupied' WHERE id=$room_id");
            } elseif ($status == 'Checked Out' || $status == 'Cancelled') {
                mysqli_query($conn, "UPDATE rooms SET status='Available' WHERE id=$room_id");
            }
            
            showAlert('Data booking berhasil diupdate!');
        }
    } else {
        $query = "INSERT INTO bookings (tenant_id, room_id, check_in, check_out, duration, total_price, status, notes) 
                  VALUES ('$tenant_id', '$room_id', '$check_in', '$check_out', '$duration', '$total_price', '$status', '$notes')";
        
        if (mysqli_query($conn, $query)) {
            $booking_id = mysqli_insert_id($conn);
            
            // Update room status
            if ($status == 'Confirmed' || $status == 'Checked In') {
                mysqli_query($conn, "UPDATE rooms SET status='Occupied' WHERE id=$room_id");
            }
            
            showAlert('Data booking berhasil ditambahkan!');
        }
    }
    redirect(BASE_URL . '?page=bookings');
}

// Handle Delete
if ($action == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM bookings WHERE id=$id"));
    
    if (mysqli_query($conn, "DELETE FROM bookings WHERE id=$id")) {
        // Update room status back to Available
        mysqli_query($conn, "UPDATE rooms SET status='Available' WHERE id=" . $booking['room_id']);
        showAlert('Data booking berhasil dihapus!');
    }
    redirect(BASE_URL . '?page=bookings');
}

$edit_data = null;
if ($action == 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM bookings WHERE id=$id");
    $edit_data = mysqli_fetch_assoc($result);
}
?>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-calendar-check me-2"></i> Data Booking</h4>
        <?php if ($action != 'form' && $action != 'edit'): ?>
            <a href="?page=bookings&action=form" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Booking
            </a>
        <?php else: ?>
            <a href="?page=bookings" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        <?php endif; ?>
    </div>

    <?php if ($action == 'form' || $action == 'edit'): ?>
        <form method="POST" action="?page=bookings&action=save">
            <?php if ($edit_data): ?>
                <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Penyewa <span class="text-danger">*</span></label>
                    <select name="tenant_id" class="form-select" required>
                        <option value="">Pilih Penyewa</option>
                        <?php
                        $tenants = mysqli_query($conn, "SELECT * FROM tenants ORDER BY full_name");
                        while ($t = mysqli_fetch_assoc($tenants)):
                            $selected = ($edit_data['tenant_id'] ?? '') == $t['id'] ? 'selected' : '';
                        ?>
                            <option value="<?= $t['id'] ?>" <?= $selected ?>><?= htmlspecialchars($t['full_name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kamar <span class="text-danger">*</span></label>
                    <select name="room_id" class="form-select" required>
                        <option value="">Pilih Kamar</option>
                        <?php
                        // Show all rooms, but highlight available ones
                        $rooms = mysqli_query($conn, "SELECT * FROM rooms ORDER BY room_number");
                        while ($r = mysqli_fetch_assoc($rooms)):
                            $selected = ($edit_data['room_id'] ?? '') == $r['id'] ? 'selected' : '';
                            $disabled = ($r['status'] != 'Available' && $r['id'] != ($edit_data['room_id'] ?? 0)) ? 'disabled' : '';
                            $statusText = $r['status'] == 'Available' ? '' : " - ($r[status])";
                        ?>
                            <option value="<?= $r['id'] ?>" <?= $selected ?> <?= $disabled ?>>
                                <?= htmlspecialchars($r['room_number']) ?> - <?= $r['room_name'] ?> (Rp <?= number_format($r['price']) ?>)<?= $statusText ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <small class="text-muted">Hanya kamar Available yang bisa dipilih</small>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Check In <span class="text-danger">*</span></label>
                    <input type="date" name="check_in" class="form-control" 
                           value="<?= $edit_data['check_in'] ?? date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Check Out</label>
                    <input type="date" name="check_out" class="form-control" 
                           value="<?= $edit_data['check_out'] ?? '' ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Durasi (Bulan)</label>
                    <input type="number" name="duration" class="form-control" 
                           value="<?= $edit_data['duration'] ?? '1' ?>" min="1">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Total Harga (Rp)</label>
                    <input type="number" name="total_price" class="form-control" 
                           value="<?= $edit_data['total_price'] ?? '' ?>" id="total_price">
                    <small class="text-muted">Akan terisi otomatis jika kamar dipilih</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required id="status">
                        <option value="Pending" <?= ($edit_data['status'] ?? '') == 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Confirmed" <?= ($edit_data['status'] ?? '') == 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                        <option value="Checked In" <?= ($edit_data['status'] ?? '') == 'Checked In' ? 'selected' : '' ?>>Checked In</option>
                        <option value="Checked Out" <?= ($edit_data['status'] ?? '') == 'Checked Out' ? 'selected' : '' ?>>Checked Out</option>
                        <option value="Cancelled" <?= ($edit_data['status'] ?? '') == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
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
        
        <script>
        // Auto-calculate total price when room is selected
        document.querySelector('select[name="room_id"]').addEventListener('change', function() {
            const roomId = this.value;
            const duration = document.querySelector('select[name="duration"]').value || 1;
            
            if (roomId) {
                // Fetch room price
                fetch('?page=bookings&action=get_room_price&id=' + roomId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.price) {
                            const totalPrice = data.price * duration;
                            document.getElementById('total_price').value = totalPrice;
                        }
                    });
            }
        });
        
        // Auto-calculate when duration changes
        document.querySelector('input[name="duration"]').addEventListener('input', function() {
            const roomId = document.querySelector('select[name="room_id"]').value;
            const duration = this.value || 1;
            
            if (roomId) {
                fetch('?page=bookings&action=get_room_price&id=' + roomId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.price) {
                            const totalPrice = data.price * duration;
                            document.getElementById('total_price').value = totalPrice;
                        }
                    });
            }
        });
        </script>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Penyewa</th>
                        <th>Kamar</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $bookings = mysqli_query($conn, "SELECT b.*, t.full_name, r.room_number, r.room_name, r.price 
                        FROM bookings b 
                        JOIN tenants t ON b.tenant_id = t.id 
                        JOIN rooms r ON b.room_id = r.id 
                        ORDER BY b.created_at DESC");
                    $no = 1;
                    while ($booking = mysqli_fetch_assoc($bookings)):
                        $badgeClass = match($booking['status']) {
                            'Confirmed', 'Checked In' => 'success',
                            'Pending' => 'warning',
                            'Checked Out' => 'info',
                            'Cancelled' => 'danger',
                            default => 'secondary'
                        };
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong><?= htmlspecialchars($booking['full_name']) ?></strong></td>
                        <td><?= htmlspecialchars($booking['room_number']) ?> - <?= htmlspecialchars($booking['room_name']) ?></td>
                        <td><?= date('d/m/Y', strtotime($booking['check_in'])) ?></td>
                        <td><?= $booking['check_out'] ? date('d/m/Y', strtotime($booking['check_out'])) : '-' ?></td>
                        <td><span class="badge bg-<?= $badgeClass ?>"><?= $booking['status'] ?></span></td>
                        <td>Rp <?= number_format($booking['total_price'], 0, ',', '.') ?></td>
                        <td>
                            <!-- TOMBOL EDIT -->
                            <a href="?page=bookings&action=edit&id=<?= $booking['id'] ?>" 
                               class="btn btn-sm btn-warning" 
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <!-- TOMBOL HAPUS -->
                            <a href="?page=bookings&action=delete&id=<?= $booking['id'] ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Yakin ingin menghapus?')"
                               title="Hapus">
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
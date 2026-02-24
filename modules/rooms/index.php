<?php
$action = $_GET['action'] ?? 'list';

// Handle Save
if ($action == 'save' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $room_number = sanitize($_POST['room_number']);
    $room_name = sanitize($_POST['room_name']);
    $type = sanitize($_POST['type']);
    $price = sanitize($_POST['price']);
    $status = sanitize($_POST['status']);
    $description = sanitize($_POST['description']);
    $facilities = sanitize($_POST['facilities']);
    
    if ($id) {
        $query = "UPDATE rooms SET room_number='$room_number', room_name='$room_name', type='$type', 
                  price='$price', status='$status', description='$description', facilities='$facilities' 
                  WHERE id=$id";
        showAlert('Data kamar berhasil diupdate!');
    } else {
        $query = "INSERT INTO rooms (room_number, room_name, type, price, status, description, facilities) 
                  VALUES ('$room_number', '$room_name', '$type', '$price', '$status', '$description', '$facilities')";
        showAlert('Data kamar berhasil ditambahkan!');
    }
    mysqli_query($conn, $query);
    redirect(BASE_URL . '?page=rooms');
}

// Handle Delete
if ($action == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    mysqli_query($conn, "DELETE FROM rooms WHERE id=$id");
    showAlert('Data kamar berhasil dihapus!');
    redirect(BASE_URL . '?page=rooms');
}

$edit_data = null;
if ($action == 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM rooms WHERE id=$id");
    $edit_data = mysqli_fetch_assoc($result);
}
?>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-door-open me-2"></i> Data Kamar</h4>
        <?php if ($action != 'form' && $action != 'edit'): ?>
            <a href="?page=rooms&action=form" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Kamar
            </a>
        <?php else: ?>
            <a href="?page=rooms" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        <?php endif; ?>
    </div>

    <?php if ($action == 'form' || $action == 'edit'): ?>
        <form method="POST" action="?page=rooms&action=save">
            <?php if ($edit_data): ?>
                <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nomor Kamar</label>
                    <input type="text" name="room_number" class="form-control" 
                           value="<?= $edit_data['room_number'] ?? '' ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Kamar</label>
                    <input type="text" name="room_name" class="form-control" 
                           value="<?= $edit_data['room_name'] ?? '' ?>" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tipe</label>
                    <select name="type" class="form-select" required>
                        <option value="Single" <?= ($edit_data['type'] ?? '') == 'Single' ? 'selected' : '' ?>>Single</option>
                        <option value="Double" <?= ($edit_data['type'] ?? '') == 'Double' ? 'selected' : '' ?>>Double</option>
                        <option value="Suite" <?= ($edit_data['type'] ?? '') == 'Suite' ? 'selected' : '' ?>>Suite</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Harga per Bulan (Rp)</label>
                    <input type="number" name="price" class="form-control" 
                           value="<?= $edit_data['price'] ?? '' ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="Available" <?= ($edit_data['status'] ?? '') == 'Available' ? 'selected' : '' ?>>Tersedia</option>
                        <option value="Occupied" <?= ($edit_data['status'] ?? '') == 'Occupied' ? 'selected' : '' ?>>Terisi</option>
                        <option value="Maintenance" <?= ($edit_data['status'] ?? '') == 'Maintenance' ? 'selected' : '' ?>>Maintenance</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-control" rows="3"><?= $edit_data['description'] ?? '' ?></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Fasilitas (pisahkan dengan koma)</label>
                <textarea name="facilities" class="form-control" rows="2" 
                          placeholder="AC, WiFi, Kamar Mandi Dalam,"><?= $edit_data['facilities'] ?? '' ?></textarea>
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
                        <th>No. Kamar</th>
                        <th>Nama</th>
                        <th>Tipe</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rooms = mysqli_query($conn, "SELECT * FROM rooms ORDER BY room_number");
                    $no = 1;
                    while ($room = mysqli_fetch_assoc($rooms)):
                        $badgeClass = $room['status'] == 'Available' ? 'success' : ($room['status'] == 'Occupied' ? 'danger' : 'warning');
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong><?= htmlspecialchars($room['room_number']) ?></strong></td>
                        <td><?= htmlspecialchars($room['room_name']) ?></td>
                        <td><?= $room['type'] ?></td>
                        <td>Rp <?= number_format($room['price'], 0, ',', '.') ?></td>
                        <td><span class="badge bg-<?= $badgeClass ?>"><?= $room['status'] ?></span></td>
                        <td>
                            <a href="?page=rooms&action=edit&id=<?= $room['id'] ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?page=rooms&action=delete&id=<?= $room['id'] ?>" 
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
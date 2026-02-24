<?php
$action = $_GET['action'] ?? 'list';

// Handle Save
if ($action == 'save' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $id_card = sanitize($_POST['id_card']);
    $address = sanitize($_POST['address']);
    $emergency_contact = sanitize($_POST['emergency_contact']);
    $emergency_name = sanitize($_POST['emergency_name']);
    
    if ($id) {
        $query = "UPDATE tenants SET full_name='$full_name', email='$email', phone='$phone', 
                  id_card='$id_card', address='$address', emergency_contact='$emergency_contact', 
                  emergency_name='$emergency_name' WHERE id=$id";
        showAlert('Data penyewa berhasil diupdate!');
    } else {
        $query = "INSERT INTO tenants (full_name, email, phone, id_card, address, emergency_contact, emergency_name) 
                  VALUES ('$full_name', '$email', '$phone', '$id_card', '$address', '$emergency_contact', '$emergency_name')";
        showAlert('Data penyewa berhasil ditambahkan!');
    }
    mysqli_query($conn, $query);
    redirect(BASE_URL . '?page=tenants');
}

// Handle Delete
if ($action == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    mysqli_query($conn, "DELETE FROM tenants WHERE id=$id");
    showAlert('Data penyewa berhasil dihapus!');
    redirect(BASE_URL . '?page=tenants');
}

$edit_data = null;
if ($action == 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM tenants WHERE id=$id");
    $edit_data = mysqli_fetch_assoc($result);
}
?>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-users me-2"></i> Data Penyewa</h4>
        <?php if ($action != 'form' && $action != 'edit'): ?>
            <a href="?page=tenants&action=form" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Penyewa
            </a>
        <?php else: ?>
            <a href="?page=tenants" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        <?php endif; ?>
    </div>

    <?php if ($action == 'form' || $action == 'edit'): ?>
        <form method="POST" action="?page=tenants&action=save">
            <?php if ($edit_data): ?>
                <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="full_name" class="form-control" 
                           value="<?= $edit_data['full_name'] ?? '' ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" 
                           value="<?= $edit_data['email'] ?? '' ?>" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">No. Telepon <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control" 
                           value="<?= $edit_data['phone'] ?? '' ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">No. KTP/ID Card <span class="text-danger">*</span></label>
                    <input type="text" name="id_card" class="form-control" 
                           value="<?= $edit_data['id_card'] ?? '' ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Kontak Darurat</label>
                    <input type="text" name="emergency_contact" class="form-control" 
                           value="<?= $edit_data['emergency_contact'] ?? '' ?>">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Nama Kontak Darurat</label>
                <input type="text" name="emergency_name" class="form-control" 
                       value="<?= $edit_data['emergency_name'] ?? '' ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                <textarea name="address" class="form-control" rows="3" required><?= $edit_data['address'] ?? '' ?></textarea>
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
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>No. KTP</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $tenants = mysqli_query($conn, "SELECT * FROM tenants ORDER BY created_at DESC");
                    $no = 1;
                    while ($tenant = mysqli_fetch_assoc($tenants)):
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong><?= htmlspecialchars($tenant['full_name']) ?></strong></td>
                        <td><?= htmlspecialchars($tenant['email']) ?></td>
                        <td><?= htmlspecialchars($tenant['phone']) ?></td>
                        <td><?= htmlspecialchars($tenant['id_card']) ?></td>
                        <td>
                            <a href="?page=tenants&action=edit&id=<?= $tenant['id'] ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?page=tenants&action=delete&id=<?= $tenant['id'] ?>" 
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
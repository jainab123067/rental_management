<?php
$action = $_GET['action'] ?? 'list';

// Handle Save
if ($action == 'save' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $tenant_id = sanitize($_POST['tenant_id']);
    $room_id = sanitize($_POST['room_id']);
    $subject = sanitize($_POST['subject']);
    $description = sanitize($_POST['description']);
    $priority = sanitize($_POST['priority']);
    $status = sanitize($_POST['status']);
    $response = sanitize($_POST['response']);
    
    if ($id) {
        $resolved_at = ($status == 'Resolved' || $status == 'Closed') ? date('Y-m-d H:i:s') : 'NULL';
        $query = "UPDATE complaints SET tenant_id='$tenant_id', room_id='$room_id', subject='$subject', 
                  description='$description', priority='$priority', status='$status', response='$response',
                  resolved_at=" . ($resolved_at == 'NULL' ? 'NULL' : "'$resolved_at'") . " WHERE id=$id";
        showAlert('Data komplain berhasil diupdate!');
    } else {
        $query = "INSERT INTO complaints (tenant_id, room_id, subject, description, priority, status) 
                  VALUES ('$tenant_id', '$room_id', '$subject', '$description', '$priority', '$status')";
        showAlert('Data komplain berhasil ditambahkan!');
    }
    mysqli_query($conn, $query);
    redirect(BASE_URL . '?page=complaints');
}

// Handle Delete
if ($action == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    mysqli_query($conn, "DELETE FROM complaints WHERE id=$id");
    showAlert('Data komplain berhasil dihapus!');
    redirect(BASE_URL . '?page=complaints');
}

$edit_data = null;
if ($action == 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM complaints WHERE id=$id");
    $edit_data = mysqli_fetch_assoc($result);
}
?>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i> Data Komplain</h4>
        <?php if ($action != 'form' && $action != 'edit'): ?>
            <a href="?page=complaints&action=form" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Komplain
            </a>
        <?php else: ?>
            <a href="?page=complaints" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        <?php endif; ?>
    </div>

    <?php if ($action == 'form' || $action == 'edit'): ?>
        <form method="POST" action="?page=complaints&action=save">
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
                    <label class="form-label">Kamar</label>
                    <select name="room_id" class="form-select">
                        <option value="">Pilih Kamar (Opsional)</option>
                        <?php
                        $rooms = mysqli_query($conn, "SELECT * FROM rooms ORDER BY room_number");
                        while ($r = mysqli_fetch_assoc($rooms)):
                            $selected = ($edit_data['room_id'] ?? '') == $r['id'] ? 'selected' : '';
                        ?>
                            <option value="<?= $r['id'] ?>" <?= $selected ?>><?= htmlspecialchars($r['room_number']) ?> - <?= $r['room_name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Subjek <span class="text-danger">*</span></label>
                <input type="text" name="subject" class="form-control" 
                       value="<?= $edit_data['subject'] ?? '' ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                <textarea name="description" class="form-control" rows="4" required><?= $edit_data['description'] ?? '' ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Prioritas</label>
                    <select name="priority" class="form-select">
                        <option value="Low" <?= ($edit_data['priority'] ?? '') == 'Low' ? 'selected' : '' ?>>Rendah</option>
                        <option value="Medium" <?= ($edit_data['priority'] ?? '') == 'Medium' ? 'selected' : '' ?>>Sedang</option>
                        <option value="High" <?= ($edit_data['priority'] ?? '') == 'High' ? 'selected' : '' ?>>Tinggi</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="Open" <?= ($edit_data['status'] ?? '') == 'Open' ? 'selected' : '' ?>>Open</option>
                        <option value="In Progress" <?= ($edit_data['status'] ?? '') == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="Resolved" <?= ($edit_data['status'] ?? '') == 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                        <option value="Closed" <?= ($edit_data['status'] ?? '') == 'Closed' ? 'selected' : '' ?>>Closed</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Respon/Tindak Lanjut</label>
                <textarea name="response" class="form-control" rows="3"><?= $edit_data['response'] ?? '' ?></textarea>
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
                        <th>Subjek</th>
                        <th>Penyewa</th>
                        <th>Kamar</th>
                        <th>Prioritas</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $complaints = mysqli_query($conn, "SELECT c.*, t.full_name, r.room_number 
                        FROM complaints c 
                        JOIN tenants t ON c.tenant_id = t.id 
                        LEFT JOIN rooms r ON c.room_id = r.id 
                        ORDER BY c.created_at DESC");
                    $no = 1;
                    while ($c = mysqli_fetch_assoc($complaints)):
                        $priorityClass = $c['priority'] == 'High' ? 'danger' : ($c['priority'] == 'Medium' ? 'warning' : 'info');
                        $statusClass = match($c['status']) {
                            'Open' => 'secondary',
                            'In Progress' => 'primary',
                            'Resolved' => 'success',
                            'Closed' => 'dark',
                            default => 'secondary'
                        };
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong><?= htmlspecialchars($c['subject']) ?></strong></td>
                        <td><?= htmlspecialchars($c['full_name']) ?></td>
                        <td><?= $c['room_number'] ?? '-' ?></td>
                        <td><span class="badge bg-<?= $priorityClass ?>"><?= $c['priority'] ?></span></td>
                        <td><span class="badge bg-<?= $statusClass ?>"><?= $c['status'] ?></span></td>
                        <td><?= date('d/m/Y', strtotime($c['created_at'])) ?></td>
                        <td>
                            <a href="?page=complaints&action=edit&id=<?= $c['id'] ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?page=complaints&action=delete&id=<?= $c['id'] ?>" 
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
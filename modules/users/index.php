<?php
$action = $_GET['action'] ?? 'list';

// Handle Save
if ($action == 'save' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $full_name = sanitize($_POST['full_name']);
    $role = sanitize($_POST['role']);
    $password = $_POST['password'];
    
    if ($id) {
        if (!empty($password)) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $query = "UPDATE users SET username='$username', email='$email', full_name='$full_name', 
                      role='$role', password='$password_hash' WHERE id=$id";
        } else {
            $query = "UPDATE users SET username='$username', email='$email', full_name='$full_name', 
                      role='$role' WHERE id=$id";
        }
        showAlert('Data user berhasil diupdate!');
    } else {
        if (empty($password)) {
            showAlert('Password wajib diisi untuk user baru!', 'danger');
            redirect(BASE_URL . '?page=users&action=form');
        }
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (username, email, password, full_name, role) 
                  VALUES ('$username', '$email', '$password_hash', '$full_name', '$role')";
        showAlert('Data user berhasil ditambahkan!');
    }
    mysqli_query($conn, $query);
    redirect(BASE_URL . '?page=users');
}

// Handle Delete
if ($action == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Prevent deleting yourself
    if ($id == $_SESSION['user_id']) {
        showAlert('Tidak dapat menghapus user yang sedang login!', 'danger');
        redirect(BASE_URL . '?page=users');
    }
    
    mysqli_query($conn, "DELETE FROM users WHERE id=$id");
    showAlert('Data user berhasil dihapus!');
    redirect(BASE_URL . '?page=users');
}

$edit_data = null;
if ($action == 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE id=$id");
    $edit_data = mysqli_fetch_assoc($result);
}
?>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-user-cog me-2"></i> Manajemen User</h4>
        <?php if ($action != 'form' && $action != 'edit'): ?>
            <a href="?page=users&action=form" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah User
            </a>
        <?php else: ?>
            <a href="?page=users" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        <?php endif; ?>
    </div>

    <?php if ($action == 'form' || $action == 'edit'): ?>
        <form method="POST" action="?page=users&action=save">
            <?php if ($edit_data): ?>
                <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control" 
                           value="<?= $edit_data['username'] ?? '' ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" 
                           value="<?= $edit_data['email'] ?? '' ?>" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="full_name" class="form-control" 
                       value="<?= $edit_data['full_name'] ?? '' ?>" required>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="staff" <?= ($edit_data['role'] ?? '') == 'staff' ? 'selected' : '' ?>>Staff</option>
                        <option value="admin" <?= ($edit_data['role'] ?? '') == 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Password <?= !$edit_data ? '<span class="text-danger">*</span>' : '(kosongkan jika tidak ingin mengubah)' ?></label>
                    <input type="password" name="password" class="form-control" 
                           <?= !$edit_data ? 'required' : '' ?>>
                </div>
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
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Terdaftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $users = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
                    $no = 1;
                    while ($user = mysqli_fetch_assoc($users)):
                        $roleClass = $user['role'] == 'admin' ? 'danger' : 'primary';
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong><?= htmlspecialchars($user['username']) ?></strong></td>
                        <td><?= htmlspecialchars($user['full_name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><span class="badge bg-<?= $roleClass ?>"><?= ucfirst($user['role']) ?></span></td>
                        <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <a href="?page=users&action=edit&id=<?= $user['id'] ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <a href="?page=users&action=delete&id=<?= $user['id'] ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Yakin ingin menghapus?')">
                                <i class="fas fa-trash"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
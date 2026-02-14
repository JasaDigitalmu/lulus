<?php
require_once 'header.php';

// Handle Delete
if (isset($_GET['delete'])) {
    // Prevent deleting self? Use JS confirmation or check ID.
    if ($_GET['delete'] == $_SESSION['user_id']) {
        echo "<script>alert('Anda tidak bisa menghapus akun anda sendiri sedang login!'); window.location='users.php';</script>";
    } else {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt->execute([$_GET['delete']])) {
            echo "<script>alert('User berhasil dihapus!'); window.location='users.php';</script>";
        }
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf_token();
    
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Password Validation Function
    function validate_password($pwd) {
        if (strlen($pwd) < 8) return "Password minimal 8 karakter!";
        if (!preg_match('/[A-Z]/', $pwd)) return "Password harus mengandung huruf besar!";
        if (!preg_match('/[a-z]/', $pwd)) return "Password harus mengandung huruf kecil!";
        if (!preg_match('/[0-9]/', $pwd)) return "Password harus mengandung angka!";
        return true;
    }
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Edit
        $id = $_POST['id'];
        $updateData = [$name, $username];
        $sql = "UPDATE users SET name=?, username=?";

        if (!empty($password)) {
            $valid = validate_password($password);
            if ($valid !== true) {
                echo "<script>alert('$valid'); window.location='users.php';</script>";
                exit;
            }
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql .= ", password=?";
            $updateData[] = $hashed_password;
        }

        $sql .= " WHERE id=?";
        $updateData[] = $id;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($updateData);
        echo "<script>alert('Data user diperbarui!'); window.location='users.php';</script>";
    } else {
        // Add
        if (!empty($password)) {
            $valid = validate_password($password);
            if ($valid !== true) {
                 echo "<script>alert('$valid'); window.location='users.php';</script>";
                 exit;
            }

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, username, password) VALUES (?, ?, ?)");
            try {
                $stmt->execute([$name, $username, $hashed_password]);
                echo "<script>alert('User baru ditambahkan!'); window.location='users.php';</script>";
            } catch (PDOException $e) {
                 echo "<script>alert('Gagal! Username mungkin sudah ada.');</script>";
            }
        } else {
            echo "<script>alert('Password wajib diisi untuk user baru!');</script>";
        }
    }
}

$users = $pdo->query("SELECT * FROM users ORDER BY name")->fetchAll();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manajemen User</h1>
    <div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick="resetForm()">
            <i class="bi bi-person-plus"></i> Tambah User
        </button>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark text-center">
            <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>Username</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $index => $user): ?>
            <tr>
                <td class="text-center"><?= $index + 1 ?></td>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td class="text-center">
                    <button class="btn btn-sm btn-warning" 
                        data-id="<?= $user['id'] ?>"
                        data-name="<?= htmlspecialchars($user['name']) ?>"
                        data-username="<?= htmlspecialchars($user['username']) ?>"
                        onclick="editUser(this)">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                    <form method="POST" action="users.php?delete=<?= $user['id'] ?>" class="d-inline" onsubmit="return confirm('Hapus user ini?')">
                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="userForm" onsubmit="return validateForm()">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" id="userId">
                    <div class="mb-3">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" id="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
                        <div class="form-text">Minimal 8 karakter, huruf besar, huruf kecil, dan angka.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editUser(button) {
    document.getElementById('userId').value = button.getAttribute('data-id');
    document.getElementById('name').value = button.getAttribute('data-name');
    document.getElementById('username').value = button.getAttribute('data-username');
    document.getElementById('password').value = ''; 
    document.getElementById('password').placeholder = "Kosongkan jika tidak ingin mengubah password";
    
    var mb = new bootstrap.Modal(document.getElementById('userModal'));
    mb.show();
}

function resetForm() {
    document.getElementById('userId').value = '';
    document.getElementById('userForm').reset();
    document.getElementById('password').placeholder = "Wajib diisi untuk user baru";
    // userModal.show(); // Already triggered by button
}

function validateForm() {
    var password = document.getElementById('password').value;
    var userId = document.getElementById('userId').value;
    
    // If Editing and password empty, skip check
    if (userId && password === '') {
        return true;
    }

    // Validation Logic
    if (password.length < 8) {
        alert("Password minimal 8 karakter!");
        return false;
    }
    if (!/[A-Z]/.test(password)) {
        alert("Password harus mengandung huruf besar!");
        return false;
    }
    if (!/[a-z]/.test(password)) {
        alert("Password harus mengandung huruf kecil!");
        return false;
    }
    if (!/[0-9]/.test(password)) {
        alert("Password harus mengandung angka!");
        return false;
    }
    
    return true;
}
</script>

<?php require_once 'footer.php'; ?>

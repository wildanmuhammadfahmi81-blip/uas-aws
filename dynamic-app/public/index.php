<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_login();

$user = current_user();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($title === '') {
            $error = 'Judul wajib diisi.';
        } else {
            $stmt = db()->prepare('INSERT INTO notes (title, description, created_by) VALUES (?, ?, ?)');
            $userId = (int) $user['id'];
            $stmt->bind_param('ssi', $title, $description, $userId);
            $stmt->execute();
            $message = 'Data berhasil ditambahkan.';
        }
    }

    if ($action === 'update') {
        $id = (int) ($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($id <= 0 || $title === '') {
            $error = 'Data tidak valid.';
        } else {
            $stmt = db()->prepare('UPDATE notes SET title = ?, description = ? WHERE id = ?');
            $stmt->bind_param('ssi', $title, $description, $id);
            $stmt->execute();
            $message = 'Data berhasil diperbarui.';
        }
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id > 0) {
            $stmt = db()->prepare('DELETE FROM notes WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $message = 'Data berhasil dihapus.';
        }
    }
}

$editing = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = db()->prepare('SELECT id, title, description FROM notes WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $editing = $stmt->get_result()->fetch_assoc();
}

$notes = db()->query(
    'SELECT notes.id, notes.title, notes.description, notes.created_at, users.name AS author
     FROM notes
     LEFT JOIN users ON users.id = notes.created_by
     ORDER BY notes.id DESC'
);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <main class="shell">
        <header class="topbar">
            <div>
                <p class="eyebrow">UAS Administrasi Server Wildan Muhammad Fahmi-2388010030</p>
                <h1><?= e(APP_NAME) ?></h1>
            </div>
            <div class="account">
                <span><?= e($user['name']) ?></span>
                <a href="logout.php">Logout</a>
            </div>
        </header>

        <?php if ($message !== ''): ?>
            <div class="alert success"><?= e($message) ?></div>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <div class="alert error"><?= e($error) ?></div>
        <?php endif; ?>

        <section class="workspace">
            <form method="post" class="panel">
                <h2><?= $editing ? 'Edit Data' : 'Tambah Data' ?></h2>
                <input type="hidden" name="action" value="<?= $editing ? 'update' : 'create' ?>">
                <?php if ($editing): ?>
                    <input type="hidden" name="id" value="<?= (int) $editing['id'] ?>">
                <?php endif; ?>

                <label>
                    Judul
                    <input name="title" required value="<?= e($editing['title'] ?? '') ?>" placeholder="Contoh: Checklist deployment">
                </label>

                <label>
                    Deskripsi
                    <textarea name="description" rows="5" placeholder="Tulis detail data"><?= e($editing['description'] ?? '') ?></textarea>
                </label>

                <div class="actions">
                    <button type="submit"><?= $editing ? 'Simpan Perubahan' : 'Tambah Data' ?></button>
                    <?php if ($editing): ?>
                        <a class="secondary" href="index.php">Batal</a>
                    <?php endif; ?>
                </div>
            </form>

            <section class="panel table-panel">
                <h2>Data CRUD</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Deskripsi</th>
                                <th>Dibuat Oleh</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $notes->fetch_assoc()): ?>
                                <tr>
                                    <td><?= e($row['title']) ?></td>
                                    <td><?= e($row['description'] ?? '') ?></td>
                                    <td><?= e($row['author'] ?? '-') ?></td>
                                    <td><?= e($row['created_at']) ?></td>
                                    <td class="row-actions">
                                        <a href="?edit=<?= (int) $row['id'] ?>">Edit</a>
                                        <form method="post" onsubmit="return confirm('Hapus data ini?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                            <button type="submit">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </section>
    </main>
</body>
</html>

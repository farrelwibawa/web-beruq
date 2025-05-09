<?php
require_once 'Database.php';
require_once 'Phone.php';

$database = new Database();
$db = $database->getConnection();
$phone = new Phone($db);

// Ambil data phone
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $phone->aiphone_id = $_GET['id'];
    $stmt = $phone->readSingle();
    $phoneData = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$phoneData) {
        die("Phone tidak ditemukan!");
    }
} else {
    die("ID phone tidak valid!");
}

// Proses update
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $phone->aiphone_id = $_POST['aiphone_id'];
    $phone->Name = htmlspecialchars(trim($_POST['Name']));
    $phone->Price = floatval($_POST['Price']);
    $phone->Storage = $_POST['Storage'];
    $phone->Specification = htmlspecialchars(trim($_POST['Specification']));

    if (isset($_FILES['Image']) && $_FILES['Image']['error'] == 0) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $targetFile = $targetDir . uniqid() . '_' . basename($_FILES['Image']['name']);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $maxFileSize = 5 * 1024 * 1024;

        $check = getimagesize($_FILES['Image']['tmp_name']);
        if ($check !== false) {
            if ($_FILES['Image']['size'] <= $maxFileSize && in_array($imageFileType, $allowedTypes)) {
                if (move_uploaded_file($_FILES['Image']['tmp_name'], $targetFile)) {
                    $phone->ImageURL = $targetFile;
                } else {
                    echo "Gagal mengunggah file gambar.";
                }
            } else {
                echo "Ukuran gambar terlalu besar atau tipe file tidak diperbolehkan.";
            }
        } else {
            echo "File yang diunggah bukan gambar.";
        }
    } else {
        $phone->ImageURL = $phoneData['ImageURL'];
    }

    if ($phone->update()) {
        header("Location: index.php");
        exit;
    } else {
        echo "Gagal memperbarui data.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-content">
                <a href="index.php" class="navbar-brand">
                    <img src="assets\image\logo.png" alt="Logo" class="navbar-logo">
                    <span>AiPhone Manager</span>
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <form class="form" method="post" enctype="multipart/form-data">
            <h2>Edit Produk</h2>
            <input type="hidden" name="aiphone_id" value="<?= $phoneData['aiphone_id']; ?>">
            <input type="text" name="Name" value="<?= htmlspecialchars($phoneData['Name']); ?>" placeholder="Nama Barang" required>
            <input type="number" name="Price" value="<?= $phoneData['Price']; ?>" placeholder="Harga" required>
            <select name="Storage" required>
                <option value="">Pilih Kapasitas</option>
                <option value="128" <?= ($phoneData['Storage'] == '128') ? 'selected' : ''; ?>>128 GB</option>
                <option value="256" <?= ($phoneData['Storage'] == '256') ? 'selected' : ''; ?>>256 GB</option>
                <option value="512" <?= ($phoneData['Storage'] == '512') ? 'selected' : ''; ?>>512 GB</option>
            </select>
            <input type="text" name="Specification" value="<?= htmlspecialchars($phoneData['Specification']); ?>" placeholder="Spesifikasi" required>
            <input type="file" name="Image" accept="image/*">
            <button type="submit" name="action" value="update">Simpan Perubahan</button>
        </form>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>© <?php echo date('Y'); ?> Kelompok 8 - XI SIJA 1. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
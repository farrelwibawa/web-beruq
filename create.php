<?php
require_once 'Database.php';
require_once 'Phone.php';

$database = new Database();
$db = $database->getConnection();
$phone = new Phone($db);

// Ensure uploads directory exists
$targetDir = "uploads/";
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

// Handle form submission
if (isset($_POST['action']) && $_POST['action'] == 'create') {
    $phone->Name = htmlspecialchars(trim($_POST['Name']));
    $phone->Price = floatval($_POST['Price']);
    $phone->Storage = htmlspecialchars(trim($_POST['Storage']));
    $phone->Specification = htmlspecialchars(trim($_POST['Specification']));

    // Handle image upload
    if (isset($_FILES['Image']) && $_FILES['Image']['error'] == 0) {
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        $targetFile = $targetDir . uniqid() . '_' . basename($_FILES['Image']['name']);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Verify if the file is an image
        $check = getimagesize($_FILES['Image']['tmp_name']);
        if ($check !== false) {
            // Check file size and type
            if ($_FILES['Image']['size'] <= $maxFileSize && in_array($imageFileType, $allowedTypes)) {
                if (move_uploaded_file($_FILES['Image']['tmp_name'], $targetFile)) {
                    $phone->ImageURL = $targetFile;
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            } else {
                echo "File is too large or invalid file type.";
            }
        } else {
            echo "The file is not an image.";
        }
    } else {
        $phone->ImageURL = '';
    }

    if ($phone->create()) {
        header("Location: index.php");
        exit;
    } else {
        echo "Unable to create product.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambahkan Produk</title>
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
            <h2>Tambahkan Produk</h2>
            <input type="text" name="Name" placeholder="Nama Barang" required>
            <input type="number" name="Price" placeholder="Harga" required>
            <select name="Storage" required>
                <option value="">Pilih Kapasitas</option>
                <option value="128">128 GB</option>
                <option value="256">256 GB</option>
            </select>
            <input type="text" name="Specification" placeholder="Spesifikasi" required>
            <input type="file" name="Image" accept="image/*" required>
            <button type="submit" name="action" value="create">Tambahkan</button>
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
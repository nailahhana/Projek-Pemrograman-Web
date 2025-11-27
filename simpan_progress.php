<?php
session_start();
include 'koneksi.php';

// Cek apakah form di-submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Ambil email dari session
    $email = $_SESSION['email'] ?? 'user@example.com'; // Sesuaikan dengan sistem login kamu
    
    $tanggal = $_POST['tanggal'];
    $berat = $_POST['berat'];
    $catatan = $_POST['catatan'] ?? '';
    
    // Ambil goal dari tabel pengguna
    $query_user = "SELECT goal, plan_id FROM pengguna WHERE email = ?";
    $stmt = $conn->prepare($query_user);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user_data = $stmt->get_result()->fetch_assoc();
    $goal = $user_data['goal'];
    $plan_id = $user_data['plan_id'];
    
    // Handle upload foto olahraga
    $foto_olahraga = '';
    if (isset($_FILES['foto_olahraga']) && $_FILES['foto_olahraga']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $imageFileType = strtolower(pathinfo($_FILES["foto_olahraga"]["name"], PATHINFO_EXTENSION));
        
        if (in_array($imageFileType, $allowed)) {
            $foto_olahraga = $target_dir . uniqid() . '_olahraga.' . $imageFileType;
            move_uploaded_file($_FILES["foto_olahraga"]["tmp_name"], $foto_olahraga);
        }
    }
    
    // Handle upload foto makanan
    $foto_makanan = '';
    if (isset($_FILES['foto_makanan']) && $_FILES['foto_makanan']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $imageFileType = strtolower(pathinfo($_FILES["foto_makanan"]["name"], PATHINFO_EXTENSION));
        
        if (in_array($imageFileType, $allowed)) {
            $foto_makanan = $target_dir . uniqid() . '_makanan.' . $imageFileType;
            move_uploaded_file($_FILES["foto_makanan"]["tmp_name"], $foto_makanan);
        }
    }
    
    // Simpan ke tabel progres
    $query = "INSERT INTO progres (email, plan_id, tanggal, goal, catatan, foto_olahraga, foto_makanan, berat, created_at) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
              ON DUPLICATE KEY UPDATE 
              catatan = VALUES(catatan), 
              foto_olahraga = VALUES(foto_olahraga), 
              foto_makanan = VALUES(foto_makanan),
              berat = VALUES(berat)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sisssssd", $email, $plan_id, $tanggal, $goal, $catatan, $foto_olahraga, $foto_makanan, $berat);
    
    if ($stmt->execute()) {
        // Redirect kembali ke halaman progress dengan pesan sukses
        header("Location: progres.php?success=1");
        exit();
    } else {
        // Redirect dengan pesan error
        header("Location: progres.php?error=1");
        exit();
    }
    
    $stmt->close();
    $conn->close();
} else {
    // Kalau akses langsung tanpa POST, redirect ke progres.php
    header("Location: progres.php");
    exit();
}
?>
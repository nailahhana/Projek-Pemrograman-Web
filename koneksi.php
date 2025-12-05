<?php
// koneksi.php - File koneksi database

// Konfigurasi database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'dietly');

// Koneksi ke database
$koneksi = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset UTF-8
mysqli_set_charset($koneksi, "utf8mb4");

// Fungsi untuk membersihkan input
function bersihkan_input($data) {
    global $koneksi;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = mysqli_real_escape_string($koneksi, $data);
    return $data;
}

// Fungsi untuk redirect
function redirect($url) {
    header("Location: $url");
    exit();
}

// Fungsi untuk set pesan session
function set_pesan($tipe, $isi) {
    $_SESSION['pesan_tipe'] = $tipe; // success, error, warning, info
    $_SESSION['pesan_isi'] = $isi;
}

// Fungsi untuk get dan hapus pesan session
function get_pesan() {
    if (isset($_SESSION['pesan_tipe']) && isset($_SESSION['pesan_isi'])) {
        $pesan = array(
            'tipe' => $_SESSION['pesan_tipe'],
            'isi' => $_SESSION['pesan_isi']
        );
        unset($_SESSION['pesan_tipe']);
        unset($_SESSION['pesan_isi']);
        return $pesan;
    }
    return null;
}

// Fungsi untuk format tanggal Indonesia
function format_tanggal($tanggal) {
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    $pecah = explode('-', date('Y-m-d', strtotime($tanggal)));
    return $pecah[2] . ' ' . $bulan[(int)$pecah[1]] . ' ' . $pecah[0];
}

// Fungsi untuk upload file
function upload_foto($file, $folder = 'uploads/') {
    $nama_file = $file['name'];
    $ukuran_file = $file['size'];
    $error = $file['error'];
    $tmp_name = $file['tmp_name'];
    
    // Cek apakah ada error
    if ($error === 4) {
        return array('status' => false, 'pesan' => 'Tidak ada file yang diupload');
    }
    
    // Cek ekstensi file
    $ekstensi_valid = array('jpg', 'jpeg', 'png', 'gif');
    $ekstensi_file = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
    
    if (!in_array($ekstensi_file, $ekstensi_valid)) {
        return array('status' => false, 'pesan' => 'Format file tidak valid. Hanya JPG, JPEG, PNG, GIF yang diperbolehkan');
    }
    
    // Cek ukuran file (max 5MB)
    if ($ukuran_file > 5000000) {
        return array('status' => false, 'pesan' => 'Ukuran file terlalu besar. Maksimal 5MB');
    }
    
    // Generate nama file baru
    $nama_file_baru = uniqid() . '_' . time() . '.' . $ekstensi_file;
    
    // Pastikan folder ada
    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }
    
    // Upload file
    if (move_uploaded_file($tmp_name, $folder . $nama_file_baru)) {
        return array('status' => true, 'nama_file' => $nama_file_baru, 'path' => $folder . $nama_file_baru);
    } else {
        return array('status' => false, 'pesan' => 'Gagal mengupload file');
    }
}

// Fungsi untuk hapus file
function hapus_foto($path) {
    if (file_exists($path)) {
        return unlink($path);
    }
    return false;
}

// Start session jika belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
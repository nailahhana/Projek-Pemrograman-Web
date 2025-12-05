<?php
// auth.php - Handler untuk autentikasi (NO PASSWORD HASHING)
require_once 'koneksi.php';

// Cek apakah ada request POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $aksi = isset($_POST['aksi']) ? $_POST['aksi'] : '';
    
    // REGISTER - Daftar Akun Baru
    if ($aksi == 'register') {
        $email = bersihkan_input($_POST['email']);
        $kata_sandi = bersihkan_input($_POST['kata_sandi']);
        
        // Validasi email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            set_pesan('error', 'Format email tidak valid!');
            redirect('index.php#auth');
        }
        
        // Cek apakah email sudah terdaftar
        $cek_email = mysqli_query($koneksi, "SELECT * FROM pengguna WHERE email = '$email'");
        if (mysqli_num_rows($cek_email) > 0) {
            set_pesan('error', 'Email sudah terdaftar!');
            redirect('index.php#auth');
        }
        
        // Cek apakah kuisioner sudah dilengkapi (dari session)
        if (!isset($_SESSION['data_kuisioner'])) {
            set_pesan('warning', 'Mohon lengkapi kuisioner profil terlebih dahulu!');
            redirect('splash.php');
        }
        
        // Ambil data dari session kuisioner
        $data_kuisioner = $_SESSION['data_kuisioner'];
        
        // Insert ke database (PASSWORD PLAIN TEXT - TIDAK DI-HASH)
        $query = "INSERT INTO pengguna (nama_lengkap, email, kata_sandi, usia, jenis_kelamin, 
                  tinggi_badan, berat_badan, berat_target, bmi, level_aktivitas, tujuan_diet, lokasi) 
                  VALUES (
                      '{$data_kuisioner['nama']}',
                      '$email',
                      '$kata_sandi',
                      '{$data_kuisioner['usia']}',
                      '{$data_kuisioner['jenis_kelamin']}',
                      '{$data_kuisioner['tinggi_badan']}',
                      '{$data_kuisioner['berat_badan']}',
                      '{$data_kuisioner['berat_target']}',
                      '{$data_kuisioner['bmi']}',
                      '{$data_kuisioner['level_aktivitas']}',
                      '{$data_kuisioner['tujuan_diet']}',
                      '{$data_kuisioner['lokasi']}'
                  )";
        
        if (mysqli_query($koneksi, $query)) {
            $id_pengguna = mysqli_insert_id($koneksi);
            
            // Set session login
            $_SESSION['sudah_login'] = true;
            $_SESSION['id_pengguna'] = $id_pengguna;
            $_SESSION['nama_pengguna'] = $data_kuisioner['nama'];
            $_SESSION['email_pengguna'] = $email;
            $_SESSION['tipe_pengguna'] = 'pengguna';
            
            // Hapus data kuisioner dari session
            unset($_SESSION['data_kuisioner']);
            
            // Set cookie remember me (30 hari)
            if (isset($_POST['ingat_saya'])) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/');
                setcookie('id_pengguna', $id_pengguna, time() + (30 * 24 * 60 * 60), '/');
            }
            
            set_pesan('success', 'Akun berhasil dibuat! Selamat datang di Dietly!');
            redirect('index.php');
        } else {
            set_pesan('error', 'Gagal membuat akun: ' . mysqli_error($koneksi));
            redirect('index.php#auth');
        }
    }
    
    // LOGIN - Masuk ke Akun (PASSWORD PLAIN TEXT)
    elseif ($aksi == 'login') {
        $email = bersihkan_input($_POST['email']);
        $kata_sandi = bersihkan_input($_POST['kata_sandi']);
        
        // Cari pengguna dengan password plain text
        $query = "SELECT * FROM pengguna WHERE email = '$email' AND kata_sandi = '$kata_sandi'";
        $result = mysqli_query($koneksi, $query);
        
        if (mysqli_num_rows($result) == 1) {
            $pengguna = mysqli_fetch_assoc($result);
            
            // Set session
            $_SESSION['sudah_login'] = true;
            $_SESSION['id_pengguna'] = $pengguna['id_pengguna'];
            $_SESSION['nama_pengguna'] = $pengguna['nama_lengkap'];
            $_SESSION['email_pengguna'] = $pengguna['email'];
            $_SESSION['tipe_pengguna'] = 'pengguna';
            
            // Set cookie remember me jika dicentang
            if (isset($_POST['ingat_saya'])) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/');
                setcookie('id_pengguna', $pengguna['id_pengguna'], time() + (30 * 24 * 60 * 60), '/');
            }
            
            set_pesan('success', 'Login berhasil! Selamat datang kembali!');
            redirect('index.php');
        } else {
            set_pesan('error', 'Email atau password salah!');
            redirect('index.php#auth');
        }
    }
    
    // LOGOUT - Keluar dari Akun
    elseif ($aksi == 'logout') {
        // Hapus semua session
        session_unset();
        session_destroy();
        
        // Hapus cookie
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }
        if (isset($_COOKIE['id_pengguna'])) {
            setcookie('id_pengguna', '', time() - 3600, '/');
        }
        
        set_pesan('success', 'Logout berhasil! Sampai jumpa lagi!');
        redirect('index.php');
    }
    
    // LOGIN ADMIN (PASSWORD PLAIT)
    elseif ($aksi == 'login_admin') {
        $email = bersihkan_input($_POST['email']);
        $kata_sandi = bersihkan_input($_POST['kata_sandi']);
        
        // Cari admin dengan password plain text
        $query = "SELECT * FROM admin WHERE email = '$email' AND kata_sandi = '$kata_sandi'";
        $result = mysqli_query($koneksi, $query);
        
        if (mysqli_num_rows($result) == 1) {
            $admin = mysqli_fetch_assoc($result);
            
            // Set session admin
            $_SESSION['sudah_login'] = true;
            $_SESSION['id_admin'] = $admin['id_admin'];
            $_SESSION['nama_admin'] = $admin['nama_admin'];
            $_SESSION['email'] = $admin['email'];
            $_SESSION['tipe_pengguna'] = 'admin';
            
            set_pesan('success', 'Login berhasil! Selamat datang, Admin!');
            redirect('admin-dashboard.php');
        } else {
            set_pesan('error', 'Email atau password salah!');
            redirect('admin-login.php');
        }
    }
}

// Cek auto login dari cookie
if (!isset($_SESSION['sudah_login']) && isset($_COOKIE['id_pengguna'])) {
    $id_pengguna = $_COOKIE['id_pengguna'];
    
    $query = "SELECT * FROM pengguna WHERE id_pengguna = '$id_pengguna'";
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $pengguna = mysqli_fetch_assoc($result);
        
        $_SESSION['sudah_login'] = true;
        $_SESSION['id_pengguna'] = $pengguna['id_pengguna'];
        $_SESSION['nama_pengguna'] = $pengguna['nama_lengkap'];
        $_SESSION['email_pengguna'] = $pengguna['email'];
        $_SESSION['tipe_pengguna'] = 'pengguna';
    }
}

// Fungsi cek apakah sudah login
function cek_login() {
    if (!isset($_SESSION['sudah_login']) || $_SESSION['sudah_login'] !== true) {
        set_pesan('warning', 'Silakan login terlebih dahulu!');
        redirect('index.php#auth');
    }
}

// Fungsi cek apakah admin
function cek_admin() {
    if (!isset($_SESSION['tipe_pengguna']) || $_SESSION['tipe_pengguna'] !== 'admin') {
        set_pesan('error', 'Akses ditolak! Anda harus login sebagai admin.');
        redirect('admin-login.php');
    }
}
?>
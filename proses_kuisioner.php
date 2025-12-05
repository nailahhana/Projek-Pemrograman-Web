<?php
// proses_kuisioner.php - Handler untuk menyimpan data kuisioner
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Ambil data dari POST
    $nama = bersihkan_input($_POST['nama']);
    $usia = (int) $_POST['usia'];
    $jenis_kelamin = bersihkan_input($_POST['jenis_kelamin']);
    $tinggi_badan = (float) $_POST['tinggi_badan'];
    $berat_badan = (float) $_POST['berat_badan'];
    $berat_target = (float) $_POST['berat_target'];
    $level_aktivitas = bersihkan_input($_POST['level_aktivitas']);
    $tujuan_diet = bersihkan_input($_POST['tujuan_diet']);
    $lokasi = bersihkan_input($_POST['lokasi']);
    
    // Hitung BMI
    $tinggi_meter = $tinggi_badan / 100;
    $bmi = $berat_badan / ($tinggi_meter * $tinggi_meter);
    $bmi = round($bmi, 2);
    
    // Simpan data ke session sementara (untuk proses registrasi)
    // PERBAIKAN: Nama variabel session harus konsisten
    $_SESSION['data_kuisioner'] = array(
        'nama' => $nama,
        'usia' => $usia,
        'jenis_kelamin' => $jenis_kelamin,
        'tinggi_badan' => $tinggi_badan,
        'berat_badan' => $berat_badan,
        'berat_target' => $berat_target,
        'bmi' => $bmi,
        'level_aktivitas' => $level_aktivitas,
        'tujuan_diet' => $tujuan_diet,
        'lokasi' => $lokasi
    );
    
    // Set cookie untuk data kuisioner (7 hari)
    setcookie('kuisioner_selesai', '1', time() + (7 * 24 * 60 * 60), '/');
    
    // Redirect ke halaman registrasi
    set_pesan('success', 'Profil berhasil dibuat! Silakan daftar untuk melanjutkan.');
    redirect('index.php#auth');
    
} else {
    redirect('splash.php');
}
?>
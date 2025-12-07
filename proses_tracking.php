<?php
// proses_tracking.php - Handler untuk tracking harian (FIXED)
require_once 'koneksi.php';
require_once 'auth.php';

// Cek login
cek_login();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $id_pengguna = $_SESSION['id_pengguna'];
    $tanggal_tracking = bersihkan_input($_POST['tanggal_tracking']);
    $berat_badan = (float) $_POST['berat_badan'];
    $catatan = bersihkan_input($_POST['catatan']);
    
    // Cek apakah sudah ada tracking untuk tanggal ini
    $cek_query = "SELECT * FROM tracking_harian WHERE id_pengguna = '$id_pengguna' AND tanggal_tracking = '$tanggal_tracking'";
    $cek_result = mysqli_query($koneksi, $cek_query);
    
    $foto_olahraga = '';
    $foto_sarapan = '';
    $foto_makan_siang = '';
    $foto_makan_malam = '';
    
    // Upload foto-foto
    if (isset($_FILES['foto_olahraga']) && $_FILES['foto_olahraga']['error'] != 4) {
        $upload = upload_foto($_FILES['foto_olahraga'], 'uploads/tracking/');
        if ($upload['status']) {
            $foto_olahraga = $upload['path'];
        }
    }
    
    if (isset($_FILES['foto_sarapan']) && $_FILES['foto_sarapan']['error'] != 4) {
        $upload = upload_foto($_FILES['foto_sarapan'], 'uploads/tracking/');
        if ($upload['status']) {
            $foto_sarapan = $upload['path'];
        }
    }
    
    if (isset($_FILES['foto_makan_siang']) && $_FILES['foto_makan_siang']['error'] != 4) {
        $upload = upload_foto($_FILES['foto_makan_siang'], 'uploads/tracking/');
        if ($upload['status']) {
            $foto_makan_siang = $upload['path'];
        }
    }
    
    if (isset($_FILES['foto_makan_malam']) && $_FILES['foto_makan_malam']['error'] != 4) {
        $upload = upload_foto($_FILES['foto_makan_malam'], 'uploads/tracking/');
        if ($upload['status']) {
            $foto_makan_malam = $upload['path'];
        }
    }
    
    // Tentukan status lengkap
    $status_lengkap = 'tidak_lengkap';
    if ($berat_badan > 0 && !empty($foto_olahraga) && !empty($foto_sarapan) && !empty($foto_makan_siang) && !empty($foto_makan_malam)) {
        $status_lengkap = 'lengkap';
    }
    
    if (mysqli_num_rows($cek_result) > 0) {
        // Update tracking yang sudah ada
        $tracking_lama = mysqli_fetch_assoc($cek_result);
        
        // Gunakan foto lama jika tidak upload baru
        if (empty($foto_olahraga)) $foto_olahraga = $tracking_lama['foto_olahraga'];
        if (empty($foto_sarapan)) $foto_sarapan = $tracking_lama['foto_sarapan'];
        if (empty($foto_makan_siang)) $foto_makan_siang = $tracking_lama['foto_makan_siang'];
        if (empty($foto_makan_malam)) $foto_makan_malam = $tracking_lama['foto_makan_malam'];
        
        $query = "UPDATE tracking_harian SET 
                  berat_badan = '$berat_badan',
                  foto_olahraga = '$foto_olahraga',
                  foto_sarapan = '$foto_sarapan',
                  foto_makan_siang = '$foto_makan_siang',
                  foto_makan_malam = '$foto_makan_malam',
                  catatan = '$catatan',
                  status_lengkap = '$status_lengkap'
                  WHERE id_pengguna = '$id_pengguna' AND tanggal_tracking = '$tanggal_tracking'";
        
        $pesan_sukses = 'Tracking berhasil diupdate!';
    } else {
        // Insert tracking baru
        $query = "INSERT INTO tracking_harian (id_pengguna, tanggal_tracking, berat_badan, 
                  foto_olahraga, foto_sarapan, foto_makan_siang, foto_makan_malam, catatan, status_lengkap)
                  VALUES ('$id_pengguna', '$tanggal_tracking', '$berat_badan', 
                  '$foto_olahraga', '$foto_sarapan', '$foto_makan_siang', '$foto_makan_malam', '$catatan', '$status_lengkap')";
        
        $pesan_sukses = 'Aktivitas berhasil disimpan!';
    }
    
    if (mysqli_query($koneksi, $query)) {
        // PERBAIKAN: Jangan update berat_badan di tabel pengguna
        // Berat awal tetap tersimpan, berat terkini diambil dari tracking_harian
        
        set_pesan('success', $pesan_sukses . ' Terus semangat! 💪');
    } else {
        set_pesan('error', 'Gagal menyimpan tracking: ' . mysqli_error($koneksi));
    }
    
    redirect('progres.php');
    
} else {
    redirect('progres.php');
}
?>
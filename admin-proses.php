<?php
require_once 'koneksi.php';
require_once 'auth.php';

cek_admin();

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

// ==================== USER MANAGEMENT ====================

// UPDATE USER
if ($action == 'update_user' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pengguna = (int)$_POST['id_pengguna'];
    $nama_lengkap = bersihkan_input($_POST['nama_lengkap']);
    $email = bersihkan_input($_POST['email']);
    $usia = (int)$_POST['usia'];
    $jenis_kelamin = bersihkan_input($_POST['jenis_kelamin']);
    $tinggi_badan = (float)$_POST['tinggi_badan'];
    $berat_badan = (float)$_POST['berat_badan'];
    $berat_target = (float)$_POST['berat_target'];
    $level_aktivitas = bersihkan_input($_POST['level_aktivitas']);
    $tujuan_diet = bersihkan_input($_POST['tujuan_diet']);
    $lokasi = bersihkan_input($_POST['lokasi']);
    
    // Hitung BMI
    $tinggi_meter = $tinggi_badan / 100;
    $bmi = $berat_badan / ($tinggi_meter * $tinggi_meter);
    $bmi = round($bmi, 2);
    
    // Update password jika diisi
    $password_update = '';
    if (!empty($_POST['kata_sandi_baru'])) {
        $kata_sandi_baru = bersihkan_input($_POST['kata_sandi_baru']);
        $password_update = ", kata_sandi = '$kata_sandi_baru'";
    }
    
    $query = "UPDATE pengguna SET 
              nama_lengkap = '$nama_lengkap',
              email = '$email',
              usia = '$usia',
              jenis_kelamin = '$jenis_kelamin',
              tinggi_badan = '$tinggi_badan',
              berat_badan = '$berat_badan',
              berat_target = '$berat_target',
              bmi = '$bmi',
              level_aktivitas = '$level_aktivitas',
              tujuan_diet = '$tujuan_diet',
              lokasi = '$lokasi'
              $password_update
              WHERE id_pengguna = '$id_pengguna'";
    
    if (mysqli_query($koneksi, $query)) {
        set_pesan('success', 'Data pengguna berhasil diupdate!');
    } else {
        set_pesan('error', 'Gagal update pengguna: ' . mysqli_error($koneksi));
    }
    
    redirect('admin-users.php');
}

// DELETE USER
elseif ($action == 'delete_user' && isset($_GET['id'])) {
    $id_pengguna = (int)$_GET['id'];
    
    // Hapus foto profil jika ada
    $query = "SELECT foto_profil FROM pengguna WHERE id_pengguna = '$id_pengguna'";
    $result = mysqli_query($koneksi, $query);
    $user = mysqli_fetch_assoc($result);
    
    if ($user && $user['foto_profil'] && file_exists($user['foto_profil'])) {
        hapus_foto($user['foto_profil']);
    }
    
    // Hapus semua foto tracking
    $query_tracking = "SELECT foto_olahraga, foto_sarapan, foto_makan_siang, foto_makan_malam 
                       FROM tracking_harian WHERE id_pengguna = '$id_pengguna'";
    $result_tracking = mysqli_query($koneksi, $query_tracking);
    
    while ($tracking = mysqli_fetch_assoc($result_tracking)) {
        if ($tracking['foto_olahraga'] && file_exists($tracking['foto_olahraga'])) {
            hapus_foto($tracking['foto_olahraga']);
        }
        if ($tracking['foto_sarapan'] && file_exists($tracking['foto_sarapan'])) {
            hapus_foto($tracking['foto_sarapan']);
        }
        if ($tracking['foto_makan_siang'] && file_exists($tracking['foto_makan_siang'])) {
            hapus_foto($tracking['foto_makan_siang']);
        }
        if ($tracking['foto_makan_malam'] && file_exists($tracking['foto_makan_malam'])) {
            hapus_foto($tracking['foto_makan_malam']);
        }
    }
    
    // Hapus user (CASCADE akan hapus data terkait)
    $query = "DELETE FROM pengguna WHERE id_pengguna = '$id_pengguna'";
    
    if (mysqli_query($koneksi, $query)) {
        set_pesan('success', 'Pengguna dan semua datanya berhasil dihapus!');
    } else {
        set_pesan('error', 'Gagal menghapus pengguna: ' . mysqli_error($koneksi));
    }
    
    redirect('admin-users.php');
}

// ==================== RESEP MANAGEMENT ====================

// UPDATE RESEP
elseif ($action == 'update_resep' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_resep = (int)$_POST['id_resep'];
    $nama_resep = bersihkan_input($_POST['nama_resep']);
    $id_kategori = (int)$_POST['id_kategori'];
    $deskripsi = bersihkan_input($_POST['deskripsi']);
    $waktu_masak = (int)$_POST['waktu_masak'];
    $porsi = (int)$_POST['porsi'];
    $kalori = (int)$_POST['kalori'];
    $protein = (int)$_POST['protein'];
    $karbohidrat = (int)$_POST['karbohidrat'];
    $lemak = (int)$_POST['lemak'];
    $bahan_bahan = bersihkan_input($_POST['bahan_bahan']);
    $cara_membuat = bersihkan_input($_POST['cara_membuat']);
    $tips = bersihkan_input($_POST['tips']);
    $tingkat_kesulitan = bersihkan_input($_POST['tingkat_kesulitan']);
    
    // Handle foto resep
    $foto_update = '';
    if (isset($_FILES['foto_resep']) && $_FILES['foto_resep']['error'] != 4) {
        // Hapus foto lama
        $query = "SELECT foto_resep FROM resep WHERE id_resep = '$id_resep'";
        $result = mysqli_query($koneksi, $query);
        $resep = mysqli_fetch_assoc($result);
        
        if ($resep && $resep['foto_resep'] && file_exists($resep['foto_resep'])) {
            hapus_foto($resep['foto_resep']);
        }
        
        // Upload foto baru
        $upload = upload_foto($_FILES['foto_resep'], 'uploads/resep/');
        if ($upload['status']) {
            $foto_update = ", foto_resep = '{$upload['path']}'";
        }
    }
    
    $query = "UPDATE resep SET 
              nama_resep = '$nama_resep',
              id_kategori = '$id_kategori',
              deskripsi = '$deskripsi',
              waktu_masak = '$waktu_masak',
              porsi = '$porsi',
              kalori = '$kalori',
              protein = '$protein',
              karbohidrat = '$karbohidrat',
              lemak = '$lemak',
              bahan_bahan = '$bahan_bahan',
              cara_membuat = '$cara_membuat',
              tips = '$tips',
              tingkat_kesulitan = '$tingkat_kesulitan'
              $foto_update
              WHERE id_resep = '$id_resep'";
    
    if (mysqli_query($koneksi, $query)) {
        set_pesan('success', 'Resep berhasil diupdate!');
    } else {
        set_pesan('error', 'Gagal update resep: ' . mysqli_error($koneksi));
    }
    
    redirect('admin-resep.php');
}

// ADD RESEP
elseif ($action == 'add_resep' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_resep = bersihkan_input($_POST['nama_resep']);
    $id_kategori = (int)$_POST['id_kategori'];
    $deskripsi = bersihkan_input($_POST['deskripsi']);
    $waktu_masak = (int)$_POST['waktu_masak'];
    $porsi = (int)$_POST['porsi'];
    $kalori = (int)$_POST['kalori'];
    $protein = (int)$_POST['protein'];
    $karbohidrat = (int)$_POST['karbohidrat'];
    $lemak = (int)$_POST['lemak'];
    $bahan_bahan = bersihkan_input($_POST['bahan_bahan']);
    $cara_membuat = bersihkan_input($_POST['cara_membuat']);
    $tips = bersihkan_input($_POST['tips']);
    $tingkat_kesulitan = bersihkan_input($_POST['tingkat_kesulitan']);
    
    $foto_resep = '';
    if (isset($_FILES['foto_resep']) && $_FILES['foto_resep']['error'] != 4) {
        $upload = upload_foto($_FILES['foto_resep'], 'uploads/resep/');
        if ($upload['status']) {
            $foto_resep = $upload['path'];
        }
    }
    
    $query = "INSERT INTO resep (nama_resep, id_kategori, deskripsi, foto_resep, waktu_masak, porsi, 
              kalori, protein, karbohidrat, lemak, bahan_bahan, cara_membuat, tips, tingkat_kesulitan)
              VALUES ('$nama_resep', '$id_kategori', '$deskripsi', '$foto_resep', '$waktu_masak', '$porsi',
              '$kalori', '$protein', '$karbohidrat', '$lemak', '$bahan_bahan', '$cara_membuat', '$tips', '$tingkat_kesulitan')";
    
    if (mysqli_query($koneksi, $query)) {
        set_pesan('success', 'Resep berhasil ditambahkan!');
    } else {
        set_pesan('error', 'Gagal menambah resep: ' . mysqli_error($koneksi));
    }
    
    redirect('admin-resep.php');
}

// DELETE RESEP
elseif ($action == 'delete_resep' && isset($_GET['id'])) {
    $id_resep = (int)$_GET['id'];
    
    // Hapus foto resep
    $query = "SELECT foto_resep FROM resep WHERE id_resep = '$id_resep'";
    $result = mysqli_query($koneksi, $query);
    $resep = mysqli_fetch_assoc($result);
    
    if ($resep && $resep['foto_resep'] && file_exists($resep['foto_resep'])) {
        hapus_foto($resep['foto_resep']);
    }
    
    // Hapus resep
    $query = "DELETE FROM resep WHERE id_resep = '$id_resep'";
    
    if (mysqli_query($koneksi, $query)) {
        set_pesan('success', 'Resep berhasil dihapus!');
    } else {
        set_pesan('error', 'Gagal menghapus resep: ' . mysqli_error($koneksi));
    }
    
    redirect('admin-resep.php');
}

// ==================== TRACKING MANAGEMENT ====================

// DELETE TRACKING
elseif ($action == 'delete_tracking' && isset($_GET['id'])) {
    $id_tracking = (int)$_GET['id'];
    
    // Hapus foto-foto tracking
    $query = "SELECT foto_olahraga, foto_sarapan, foto_makan_siang, foto_makan_malam 
              FROM tracking_harian WHERE id_tracking = '$id_tracking'";
    $result = mysqli_query($koneksi, $query);
    $tracking = mysqli_fetch_assoc($result);
    
    if ($tracking) {
        if ($tracking['foto_olahraga'] && file_exists($tracking['foto_olahraga'])) {
            hapus_foto($tracking['foto_olahraga']);
        }
        if ($tracking['foto_sarapan'] && file_exists($tracking['foto_sarapan'])) {
            hapus_foto($tracking['foto_sarapan']);
        }
        if ($tracking['foto_makan_siang'] && file_exists($tracking['foto_makan_siang'])) {
            hapus_foto($tracking['foto_makan_siang']);
        }
        if ($tracking['foto_makan_malam'] && file_exists($tracking['foto_makan_malam'])) {
            hapus_foto($tracking['foto_makan_malam']);
        }
    }
    
    // Hapus tracking
    $query = "DELETE FROM tracking_harian WHERE id_tracking = '$id_tracking'";
    
    if (mysqli_query($koneksi, $query)) {
        set_pesan('success', 'Data tracking berhasil dihapus!');
    } else {
        set_pesan('error', 'Gagal menghapus tracking: ' . mysqli_error($koneksi));
    }
    
    redirect('admin-tracking.php');
}

// ==================== TESTIMONI MANAGEMENT ====================

// TOGGLE TESTIMONI STATUS
elseif ($action == 'toggle_testimoni' && isset($_GET['id'])) {
    $id_testimoni = (int)$_GET['id'];
    
    $query = "UPDATE testimoni SET 
              status_tampil = IF(status_tampil = 'aktif', 'tidak_aktif', 'aktif')
              WHERE id_testimoni = '$id_testimoni'";
    
    if (mysqli_query($koneksi, $query)) {
        set_pesan('success', 'Status testimoni berhasil diubah!');
    } else {
        set_pesan('error', 'Gagal mengubah status: ' . mysqli_error($koneksi));
    }
    
    redirect('admin-testimoni.php');
}

// DELETE TESTIMONI
elseif ($action == 'delete_testimoni' && isset($_GET['id'])) {
    $id_testimoni = (int)$_GET['id'];
    
    $query = "DELETE FROM testimoni WHERE id_testimoni = '$id_testimoni'";
    
    if (mysqli_query($koneksi, $query)) {
        set_pesan('success', 'Testimoni berhasil dihapus!');
    } else {
        set_pesan('error', 'Gagal menghapus testimoni: ' . mysqli_error($koneksi));
    }
    
    redirect('admin-testimoni.php');
}

// ==================== FILE MANAGEMENT ====================

// DELETE FILE
elseif ($action == 'delete_file' && isset($_GET['path'])) {
    $file_path = $_GET['path'];
    
    if (file_exists($file_path)) {
        if (hapus_foto($file_path)) {
            set_pesan('success', 'File berhasil dihapus!');
        } else {
            set_pesan('error', 'Gagal menghapus file!');
        }
    } else {
        set_pesan('error', 'File tidak ditemukan!');
    }
    
    redirect('admin-files.php');
}

else {
    set_pesan('error', 'Aksi tidak valid!');
    redirect('admin-dashboard.php');
}
?>
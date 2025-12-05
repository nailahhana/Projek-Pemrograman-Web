<?php
// proses_profil.php - Handler untuk update profil pengguna
require_once 'koneksi.php';
require_once 'auth.php';

// Cek login
cek_login();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $aksi = isset($_POST['aksi']) ? $_POST['aksi'] : '';
    $id_pengguna = $_SESSION['id_pengguna'];
    
    // UPDATE NAMA
    if ($aksi == 'update_nama') {
        $nama_baru = bersihkan_input($_POST['nama_baru']);
        
        if (empty($nama_baru)) {
            set_pesan('error', 'Nama tidak boleh kosong!');
            redirect('profile.php');
        }
        
        $query = "UPDATE pengguna SET nama_lengkap = '$nama_baru' WHERE id_pengguna = '$id_pengguna'";
        
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['nama_pengguna'] = $nama_baru;
            set_pesan('success', 'Nama berhasil diupdate!');
        } else {
            set_pesan('error', 'Gagal mengupdate nama!');
        }
        
        redirect('profile.php');
    }
    
    // UPDATE FOTO PROFIL
    elseif ($aksi == 'update_foto') {
        if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] != 4) {
            
            // Ambil foto lama
            $query = "SELECT foto_profil FROM pengguna WHERE id_pengguna = '$id_pengguna'";
            $result = mysqli_query($koneksi, $query);
            $data = mysqli_fetch_assoc($result);
            $foto_lama = $data['foto_profil'];
            
            // Upload foto baru
            $upload = upload_foto($_FILES['foto_profil'], 'uploads/profil/');
            
            if ($upload['status']) {
                // Hapus foto lama jika ada
                if ($foto_lama && file_exists($foto_lama)) {
                    hapus_foto($foto_lama);
                }
                
                // Update database
                $path_foto = $upload['path'];
                $query = "UPDATE pengguna SET foto_profil = '$path_foto' WHERE id_pengguna = '$id_pengguna'";
                
                if (mysqli_query($koneksi, $query)) {
                    set_pesan('success', 'Foto profil berhasil diupdate!');
                } else {
                    set_pesan('error', 'Gagal menyimpan foto profil!');
                }
            } else {
                set_pesan('error', $upload['pesan']);
            }
        }
        
        redirect('profile.php');
    }
    
    // UPDATE DATA FISIK
    elseif ($aksi == 'update_data_fisik') {
        $tinggi_badan = (float) $_POST['tinggi_badan'];
        $berat_badan = (float) $_POST['berat_badan'];
        $berat_target = (float) $_POST['berat_target'];
        $tujuan_diet = bersihkan_input($_POST['tujuan_diet']);
        
        // Hitung ulang BMI
        $tinggi_meter = $tinggi_badan / 100;
        $bmi = $berat_badan / ($tinggi_meter * $tinggi_meter);
        $bmi = round($bmi, 2);
        
        $query = "UPDATE pengguna SET 
                  tinggi_badan = '$tinggi_badan',
                  berat_badan = '$berat_badan',
                  berat_target = '$berat_target',
                  bmi = '$bmi',
                  tujuan_diet = '$tujuan_diet'
                  WHERE id_pengguna = '$id_pengguna'";
        
        if (mysqli_query($koneksi, $query)) {
            set_pesan('success', 'Data fisik berhasil diupdate!');
        } else {
            set_pesan('error', 'Gagal mengupdate data fisik!');
        }
        
        redirect('profile.php');
    }
    
    // UPDATE LEVEL AKTIVITAS
    elseif ($aksi == 'update_aktivitas') {
        $level_aktivitas = bersihkan_input($_POST['level_aktivitas']);
        
        $query = "UPDATE pengguna SET level_aktivitas = '$level_aktivitas' WHERE id_pengguna = '$id_pengguna'";
        
        if (mysqli_query($koneksi, $query)) {
            set_pesan('success', 'Level aktivitas berhasil diupdate!');
        } else {
            set_pesan('error', 'Gagal mengupdate level aktivitas!');
        }
        
        redirect('profile.php');
    }
    
} else {
    redirect('profile.php');
}
?>
<?php
// proses_resep.php - Handler untuk aksi pada resep
require_once 'koneksi.php';
require_once 'auth.php';

// Cek login (hanya untuk POST request)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    cek_login();
    
    $aksi = isset($_POST['aksi']) ? $_POST['aksi'] : '';
    $id_pengguna = $_SESSION['id_pengguna'];
    
    // SIMPAN RESEP
    if ($aksi == 'simpan_resep') {
        $id_resep = (int) $_POST['id_resep'];
        
        // Cek apakah sudah disimpan sebelumnya
        $cek_query = "SELECT * FROM resep_tersimpan WHERE id_pengguna = '$id_pengguna' AND id_resep = '$id_resep'";
        $cek_result = mysqli_query($koneksi, $cek_query);
        
        if (mysqli_num_rows($cek_result) > 0) {
            set_pesan('info', 'Resep sudah disimpan sebelumnya!');
        } else {
            $query = "INSERT INTO resep_tersimpan (id_pengguna, id_resep) VALUES ('$id_pengguna', '$id_resep')";
            
            if (mysqli_query($koneksi, $query)) {
                set_pesan('success', 'Resep berhasil disimpan! ✅');
            } else {
                set_pesan('error', 'Gagal menyimpan resep!');
            }
        }
        
        // Redirect kembali
        $redirect_url = isset($_POST['redirect']) ? $_POST['redirect'] : 'resep.php';
        redirect($redirect_url);
    }
    
    // HAPUS RESEP TERSIMPAN
    elseif ($aksi == 'hapus_simpanan') {
        $id_resep = (int) $_POST['id_resep'];
        
        $query = "DELETE FROM resep_tersimpan WHERE id_pengguna = '$id_pengguna' AND id_resep = '$id_resep'";
        
        if (mysqli_query($koneksi, $query)) {
            set_pesan('success', 'Resep berhasil dihapus dari simpanan!');
        } else {
            set_pesan('error', 'Gagal menghapus resep!');
        }
        
        redirect('resep-tersimpan.php');
    }
    
    else {
        redirect('resep.php');
    }
}

// ============================================================================
// FUNGSI-FUNGSI HELPER UNTUK RESEP
// ============================================================================

/**
 * Get resep berdasarkan kategori
 * @param string|null $kategori - Nama kategori atau null untuk semua
 * @return array Array of resep
 */
function get_resep_kategori($kategori = null) {
    global $koneksi;
    
    if ($kategori && $kategori != 'semua') {
        $query = "SELECT r.*, k.nama_kategori 
                  FROM resep r 
                  LEFT JOIN kategori_resep k ON r.id_kategori = k.id_kategori 
                  WHERE k.nama_kategori = '$kategori'
                  ORDER BY r.tanggal_dibuat DESC";
    } else {
        $query = "SELECT r.*, k.nama_kategori 
                  FROM resep r 
                  LEFT JOIN kategori_resep k ON r.id_kategori = k.id_kategori 
                  ORDER BY r.tanggal_dibuat DESC";
    }
    
    $result = mysqli_query($koneksi, $query);
    $resep = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $resep[] = $row;
    }
    
    return $resep;
}

/**
 * Get detail resep berdasarkan ID
 * @param int $id_resep
 * @return array|null Data resep atau null jika tidak ditemukan
 */
function get_detail_resep($id_resep) {
    global $koneksi;
    
    $id_resep = (int) $id_resep;
    
    $query = "SELECT r.*, k.nama_kategori 
              FROM resep r 
              LEFT JOIN kategori_resep k ON r.id_kategori = k.id_kategori 
              WHERE r.id_resep = '$id_resep'";
    
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

/**
 * Cek apakah resep sudah disimpan oleh user
 * @param int $id_pengguna
 * @param int $id_resep
 * @return bool True jika sudah disimpan
 */
function is_resep_tersimpan($id_pengguna, $id_resep) {
    global $koneksi;
    
    $id_pengguna = (int) $id_pengguna;
    $id_resep = (int) $id_resep;
    
    $query = "SELECT * FROM resep_tersimpan 
              WHERE id_pengguna = '$id_pengguna' AND id_resep = '$id_resep'";
    $result = mysqli_query($koneksi, $query);
    
    return mysqli_num_rows($result) > 0;
}

/**
 * Get semua resep yang disimpan oleh user
 * @param int $id_pengguna
 * @return array Array of resep tersimpan
 */
function get_resep_tersimpan($id_pengguna) {
    global $koneksi;
    
    $id_pengguna = (int) $id_pengguna;
    
    $query = "SELECT r.*, k.nama_kategori, rs.tanggal_disimpan 
              FROM resep_tersimpan rs
              JOIN resep r ON rs.id_resep = r.id_resep
              LEFT JOIN kategori_resep k ON r.id_kategori = k.id_kategori
              WHERE rs.id_pengguna = '$id_pengguna'
              ORDER BY rs.tanggal_disimpan DESC";
    
    $result = mysqli_query($koneksi, $query);
    $resep = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $resep[] = $row;
    }
    
    return $resep;
}

/**
 * Get jumlah resep tersimpan oleh user
 * @param int $id_pengguna
 * @return int Jumlah resep tersimpan
 */
function get_jumlah_resep_tersimpan($id_pengguna) {
    global $koneksi;
    
    $id_pengguna = (int) $id_pengguna;
    
    $query = "SELECT COUNT(*) as jumlah FROM resep_tersimpan WHERE id_pengguna = '$id_pengguna'";
    $result = mysqli_query($koneksi, $query);
    $data = mysqli_fetch_assoc($result);
    
    return (int) $data['jumlah'];
}

/**
 * Get resep populer (paling banyak disimpan)
 * @param int $limit - Jumlah resep yang diambil
 * @return array Array of resep populer
 */
function get_resep_populer($limit = 6) {
    global $koneksi;
    
    $limit = (int) $limit;
    
    $query = "SELECT r.*, k.nama_kategori, COUNT(rs.id_simpan) as jumlah_simpan
              FROM resep r
              LEFT JOIN kategori_resep k ON r.id_kategori = k.id_kategori
              LEFT JOIN resep_tersimpan rs ON r.id_resep = rs.id_resep
              GROUP BY r.id_resep
              ORDER BY jumlah_simpan DESC, r.tanggal_dibuat DESC
              LIMIT $limit";
    
    $result = mysqli_query($koneksi, $query);
    $resep = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $resep[] = $row;
    }
    
    return $resep;
}

/**
 * Search resep berdasarkan keyword
 * @param string $keyword
 * @param string|null $kategori
 * @return array Array of resep
 */
function search_resep($keyword, $kategori = null) {
    global $koneksi;
    
    $keyword = bersihkan_input($keyword);
    
    $where = "WHERE (r.nama_resep LIKE '%$keyword%' OR r.deskripsi LIKE '%$keyword%')";
    
    if ($kategori && $kategori != 'semua') {
        $kategori = bersihkan_input($kategori);
        $where .= " AND k.nama_kategori = '$kategori'";
    }
    
    $query = "SELECT r.*, k.nama_kategori 
              FROM resep r 
              LEFT JOIN kategori_resep k ON r.id_kategori = k.id_kategori 
              $where
              ORDER BY r.tanggal_dibuat DESC";
    
    $result = mysqli_query($koneksi, $query);
    $resep = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $resep[] = $row;
    }
    
    return $resep;
}

/**
 * Get resep rekomendasi berdasarkan tujuan diet user
 * @param int $id_pengguna
 * @param int $limit
 * @return array Array of resep rekomendasi
 */
function get_resep_rekomendasi($id_pengguna, $limit = 6) {
    global $koneksi;
    
    $id_pengguna = (int) $id_pengguna;
    $limit = (int) $limit;
    
    // Get user's goal
    $query_user = "SELECT tujuan_diet FROM pengguna WHERE id_pengguna = '$id_pengguna'";
    $result_user = mysqli_query($koneksi, $query_user);
    $user = mysqli_fetch_assoc($result_user);
    
    if (!$user) {
        return get_resep_kategori(); // Return all if user not found
    }
    
    $tujuan = $user['tujuan_diet'];
    
    // Rekomendasi berdasarkan kalori
    $where_kalori = '';
    if ($tujuan == 'lose') {
        $where_kalori = 'r.kalori <= 400'; // Low calorie
    } elseif ($tujuan == 'gain') {
        $where_kalori = 'r.kalori >= 500'; // High calorie
    } else {
        $where_kalori = 'r.kalori BETWEEN 300 AND 500'; // Moderate
    }
    
    $query = "SELECT r.*, k.nama_kategori 
              FROM resep r 
              LEFT JOIN kategori_resep k ON r.id_kategori = k.id_kategori 
              WHERE $where_kalori
              ORDER BY RAND()
              LIMIT $limit";
    
    $result = mysqli_query($koneksi, $query);
    $resep = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $resep[] = $row;
    }
    
    return $resep;
}
?>
<?php
require_once 'koneksi.php';
require_once 'auth.php';

cek_admin();

// Get resep
$query = "SELECT r.*, k.nama_kategori FROM resep r 
          LEFT JOIN kategori_resep k ON r.id_kategori = k.id_kategori 
          ORDER BY r.tanggal_dibuat DESC";
$result = mysqli_query($koneksi, $query);

$pesan = get_pesan();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Resep - Admin Dietly</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50">

  <?php if ($pesan): ?>
  <div id="alertPesan" class="fixed top-4 right-4 z-50 bg-<?php echo $pesan['tipe'] == 'success' ? 'green' : 'red'; ?>-100 border border-<?php echo $pesan['tipe'] == 'success' ? 'green' : 'red'; ?>-400 text-<?php echo $pesan['tipe'] == 'success' ? 'green' : 'red'; ?>-700 px-4 py-3 rounded-lg shadow-lg max-w-md">
    <p class="font-bold"><?php echo $pesan['isi']; ?></p>
  </div>
  <script>setTimeout(() => document.getElementById('alertPesan').style.display = 'none', 5000);</script>
  <?php endif; ?>

  <div class="flex h-screen">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-white flex flex-col">
      <div class="p-6 border-b border-slate-800">
        <h1 class="text-2xl font-bold text-emerald-400">Dietly Admin</h1>
      </div>
      <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
        <a href="admin-dashboard.php" class="block px-4 py-3 hover:bg-slate-800 rounded-lg">📊 Dashboard</a>
        <a href="admin-users.php" class="block px-4 py-3 hover:bg-slate-800 rounded-lg">👥 Kelola Pengguna</a>
        <a href="admin-resep.php" class="block px-4 py-3 bg-emerald-600 rounded-lg font-medium">🍽️ Kelola Resep</a>
        <a href="admin-tracking.php" class="block px-4 py-3 hover:bg-slate-800 rounded-lg">📈 Tracking</a>
        <a href="admin-testimoni.php" class="block px-4 py-3 hover:bg-slate-800 rounded-lg">💬 Testimoni</a>
        <a href="admin-files.php" class="block px-4 py-3 hover:bg-slate-800 rounded-lg">📁 Files</a>
      </nav>
      <div class="p-4 border-t border-slate-800">
        <form method="POST" action="auth.php">
          <input type="hidden" name="aksi" value="logout">
          <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg text-sm font-medium">Logout</button>
        </form>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto">
      <header class="bg-white border-b px-8 py-4">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-bold text-slate-800">Kelola Resep</h2>
            <p class="text-sm text-slate-600">Manage recipe database</p>
          </div>
          <a href="admin-resep-add.php" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-medium">
            + Tambah Resep
          </a>
        </div>
      </header>

      <div class="p-8">
        
        <!-- Resep Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          
          <?php while ($resep = mysqli_fetch_assoc($result)): ?>
          <div class="bg-white rounded-xl shadow overflow-hidden">
            <?php if ($resep['foto_resep']): ?>
              <img src="<?php echo $resep['foto_resep']; ?>" alt="<?php echo $resep['nama_resep']; ?>" class="w-full h-48 object-cover">
            <?php else: ?>
              <div class="w-full h-48 bg-gradient-to-br from-emerald-100 to-emerald-200 flex items-center justify-center">
                <span class="text-6xl">🍽️</span>
              </div>
            <?php endif; ?>
            
            <div class="p-4">
              <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded text-xs font-medium">
                  <?php echo ucfirst($resep['nama_kategori']); ?>
                </span>
                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-medium">
                  <?php echo $resep['kalori']; ?> kcal
                </span>
              </div>
              
              <h3 class="font-bold text-slate-800 mb-2"><?php echo $resep['nama_resep']; ?></h3>
              <p class="text-sm text-slate-600 mb-3 line-clamp-2">
                <?php echo substr($resep['deskripsi'], 0, 100); ?>...
              </p>
              
              <div class="flex items-center gap-2 text-xs text-slate-500 mb-3">
                <span>⏱️ <?php echo $resep['waktu_masak']; ?> min</span>
                <span>•</span>
                <span>👥 <?php echo $resep['porsi']; ?> porsi</span>
              </div>
              
              <div class="flex gap-2">
                <a href="admin-resep-edit.php?id=<?php echo $resep['id_resep']; ?>" class="flex-1 px-3 py-2 bg-emerald-100 text-emerald-700 rounded text-center hover:bg-emerald-200 text-sm font-medium">
                  Edit
                </a>
                <button onclick="hapusResep(<?php echo $resep['id_resep']; ?>, '<?php echo $resep['nama_resep']; ?>')" class="flex-1 px-3 py-2 bg-red-100 text-red-700 rounded hover:bg-red-200 text-sm font-medium">
                  Hapus
                </button>
              </div>
            </div>
          </div>
          <?php endwhile; ?>

        </div>

        <?php if (mysqli_num_rows($result) == 0): ?>
        <div class="text-center py-16">
          <svg class="w-24 h-24 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
          </svg>
          <p class="text-slate-500 text-lg font-medium">Belum ada resep</p>
          <a href="admin-resep-add.php" class="inline-block mt-4 px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
            Tambah Resep Pertama
          </a>
        </div>
        <?php endif; ?>

      </div>
    </main>

  </div>

  <script>
    function hapusResep(id, nama) {
      if (confirm('Yakin ingin menghapus resep "' + nama + '"?\n\nSemua data bookmark resep ini juga akan terhapus!')) {
        window.location.href = 'admin-proses.php?action=delete_resep&id=' + id;
      }
    }
  </script>

</body>
</html>
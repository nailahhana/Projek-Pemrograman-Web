<?php
// admin-testimoni.php
require_once 'koneksi.php';
require_once 'auth.php';

cek_admin();

// Get testimoni dengan info pengguna
$query = "SELECT t.*, p.nama_lengkap, p.email, p.foto_profil
          FROM testimoni t
          JOIN pengguna p ON t.id_pengguna = p.id_pengguna
          ORDER BY t.tanggal_dibuat DESC";
$result = mysqli_query($koneksi, $query);

$pesan = get_pesan();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Testimoni - Admin Dietly</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50">

  <?php if ($pesan): ?>
  <div id="alertPesan" class="fixed top-4 right-4 z-50 bg-<?php echo $pesan['tipe'] == 'success' ? 'green' : 'red'; ?>-100 border border-<?php echo $pesan['tipe'] == 'success' ? 'green' : 'red'; ?>-400 text-<?php echo $pesan['tipe'] == 'success' ? 'green' : 'red'; ?>-700 px-4 py-3 rounded-lg shadow-lg">
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
        <a href="admin-users.php" class="block px-4 py-3 hover:bg-slate-800 rounded-lg">👥 Pengguna</a>
        <a href="admin-resep.php" class="block px-4 py-3 hover:bg-slate-800 rounded-lg">🍽️ Resep</a>
        <a href="admin-tracking.php" class="block px-4 py-3 hover:bg-slate-800 rounded-lg">📈 Tracking</a>
        <a href="admin-testimoni.php" class="block px-4 py-3 bg-emerald-600 rounded-lg font-medium">💬 Testimoni</a>
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
        <div>
          <h2 class="text-2xl font-bold text-slate-800">Kelola Testimoni</h2>
          <p class="text-sm text-slate-600">Manage user testimonials</p>
        </div>
      </header>

      <div class="p-8">
        
        <!-- Info Box -->
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl">
          <p class="text-sm text-blue-800">
            <strong>ℹ️ Info:</strong> Testimoni dengan status "Aktif" akan ditampilkan di halaman beranda. Klik toggle untuk mengubah status.
          </p>
        </div>

        <!-- Testimoni Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          
          <?php while ($testimoni = mysqli_fetch_assoc($result)): ?>
          <div class="bg-white rounded-xl shadow p-6 <?php echo $testimoni['status_tampil'] == 'aktif' ? 'border-2 border-emerald-500' : ''; ?>">
            
            <!-- Status Badge -->
            <div class="flex items-center justify-between mb-4">
              <?php if ($testimoni['status_tampil'] == 'aktif'): ?>
                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-medium">✓ Aktif</span>
              <?php else: ?>
                <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-medium">○ Tidak Aktif</span>
              <?php endif; ?>
              
              <span class="text-xs text-slate-400"><?php echo date('d/m/Y', strtotime($testimoni['tanggal_dibuat'])); ?></span>
            </div>

            <!-- User Info -->
            <div class="flex items-center gap-3 mb-4">
              <?php if ($testimoni['foto_profil']): ?>
                <img src="<?php echo $testimoni['foto_profil']; ?>" class="w-12 h-12 rounded-full object-cover" alt="<?php echo $testimoni['nama_lengkap']; ?>">
              <?php else: ?>
                <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center">
                  <span class="font-bold text-emerald-600 text-lg"><?php echo strtoupper(substr($testimoni['nama_lengkap'], 0, 1)); ?></span>
                </div>
              <?php endif; ?>
              <div>
                <p class="font-semibold text-slate-800"><?php echo $testimoni['nama_lengkap']; ?></p>
                <p class="text-xs text-slate-500"><?php echo $testimoni['email']; ?></p>
              </div>
            </div>

            <!-- Testimoni Content -->
            <p class="text-sm text-slate-700 mb-4 italic">"<?php echo $testimoni['isi_testimoni']; ?>"</p>

            <!-- Achievement -->
            <?php if ($testimoni['penurunan_berat']): ?>
            <div class="mb-4 p-3 bg-gradient-to-r from-emerald-50 to-blue-50 rounded-lg">
              <p class="text-xs text-slate-600">Pencapaian:</p>
              <p class="text-sm font-bold text-emerald-700">
                <?php echo $testimoni['penurunan_berat']; ?> kg dalam <?php echo $testimoni['durasi']; ?>
              </p>
            </div>
            <?php endif; ?>

            <!-- Actions -->
            <div class="flex gap-2">
              <button onclick="toggleStatus(<?php echo $testimoni['id_testimoni']; ?>)" class="flex-1 px-3 py-2 <?php echo $testimoni['status_tampil'] == 'aktif' ? 'bg-slate-100 text-slate-700' : 'bg-emerald-100 text-emerald-700'; ?> rounded hover:opacity-80 text-sm font-medium">
                <?php echo $testimoni['status_tampil'] == 'aktif' ? '○ Nonaktifkan' : '✓ Aktifkan'; ?>
              </button>
              <button onclick="hapusTestimoni(<?php echo $testimoni['id_testimoni']; ?>, '<?php echo $testimoni['nama_lengkap']; ?>')" class="px-3 py-2 bg-red-100 text-red-700 rounded hover:bg-red-200 text-sm font-medium">
                Hapus
              </button>
            </div>
          </div>
          <?php endwhile; ?>

        </div>

        <?php if (mysqli_num_rows($result) == 0): ?>
        <div class="text-center py-16">
          <svg class="w-24 h-24 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
          </svg>
          <p class="text-slate-500 text-lg font-medium">Belum ada testimoni</p>
        </div>
        <?php endif; ?>

      </div>
    </main>

  </div>

  <script>
    function toggleStatus(id) {
      window.location.href = 'admin-proses.php?action=toggle_testimoni&id=' + id;
    }

    function hapusTestimoni(id, nama) {
      if (confirm('Yakin ingin menghapus testimoni dari ' + nama + '?')) {
        window.location.href = 'admin-proses.php?action=delete_testimoni&id=' + id;
      }
    }
  </script>

</body>
</html>
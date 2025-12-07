<?php
// admin-tracking.php
require_once 'koneksi.php';
require_once 'auth.php';

cek_admin();

// Get tracking dengan info pengguna
$query = "SELECT t.*, p.nama_lengkap, p.email 
          FROM tracking_harian t
          JOIN pengguna p ON t.id_pengguna = p.id_pengguna
          ORDER BY t.tanggal_tracking DESC
          LIMIT 100";
$result = mysqli_query($koneksi, $query);

$pesan = get_pesan();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tracking Pengguna - Admin Dietly</title>
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
        <a href="admin-tracking.php" class="block px-4 py-3 bg-emerald-600 rounded-lg font-medium">📈 Tracking</a>
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
        <div>
          <h2 class="text-2xl font-bold text-slate-800">Tracking Pengguna</h2>
          <p class="text-sm text-slate-600">Monitor user activity logs</p>
        </div>
      </header>

      <div class="p-8">
        
        <!-- Tracking Table -->
        <div class="bg-white rounded-xl shadow overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-slate-50 border-b">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Pengguna</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tanggal</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Berat</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Foto</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-200">
                <?php while ($tracking = mysqli_fetch_assoc($result)): ?>
                <tr class="hover:bg-slate-50">
                  <td class="px-6 py-4">
                    <p class="font-medium text-slate-800"><?php echo $tracking['nama_lengkap']; ?></p>
                    <p class="text-xs text-slate-500"><?php echo $tracking['email']; ?></p>
                  </td>
                  <td class="px-6 py-4 text-sm"><?php echo date('d/m/Y', strtotime($tracking['tanggal_tracking'])); ?></td>
                  <td class="px-6 py-4">
                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-sm font-medium">
                      <?php echo $tracking['berat_badan']; ?> kg
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <?php if ($tracking['status_lengkap'] == 'lengkap'): ?>
                      <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium">Lengkap</span>
                    <?php else: ?>
                      <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-medium">Sebagian</span>
                    <?php endif; ?>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex gap-1">
                      <?php if ($tracking['foto_olahraga']): ?><span title="Olahraga">🏃</span><?php endif; ?>
                      <?php if ($tracking['foto_sarapan']): ?><span title="Sarapan">🍳</span><?php endif; ?>
                      <?php if ($tracking['foto_makan_siang']): ?><span title="Siang">🍱</span><?php endif; ?>
                      <?php if ($tracking['foto_makan_malam']): ?><span title="Malam">🍽️</span><?php endif; ?>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <button onclick="hapusTracking(<?php echo $tracking['id_tracking']; ?>)" class="px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 text-xs font-medium">
                      Hapus
                    </button>
                  </td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </main>

  </div>

  <script>
    function hapusTracking(id) {
      if (confirm('Yakin ingin menghapus data tracking ini?')) {
        window.location.href = 'admin-proses.php?action=delete_tracking&id=' + id;
      }
    }
  </script>

</body>
</html>
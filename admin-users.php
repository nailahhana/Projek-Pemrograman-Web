<?php
require_once 'koneksi.php';
require_once 'auth.php';

cek_admin();

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search
$search = isset($_GET['search']) ? bersihkan_input($_GET['search']) : '';
$where = $search ? "WHERE nama_lengkap LIKE '%$search%' OR email LIKE '%$search%'" : '';

// Get total users
$query_total = "SELECT COUNT(*) as total FROM pengguna $where";
$result_total = mysqli_query($koneksi, $query_total);
$total_users = mysqli_fetch_assoc($result_total)['total'];
$total_pages = ceil($total_users / $limit);

// Get users
$query = "SELECT * FROM pengguna $where ORDER BY tanggal_daftar DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($koneksi, $query);

$pesan = get_pesan();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Pengguna - Admin Dietly</title>
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
        <a href="admin-users.php" class="block px-4 py-3 bg-emerald-600 rounded-lg font-medium">👥 Kelola Pengguna</a>
        <a href="admin-resep.php" class="block px-4 py-3 hover:bg-slate-800 rounded-lg">🍽️ Kelola Resep</a>
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
            <h2 class="text-2xl font-bold text-slate-800">Kelola Pengguna</h2>
            <p class="text-sm text-slate-600">Manage user accounts</p>
          </div>
        </div>
      </header>

      <div class="p-8">
        
        <!-- Search & Filter -->
        <div class="bg-white rounded-xl shadow p-6 mb-6">
          <form method="GET" class="flex gap-4">
            <input type="text" name="search" value="<?php echo $search; ?>" placeholder="Cari nama atau email..." class="flex-1 px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
            <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Cari</button>
            <?php if ($search): ?>
            <a href="admin-users.php" class="px-6 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300">Reset</a>
            <?php endif; ?>
          </form>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-xl shadow overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-slate-50 border-b">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">ID</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Nama</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Email</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">BMI</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tujuan</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Bergabung</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-200">
                <?php while ($user = mysqli_fetch_assoc($result)): ?>
                <tr class="hover:bg-slate-50">
                  <td class="px-6 py-4 text-sm"><?php echo $user['id_pengguna']; ?></td>
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                        <span class="font-bold text-emerald-600"><?php echo strtoupper(substr($user['nama_lengkap'], 0, 1)); ?></span>
                      </div>
                      <div>
                        <p class="font-medium text-slate-800"><?php echo $user['nama_lengkap']; ?></p>
                        <p class="text-xs text-slate-500"><?php echo $user['usia']; ?> tahun</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 text-sm"><?php echo $user['email']; ?></td>
                  <td class="px-6 py-4">
                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-medium">
                      <?php echo number_format($user['bmi'], 1); ?>
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <?php
                    $tujuan_badge = array(
                      'lose' => 'bg-red-100 text-red-700',
                      'maintain' => 'bg-yellow-100 text-yellow-700',
                      'gain' => 'bg-green-100 text-green-700'
                    );
                    $tujuan_text = array('lose' => 'Turun', 'maintain' => 'Jaga', 'gain' => 'Naik');
                    ?>
                    <span class="px-2 py-1 <?php echo $tujuan_badge[$user['tujuan_diet']]; ?> rounded text-xs font-medium">
                      <?php echo $tujuan_text[$user['tujuan_diet']]; ?>
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-slate-600">
                    <?php echo date('d/m/Y', strtotime($user['tanggal_daftar'])); ?>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex gap-2">
                      <a href="admin-user-detail.php?id=<?php echo $user['id_pengguna']; ?>" class="px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 text-xs font-medium">
                        Detail
                      </a>
                      <a href="admin-user-edit.php?id=<?php echo $user['id_pengguna']; ?>" class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded hover:bg-emerald-200 text-xs font-medium">
                        Edit
                      </a>
                      <button onclick="hapusUser(<?php echo $user['id_pengguna']; ?>, '<?php echo $user['nama_lengkap']; ?>')" class="px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 text-xs font-medium">
                        Hapus
                      </button>
                    </div>
                  </td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <?php if ($total_pages > 1): ?>
          <div class="px-6 py-4 border-t flex items-center justify-between">
            <p class="text-sm text-slate-600">
              Menampilkan <?php echo $offset + 1; ?> - <?php echo min($offset + $limit, $total_users); ?> dari <?php echo $total_users; ?> pengguna
            </p>
            <div class="flex gap-2">
              <?php if ($page > 1): ?>
              <a href="?page=<?php echo $page - 1; ?>&search=<?php echo $search; ?>" class="px-3 py-1 border rounded hover:bg-slate-50">Previous</a>
              <?php endif; ?>
              
              <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i == $page): ?>
                  <span class="px-3 py-1 bg-emerald-600 text-white rounded"><?php echo $i; ?></span>
                <?php else: ?>
                  <a href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>" class="px-3 py-1 border rounded hover:bg-slate-50"><?php echo $i; ?></a>
                <?php endif; ?>
              <?php endfor; ?>
              
              <?php if ($page < $total_pages): ?>
              <a href="?page=<?php echo $page + 1; ?>&search=<?php echo $search; ?>" class="px-3 py-1 border rounded hover:bg-slate-50">Next</a>
              <?php endif; ?>
            </div>
          </div>
          <?php endif; ?>
        </div>

      </div>
    </main>

  </div>

  <script>
    function hapusUser(id, nama) {
      if (confirm('Yakin ingin menghapus pengguna "' + nama + '"?\n\nSemua data terkait (tracking, resep tersimpan, testimoni) juga akan terhapus!')) {
        window.location.href = 'admin-proses.php?action=delete_user&id=' + id;
      }
    }
  </script>

</body>
</html>
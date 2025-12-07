<?php
require_once 'koneksi.php';
require_once 'auth.php';

cek_admin();

$id_pengguna = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get user data
$query = "SELECT * FROM pengguna WHERE id_pengguna = '$id_pengguna'";
$result = mysqli_query($koneksi, $query);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    set_pesan('error', 'Pengguna tidak ditemukan!');
    redirect('admin-users.php');
}

$pesan = get_pesan();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Pengguna - Admin Dietly</title>
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
        <div class="flex items-center gap-4">
          <a href="admin-users.php" class="text-slate-600 hover:text-slate-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </a>
          <div>
            <h2 class="text-2xl font-bold text-slate-800">Edit Pengguna</h2>
            <p class="text-sm text-slate-600">Edit data pengguna <?php echo $user['nama_lengkap']; ?></p>
          </div>
        </div>
      </header>

      <div class="p-8 max-w-4xl">
        
        <!-- Edit Form -->
        <form method="POST" action="admin-proses.php" enctype="multipart/form-data" class="bg-white rounded-xl shadow p-6">
          <input type="hidden" name="action" value="update_user">
          <input type="hidden" name="id_pengguna" value="<?php echo $user['id_pengguna']; ?>">

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Nama Lengkap -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap</label>
              <input type="text" name="nama_lengkap" value="<?php echo $user['nama_lengkap']; ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500" required>
            </div>

            <!-- Email -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
              <input type="email" name="email" value="<?php echo $user['email']; ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500" required>
            </div>

            <!-- Password -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Password Baru (kosongkan jika tidak diubah)</label>
              <input type="password" name="kata_sandi_baru" placeholder="••••••••" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- Usia -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Usia</label>
              <input type="number" name="usia" value="<?php echo $user['usia']; ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500" required>
            </div>

            <!-- Jenis Kelamin -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Jenis Kelamin</label>
              <select name="jenis_kelamin" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500" required>
                <option value="laki-laki" <?php echo $user['jenis_kelamin'] == 'laki-laki' ? 'selected' : ''; ?>>Laki-laki</option>
                <option value="perempuan" <?php echo $user['jenis_kelamin'] == 'perempuan' ? 'selected' : ''; ?>>Perempuan</option>
                <option value="lainnya" <?php echo $user['jenis_kelamin'] == 'lainnya' ? 'selected' : ''; ?>>Lainnya</option>
              </select>
            </div>

            <!-- Tinggi Badan -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Tinggi Badan (cm)</label>
              <input type="number" name="tinggi_badan" value="<?php echo $user['tinggi_badan']; ?>" step="0.1" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500" required>
            </div>

            <!-- Berat Badan -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Berat Badan (kg)</label>
              <input type="number" name="berat_badan" value="<?php echo $user['berat_badan']; ?>" step="0.1" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500" required>
            </div>

            <!-- Berat Target -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Berat Target (kg)</label>
              <input type="number" name="berat_target" value="<?php echo $user['berat_target']; ?>" step="0.1" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500" required>
            </div>

            <!-- Level Aktivitas -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Level Aktivitas</label>
              <select name="level_aktivitas" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500" required>
                <option value="sedentary" <?php echo $user['level_aktivitas'] == 'sedentary' ? 'selected' : ''; ?>>Sedentary</option>
                <option value="light" <?php echo $user['level_aktivitas'] == 'light' ? 'selected' : ''; ?>>Light</option>
                <option value="moderate" <?php echo $user['level_aktivitas'] == 'moderate' ? 'selected' : ''; ?>>Moderate</option>
                <option value="active" <?php echo $user['level_aktivitas'] == 'active' ? 'selected' : ''; ?>>Active</option>
              </select>
            </div>

            <!-- Tujuan Diet -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Tujuan Diet</label>
              <select name="tujuan_diet" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500" required>
                <option value="lose" <?php echo $user['tujuan_diet'] == 'lose' ? 'selected' : ''; ?>>Turunkan Berat</option>
                <option value="maintain" <?php echo $user['tujuan_diet'] == 'maintain' ? 'selected' : ''; ?>>Jaga Berat</option>
                <option value="gain" <?php echo $user['tujuan_diet'] == 'gain' ? 'selected' : ''; ?>>Naikkan Berat</option>
              </select>
            </div>

            <!-- Lokasi -->
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-slate-700 mb-2">Lokasi</label>
              <input type="text" name="lokasi" value="<?php echo $user['lokasi']; ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500" required>
            </div>

          </div>

          <!-- Info Box -->
          <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-800">
              <strong>Info:</strong> BMI akan dihitung otomatis berdasarkan tinggi dan berat badan yang dimasukkan.
            </p>
          </div>

          <!-- Buttons -->
          <div class="mt-6 flex gap-3">
            <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-medium">
              Simpan Perubahan
            </button>
            <a href="admin-users.php" class="px-6 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 font-medium">
              Batal
            </a>
          </div>
        </form>

      </div>
    </main>

  </div>

</body>
</html>
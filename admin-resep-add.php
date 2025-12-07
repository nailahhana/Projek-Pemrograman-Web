<?php
require_once 'koneksi.php';
require_once 'auth.php';

cek_admin();

// Get kategori
$query_kategori = "SELECT * FROM kategori_resep ORDER BY id_kategori";
$result_kategori = mysqli_query($koneksi, $query_kategori);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Resep - Admin Dietly</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50">

  <div class="flex h-screen">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-white flex flex-col">
      <div class="p-6 border-b border-slate-800">
        <h1 class="text-2xl font-bold text-emerald-400">Dietly Admin</h1>
      </div>
      <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
        <a href="admin-dashboard.php" class="block px-4 py-3 hover:bg-slate-800 rounded-lg">📊 Dashboard</a>
        <a href="admin-users.php" class="block px-4 py-3 hover:bg-slate-800 rounded-lg">👥 Pengguna</a>
        <a href="admin-resep.php" class="block px-4 py-3 bg-emerald-600 rounded-lg font-medium">🍽️ Resep</a>
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
          <a href="admin-resep.php" class="text-slate-600 hover:text-slate-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </a>
          <div>
            <h2 class="text-2xl font-bold text-slate-800">Tambah Resep Baru</h2>
            <p class="text-sm text-slate-600">Create new recipe</p>
          </div>
        </div>
      </header>

      <div class="p-8 max-w-4xl">
        
        <!-- Add Form -->
        <form method="POST" action="admin-proses.php" enctype="multipart/form-data" class="bg-white rounded-xl shadow p-6">
          <input type="hidden" name="action" value="add_resep">

          <div class="space-y-6">
            
            <!-- Nama Resep -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Nama Resep *</label>
              <input type="text" name="nama_resep" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Kategori -->
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Kategori *</label>
                <select name="id_kategori" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500" required>
                  <option value="">Pilih Kategori</option>
                  <?php while ($kategori = mysqli_fetch_assoc($result_kategori)): ?>
                    <option value="<?php echo $kategori['id_kategori']; ?>">
                      <?php echo ucfirst($kategori['nama_kategori']); ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>

              <!-- Tingkat Kesulitan -->
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Tingkat Kesulitan *</label>
                <select name="tingkat_kesulitan" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500" required>
                  <option value="mudah">Mudah</option>
                  <option value="sedang">Sedang</option>
                  <option value="sulit">Sulit</option>
                </select>
              </div>
            </div>

            <!-- Deskripsi -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Deskripsi *</label>
              <textarea name="deskripsi" rows="3" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500" required></textarea>
            </div>

            <!-- Foto Resep -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Foto Resep</label>
              <input type="file" name="foto_resep" accept="image/*" class="w-full px-4 py-2 border border-slate-300 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <!-- Waktu Masak -->
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Waktu Masak (menit) *</label>
                <input type="number" name="waktu_masak" min="1" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500" required>
              </div>

              <!-- Porsi -->
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Porsi *</label>
                <input type="number" name="porsi" min="1" value="1" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500" required>
              </div>

              <!-- Kalori -->
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Kalori (per porsi) *</label>
                <input type="number" name="kalori" min="0" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500" required>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <!-- Protein -->
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Protein (g) *</label>
                <input type="number" name="protein" min="0" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500" required>
              </div>

              <!-- Karbohidrat -->
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Karbohidrat (g) *</label>
                <input type="number" name="karbohidrat" min="0" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500" required>
              </div>

              <!-- Lemak -->
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Lemak (g) *</label>
                <input type="number" name="lemak" min="0" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500" required>
              </div>
            </div>

            <!-- Bahan-bahan -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Bahan-Bahan *</label>
              <textarea name="bahan_bahan" rows="5" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500" placeholder="Format: JUDUL:|bahan1|bahan2||JUDUL2:|bahan3|bahan4" required></textarea>
              <p class="text-xs text-slate-500 mt-1">Format: JUDUL:|item1|item2||JUDUL2:|item3</p>
            </div>

            <!-- Cara Membuat -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Cara Membuat *</label>
              <textarea name="cara_membuat" rows="8" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500" placeholder="1. Langkah pertama|2. Langkah kedua|3. Langkah ketiga" required></textarea>
              <p class="text-xs text-slate-500 mt-1">Pisahkan setiap langkah dengan tanda |</p>
            </div>

            <!-- Tips -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Tips (opsional)</label>
              <textarea name="tips" rows="3" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500" placeholder="Tip pertama. Tip kedua. Tip ketiga"></textarea>
              <p class="text-xs text-slate-500 mt-1">Pisahkan setiap tip dengan tanda titik (.)</p>
            </div>

          </div>

          <!-- Buttons -->
          <div class="mt-6 flex gap-3">
            <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-medium">
              Tambah Resep
            </button>
            <a href="admin-resep.php" class="px-6 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 font-medium">
              Batal
            </a>
          </div>
        </form>

      </div>
    </main>

  </div>

</body>
</html>
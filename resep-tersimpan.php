<?php
require_once 'koneksi.php';
require_once 'auth.php';
require_once 'proses_resep.php';

// Cek login
cek_login();

$id_pengguna = $_SESSION['id_pengguna'];

// Get data pengguna
$query = "SELECT * FROM pengguna WHERE id_pengguna = '$id_pengguna'";
$result = mysqli_query($koneksi, $query);
$pengguna = mysqli_fetch_assoc($result);

// Get resep yang disimpan
$resep_tersimpan = get_resep_tersimpan($id_pengguna);

// Get pesan
$pesan = get_pesan();

// Hitung jumlah resep tersimpan
$jumlah_resep = count($resep_tersimpan);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Resep Tersimpan - Dietly</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; }
    .recipe-card {
      transition: all 0.3s ease;
    }
    .recipe-card:hover {
      transform: translateY(-4px);
    }
  </style>
</head>
<body class="bg-gradient-to-b from-emerald-50 to-white text-slate-800">

  <?php if ($pesan): ?>
  <div id="alertPesan" class="fixed top-4 right-4 z-50 bg-<?php echo $pesan['tipe'] == 'success' ? 'green' : 'red'; ?>-100 border border-<?php echo $pesan['tipe'] == 'success' ? 'green' : 'red'; ?>-400 text-<?php echo $pesan['tipe'] == 'success' ? 'green' : 'red'; ?>-700 px-4 py-3 rounded-lg shadow-lg max-w-md">
    <p class="font-bold"><?php echo $pesan['isi']; ?></p>
  </div>
  <script>
    setTimeout(function() {
      document.getElementById('alertPesan').style.display = 'none';
    }, 3000);
  </script>
  <?php endif; ?>

  <!-- DESKTOP NAVBAR -->
  <header class="fixed top-0 left-0 right-0 bg-white/40 backdrop-blur z-30 shadow-sm hidden md:block">
    <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">
      <a href="index.php" class="flex items-center gap-3">
        <h1 class="text-2xl font-bold text-emerald-600">Dietly</h1>
      </a>
      <nav class="flex items-center gap-4">
        <a href="index.php" class="px-3 py-2 rounded hover:text-emerald-600">Beranda</a>
        <a href="profile.php" class="px-3 py-2 rounded hover:text-emerald-600">Profil</a>
        <a href="progres.php" class="px-3 py-2 rounded hover:text-emerald-600">Progress</a>
        <a href="resep.php" class="px-3 py-2 rounded hover:text-emerald-600">Resep</a>
        <a href="resep-tersimpan.php" class="px-3 py-2 text-emerald-600 font-semibold rounded">Tersimpan</a>
        <form method="POST" action="auth.php" class="inline">
          <input type="hidden" name="aksi" value="logout">
          <button type="submit" class="ml-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Logout</button>
        </form>
      </nav>
    </div>
  </header>

  <!-- MOBILE BOTTOM NAV -->
  <nav class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur border-t z-30 md:hidden" style="padding-bottom: env(safe-area-inset-bottom);">
    <div class="max-w-6xl mx-auto">
      <div class="flex justify-between items-center">
        <a href="index.php" class="w-full py-2 flex flex-col items-center justify-center text-slate-700 hover:text-emerald-600">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10.5L12 4l9 6.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1V10.5z" />
          </svg>
          <span class="text-xs">Beranda</span>
        </a>
        <a href="profile.php" class="w-full py-2 flex flex-col items-center justify-center text-slate-700 hover:text-emerald-600">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1118.879 6.196 9 9 0 015.121 17.804zM15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          <span class="text-xs">Profil</span>
        </a>
        <a href="progres.php" class="w-full py-2 flex flex-col items-center justify-center text-slate-700 hover:text-emerald-600">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3v18M4 12v6m14-10v10" />
          </svg>
          <span class="text-xs">Progress</span>
        </a>
        <a href="resep.php" class="w-full py-2 flex flex-col items-center justify-center text-slate-700 hover:text-emerald-600">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20h9M3 20h9M3 4h6v16H3z" />
          </svg>
          <span class="text-xs">Resep</span>
        </a>
        <a href="resep-tersimpan.php" class="w-full py-2 flex flex-col items-center justify-center text-emerald-600">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-1" fill="currentColor" viewBox="0 0 24 24">
            <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
          </svg>
          <span class="text-xs font-medium">Tersimpan</span>
        </a>
      </div>
    </div>
  </nav>

  <!-- MAIN CONTENT -->
  <main class="pt-4 pb-20 md:pt-20 md:pb-12">
    <div class="max-w-7xl mx-auto px-4">
      
      <!-- PROFILE BAR -->
      <section class="flex flex-col md:flex-row items-center gap-4 mb-6">
        <div class="flex-1 w-full">
          <div class="md:hidden w-full flex items-center justify-between">
            <div>
              <h2 class="text-base font-semibold"><?php echo $pengguna['nama_lengkap']; ?></h2>
              <p class="text-xs text-slate-500 -mt-1"><?php echo $pengguna['lokasi']; ?></p>
            </div>
            <a href="profile.php" class="p-1.5 rounded-full bg-emerald-50">
              <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle cx="12" cy="8" r="3" stroke-width="1.5"/>
                <path d="M6 20c1.5-3 4.5-4 6-4s4.5 1 6 4" stroke-width="1.5"/>
              </svg>
            </a>
          </div>
          <!-- Desktop Profile Info -->
          <div class="hidden md:flex items-center gap-4">
            <div class="p-2 rounded-full bg-emerald-50">
              <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle cx="12" cy="8" r="3" stroke-width="1.5"/>
                <path d="M6 20c1.5-3 4.5-4 6-4s4.5 1 6 4" stroke-width="1.5"/>
              </svg>
            </div>
            <div>
              <h2 class="text-lg font-semibold"><?php echo $pengguna['nama_lengkap']; ?></h2>
              <p class="text-sm text-slate-500"><?php echo $pengguna['lokasi']; ?></p>
            </div>
          </div>
        </div>
      </section>

      <!-- Header -->
      <div class="mb-6">
        <div class="flex items-center justify-between mb-3">
          <div>
            <h1 class="text-3xl font-bold text-slate-800">Resep Tersimpan</h1>
            <p class="text-sm text-slate-600 mt-1">Koleksi resep favoritmu</p>
          </div>
          <a href="resep.php" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition text-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Cari Resep Baru
          </a>
        </div>

        <!-- Stats Card -->
        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-2xl p-6 text-white shadow-lg">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-emerald-100 text-sm mb-1">Total Resep Tersimpan</p>
              <p class="text-4xl font-bold"><?php echo $jumlah_resep; ?></p>
            </div>
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
              <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
              </svg>
            </div>
          </div>
          <div class="mt-4 pt-4 border-t border-white/20">
            <p class="text-emerald-100 text-sm">Simpan resep favoritmu untuk akses cepat kapan saja!</p>
          </div>
        </div>
      </div>

      <!-- Daftar Resep Tersimpan -->
      <section>
        <?php if ($jumlah_resep > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          
          <?php foreach ($resep_tersimpan as $resep): ?>
          <article class="recipe-card bg-white rounded-2xl shadow-md hover:shadow-xl overflow-hidden">
            <?php if ($resep['foto_resep']): ?>
              <img src="<?php echo $resep['foto_resep']; ?>" alt="<?php echo $resep['nama_resep']; ?>" class="w-full h-48 object-cover">
            <?php else: ?>
              <div class="w-full h-48 bg-gradient-to-br from-emerald-100 to-emerald-200 flex items-center justify-center">
                <span class="text-6xl">🍽️</span>
              </div>
            <?php endif; ?>
            
            <div class="p-5">
              <!-- Badge Kategori & Saved Date -->
              <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full">
                  <?php 
                    $kategori_label = array(
                      'sarapan' => '🌅 Sarapan',
                      'siang' => '☀️ Siang',
                      'malam' => '🌙 Malam',
                      'snack' => '🍪 Snack'
                    );
                    echo isset($kategori_label[$resep['nama_kategori']]) ? $kategori_label[$resep['nama_kategori']] : $resep['nama_kategori'];
                  ?>
                </span>
                <span class="text-xs text-slate-400">
                  <?php echo date('d M', strtotime($resep['tanggal_disimpan'])); ?>
                </span>
              </div>

              <h3 class="font-semibold text-lg text-slate-800 mb-2"><?php echo $resep['nama_resep']; ?></h3>
              <p class="text-sm text-slate-600 mb-4 line-clamp-2">
                <?php echo substr($resep['deskripsi'], 0, 100); ?><?php echo strlen($resep['deskripsi']) > 100 ? '...' : ''; ?>
              </p>
              
              <!-- Info Nutrisi -->
              <div class="flex items-center justify-between text-xs text-slate-500 mb-4">
                <span class="flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                  </svg>
                  <?php echo $resep['kalori']; ?> kcal
                </span>
                <span class="flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <?php echo $resep['waktu_masak']; ?> menit
                </span>
              </div>
              
              <!-- Action Buttons -->
              <div class="flex gap-2">
                <a href="resep-detail.php?id=<?php echo $resep['id_resep']; ?>" class="flex-1 text-center px-4 py-2 bg-emerald-600 text-white rounded-lg font-medium hover:bg-emerald-700 transition text-sm">
                  Lihat Resep
                </a>
                
                <form method="POST" action="proses_resep.php" class="flex-shrink-0">
                  <input type="hidden" name="aksi" value="hapus_simpanan">
                  <input type="hidden" name="id_resep" value="<?php echo $resep['id_resep']; ?>">
                  <button type="submit" onclick="return confirm('Hapus resep ini dari simpanan?')" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition" title="Hapus dari simpanan">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </form>
              </div>
            </div>
          </article>
          <?php endforeach; ?>

        </div>
        <?php else: ?>
        <!-- Empty State -->
        <div class="text-center py-16">
          <div class="w-32 h-32 mx-auto mb-6 bg-slate-100 rounded-full flex items-center justify-center">
            <svg class="w-16 h-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-slate-800 mb-2">Belum Ada Resep Tersimpan</h3>
          <p class="text-slate-600 mb-6 max-w-md mx-auto">
            Mulai simpan resep favoritmu untuk akses cepat kapan saja. Klik ikon bookmark di halaman detail resep!
          </p>
          
          <div class="flex flex-col sm:flex-row gap-3 justify-center items-center">
            <a href="resep.php" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 text-white rounded-xl font-semibold hover:bg-emerald-700 transition">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
              Jelajahi Resep
            </a>
            
            <a href="resep.php?kategori=sarapan" class="inline-flex items-center gap-2 px-6 py-3 border-2 border-slate-200 text-slate-700 rounded-xl font-semibold hover:bg-slate-50 transition">
              <span>🌅</span>
              Lihat Resep Sarapan
            </a>
          </div>

          <!-- Quick Links -->
          <div class="mt-12 grid grid-cols-2 md:grid-cols-4 gap-4 max-w-3xl mx-auto">
            <a href="resep.php?kategori=sarapan" class="p-4 bg-orange-50 rounded-xl hover:bg-orange-100 transition text-center">
              <div class="text-3xl mb-2">🌅</div>
              <p class="text-sm font-medium text-slate-700">Sarapan</p>
            </a>
            <a href="resep.php?kategori=siang" class="p-4 bg-yellow-50 rounded-xl hover:bg-yellow-100 transition text-center">
              <div class="text-3xl mb-2">☀️</div>
              <p class="text-sm font-medium text-slate-700">Makan Siang</p>
            </a>
            <a href="resep.php?kategori=malam" class="p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition text-center">
              <div class="text-3xl mb-2">🌙</div>
              <p class="text-sm font-medium text-slate-700">Makan Malam</p>
            </a>
            <a href="resep.php?kategori=snack" class="p-4 bg-pink-50 rounded-xl hover:bg-pink-100 transition text-center">
              <div class="text-3xl mb-2">🍪</div>
              <p class="text-sm font-medium text-slate-700">Snack</p>
            </a>
          </div>
        </div>
        <?php endif; ?>
      </section>

      <!-- Tips Section -->
      <?php if ($jumlah_resep > 0): ?>
      <section class="mt-12 bg-blue-50 border border-blue-200 rounded-2xl p-6">
        <div class="flex items-start gap-4">
          <div class="flex-shrink-0">
            <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
          </div>
          <div class="flex-1">
            <h3 class="font-bold text-slate-800 mb-2">💡 Tips Menggunakan Resep Tersimpan</h3>
            <ul class="space-y-2 text-sm text-slate-700">
              <li class="flex items-start gap-2">
                <span class="text-blue-600 mt-0.5">✓</span>
                <span>Resep tersimpan bisa diakses kapan saja tanpa perlu mencari lagi</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-blue-600 mt-0.5">✓</span>
                <span>Buat meal plan mingguan dari koleksi resep favoritmu</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-blue-600 mt-0.5">✓</span>
                <span>Simpan resep sesuai dengan program diet dan target kalorimu</span>
              </li>
            </ul>
          </div>
        </div>
      </section>
      <?php endif; ?>

    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-slate-900 text-white py-6 text-center mb-14 md:mb-0 mt-8">
    <p class="text-sm">© 2025 Dietly - Semua Hak Dilindungi</p>
  </footer>

</body>
</html>
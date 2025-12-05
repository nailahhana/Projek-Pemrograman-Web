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

// Get kategori filter (jika ada)
$kategori_filter = isset($_GET['kategori']) ? bersihkan_input($_GET['kategori']) : null;

// Get resep berdasarkan kategori
$resep_list = get_resep_kategori($kategori_filter);

// Get pesan
$pesan = get_pesan();

// Label kategori
$label_kategori = 'Semua Resep';
if ($kategori_filter) {
    $nama_kategori = array(
        'sarapan' => 'Sarapan',
        'siang' => 'Makan Siang',
        'malam' => 'Makan Malam',
        'snack' => 'Snack'
    );
    $label_kategori = isset($nama_kategori[$kategori_filter]) ? $nama_kategori[$kategori_filter] : 'Semua Resep';
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Resep Makanan - Dietly</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; }
    .category-btn { transition: all 0.2s ease; }
    .category-btn.active { transform: scale(1.05); }
  </style>
</head>
<body class="bg-gradient-to-b from-emerald-50 to-white text-slate-800">

  <?php if ($pesan): ?>
  <div id="alertPesan" class="fixed top-4 right-4 z-50 bg-<?php echo $pesan['tipe'] == 'success' ? 'green' : ($pesan['tipe'] == 'info' ? 'blue' : 'red'); ?>-100 border border-<?php echo $pesan['tipe'] == 'success' ? 'green' : ($pesan['tipe'] == 'info' ? 'blue' : 'red'); ?>-400 text-<?php echo $pesan['tipe'] == 'success' ? 'green' : ($pesan['tipe'] == 'info' ? 'blue' : 'red'); ?>-700 px-4 py-3 rounded-lg shadow-lg max-w-md">
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
        <a href="resep.php" class="px-3 py-2 text-emerald-600 font-semibold rounded">Resep</a>
        <a href="resep-tersimpan.php" class="px-3 py-2 rounded hover:text-emerald-600">
  Tersimpan
</a>
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
        <a href="resep.php" class="w-full py-2 flex flex-col items-center justify-center text-emerald-600">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20h9M3 20h9M3 4h6v16H3z" />
          </svg>
          <span class="text-xs">Resep</span>
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
      <div class="text-center mb-6">
        <h1 class="text-3xl font-bold text-slate-800 mb-2">Resep Makanan</h1>
        <p class="text-sm text-slate-600">Temukan resep sehat untuk program dietmu</p>
      </div>

      <!-- Info Box -->
      <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 mb-6">
        <p class="text-sm text-slate-700">
          💡 Resep yang ditampilkan disesuaikan dengan profil dan program dietmu. 
          Belum punya program? Tentukan di 
          <a href="profile.php" class="text-emerald-600 font-medium underline">profil pengguna</a>
        </p>
      </div>

      <!-- Kategori Filter -->
      <section class="mb-8">
        <h2 class="text-lg font-bold text-slate-800 mb-4">Kategori Makanan</h2>
        <div class="grid grid-cols-4 md:grid-cols-4 gap-3">
          <a href="resep.php?kategori=sarapan" class="category-btn bg-white p-4 rounded-2xl shadow hover:shadow-lg flex flex-col items-center <?php echo $kategori_filter == 'sarapan' ? 'active ring-2 ring-emerald-500' : ''; ?>">
            <div class="w-14 h-14 bg-orange-100 rounded-full flex items-center justify-center mb-2">
              <span class="text-2xl">🌅</span>
            </div>
            <h3 class="font-medium text-slate-800 text-sm">Sarapan</h3>
          </a>
          
          <a href="resep.php?kategori=siang" class="category-btn bg-white p-4 rounded-2xl shadow hover:shadow-lg flex flex-col items-center <?php echo $kategori_filter == 'siang' ? 'active ring-2 ring-emerald-500' : ''; ?>">
            <div class="w-14 h-14 bg-yellow-100 rounded-full flex items-center justify-center mb-2">
              <span class="text-2xl">☀️</span>
            </div>
            <h3 class="font-medium text-slate-800 text-sm">Siang</h3>
          </a>

          <a href="resep.php?kategori=malam" class="category-btn bg-white p-4 rounded-2xl shadow hover:shadow-lg flex flex-col items-center <?php echo $kategori_filter == 'malam' ? 'active ring-2 ring-emerald-500' : ''; ?>">
            <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center mb-2">
              <span class="text-2xl">🌙</span>
            </div>
            <h3 class="font-medium text-slate-800 text-sm">Malam</h3>
          </a>

          <a href="resep.php?kategori=snack" class="category-btn bg-white p-4 rounded-2xl shadow hover:shadow-lg flex flex-col items-center <?php echo $kategori_filter == 'snack' ? 'active ring-2 ring-emerald-500' : ''; ?>">
            <div class="w-14 h-14 bg-pink-100 rounded-full flex items-center justify-center mb-2">
              <span class="text-2xl">🍪</span>
            </div>
            <h3 class="font-medium text-slate-800 text-sm">Snack</h3>
          </a>
        </div>
      </section>

      <!-- Daftar Resep -->
      <section>
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-bold text-slate-800">Resep Pilihan</h2>
          <div class="flex items-center gap-2">
            <span class="text-sm text-emerald-600 font-medium"><?php echo $label_kategori; ?></span>
            <?php if ($kategori_filter): ?>
              <a href="resep.php" class="text-xs text-slate-500 hover:text-slate-700">(Reset)</a>
            <?php endif; ?>
          </div>
        </div>

        <?php if (count($resep_list) > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          
          <?php foreach ($resep_list as $resep): ?>
          <article class="recipe-card bg-white rounded-2xl shadow hover:shadow-xl transition overflow-hidden">
            <?php if ($resep['foto_resep']): ?>
              <img src="<?php echo $resep['foto_resep']; ?>" alt="<?php echo $resep['nama_resep']; ?>" class="w-full h-48 object-cover">
            <?php else: ?>
              <div class="w-full h-48 bg-gradient-to-br from-emerald-100 to-emerald-200 flex items-center justify-center">
                <span class="text-6xl">🍽️</span>
              </div>
            <?php endif; ?>
            
            <div class="p-5">
              <h3 class="font-semibold text-lg text-slate-800 mb-2"><?php echo $resep['nama_resep']; ?></h3>
              <p class="text-sm text-slate-600 mb-4 line-clamp-2">
                <?php echo substr($resep['deskripsi'], 0, 100); ?><?php echo strlen($resep['deskripsi']) > 100 ? '...' : ''; ?>
              </p>
              
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
              
              <a href="resep-detail.php?id=<?php echo $resep['id_resep']; ?>" class="block w-full bg-emerald-600 text-white text-center py-2 rounded-lg font-medium hover:bg-emerald-700 transition">
                Lihat Resep
              </a>
            </div>
          </article>
          <?php endforeach; ?>

        </div>
        <?php else: ?>
        <div class="text-center py-16">
          <svg class="w-24 h-24 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
          </svg>
          <p class="text-slate-500 text-lg font-medium">Belum ada resep untuk kategori ini</p>
          <p class="text-slate-400 text-sm mt-2">Coba kategori lain atau lihat semua resep</p>
          <a href="resep.php" class="inline-block mt-4 px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
            Lihat Semua Resep
          </a>
        </div>
        <?php endif; ?>
      </section>

    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-slate-900 text-white py-6 text-center mb-14 md:mb-0 md:mt-8">
    <p class="text-sm">© 2025 Dietly - Semua Hak Dilindungi</p>
  </footer>

</body>
</html>
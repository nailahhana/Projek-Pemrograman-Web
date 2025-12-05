<?php
require_once 'koneksi.php';
require_once 'auth.php';
require_once 'proses_resep.php';

// Cek login
cek_login();

$id_pengguna = $_SESSION['id_pengguna'];

// Get ID resep dari URL
$id_resep = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Get detail resep
$resep = get_detail_resep($id_resep);

// Jika resep tidak ditemukan, redirect
if (!$resep) {
    set_pesan('error', 'Resep tidak ditemukan!');
    redirect('resep.php');
}

// Cek apakah sudah disimpan
$sudah_disimpan = is_resep_tersimpan($id_pengguna, $id_resep);

// Parse bahan-bahan (format: JUDUL:|item1|item2||JUDUL2:|item3)
$bahan_sections = explode('||', $resep['bahan_bahan']);

// Parse cara membuat (format: 1. step|2. step|3. step)
$cara_steps = explode('|', $resep['cara_membuat']);

// Parse tips (format: tip1. desc|tip2. desc)
$tips_items = $resep['tips'] ? explode('.', $resep['tips']) : array();

// Get pesan
$pesan = get_pesan();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title><?php echo $resep['nama_resep']; ?> - Dietly</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; }
  </style>
</head>
<body class="bg-gradient-to-b from-emerald-50 to-white text-slate-800 min-h-screen">

  <?php if ($pesan): ?>
  <div id="alertPesan" class="fixed top-4 right-4 z-50 bg-<?php echo $pesan['tipe'] == 'success' ? 'green' : 'blue'; ?>-100 border border-<?php echo $pesan['tipe'] == 'success' ? 'green' : 'blue'; ?>-400 text-<?php echo $pesan['tipe'] == 'success' ? 'green' : 'blue'; ?>-700 px-4 py-3 rounded-lg shadow-lg max-w-md">
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
    <div class="max-w-4xl mx-auto px-4">
      
      <!-- Back Button -->
      <div class="mb-4">
        <a href="resep.php" class="inline-flex items-center gap-2 text-emerald-600 hover:text-emerald-700 font-medium">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
          Kembali ke Daftar Resep
        </a>
      </div>

      <!-- Recipe Header -->
      <article class="bg-white rounded-3xl shadow-lg overflow-hidden">
        <div class="relative">
          <?php if ($resep['foto_resep']): ?>
            <img src="<?php echo $resep['foto_resep']; ?>" alt="<?php echo $resep['nama_resep']; ?>" class="w-full h-64 md:h-96 object-cover">
          <?php else: ?>
            <div class="w-full h-64 md:h-96 bg-gradient-to-br from-emerald-100 to-emerald-200 flex items-center justify-center">
              <span class="text-9xl">🍽️</span>
            </div>
          <?php endif; ?>
          
          <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-6">
            <h1 class="text-2xl md:text-3xl font-bold text-white mb-2"><?php echo $resep['nama_resep']; ?></h1>
            <div class="flex items-center gap-4 text-white text-sm">
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
              <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <?php echo $resep['porsi']; ?> porsi
              </span>
            </div>
          </div>
        </div>

        <div class="p-6">
          <!-- Info Box -->
          <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
            <p class="text-sm text-slate-700">
              💡 Resep ini disesuaikan dengan program dietmu. Belum punya program? Tentukan di 
              <a href="profile.php" class="text-emerald-600 font-medium underline">profil pengguna</a>
            </p>
          </div>

          <!-- Nutrition Facts -->
          <section class="mb-8">
            <h2 class="text-xl font-bold text-slate-800 mb-4">Informasi Nutrisi (per porsi)</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div class="bg-emerald-50 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-emerald-600"><?php echo $resep['kalori']; ?></p>
                <p class="text-xs text-slate-600">Kalori</p>
              </div>
              <div class="bg-blue-50 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-blue-600"><?php echo $resep['protein']; ?>g</p>
                <p class="text-xs text-slate-600">Protein</p>
              </div>
              <div class="bg-yellow-50 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-yellow-600"><?php echo $resep['karbohidrat']; ?>g</p>
                <p class="text-xs text-slate-600">Karbohidrat</p>
              </div>
              <div class="bg-orange-50 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-orange-600"><?php echo $resep['lemak']; ?>g</p>
                <p class="text-xs text-slate-600">Lemak</p>
              </div>
            </div>
          </section>

          <!-- Ingredients -->
          <section class="mb-8">
            <h2 class="text-xl font-bold text-slate-800 mb-4">Bahan-Bahan</h2>
            
            <?php foreach ($bahan_sections as $section): ?>
              <?php 
                $parts = explode(':|', $section);
                if (count($parts) == 2) {
                    $judul = trim($parts[0]);
                    $items = explode('|', $parts[1]);
              ?>
              <div class="mb-4">
                <h3 class="font-semibold text-slate-700 mb-3 flex items-center gap-2">
                  <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                  <?php echo $judul; ?>
                </h3>
                <ul class="space-y-2 ml-4">
                  <?php foreach ($items as $item): ?>
                    <?php if (trim($item)): ?>
                    <li class="flex items-start gap-2 text-slate-600">
                      <span class="text-emerald-600 mt-1">•</span>
                      <span><?php echo trim($item); ?></span>
                    </li>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </ul>
              </div>
              <?php } ?>
            <?php endforeach; ?>
          </section>

          <!-- Instructions -->
          <section class="mb-8">
            <h2 class="text-xl font-bold text-slate-800 mb-4">Cara Membuat</h2>
            <ol class="space-y-4">
              <?php 
              $step_num = 1;
              foreach ($cara_steps as $step): 
                $step = trim($step);
                // Remove number prefix if exists (e.g., "1. ", "2. ")
                $step = preg_replace('/^\d+\.\s*/', '', $step);
                if ($step):
              ?>
              <li class="flex gap-4">
                <span class="flex-shrink-0 w-8 h-8 bg-emerald-600 text-white rounded-full flex items-center justify-center font-bold text-sm"><?php echo $step_num; ?></span>
                <p class="text-slate-600 pt-1"><?php echo $step; ?></p>
              </li>
              <?php 
                $step_num++;
                endif;
              endforeach; 
              ?>
            </ol>
          </section>

          <!-- Tips -->
          <?php if ($resep['tips']): ?>
          <section class="bg-yellow-50 border border-yellow-200 rounded-xl p-5 mb-6">
            <h3 class="font-bold text-slate-800 mb-3 flex items-center gap-2">
              <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
              </svg>
              Tips Memasak
            </h3>
            <ul class="space-y-2 text-sm text-slate-700">
              <?php 
              $tips_array = explode('.', $resep['tips']);
              foreach ($tips_array as $tip): 
                $tip = trim($tip);
                if ($tip):
              ?>
              <li class="flex items-start gap-2">
                <span class="text-yellow-600 mt-0.5">✓</span>
                <span><?php echo $tip; ?></span>
              </li>
              <?php 
                endif;
              endforeach; 
              ?>
            </ul>
          </section>
          <?php endif; ?>

          <!-- Action Buttons -->
          <div class="flex gap-3">
            <form method="POST" action="proses_resep.php" class="flex-1">
              <input type="hidden" name="aksi" value="simpan_resep">
              <input type="hidden" name="id_resep" value="<?php echo $resep['id_resep']; ?>">
              <input type="hidden" name="redirect" value="resep-detail.php?id=<?php echo $resep['id_resep']; ?>">
              <button type="submit" class="w-full bg-<?php echo $sudah_disimpan ? 'slate' : 'emerald'; ?>-600 text-white py-3 rounded-xl font-semibold hover:bg-<?php echo $sudah_disimpan ? 'slate' : 'emerald'; ?>-700 transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="<?php echo $sudah_disimpan ? 'currentColor' : 'none'; ?>" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                </svg>
                <?php echo $sudah_disimpan ? 'Tersimpan' : 'Simpan Resep'; ?>
              </button>
            </form>
            
            <button onclick="shareRecipe()" class="px-6 bg-slate-100 text-slate-700 py-3 rounded-xl font-semibold hover:bg-slate-200 transition">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
              </svg>
            </button>
          </div>
        </div>
      </article>

    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-slate-900 text-white py-6 text-center mt-8 mb-14 md:mb-0">
    <p class="text-sm">© 2025 Dietly - Semua Hak Dilindungi</p>
  </footer>

  <script>
    function shareRecipe() {
      var url = window.location.href;
      if (navigator.share) {
        navigator.share({
          title: '<?php echo $resep['nama_resep']; ?>',
          text: 'Lihat resep ini di Dietly!',
          url: url
        });
      } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(url);
        alert('Link resep telah disalin! 📋\n\nKamu bisa bagikan ke teman-temanmu');
      }
    }
  </script>

</body>
</html>
<?php
require_once 'koneksi.php';
require_once 'auth.php';

// Cek login
cek_login();

$id_pengguna = $_SESSION['id_pengguna'];

// Get data pengguna
$query = "SELECT * FROM pengguna WHERE id_pengguna = '$id_pengguna'";
$result = mysqli_query($koneksi, $query);
$pengguna = mysqli_fetch_assoc($result);

// Get program diet aktif
$query_program = "SELECT * FROM program_diet 
                  WHERE id_pengguna = '$id_pengguna' AND status = 'aktif' 
                  ORDER BY tanggal_mulai DESC LIMIT 1";
$result_program = mysqli_query($koneksi, $query_program);
$program = mysqli_fetch_assoc($result_program);

// Get tracking history (10 terakhir)
$query_tracking = "SELECT * FROM tracking_harian 
                   WHERE id_pengguna = '$id_pengguna' 
                   ORDER BY tanggal_tracking DESC LIMIT 10";
$result_tracking = mysqli_query($koneksi, $query_tracking);

// Hitung progress
$berat_awal = $pengguna['berat_badan'];
$berat_target = $pengguna['berat_target'];

// Get berat terkini dari tracking terakhir
$query_berat_terkini = "SELECT berat_badan FROM tracking_harian 
                        WHERE id_pengguna = '$id_pengguna' 
                        ORDER BY tanggal_tracking DESC LIMIT 1";
$result_berat = mysqli_query($koneksi, $query_berat_terkini);
$berat_sekarang = $berat_awal;
if (mysqli_num_rows($result_berat) > 0) {
    $data_berat = mysqli_fetch_assoc($result_berat);
    $berat_sekarang = $data_berat['berat_badan'];
}

// Hitung persentase progress
$target_perubahan = $berat_target - $berat_awal;
$perubahan_saat_ini = $berat_sekarang - $berat_awal;
$progress_persen = 0;
if ($target_perubahan != 0) {
    $progress_persen = abs(($perubahan_saat_ini / $target_perubahan) * 100);
    $progress_persen = min(100, max(0, round($progress_persen)));
}

// Hitung hari progress
$hari_progress = 0;
$total_hari = 30; // default
if ($program) {
    $tanggal_mulai = new DateTime($program['tanggal_mulai']);
    $tanggal_sekarang = new DateTime();
    $selisih = $tanggal_sekarang->diff($tanggal_mulai);
    $hari_progress = $selisih->days;
    $total_hari = $program['durasi_hari'];
}

// Get pesan
$pesan = get_pesan();

// Tentukan nama tujuan diet
$nama_tujuan = array(
    'lose' => 'Turunkan Berat',
    'maintain' => 'Jaga Berat', 
    'gain' => 'Naikkan Berat'
);
$tujuan_text = isset($nama_tujuan[$pengguna['tujuan_diet']]) ? $nama_tujuan[$pengguna['tujuan_diet']] : 'Belum ditentukan';

// Set tanggal hari ini untuk form
$tanggal_hari_ini = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Progress Diet - Dietly</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; }
    .preview-img { max-width: 120px; max-height: 120px; object-fit: cover; }
  </style>
</head>
<body class="bg-gradient-to-b from-emerald-50 to-white text-slate-800">

  <?php if ($pesan): ?>
  <div id="alertPesan" class="fixed top-4 right-4 z-50 bg-<?php echo $pesan['tipe'] == 'success' ? 'green' : 'red'; ?>-100 border border-<?php echo $pesan['tipe'] == 'success' ? 'green' : 'red'; ?>-400 text-<?php echo $pesan['tipe'] == 'success' ? 'green' : 'red'; ?>-700 px-4 py-3 rounded-lg shadow-lg max-w-md">
    <p class="font-bold"><?php echo $pesan['tipe'] == 'success' ? '✓' : '✗'; ?> <?php echo ucfirst($pesan['tipe']); ?></p>
    <p class="text-sm"><?php echo $pesan['isi']; ?></p>
  </div>
  <script>
    setTimeout(function() {
      document.getElementById('alertPesan').style.display = 'none';
    }, 5000);
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
        <a href="progres.php" class="px-3 py-2 text-emerald-600 font-semibold rounded">Progress</a>
        <a href="resep.php" class="px-3 py-2 rounded hover:text-emerald-600">Resep</a>
        <a href="resep-tersimpan.php" class="px-3 py-2 rounded hover:text-emerald-600">Resep Tersimpan</a>
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
        <a href="progres.php" class="w-full py-2 flex flex-col items-center justify-center text-emerald-600">
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
      </div>
    </div>
  </nav>

  <!-- MAIN CONTENT -->
  <main class="pt-4 pb-20 md:pt-20 md:pb-12 min-h-screen">
    <div class="max-w-5xl mx-auto px-4">

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

      <!-- Page Header -->
      <div class="text-center mb-6">
        <h1 class="text-3xl font-bold text-slate-800 mb-2">Progress Diet Kamu</h1>
        <p class="text-sm text-slate-600">Pantau perkembangan program dietmu</p>
      </div>

      <!-- Program Info -->
      <section class="bg-white rounded-3xl shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-slate-800 mb-4">Program Diet Kamu</h2>
        <div class="grid md:grid-cols-2 gap-4">
          <div class="bg-emerald-50 rounded-xl p-4">
            <p class="text-sm text-emerald-700 font-medium mb-1">Diet Plan</p>
            <p class="text-lg font-bold text-emerald-900"><?php echo $program ? $program['nama_program'] : $tujuan_text; ?></p>
          </div>
          <div class="bg-blue-50 rounded-xl p-4">
            <p class="text-sm text-blue-700 font-medium mb-1">Goal</p>
            <p class="text-lg font-bold text-blue-900"><?php echo $tujuan_text; ?></p>
          </div>
        </div>
      </section>

      <!-- Progress Summary -->
      <section class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-3xl shadow-lg p-6 mb-6 text-white">
        <h2 class="text-xl font-bold mb-4">Ringkasan Progress</h2>
        
        <div class="mb-6">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm opacity-90">Progress Program</span>
            <span class="text-sm font-bold"><?php echo $progress_persen; ?>%</span>
          </div>
          <div class="w-full bg-white/30 rounded-full h-3 overflow-hidden">
            <div class="bg-white h-3 transition-all duration-500" style="width: <?php echo $progress_persen; ?>%"></div>
          </div>
          <p class="text-xs opacity-80 mt-2"><?php echo $hari_progress; ?> hari dari <?php echo $total_hari; ?> hari program</p>
        </div>

        <div class="grid grid-cols-3 gap-4">
          <div class="text-center">
            <p class="text-2xl font-bold"><?php echo $berat_awal; ?> kg</p>
            <p class="text-xs opacity-80">Berat Awal</p>
          </div>
          <div class="text-center">
            <p class="text-2xl font-bold"><?php echo $berat_sekarang; ?> kg</p>
            <p class="text-xs opacity-80">Berat Saat Ini</p>
          </div>
          <div class="text-center">
            <p class="text-2xl font-bold"><?php echo $berat_target; ?> kg</p>
            <p class="text-xs opacity-80">Target</p>
          </div>
        </div>
      </section>

      <!-- Daily Tracking Form -->
      <section class="bg-white rounded-3xl shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-slate-800 mb-4">Tracking Harian</h2>
        
        <form method="POST" action="proses_tracking.php" enctype="multipart/form-data" class="space-y-6">
          <!-- Date and Weight -->
          <div class="grid md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal</label>
              <input type="date" name="tanggal_tracking" value="<?php echo $tanggal_hari_ini; ?>" class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Berat Badan (kg)</label>
              <input type="number" name="berat_badan" step="0.1" placeholder="<?php echo $berat_sekarang; ?>" class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
            </div>
          </div>

          <!-- Exercise Photo -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">
              📸 Bukti Olahraga
            </label>
            <input type="file" name="foto_olahraga" accept="image/*" class="w-full border border-slate-300 rounded-lg px-4 py-2 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100" onchange="previewImage(this, 'previewOlahraga')">
            <div id="previewOlahraga" class="mt-2"></div>
          </div>

          <!-- Meal Photos -->
          <div class="grid md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">🍳 Sarapan</label>
              <input type="file" name="foto_sarapan" accept="image/*" class="w-full border border-slate-300 rounded-lg px-4 py-2 text-sm file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-orange-50 file:text-orange-700 file:text-xs hover:file:bg-orange-100" onchange="previewImage(this, 'previewSarapan')">
              <div id="previewSarapan" class="mt-2"></div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">🍱 Makan Siang</label>
              <input type="file" name="foto_makan_siang" accept="image/*" class="w-full border border-slate-300 rounded-lg px-4 py-2 text-sm file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-yellow-50 file:text-yellow-700 file:text-xs hover:file:bg-yellow-100" onchange="previewImage(this, 'previewSiang')">
              <div id="previewSiang" class="mt-2"></div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">🍽️ Makan Malam</label>
              <input type="file" name="foto_makan_malam" accept="image/*" class="w-full border border-slate-300 rounded-lg px-4 py-2 text-sm file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-purple-50 file:text-purple-700 file:text-xs hover:file:bg-purple-100" onchange="previewImage(this, 'previewMalam')">
              <div id="previewMalam" class="mt-2"></div>
            </div>
          </div>

          <!-- Notes -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Catatan (opsional)</label>
            <textarea name="catatan" rows="3" placeholder="Bagaimana perasaanmu hari ini? Ada kendala?" class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
          </div>

          <!-- Submit Button -->
          <button type="submit" class="w-full bg-emerald-600 text-white py-3 rounded-xl font-semibold hover:bg-emerald-700 transition flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Simpan Aktivitas Hari Ini
          </button>
        </form>
      </section>

      <!-- Recent Activity -->
      <section class="bg-white rounded-3xl shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-bold text-slate-800">Aktivitas Terakhir</h2>
        </div>
        
        <div class="space-y-4">
          <?php if (mysqli_num_rows($result_tracking) > 0): ?>
            <?php while ($tracking = mysqli_fetch_assoc($result_tracking)): ?>
            <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl">
              <div class="w-12 h-12 bg-<?php echo $tracking['status_lengkap'] == 'lengkap' ? 'emerald' : 'slate'; ?>-100 rounded-full flex items-center justify-center">
                <span class="text-xl"><?php echo $tracking['status_lengkap'] == 'lengkap' ? '✓' : '○'; ?></span>
              </div>
              <div class="flex-1">
                <p class="font-medium text-slate-800">Log <?php echo format_tanggal($tracking['tanggal_tracking']); ?></p>
                <p class="text-xs text-slate-500">Berat: <?php echo $tracking['berat_badan']; ?> kg</p>
                <?php if ($tracking['catatan']): ?>
                  <p class="text-xs text-slate-600 mt-1 italic"><?php echo substr($tracking['catatan'], 0, 50); ?><?php echo strlen($tracking['catatan']) > 50 ? '...' : ''; ?></p>
                <?php endif; ?>
              </div>
              <span class="text-xs text-<?php echo $tracking['status_lengkap'] == 'lengkap' ? 'emerald' : 'slate'; ?>-600 font-medium">
                <?php echo $tracking['status_lengkap'] == 'lengkap' ? 'Lengkap' : 'Sebagian'; ?>
              </span>
            </div>
            <?php endwhile; ?>
          <?php else: ?>
            <div class="text-center py-8">
              <svg class="w-16 h-16 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              <p class="text-slate-500">Belum ada aktivitas tercatat</p>
              <p class="text-xs text-slate-400 mt-1">Mulai tracking harian untuk melihat progressmu!</p>
            </div>
          <?php endif; ?>
        </div>
      </section>

    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-slate-900 text-white mb-14 md:mb-0 py-6 text-center md:mt-8">
    <p class="text-sm">© 2025 Dietly - Semua Hak Dilindungi</p>
  </footer>

  <script>
    function previewImage(input, previewId) {
      var preview = document.getElementById(previewId);
      preview.innerHTML = '';
      
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          var img = document.createElement('img');
          img.src = e.target.result;
          img.className = 'preview-img rounded-lg border-2 border-emerald-200 mt-2';
          preview.appendChild(img);
        };
        reader.readAsDataURL(input.files[0]);
      }
    }
  </script>

</body>
</html>
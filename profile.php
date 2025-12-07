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

// Get pesan
$pesan = get_pesan();

// PERBAIKAN: Hitung progress dengan benar
// Berat awal = berat_badan dari tabel pengguna (tetap)
$berat_awal = $pengguna['berat_badan'];
$berat_target = $pengguna['berat_target'];

// Get tracking terakhir untuk berat sekarang
$query_tracking = "SELECT berat_badan FROM tracking_harian 
                   WHERE id_pengguna = '$id_pengguna' 
                   ORDER BY tanggal_tracking DESC LIMIT 1";
$result_tracking = mysqli_query($koneksi, $query_tracking);

$berat_sekarang = $berat_awal; // Default ke berat awal
if (mysqli_num_rows($result_tracking) > 0) {
    $tracking = mysqli_fetch_assoc($result_tracking);
    $berat_sekarang = $tracking['berat_badan'];
}

$selisih_berat = $berat_sekarang - $berat_awal;
$target_perubahan = abs($berat_target - $berat_awal);
$progress_persen = 0;

if ($target_perubahan > 0) {
    $progress_persen = (abs($selisih_berat) / $target_perubahan) * 100;
    $progress_persen = min(100, max(0, round($progress_persen)));
}

// Tentukan nama tujuan diet
$nama_tujuan = array(
    'lose' => 'Turunkan Berat',
    'maintain' => 'Jaga Berat',
    'gain' => 'Naikkan Berat'
);
$tujuan_text = isset($nama_tujuan[$pengguna['tujuan_diet']]) ? $nama_tujuan[$pengguna['tujuan_diet']] : 'Belum ditentukan';

// Avatar URL
$avatar_url = $pengguna['foto_profil'] ? $pengguna['foto_profil'] : 'https://ui-avatars.com/api/?name=' . urlencode($pengguna['nama_lengkap']) . '&background=10b981&color=fff';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1, viewport-fit=cover"/>
  <title>Profil Pengguna - Dietly</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
  </style>
</head>
<body class="bg-slate-50 text-slate-800 pb-28">
  
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
        <a href="profile.php" class="px-3 py-2 text-emerald-600 font-semibold rounded">Profil</a>
        <a href="progres.php" class="px-3 py-2 rounded hover:text-emerald-600">Progress</a>
        <a href="resep.php" class="px-3 py-2 rounded hover:text-emerald-600">Resep</a>
        <a href="resep-tersimpan.php" class="px-3 py-2 rounded hover:text-emerald-600">Tersimpan</a>
        <form method="POST" action="auth.php" class="inline">
          <input type="hidden" name="aksi" value="logout">
          <button type="submit" class="ml-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Logout</button>
        </form>
      </nav>
    </div>
  </header>

  <main class="max-w-md md:max-w-5xl lg:max-w-7xl mx-auto px-4 pt-6 md:pt-24 space-y-6">

    <!-- Profile Card -->
    <div class="bg-white rounded-3xl p-6 md:px-24 shadow-sm border border-slate-100 relative overflow-hidden">
      
      <!-- Form Update Foto -->
      <form method="POST" action="proses_profil.php" enctype="multipart/form-data" id="formFoto">
        <input type="hidden" name="aksi" value="update_foto">
        <input id="avatarInput" type="file" name="foto_profil" accept="image/*" class="hidden" onchange="this.form.submit()" />
      </form>

      <div class="flex items-start gap-4 mb-6">
        <div class="relative group cursor-pointer" onclick="document.getElementById('avatarInput').click()">
          <img src="<?php echo $avatar_url; ?>" alt="Avatar" class="w-20 h-20 rounded-full object-cover border-4 border-slate-50 shadow-md bg-slate-200">
          <div class="absolute -bottom-1 -right-1 bg-yellow-400 text-[10px] font-bold px-2 py-0.5 rounded-full text-white border-2 border-white">PRO</div>
          <div class="absolute inset-0 bg-black/30 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
          </div>
        </div>
        
        <div class="flex-1 min-w-0">
          <h2 class="font-bold text-slate-800 text-lg truncate cursor-pointer hover:text-emerald-600 border-b border-dashed border-transparent hover:border-emerald-300 inline-block" onclick="editNama()">
            <?php echo $pengguna['nama_lengkap']; ?>
          </h2>
          <div class="text-[10px] text-slate-400 mb-1">(Klik nama/foto untuk edit)</div>
          
          <div class="flex flex-wrap items-center gap-2 text-xs text-slate-600 mt-1">
            <span class="bg-slate-100 px-2 py-1 rounded-md">🎂 <?php echo $pengguna['usia']; ?> thn</span>
            <span class="bg-emerald-50 text-emerald-700 px-2 py-1 rounded-md font-medium">💪 <?php echo $tujuan_text; ?></span>
          </div>
        </div>
      </div>

      <div class="flex justify-between items-center border-t border-slate-100 pt-6 pb-4 md:pb-12 md:px-24">
        <div>
          <p class="text-xs text-slate-400 mb-1">BMI</p>
          <p class="text-2xl font-bold text-slate-800"><?php echo number_format($pengguna['bmi'], 1); ?></p>
        </div>
        <div>
          <p class="text-xs text-slate-400 mb-1">Tinggi</p>
          <p class="text-2xl font-bold text-slate-800"><?php echo $pengguna['tinggi_badan']; ?> cm</p>
        </div>
        <div>
          <p class="text-xs text-slate-400 mb-1">Berat Awal</p>
          <p class="text-2xl font-bold text-slate-800"><?php echo $berat_awal; ?> kg</p>
        </div>
      </div>
    </div>

    <!-- Progress Section -->
    <section>
      <div class="flex justify-between items-end mb-3 px-1">
        <h3 class="font-bold text-slate-800 text-lg">Progress Saya</h3>
        <a href="progres.php" class="text-emerald-600 text-sm font-medium hover:underline">Analisis</a>
      </div>

      <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 text-center">
        <h4 class="text-lg font-bold text-slate-800">
          <?php if ($pengguna['tujuan_diet'] == 'lose'): ?>
            Kamu sudah turun <?php echo abs($selisih_berat); ?> kg!
          <?php elseif ($pengguna['tujuan_diet'] == 'gain'): ?>
            Kamu sudah naik <?php echo abs($selisih_berat); ?> kg!
          <?php else: ?>
            Pertahankan berat badanmu!
          <?php endif; ?>
        </h4>
        <p class="text-xs text-slate-400 mb-6">terus semangat sampai mencapai target</p>

        <div class="relative w-full h-3 bg-slate-100 rounded-full mb-2">
          <div class="absolute left-0 top-1/2 -translate-y-1/2 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full shadow z-10" style="left: <?php echo $progress_persen; ?>%"></div>
          <div class="h-full bg-emerald-500 rounded-full opacity-20" style="width: <?php echo $progress_persen; ?>%"></div>
          <div class="absolute right-0 top-1/2 -translate-y-1/2 w-3 h-3 bg-slate-200 rounded-full"></div>
        </div>

        <div class="flex justify-between text-xs font-semibold text-slate-700 mt-2">
          <span>Start: <?php echo $berat_awal; ?> kg</span>
          <span>Now: <?php echo $berat_sekarang; ?> kg</span>
          <span>Goal: <?php echo $berat_target; ?> kg</span>
        </div>
        
        <div class="mt-4 text-sm text-slate-600">
          <p>Progress: <strong class="text-emerald-600"><?php echo $progress_persen; ?>%</strong></p>
          <p class="text-xs text-slate-500 mt-1">
            Sisa <?php echo abs($berat_target - $berat_sekarang); ?> kg lagi untuk mencapai target!
          </p>
        </div>
      </div>
    </section>

    <!-- Goals Section -->
    <section>
      <div class="flex justify-center items-center mb-3 px-1 pt-8">
        <h3 class="font-bold text-slate-800 text-lg">Tujuan Saya</h3>
      </div>

      <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 space-y-3 mb-4">
        <div class="flex items-center gap-3">
          <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
          <span class="text-sm text-slate-700 flex-1">Tujuan</span>
          <span class="text-sm font-semibold text-slate-900"><?php echo $tujuan_text; ?></span>
        </div>
        <div class="flex items-center gap-3">
          <span class="w-2 h-2 rounded-full bg-blue-500"></span>
          <span class="text-sm text-slate-700 flex-1">Berat Awal</span>
          <span class="text-sm font-semibold text-slate-900"><?php echo $berat_awal; ?> kg</span>
        </div>
        <div class="flex items-center gap-3">
          <span class="w-2 h-2 rounded-full bg-purple-500"></span>
          <span class="text-sm text-slate-700 flex-1">Berat Sekarang</span>
          <span class="text-sm font-semibold text-slate-900"><?php echo $berat_sekarang; ?> kg</span>
        </div>
        <div class="flex items-center gap-3">
          <span class="w-2 h-2 rounded-full bg-orange-500"></span>
          <span class="text-sm text-slate-700 flex-1">Berat Target</span>
          <span class="text-sm font-semibold text-slate-900"><?php echo $berat_target; ?> kg</span>
        </div>
      </div>

      <button onclick="toggleEditForm()" class="w-full bg-emerald-600 text-white py-2 rounded-lg text-sm font-semibold shadow-md hover:bg-emerald-700">
        Edit Data
      </button>

      <!-- Form Edit -->
      <div id="editForm" class="hidden bg-slate-100 rounded-2xl p-4 border border-slate-200 mt-4">
        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Update Data Diri</h4>
        <form method="POST" action="proses_profil.php" class="grid grid-cols-1 gap-3">
          <input type="hidden" name="aksi" value="update_data_fisik">
          
          <div>
            <label class="block text-xs font-medium mb-1">Pilih Tujuan</label>
            <select name="tujuan_diet" class="w-full text-sm border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500">
              <option value="lose" <?php echo $pengguna['tujuan_diet'] == 'lose' ? 'selected' : ''; ?>>Turunkan Berat</option>
              <option value="maintain" <?php echo $pengguna['tujuan_diet'] == 'maintain' ? 'selected' : ''; ?>>Jaga Berat</option>
              <option value="gain" <?php echo $pengguna['tujuan_diet'] == 'gain' ? 'selected' : ''; ?>>Naikkan Berat</option>
            </select>
          </div>
          
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium mb-1">Tinggi (cm)</label>
              <input type="number" name="tinggi_badan" value="<?php echo $pengguna['tinggi_badan']; ?>" class="w-full text-sm rounded-lg p-2 border border-gray-300" required>
            </div>
            <div>
              <label class="block text-xs font-medium mb-1">Berat Awal (kg)</label>
              <input type="number" name="berat_badan" value="<?php echo $pengguna['berat_badan']; ?>" step="0.1" class="w-full text-sm rounded-lg p-2 border border-gray-300" required>
              <p class="text-[10px] text-amber-600 mt-1">⚠️ Berat awal sebagai referensi</p>
            </div>
          </div>
          
          <div>
            <label class="block text-xs font-medium mb-1">Target Berat (kg)</label>
            <input type="number" name="berat_target" value="<?php echo $pengguna['berat_target']; ?>" step="0.1" class="w-full text-sm rounded-lg p-2 border border-gray-300" required>
          </div>
          
          <button type="submit" class="w-full bg-emerald-600 text-white py-2 rounded-lg text-sm font-semibold mt-2 shadow-md hover:bg-emerald-700">
            Simpan Perubahan
          </button>
        </form>
      </div>
    </section>

  </main>

  <!-- Bottom Nav -->
  <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-100 z-40 pb-safe md:hidden">
    <div class="flex justify-between items-center px-6 py-3 max-w-md mx-auto">
      <a href="index.php" class="flex flex-col items-center text-slate-400 hover:text-emerald-600 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10.5L12 4l9 6.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1V10.5z" /></svg>
        <span class="text-[10px]">Beranda</span>
      </a>
      <a href="resep.php" class="flex flex-col items-center text-slate-400 hover:text-emerald-600 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20h9M3 20h9M3 4h6v16H3z" /></svg>
        <span class="text-[10px]">Resep</span>
      </a>
      <a href="progres.php" class="flex flex-col items-center text-slate-400 hover:text-emerald-600 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3v18M4 12v6m14-10v10" /></svg>
        <span class="text-[10px]">Progress</span>
      </a>
      <a href="profile.php" class="flex flex-col items-center text-emerald-600 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" /></svg>
        <span class="text-[10px] font-medium">Profil</span>
      </a>
    </div>
  </nav>

  <script>
    function editNama() {
      var namaBaru = prompt('Ubah nama pengguna:', '<?php echo $pengguna['nama_lengkap']; ?>');
      if (namaBaru && namaBaru.trim()) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'proses_profil.php';
        
        var inputAksi = document.createElement('input');
        inputAksi.type = 'hidden';
        inputAksi.name = 'aksi';
        inputAksi.value = 'update_nama';
        
        var inputNama = document.createElement('input');
        inputNama.type = 'hidden';
        inputNama.name = 'nama_baru';
        inputNama.value = namaBaru.trim();
        
        form.appendChild(inputAksi);
        form.appendChild(inputNama);
        document.body.appendChild(form);
        form.submit();
      }
    }

    function toggleEditForm() {
      var form = document.getElementById('editForm');
      form.classList.toggle('hidden');
    }
  </script>

</body>
</html>
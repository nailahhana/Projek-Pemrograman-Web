<?php
<<<<<<< HEAD
require_once 'koneksi.php';
require_once 'auth.php';

// Cek apakah sudah login
$sudah_login = isset($_SESSION['sudah_login']) && $_SESSION['sudah_login'] === true;
$nama_pengguna = $sudah_login ? $_SESSION['nama_pengguna'] : 'Nama Pengguna';

// Get pesan jika ada
$pesan = get_pesan();

// Get testimoni
$query_testimoni = "SELECT t.*, p.nama_lengkap, p.foto_profil 
                    FROM testimoni t 
                    JOIN pengguna p ON t.id_pengguna = p.id_pengguna 
                    WHERE t.status_tampil = 'aktif' 
                    ORDER BY t.tanggal_dibuat DESC 
                    LIMIT 3";
$result_testimoni = mysqli_query($koneksi, $query_testimoni);
=======
require_once 'config.php';

// Get user data if logged in
$userData = null;
if (isLoggedIn()) {
    $stmt = $conn->prepare("SELECT * FROM pengguna WHERE email = ?");
    $stmt->execute([getUserEmail()]);
    $userData = $stmt->fetch();
}
>>>>>>> 68c21fb3a2745eb75aa2501828cd1e660657b85c
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dietly - Plan diet suksesmu</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; }
    body {
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
      margin: 0;
    }
    .safe-bottom { padding-bottom: env(safe-area-inset-bottom); }
  </style>
</head>
<body class="bg-[linear-gradient(0deg,#f8fafc,white)] text-slate-800">
  
<<<<<<< HEAD
  <?php if ($pesan): ?>
  <div id="alertPesan" class="fixed top-4 right-4 z-50 bg-<?php echo $pesan['tipe'] == 'success' ? 'green' : ($pesan['tipe'] == 'error' ? 'red' : 'yellow'); ?>-100 border border-<?php echo $pesan['tipe'] == 'success' ? 'green' : ($pesan['tipe'] == 'error' ? 'red' : 'yellow'); ?>-400 text-<?php echo $pesan['tipe'] == 'success' ? 'green' : ($pesan['tipe'] == 'error' ? 'red' : 'yellow'); ?>-700 px-4 py-3 rounded-lg shadow-lg max-w-md">
    <p class="font-bold"><?php echo $pesan['tipe'] == 'success' ? '✓' : ($pesan['tipe'] == 'error' ? '✗' : '⚠'); ?> <?php echo ucfirst($pesan['tipe']); ?></p>
    <p class="text-sm"><?php echo $pesan['isi']; ?></p>
  </div>
  <script>
    setTimeout(function() {
      document.getElementById('alertPesan').style.display = 'none';
    }, 5000);
  </script>
  <?php endif; ?>
  
=======
>>>>>>> 68c21fb3a2745eb75aa2501828cd1e660657b85c
  <!-- DESKTOP NAVBAR -->
  <header class="fixed top-0 left-0 right-0 bg-white/40 backdrop-blur z-30 shadow-sm hidden md:block">
    <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">
      <a href="index.php" class="flex items-center gap-3">
        <h1 class="text-2xl font-bold text-emerald-600">Dietly</h1>
      </a>
      <nav class="flex items-center gap-4">
        <a href="index.php" class="px-3 py-2 text-emerald-600 font-semibold rounded">Beranda</a>
<<<<<<< HEAD
        <a href="profile.php" class="px-3 py-2 rounded hover:text-emerald-600">Profil</a>
        <a href="progres.php" class="px-3 py-2 rounded hover:text-emerald-600">Progress</a>
        <a href="resep.php" class="px-3 py-2 rounded hover:text-emerald-600">Resep</a>
        <a href="resep-tersimpan.php" class="px-3 py-2 rounded hover:text-emerald-600">Resep Tersimpan</a>
        <a href="#testimoni" class="px-3 py-2 rounded hover:text-emerald-600">Testimoni</a>
        
        <?php if ($sudah_login): ?>
          <form method="POST" action="auth.php" class="inline">
            <input type="hidden" name="aksi" value="logout">
            <button type="submit" class="ml-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Logout</button>
          </form>
=======
        <?php if ($userData): ?>
          <a href="profile.php" class="px-3 py-2 rounded hover:text-emerald-600">Profil</a>
          <a href="progres.php" class="px-3 py-2 rounded hover:text-emerald-600">Progress</a>
          <a href="resep.php" class="px-3 py-2 rounded hover:text-emerald-600">Resep</a>
          <a href="#testimoni" class="px-3 py-2 rounded hover:text-emerald-600">Testimoni</a>
          <button data-logout-btn class="ml-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">Logout</button>
>>>>>>> 68c21fb3a2745eb75aa2501828cd1e660657b85c
        <?php else: ?>
          <a href="#auth" class="ml-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">Login</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <!-- MOBILE BOTTOM NAV -->
  <nav class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur border-t z-30 md:hidden safe-bottom">
    <div class="max-w-6xl mx-auto">
      <div class="flex justify-between items-center">
        <a href="index.php" class="w-full py-2 flex flex-col items-center justify-center text-emerald-600">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10.5L12 4l9 6.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1V10.5z" />
          </svg>
          <span class="text-xs">Beranda</span>
        </a>
<<<<<<< HEAD
=======
        <?php if ($userData): ?>
>>>>>>> 68c21fb3a2745eb75aa2501828cd1e660657b85c
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
<<<<<<< HEAD
=======
        <?php endif; ?>
        <a href="#auth" class="w-full py-2 flex flex-col items-center justify-center text-slate-700 hover:text-emerald-600">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H3m12 0l-4-4m4 4l-4 4M20 8v6M23 11h-6" />
          </svg>
          <span class="text-xs"><?php echo $userData ? 'Logout' : 'Login'; ?></span>
        </a>
>>>>>>> 68c21fb3a2745eb75aa2501828cd1e660657b85c
      </div>
    </div>
  </nav>

  <!-- MAIN CONTENT -->
<<<<<<< HEAD
  <main class="pb-16 md:pb-2 bg-emerald-400 shadow-lg">
=======
  <main class="pb-16 md:pb-2">
    
>>>>>>> 68c21fb3a2745eb75aa2501828cd1e660657b85c
    <!-- HERO SECTION -->
    <section id="home" class="bg-[url(BGLandingPage.jpg)] bg-cover bg-center shadow-lg pb-10 rounded-b-3xl md:pt-20 md:pb-6">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center md:justify-center gap-4">
<<<<<<< HEAD
          <!-- KONTEN SEBELAH KIRI (desktop) -->
          <article class="hidden md:block md:max-w-xl lg:max-w-2xl text-left">
            <h2 class="text-3xl md:text-3xl lg:text-5xl font-extrabold leading-tight text-shadow-2xl">Mulai Perjalanan Sehatmu dengan Cara yang Fun ✨</h2>
            <p class="mt-6 text-slate-700 text-base lg:text-lg text-shadow-2xl">
              Dietly bantu kamu rancang pola makan, catat progress, dan beri rekomendasi diet berdasarkan BMI secara otomatis.
            </p>
            <div class="hidden md:flex mt-5 gap-4">
              <?php if (!$sudah_login): ?>
                <a href="splash.php" class="px-8 py-3 rounded-3xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition shadow-md">Sign Up</a>
=======
          
          <!-- DESKTOP CONTENT -->
          <article class="hidden md:block md:max-w-xl lg:max-w-2xl text-left">
            <h2 class="text-3xl md:text-3xl lg:text-5xl font-extrabold leading-tight">
              Mulai Perjalanan Sehatmu dengan Cara yang Fun ✨
            </h2>
            <p class="mt-6 text-slate-700 text-base lg:text-lg">
              Dietly bantu kamu rancang pola makan, catat progress, dan beri rekomendasi diet berdasarkan BMI secara otomatis.
            </p>
            <div class="hidden md:flex mt-5 gap-4">
              <?php if (!$userData): ?>
              <a href="#auth" class="px-8 py-3 rounded-3xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition shadow-md">Sign Up</a>
              <?php else: ?>
              <a href="profile.php" class="px-8 py-3 rounded-3xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition shadow-md">Lihat Profil</a>
>>>>>>> 68c21fb3a2745eb75aa2501828cd1e660657b85c
              <?php endif; ?>
              <a href="#fitur" class="px-6 py-3 rounded-3xl border-2 border-emerald-600 text-emerald-600 font-semibold bg-white hover:bg-emerald-600 hover:text-white transition shadow-md">Lihat Fitur</a>
            </div>
          </article>

<<<<<<< HEAD
          <!-- KONTEN SEBELAH KANAN (desktop) -->
=======
>>>>>>> 68c21fb3a2745eb75aa2501828cd1e660657b85c
          <div class="hidden md:block md:flex-shrink-0">
            <img src="Yoga1.png" class="md:w-[400px] lg:w-[500px] h-auto object-contain" alt="Hero">
          </div>

          <!-- MOBILE ELEMENTS -->
          <div class="w-full px-8 pt-4 md:hidden">
<<<<<<< HEAD
            <div class="w-full mb-3 flex items-center justify-between text-shadow-5xl">
              <div>
                <p class="text-base font-semibold"><?php echo $nama_pengguna; ?></p>
                <p class="text-xs text-slate-500">Depok, Yogyakarta</p>
=======
            <?php if ($userData): ?>
            <div class="w-full mb-3 flex items-center justify-between">
              <div>
                <p class="text-base font-semibold" data-user-name><?php echo htmlspecialchars($userData['nama']); ?></p>
                <p class="text-xs text-slate-500">Jakarta, Indonesia</p>
>>>>>>> 68c21fb3a2745eb75aa2501828cd1e660657b85c
              </div>
              <a href="profile.php" class="p-1.5 rounded-full bg-emerald-50">
                <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <circle cx="12" cy="8" r="3" stroke-width="1.5"/>
                  <path d="M6 20c1.5-3 4.5-4 6-4s4.5 1 6 4" stroke-width="1.5"/>
<<<<<<< HEAD
              </svg>
              </a>
            </div>

            <article class="bg-emerald-600 rounded-2xl min-h-40 flex flex-row items-start px-8 pt-8 pb-0 mb-0 shadow-lg relative overflow-visible">
              <div class="flex-1 text-white mb-4 pr-28">
                <h3 class="font-semibold text-lg leading-snug">Selamat Datang!</h3>
                <p class="text-sm opacity-90 mt-1">Mulai perjalanan sehatmu hari ini</p>
              </div>
              <div class="absolute bottom-0 right-0 w-28 h-28 overflow-hidden transform -translate-x-8 -translate-y-6">
                <img src="salad.png" alt="illustration" class="w-full h-full object-cover">
=======
                </svg>
              </a>
            </div>
            <?php endif; ?>

            <article class="bg-emerald-600 rounded-2xl min-h-40 flex flex-row items-start px-8 pt-8 pb-0 mb-0 shadow-lg relative overflow-visible">
              <div class="flex-1 text-white mb-4 pr-28">
                <h3 id="heroMobileText" class="font-semibold text-lg leading-snug">Special Offer</h3>
                <p id="heroMobileSub" class="text-sm opacity-90 mt-1">Join Dietly today!</p>
              </div>
              <div class="absolute bottom-0 right-0 w-28 h-28 overflow-hidden transform -translate-x-8 -translate-y-6">
                <img id="heroMobileImg" src="salad.png" alt="illustration" class="w-full h-full object-cover">
>>>>>>> 68c21fb3a2745eb75aa2501828cd1e660657b85c
              </div>
            </article>
          </div>
        </div>
      </div>
    </section>

    <!-- FEATURE GRID -->
    <section id="fitur" class="bg-[url(BGFitur.jpg)] bg-cover bg-center shadow-lg py-20">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-center mb-8">Fitur Utama</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
<<<<<<< HEAD
          <article class="bg-white p-6 rounded-2xl shadow hover:shadow-lg transition">
            <h3 class="text-xl font-semibold">Profil Pengguna & BMI</h3>
            <p class="mt-2 text-slate-600">Isi nama, umur, tinggi, berat – kalkulasi BMI & rekomendasi plan diet otomatis.</p>
            <a href="profile.php" class="mt-4 inline-block text-sm text-emerald-600 font-medium hover:text-emerald-700">Buka fitur →</a>
          </article>
          <article class="bg-white p-6 rounded-2xl shadow hover:shadow-lg transition">
            <h3 class="text-xl font-semibold">Progress Tracker</h3>
            <p class="mt-2 text-slate-600">Catat berat harian, upload bukti, dan pantau persentase progres program dietmu.</p>
            <a href="progres.php" class="mt-4 inline-block text-sm text-emerald-600 font-medium hover:text-emerald-700">Buka fitur →</a>
          </article>
=======
          
          <article class="bg-white p-6 rounded-2xl shadow hover:shadow-lg transition">
            <h3 class="text-xl font-semibold">Profil Pengguna & BMI</h3>
            <p class="mt-2 text-slate-600">Isi nama, umur, tinggi, berat – kalkulasi BMI & rekomendasi plan diet otomatis.</p>
            <a href="<?php echo $userData ? 'profile.php' : '#auth'; ?>" class="mt-4 inline-block text-sm text-emerald-600 font-medium hover:text-emerald-700">Buka fitur →</a>
          </article>
          
          <article class="bg-white p-6 rounded-2xl shadow hover:shadow-lg transition">
            <h3 class="text-xl font-semibold">Progress Tracker</h3>
            <p class="mt-2 text-slate-600">Catat berat harian, upload bukti, dan pantau persentase progres program dietmu.</p>
            <a href="<?php echo $userData ? 'progres.php' : '#auth'; ?>" class="mt-4 inline-block text-sm text-emerald-600 font-medium hover:text-emerald-700">Buka fitur →</a>
          </article>
          
>>>>>>> 68c21fb3a2745eb75aa2501828cd1e660657b85c
          <article class="bg-white p-6 rounded-2xl shadow hover:shadow-lg transition">
            <h3 class="text-xl font-semibold">Resep & Ide Menu</h3>
            <p class="mt-2 text-slate-600">Kumpulan resep & ide menu yang sesuai dengan target kalori dan protein.</p>
            <a href="resep.php" class="mt-4 inline-block text-sm text-emerald-600 font-medium hover:text-emerald-700">Buka fitur →</a>
          </article>
<<<<<<< HEAD
        </div>

        <!-- TESTIMONIALS -->
        <div id="testimoni" class="mt-16">
          <h2 class="text-2xl font-bold text-center mb-6">Apa Kata Mereka</h2>
          <div class="flex flex-col md:flex-row gap-6 items-stretch justify-center">
            <?php while ($testimoni = mysqli_fetch_assoc($result_testimoni)): ?>
            <article class="w-full md:w-80 p-6 rounded-2xl bg-white shadow-lg text-left">
              <div class="flex items-center gap-4">
                <?php if ($testimoni['foto_profil']): ?>
                  <img src="<?php echo $testimoni['foto_profil']; ?>" class="w-14 h-14 rounded-full border object-cover" alt="<?php echo $testimoni['nama_lengkap']; ?>">
                <?php else: ?>
                  <div class="w-14 h-14 rounded-full border bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-xl">
                    <?php echo strtoupper(substr($testimoni['nama_lengkap'], 0, 1)); ?>
                  </div>
                <?php endif; ?>
                <div>
                  <p class="font-semibold"><?php echo $testimoni['nama_lengkap']; ?></p>
                  <p class="text-sm text-slate-500">Pengguna Dietly</p>
                </div>
              </div>
              <p class="mt-4 text-slate-700"><?php echo $testimoni['isi_testimoni']; ?></p>
              <?php if ($testimoni['penurunan_berat']): ?>
                <p class="mt-2 text-xs text-emerald-600 font-medium">Turun <?php echo $testimoni['penurunan_berat']; ?> kg dalam <?php echo $testimoni['durasi']; ?></p>
              <?php endif; ?>
            </article>
            <?php endwhile; ?>
=======
          
        </div>

        <!-- TESTIMONIALS -->
        <div class="mt-16" id="testimoni">
          <h2 class="text-2xl font-bold text-center mb-6">Apa Kata Mereka</h2>
          <div class="flex flex-col md:flex-row gap-6 items-stretch justify-center">
            
            <article class="w-full md:w-80 p-6 rounded-2xl bg-white shadow-lg text-left">
              <div class="flex items-center gap-4">
                <img src="LAHAR.jpeg" class="w-14 h-14 rounded-full border" alt="Lahar">
                <div>
                  <p class="font-semibold">Lahar</p>
                  <p class="text-sm text-slate-500">Pengguna Dietly</p>
                </div>
              </div>
              <p class="mt-4 text-slate-700">Dietly bantu aku konsisten! Dalam 2 bulan turun 4 kg.</p>
            </article>

            <article class="w-full md:w-80 p-6 rounded-2xl bg-white shadow-lg text-left">
              <div class="flex items-center gap-4">
                <img src="NOPAL.jpeg" class="w-14 h-14 rounded-full border" alt="Nopal">
                <div>
                  <p class="font-semibold">Nopal</p>
                  <p class="text-sm text-slate-500">Pengguna Dietly</p>
                </div>
              </div>
              <p class="mt-4 text-slate-700">Fitur progressnya gampang dipakai, recommended.</p>
            </article>

            <article class="w-full md:w-80 p-6 rounded-2xl bg-white shadow-lg text-left">
              <div class="flex items-center gap-4">
                <img src="ARYA.jpeg" class="w-14 h-14 rounded-full border" alt="Arya">
                <div>
                  <p class="font-semibold">Arya</p>
                  <p class="text-sm text-slate-500">Pengguna Dietly</p>
                </div>
              </div>
              <p class="mt-4 text-slate-700">Fitur nya ngejaga motivasi gw, biar tetep konsisten dengan plan dietnya.</p>
            </article>
            
>>>>>>> 68c21fb3a2745eb75aa2501828cd1e660657b85c
          </div>
        </div>
      </div>
    </section>

    <!-- AUTH SECTION -->
<<<<<<< HEAD
    <?php if (!$sudah_login): ?>
    <section id="auth" class="py-16 bg-white shadow-lg">
=======
    <?php if (!$userData): ?>
    <section id="auth" class="py-16 shadow-lg">
>>>>>>> 68c21fb3a2745eb75aa2501828cd1e660657b85c
      <div class="max-w-4xl mx-auto px-6">
        <div class="bg-white p-6 rounded-2xl shadow">
          <div class="flex gap-4 items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">Login / Sign Up</h2>
<<<<<<< HEAD
          </div>
          
          <form method="POST" action="auth.php">
            <input type="email" name="email" placeholder="Email" required class="w-full p-3 rounded border mb-3">
            <input type="password" name="kata_sandi" placeholder="Password" required class="w-full p-3 rounded border mb-3">
            
            <div class="flex items-center mb-3">
              <input type="checkbox" name="ingat_saya" id="ingatSaya" class="mr-2">
              <label for="ingatSaya" class="text-sm text-slate-600">Ingat saya</label>
            </div>
            
            <div class="flex gap-2 flex-wrap">
              <button type="submit" name="aksi" value="register" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 transition whitespace-nowrap">Buat Akun</button>
              <button type="submit" name="aksi" value="login" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 transition">Login</button>
            </div>
          </form>
          
          <div class="mt-4 text-center">
            <p class="text-sm text-slate-500">Belum punya akun? <a href="splash.php" class="text-emerald-600 hover:underline">Daftar di sini</a></p>
          </div>
=======
            <p class="text-sm text-slate-500">Buat akun atau masuk</p>
          </div>
          
          <form id="authForm" onsubmit="return false;">
            <input type="email" name="email" placeholder="Email" required class="w-full p-3 rounded border mb-3">
            <input type="password" name="password" placeholder="Password" required class="w-full p-3 rounded border mb-3">
            <div class="flex gap-2 flex-wrap">
              <button type="button" data-register-btn class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 transition whitespace-nowrap">Buat Akun</button>
              <button type="button" data-login-btn class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 transition">Login</button>
            </div>
          </form>
>>>>>>> 68c21fb3a2745eb75aa2501828cd1e660657b85c
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- FOOTER -->
    <footer class="mt-8 py-6">
<<<<<<< HEAD
      <p class="max-w-6xl mx-auto px-6 text-center text-slate-600">© 2025 Dietly - Semua Hak Dilindungi.</p>
    </footer>
  </main>

=======
      <p class="max-w-6xl mx-auto px-6 text-center text-slate-600">© 2025 Dietly – Powered by PHP & MySQL</p>
    </footer>
    
  </main>

  <!-- Load JavaScript -->
  <script src="dietly.js"></script>
  
>>>>>>> 68c21fb3a2745eb75aa2501828cd1e660657b85c
</body>
</html>
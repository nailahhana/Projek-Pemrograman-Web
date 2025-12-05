<?php
require_once 'koneksi.php';
require_once 'auth.php';

// Cek apakah admin
cek_admin();

$nama_admin = $_SESSION['nama_admin'];
$email_admin = $_SESSION['email_admin'];

// Get statistik
$query_total_pengguna = "SELECT COUNT(*) as total FROM pengguna";
$result_pengguna = mysqli_query($koneksi, $query_total_pengguna);
$total_pengguna = mysqli_fetch_assoc($result_pengguna)['total'];

$query_program_aktif = "SELECT COUNT(*) as total FROM program_diet WHERE status = 'aktif'";
$result_program = mysqli_query($koneksi, $query_program_aktif);
$program_aktif = mysqli_fetch_assoc($result_program)['total'];

$query_total_resep = "SELECT COUNT(*) as total FROM resep";
$result_resep = mysqli_query($koneksi, $query_total_resep);
$total_resep = mysqli_fetch_assoc($result_resep)['total'];

$query_tracking_hari_ini = "SELECT COUNT(*) as total FROM tracking_harian WHERE DATE(tanggal_tracking) = CURDATE()";
$result_tracking = mysqli_query($koneksi, $query_tracking_hari_ini);
$tracking_hari_ini = mysqli_fetch_assoc($result_tracking)['total'];

// Get pengguna terbaru (5 terakhir)
$query_pengguna_baru = "SELECT * FROM pengguna ORDER BY tanggal_daftar DESC LIMIT 5";
$result_pengguna_baru = mysqli_query($koneksi, $query_pengguna_baru);

// Get pesan
$pesan = get_pesan();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Dietly</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; }
  </style>
</head>
<body class="bg-slate-50">

  <?php if ($pesan): ?>
  <div id="alertPesan" class="fixed top-4 right-4 z-50 bg-<?php echo $pesan['tipe'] == 'success' ? 'green' : 'red'; ?>-100 border border-<?php echo $pesan['tipe'] == 'success' ? 'green' : 'red'; ?>-400 text-<?php echo $pesan['tipe'] == 'success' ? 'green' : 'red'; ?>-700 px-4 py-3 rounded-lg shadow-lg max-w-md">
    <p class="font-bold"><?php echo $pesan['isi']; ?></p>
  </div>
  <script>
    setTimeout(function() {
      document.getElementById('alertPesan').style.display = 'none';
    }, 5000);
  </script>
  <?php endif; ?>

  <!-- Sidebar -->
  <div class="flex h-screen">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-white flex flex-col">
      <div class="p-6 border-b border-slate-800">
        <h1 class="text-2xl font-bold text-emerald-400">Dietly Admin</h1>
        <p class="text-xs text-slate-400 mt-1">Administrator Panel</p>
      </div>
      
      <nav class="flex-1 p-4 space-y-2">
        <a href="admin-dashboard.php" class="block px-4 py-3 bg-emerald-600 rounded-lg font-medium">
          📊 Dashboard
        </a>
        <a href="#users" class="block px-4 py-3 hover:bg-slate-800 rounded-lg">
          👥 Users
        </a>
        <a href="#recipes" class="block px-4 py-3 hover:bg-slate-800 rounded-lg">
          🍽️ Recipes
        </a>
        <a href="#analytics" class="block px-4 py-3 hover:bg-slate-800 rounded-lg">
          📈 Analytics
        </a>
        <a href="#settings" class="block px-4 py-3 hover:bg-slate-800 rounded-lg">
          ⚙️ Settings
        </a>
      </nav>
      
      <div class="p-4 border-t border-slate-800">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-10 h-10 bg-emerald-600 rounded-full flex items-center justify-center">
            <span class="font-bold"><?php echo strtoupper(substr($nama_admin, 0, 1)); ?></span>
          </div>
          <div class="flex-1">
            <p class="text-sm font-medium"><?php echo $nama_admin; ?></p>
            <p class="text-xs text-slate-400">Administrator</p>
          </div>
        </div>
        <form method="POST" action="auth.php">
          <input type="hidden" name="aksi" value="logout">
          <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg text-sm font-medium transition">
            Logout
          </button>
        </form>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto">
      
      <!-- Header -->
      <header class="bg-white border-b border-slate-200 px-8 py-4">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-bold text-slate-800">Dashboard</h2>
            <p class="text-sm text-slate-600">Selamat datang kembali, <?php echo $nama_admin; ?>!</p>
          </div>
          <div class="flex items-center gap-4">
            <button class="relative p-2 hover:bg-slate-100 rounded-lg">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
              </svg>
              <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>
          </div>
        </div>
      </header>

      <!-- Dashboard Content -->
      <div class="p-8">
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-2">
              <p class="text-sm text-slate-600">Total Users</p>
              <span class="text-2xl">👥</span>
            </div>
            <p class="text-3xl font-bold text-slate-800"><?php echo $total_pengguna; ?></p>
            <p class="text-xs text-emerald-600 mt-2">Pengguna terdaftar</p>
          </div>

          <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-2">
              <p class="text-sm text-slate-600">Active Programs</p>
              <span class="text-2xl">📊</span>
            </div>
            <p class="text-3xl font-bold text-slate-800"><?php echo $program_aktif; ?></p>
            <p class="text-xs text-emerald-600 mt-2">Program diet aktif</p>
          </div>

          <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-2">
              <p class="text-sm text-slate-600">Total Recipes</p>
              <span class="text-2xl">🍽️</span>
            </div>
            <p class="text-3xl font-bold text-slate-800"><?php echo $total_resep; ?></p>
            <p class="text-xs text-slate-600 mt-2">Resep tersedia</p>
          </div>

          <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-2">
              <p class="text-sm text-slate-600">Tracking Hari Ini</p>
              <span class="text-2xl">🎯</span>
            </div>
            <p class="text-3xl font-bold text-slate-800"><?php echo $tracking_hari_ini; ?></p>
            <p class="text-xs text-emerald-600 mt-2">Log aktivitas hari ini</p>
          </div>
        </div>

        <!-- Recent Activity & User Table -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          
          <!-- Recent Users -->
          <div class="bg-white rounded-xl shadow">
            <div class="p-6 border-b border-slate-200">
              <h3 class="text-lg font-bold text-slate-800">Pengguna Terbaru</h3>
            </div>
            <div class="p-6">
              <div class="space-y-4">
                <?php while ($pengguna = mysqli_fetch_assoc($result_pengguna_baru)): ?>
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                      <span class="font-bold text-emerald-600"><?php echo strtoupper(substr($pengguna['nama_lengkap'], 0, 1)); ?></span>
                    </div>
                    <div>
                      <p class="font-medium text-slate-800"><?php echo $pengguna['nama_lengkap']; ?></p>
                      <p class="text-xs text-slate-500"><?php echo $pengguna['email']; ?></p>
                    </div>
                  </div>
                  <span class="text-xs text-slate-500"><?php echo format_tanggal($pengguna['tanggal_daftar']); ?></span>
                </div>
                <?php endwhile; ?>
              </div>
              
              <button class="w-full mt-4 px-4 py-2 border border-slate-200 rounded-lg text-sm font-medium hover:bg-slate-50 transition">
                View All Users
              </button>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="bg-white rounded-xl shadow">
            <div class="p-6 border-b border-slate-200">
              <h3 class="text-lg font-bold text-slate-800">Quick Actions</h3>
            </div>
            <div class="p-6">
              <div class="space-y-3">
                <button class="w-full px-4 py-3 bg-emerald-50 text-emerald-700 rounded-lg font-medium hover:bg-emerald-100 transition text-left flex items-center gap-3">
                  <span class="text-xl">➕</span>
                  <span>Add New Recipe</span>
                </button>

                <button class="w-full px-4 py-3 bg-blue-50 text-blue-700 rounded-lg font-medium hover:bg-blue-100 transition text-left flex items-center gap-3">
                  <span class="text-xl">📧</span>
                  <span>Send Newsletter</span>
                </button>

                <button class="w-full px-4 py-3 bg-purple-50 text-purple-700 rounded-lg font-medium hover:bg-purple-100 transition text-left flex items-center gap-3">
                  <span class="text-xl">📊</span>
                  <span>Generate Report</span>
                </button>

                <button class="w-full px-4 py-3 bg-orange-50 text-orange-700 rounded-lg font-medium hover:bg-orange-100 transition text-left flex items-center gap-3">
                  <span class="text-xl">⚙️</span>
                  <span>System Settings</span>
                </button>
              </div>
            </div>
          </div>

        </div>

        <!-- System Info -->
        <div class="mt-6 bg-slate-800 text-white rounded-xl shadow p-6">
          <h3 class="text-lg font-bold mb-4">System Information</h3>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
              <p class="text-slate-400">Database Status</p>
              <p class="font-semibold text-emerald-400">● Active</p>
            </div>
            <div>
              <p class="text-slate-400">PHP Version</p>
              <p class="font-semibold"><?php echo phpversion(); ?></p>
            </div>
            <div>
              <p class="text-slate-400">Server Time</p>
              <p class="font-semibold"><?php echo date('H:i:s'); ?></p>
            </div>
            <div>
              <p class="text-slate-400">Total Tables</p>
              <p class="font-semibold">8 Tables</p>
            </div>
          </div>
        </div>

      </div>

    </main>

  </div>

  <script>
    // Simulated real-time updates
    setInterval(function() {
      console.log('Checking for updates...');
    }, 30000);
  </script>

</body>
</html>
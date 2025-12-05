<?php
require_once 'koneksi.php';

// Jika sudah login sebagai admin, redirect ke dashboard
if (isset($_SESSION['sudah_login']) && $_SESSION['tipe_pengguna'] == 'admin') {
    redirect('admin-dashboard.php');
}

// Get pesan
$pesan = get_pesan();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login - Dietly</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; }
  </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-900 min-h-screen flex items-center justify-center p-4">

  <div class="w-full max-w-md">
    
    <?php if ($pesan): ?>
    <div class="mb-4 bg-<?php echo $pesan['tipe'] == 'success' ? 'green' : 'red'; ?>-100 border border-<?php echo $pesan['tipe'] == 'success' ? 'green' : 'red'; ?>-400 text-<?php echo $pesan['tipe'] == 'success' ? 'green' : 'red'; ?>-700 px-4 py-3 rounded-lg">
      <p class="font-bold"><?php echo $pesan['tipe'] == 'success' ? '✓' : '✗'; ?> <?php echo $pesan['isi']; ?></p>
    </div>
    <?php endif; ?>
    
    <!-- Back Button -->
    <div class="mb-4">
      <a href="index.php" class="inline-flex items-center gap-2 text-white/70 hover:text-white transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
      </a>
    </div>

    <!-- Login Card -->
    <div class="bg-white rounded-3xl shadow-2xl p-8">
      
      <!-- Logo -->
      <div class="text-center mb-8">
        <div class="w-20 h-20 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
          <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
        </div>
        <h1 class="text-2xl font-bold text-slate-800">Admin Login</h1>
        <p class="text-slate-600 text-sm mt-2">Masuk ke dashboard administrator</p>
      </div>

      <!-- Login Form -->
      <form method="POST" action="auth.php" class="space-y-4">
        <input type="hidden" name="aksi" value="login_admin">
        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Email Admin</label>
          <input 
            type="email" 
            name="email_admin" 
            placeholder="admin@dietly.com"
            required
            class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-emerald-500 focus:outline-none"
          >
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Password</label>
          <input 
            type="password" 
            name="kata_sandi_admin" 
            placeholder="••••••••"
            required
            class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-emerald-500 focus:outline-none"
          >
        </div>

        <div class="flex items-center justify-between text-sm">
          <label class="flex items-center gap-2 text-slate-600">
            <input type="checkbox" name="ingat_saya" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
            <span>Ingat saya</span>
          </label>
          <a href="#" class="text-emerald-600 hover:text-emerald-700">Lupa password?</a>
        </div>

        <button 
          type="submit" 
          class="w-full bg-gradient-to-r from-emerald-600 to-emerald-700 text-white py-3 rounded-xl font-semibold hover:from-emerald-700 hover:to-emerald-800 transition shadow-lg"
        >
          Masuk sebagai Admin
        </button>

      </form>

      <!-- Demo Credentials -->
      <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-xl">
        <p class="text-xs text-amber-800 font-medium mb-2">🔑 Demo Credentials:</p>
        <p class="text-xs text-amber-700">Email: <code class="bg-amber-100 px-2 py-0.5 rounded">admin@dietly.com</code></p>
        <p class="text-xs text-amber-700">Password: <code class="bg-amber-100 px-2 py-0.5 rounded">admin123</code></p>
      </div>

    </div>

    <!-- Footer Note -->
    <p class="text-center text-white/50 text-xs mt-6">
      Akses terbatas hanya untuk administrator sistem
    </p>

  </div>

</body>
</html>
<?php
require_once 'koneksi.php';

// Determine error type from URL parameter or HTTP status
$error_code = isset($_GET['code']) ? $_GET['code'] : '404';
$error_message = isset($_GET['message']) ? $_GET['message'] : '';

// Error configurations
$errors = array(
    '403' => array(
        'title' => 'Akses Ditolak',
        'description' => 'Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.',
        'icon' => '🔒'
    ),
    '404' => array(
        'title' => 'Halaman Tidak Ditemukan',
        'description' => 'Maaf, halaman yang Anda cari tidak dapat ditemukan.',
        'icon' => '🔍'
    ),
    '500' => array(
        'title' => 'Kesalahan Server',
        'description' => 'Maaf, terjadi kesalahan pada server. Tim kami sedang memperbaikinya.',
        'icon' => '⚠️'
    ),
    'database' => array(
        'title' => 'Kesalahan Database',
        'description' => 'Tidak dapat terhubung ke database. Silakan coba lagi nanti.',
        'icon' => '🗄️'
    ),
    'session' => array(
        'title' => 'Sesi Berakhir',
        'description' => 'Sesi Anda telah berakhir. Silakan login kembali.',
        'icon' => '⏰'
    )
);

// Get error config or use default
$error = isset($errors[$error_code]) ? $errors[$error_code] : $errors['404'];

// Override with custom message if provided
if ($error_message) {
    $error['description'] = $error_message;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $error['title']; ?> - Dietly</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; }
    
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-20px); }
    }
    
    .float-animation {
      animation: float 3s ease-in-out infinite;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .fade-in {
      animation: fadeIn 0.6s ease-out forwards;
    }
  </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-white to-emerald-50 min-h-screen flex items-center justify-center p-4">

  <div class="max-w-2xl w-full text-center fade-in">
    
    <!-- Error Icon -->
    <div class="mb-8 float-animation">
      <div class="inline-block">
        <div class="text-9xl mb-4"><?php echo $error['icon']; ?></div>
        <div class="text-8xl font-bold text-slate-200"><?php echo $error_code; ?></div>
      </div>
    </div>
    
    <!-- Error Message -->
    <div class="mb-8">
      <h1 class="text-3xl md:text-4xl font-bold text-slate-800 mb-4">
        <?php echo $error['title']; ?>
      </h1>
      <p class="text-lg text-slate-600 mb-2">
        <?php echo $error['description']; ?>
      </p>
      
      <?php if ($error_code == '404'): ?>
        <p class="text-sm text-slate-500 mt-4">
          URL: <code class="bg-slate-100 px-2 py-1 rounded text-xs"><?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?></code>
        </p>
      <?php endif; ?>
    </div>
    
    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-8">
      <a href="javascript:history.back()" class="px-6 py-3 bg-slate-200 text-slate-700 rounded-xl font-semibold hover:bg-slate-300 transition inline-flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
      </a>
      
      <a href="index.php" class="px-6 py-3 bg-emerald-600 text-white rounded-xl font-semibold hover:bg-emerald-700 transition inline-flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Ke Beranda
      </a>
    </div>
    
    <!-- Help Section -->
    <div class="bg-white rounded-2xl shadow-lg p-6 text-left">
      <h2 class="text-lg font-bold text-slate-800 mb-3">Butuh Bantuan?</h2>
      <div class="space-y-3 text-sm text-slate-600">
        <div class="flex items-start gap-3">
          <span class="text-emerald-600 mt-1">✓</span>
          <div>
            <p class="font-medium text-slate-700">Cek URL Anda</p>
            <p class="text-xs">Pastikan alamat yang Anda masukkan sudah benar</p>
          </div>
        </div>
        
        <div class="flex items-start gap-3">
          <span class="text-emerald-600 mt-1">✓</span>
          <div>
            <p class="font-medium text-slate-700">Refresh Halaman</p>
            <p class="text-xs">Kadang refresh halaman dapat membantu</p>
          </div>
        </div>
        
        <div class="flex items-start gap-3">
          <span class="text-emerald-600 mt-1">✓</span>
          <div>
            <p class="font-medium text-slate-700">Hubungi Dukungan</p>
            <p class="text-xs">Jika masalah berlanjut, hubungi tim support kami</p>
          </div>
        </div>
      </div>
      
      <div class="mt-4 pt-4 border-t border-slate-200">
        <a href="mailto:support@dietly.com" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium inline-flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
          </svg>
          support@dietly.com
        </a>
      </div>
    </div>
    
    <!-- Footer -->
    <div class="mt-8 text-sm text-slate-500">
      <p>© 2025 Dietly. All rights reserved.</p>
      <p class="mt-2">Error Code: <span class="font-mono bg-slate-100 px-2 py-1 rounded"><?php echo $error_code; ?></span></p>
    </div>
    
  </div>

</body>
</html>
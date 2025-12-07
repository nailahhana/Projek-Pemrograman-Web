<?php
require_once 'koneksi.php';
require_once 'auth.php';

cek_admin();

// Function to scan directory recursively
function scanDirectory($dir) {
    $files = array();
    if (is_dir($dir)) {
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item != '.' && $item != '..') {
                $path = $dir . '/' . $item;
                if (is_dir($path)) {
                    $files = array_merge($files, scanDirectory($path));
                } else {
                    $files[] = array(
                        'path' => $path,
                        'name' => $item,
                        'size' => filesize($path),
                        'modified' => filemtime($path),
                        'type' => pathinfo($path, PATHINFO_EXTENSION)
                    );
                }
            }
        }
    }
    return $files;
}

// Get all uploaded files
$upload_dirs = array('uploads/profil', 'uploads/tracking', 'uploads/resep');
$all_files = array();

foreach ($upload_dirs as $dir) {
    if (is_dir($dir)) {
        $files = scanDirectory($dir);
        $all_files = array_merge($all_files, $files);
    }
}

// Sort by modified date
usort($all_files, function($a, $b) {
    return $b['modified'] - $a['modified'];
});

// Calculate total size
$total_size = 0;
foreach ($all_files as $file) {
    $total_size += $file['size'];
}

function formatSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

$pesan = get_pesan();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>File Manager - Admin Dietly</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50">

  <?php if ($pesan): ?>
  <div id="alertPesan" class="fixed top-4 right-4 z-50 bg-<?php echo $pesan['tipe'] == 'success' ? 'green' : 'red'; ?>-100 border border-<?php echo $pesan['tipe'] == 'success' ? 'green' : 'red'; ?>-400 text-<?php echo $pesan['tipe'] == 'success' ? 'green' : 'red'; ?>-700 px-4 py-3 rounded-lg shadow-lg max-w-md">
    <p class="font-bold"><?php echo $pesan['isi']; ?></p>
  </div>
  <script>setTimeout(() => document.getElementById('alertPesan').style.display = 'none', 5000);</script>
  <?php endif; ?>

  <div class="flex h-screen">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-white flex flex-col">
      <div class="p-6 border-b border-slate-800">
        <h1 class="text-2xl font-bold text-emerald-400">Dietly Admin</h1>
      </div>
      <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
        <a href="admin-dashboard.php" class="block px-4 py-3 hover:bg-slate-800 rounded-lg">📊 Dashboard</a>
        <a href="admin-users.php" class="block px-4 py-3 hover:bg-slate-800 rounded-lg">👥 Kelola Pengguna</a>
        <a href="admin-resep.php" class="block px-4 py-3 hover:bg-slate-800 rounded-lg">🍽️ Kelola Resep</a>
        <a href="admin-tracking.php" class="block px-4 py-3 hover:bg-slate-800 rounded-lg">📈 Tracking</a>
        <a href="admin-testimoni.php" class="block px-4 py-3 hover:bg-slate-800 rounded-lg">💬 Testimoni</a>
        <a href="admin-files.php" class="block px-4 py-3 bg-emerald-600 rounded-lg font-medium">📁 Files</a>
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
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-bold text-slate-800">File Manager</h2>
            <p class="text-sm text-slate-600">Manage uploaded files</p>
          </div>
        </div>
      </header>

      <div class="p-8">
        
        <!-- Storage Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-2">
              <p class="text-sm text-slate-600">Total Files</p>
              <span class="text-2xl">📄</span>
            </div>
            <p class="text-3xl font-bold text-slate-800"><?php echo count($all_files); ?></p>
            <p class="text-xs text-slate-500 mt-2">Uploaded files</p>
          </div>

          <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-2">
              <p class="text-sm text-slate-600">Total Storage</p>
              <span class="text-2xl">💾</span>
            </div>
            <p class="text-3xl font-bold text-slate-800"><?php echo formatSize($total_size); ?></p>
            <p class="text-xs text-slate-500 mt-2">Disk usage</p>
          </div>

          <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-2">
              <p class="text-sm text-slate-600">Directories</p>
              <span class="text-2xl">📁</span>
            </div>
            <p class="text-3xl font-bold text-slate-800">3</p>
            <p class="text-xs text-slate-500 mt-2">Upload directories</p>
          </div>
        </div>

        <!-- Files Table -->
        <div class="bg-white rounded-xl shadow overflow-hidden">
          <div class="p-6 border-b">
            <h3 class="font-bold text-slate-800">All Uploaded Files</h3>
          </div>
          
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-slate-50 border-b">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Preview</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">File Name</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Path</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Size</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Modified</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-200">
                <?php foreach ($all_files as $file): ?>
                <tr class="hover:bg-slate-50">
                  <td class="px-6 py-4">
                    <?php if (in_array($file['type'], array('jpg', 'jpeg', 'png', 'gif'))): ?>
                      <img src="<?php echo $file['path']; ?>" class="w-12 h-12 object-cover rounded" alt="Preview">
                    <?php else: ?>
                      <div class="w-12 h-12 bg-slate-200 rounded flex items-center justify-center">
                        <span class="text-xs font-medium"><?php echo strtoupper($file['type']); ?></span>
                      </div>
                    <?php endif; ?>
                  </td>
                  <td class="px-6 py-4">
                    <p class="text-sm font-medium text-slate-800"><?php echo $file['name']; ?></p>
                    <p class="text-xs text-slate-500">.<?php echo $file['type']; ?></p>
                  </td>
                  <td class="px-6 py-4 text-sm text-slate-600">
                    <code class="bg-slate-100 px-2 py-1 rounded text-xs"><?php echo $file['path']; ?></code>
                  </td>
                  <td class="px-6 py-4 text-sm text-slate-600">
                    <?php echo formatSize($file['size']); ?>
                  </td>
                  <td class="px-6 py-4 text-sm text-slate-600">
                    <?php echo date('d/m/Y H:i', $file['modified']); ?>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex gap-2">
                      <a href="<?php echo $file['path']; ?>" target="_blank" class="px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 text-xs font-medium">
                        View
                      </a>
                      <button onclick="hapusFile('<?php echo $file['path']; ?>', '<?php echo $file['name']; ?>')" class="px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 text-xs font-medium">
                        Delete
                      </button>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <?php if (count($all_files) == 0): ?>
          <div class="p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-slate-500">No files uploaded yet</p>
          </div>
          <?php endif; ?>
        </div>

        <!-- Warning Box -->
        <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-xl">
          <p class="text-sm text-amber-800">
            <strong>⚠️ Warning:</strong> Menghapus file akan menghapusnya secara permanen dari server. Pastikan file tidak digunakan sebelum menghapus!
          </p>
        </div>

      </div>
    </main>

  </div>

  <script>
    function hapusFile(path, name) {
      if (confirm('Yakin ingin menghapus file "' + name + '"?\n\nFile akan dihapus permanen dan tidak bisa dikembalikan!')) {
        window.location.href = 'admin-proses.php?action=delete_file&path=' + encodeURIComponent(path);
      }
    }
  </script>

</body>
</html>
<?php
include 'koneksi.php';

// Simulasi data - nanti bisa diambil dari database
$goal = "Muscular"; // Bisa diambil dari session/database user
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Progress Diet - Plan diet suksesmu</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>body{font-family:Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;}</style>
</head>
<body class="bg-gray-50 text-gray-800">

  <!-- NAVBAR -->
   <header class="fixed top-0 left-0 right-0 bg-white/60 backdrop-blur z-20 shadow-sm">
  <div class="max-w-6xl mx-auto flex items-center justify-between p-4">

    <!-- Logo -->
    <div class="flex items-center gap-3">
      <h1 class="ml-3 text-2xl font-bold text-green-600 cursor:pointer"><a href="index.html">Dietly</a></h1>
    </div>

    <!--TOMBOL HAMBURGER (mobile only) -->
    <label for="nav-toggle" class="md:hidden cursor-pointer px-3 py-2 border rounded font-bold text-green-600">
      ☰
    </label>
    <input type="checkbox" id="nav-toggle" class="hidden peer" />

    <!-- NAV MENU -->
    <nav class="flex flex-col absolute top-[64px] right-4 bg-white shadow-md rounded-xl p-4 gap-3 text-slate-700
                w-48 opacity-0 pointer-events-none scale-95 transition-all duration-200
                peer-checked:opacity-100 peer-checked:pointer-events-auto peer-checked:scale-100
                md:static md:flex-row md:p-0 md:bg-transparent md:shadow-none md:w-auto md:opacity-100 md:pointer-events-auto md:scale-100 md:items-center md:ml-auto">

      <a href="index.html" class="px-4 py-2 hover:text-green-600">Beranda</a>
      <a href="profile.html" class="px-4 py-2 hover:text-green-600">Profil Pengguna</a>
      <a href="progres.html" class="px-4 py-2 text-green-600 font-semibold">Progress</a>
      <a href="resep.html" class="px-4 py-2 hover:text-green-600">Resep Makanan</a>
      <a href="index.html#testimoni" class="px-4 py-2 hover:text-green-600">Testimoni</a>
      <a href="index.html#auth"class=" bg-green-600 text-white px-4 py-2 rounded-lg ml-2 hover:bg-green-700">
      Logout</a>
    </nav>

  </div>
</header>

  <section class="pt-28 pb-10 max-w-5xl mx-auto px-4">
    <h2 class="text-3xl font-bold mb-6 text-center">Progress Diet Kamu</h2>

    <div class="bg-white p-6 rounded-xl shadow-md mb-6">
      <h3 class="text-xl font-semibold mb-2">Program Diet Kamu</h3>
      <p><strong>Plan:</strong> Defisit kalori, tinggi protein & serat</p>
      <p><strong>Goal:</strong> <?php echo $goal; ?></p>
      <p><strong>Jangka Waktu:</strong> 3 Bulan</p>
    </div>

    <div class="bg-emerald-50 p-6 rounded-2xl shadow-inner mb-6">
      <h2 class="font-semibold mb-3">Ringkasan Progress</h2>
      <div class="mb-3">
        <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
          <div class="bg-emerald-500 h-4 w-[40%] transition-all"></div>
        </div>
        <div class="mt-2 text-sm text-gray-600">Progress: 40% – 12 hari dari 30 hari program</div>
      </div>
      <div class="text-sm text-gray-700"><strong>Goal:</strong> <?php echo $goal; ?> <br/><strong>Method:</strong> Diet Seimbang + Olahraga Teratur <br/><strong>Plan (profile):</strong> Defisit 500 kkal/hari</div>
    </div>

    <!-- JADWAL OLAHRAGA BERDASARKAN GOAL -->
    <div class="bg-white p-6 rounded-xl shadow-md mb-6">
      <h3 class="text-xl font-semibold mb-4">📋 Jadwal Olahraga Mingguan</h3>
      
      <?php
      switch($goal) {
        case "Muscular":
          ?>
          <div class="space-y-3">
            <div class="border-l-4 border-blue-500 pl-4 py-2 bg-blue-50">
              <p class="font-semibold">Senin - Chest & Triceps</p>
              <p class="text-sm text-gray-600">Bench Press, Push-ups, Dips, Cable Fly (4 set x 10-12 reps)</p>
            </div>
            <div class="border-l-4 border-blue-500 pl-4 py-2 bg-blue-50">
              <p class="font-semibold">Selasa - Back & Biceps</p>
              <p class="text-sm text-gray-600">Pull-ups, Barbell Row, Dumbbell Curl (4 set x 10-12 reps)</p>
            </div>
            <div class="border-l-4 border-green-500 pl-4 py-2 bg-green-50">
              <p class="font-semibold">Rabu - Rest / Cardio Ringan</p>
              <p class="text-sm text-gray-600">Jalan santai 30 menit atau stretching</p>
            </div>
            <div class="border-l-4 border-blue-500 pl-4 py-2 bg-blue-50">
              <p class="font-semibold">Kamis - Shoulder & Abs</p>
              <p class="text-sm text-gray-600">Shoulder Press, Lateral Raise, Plank, Crunches (4 set x 12-15 reps)</p>
            </div>
            <div class="border-l-4 border-blue-500 pl-4 py-2 bg-blue-50">
              <p class="font-semibold">Jumat - Legs</p>
              <p class="text-sm text-gray-600">Squat, Leg Press, Lunges, Calf Raises (4 set x 10-12 reps)</p>
            </div>
            <div class="border-l-4 border-green-500 pl-4 py-2 bg-green-50">
              <p class="font-semibold">Sabtu - Cardio</p>
              <p class="text-sm text-gray-600">Lari atau bersepeda 30-40 menit</p>
            </div>
            <div class="border-l-4 border-gray-400 pl-4 py-2 bg-gray-50">
              <p class="font-semibold">Minggu - Rest</p>
              <p class="text-sm text-gray-600">Istirahat penuh atau yoga ringan</p>
            </div>
          </div>
          <?php
          break;

        case "Bulky":
          ?>
          <div class="space-y-3">
            <div class="border-l-4 border-purple-500 pl-4 py-2 bg-purple-50">
              <p class="font-semibold">Senin - Upper Body Power</p>
              <p class="text-sm text-gray-600">Bench Press, Overhead Press, Barbell Row (5 set x 5 reps - heavy)</p>
            </div>
            <div class="border-l-4 border-purple-500 pl-4 py-2 bg-purple-50">
              <p class="font-semibold">Selasa - Lower Body Power</p>
              <p class="text-sm text-gray-600">Squat, Deadlift, Leg Press (5 set x 5 reps - heavy)</p>
            </div>
            <div class="border-l-4 border-green-500 pl-4 py-2 bg-green-50">
              <p class="font-semibold">Rabu - Rest / Active Recovery</p>
              <p class="text-sm text-gray-600">Stretching, foam rolling, atau jalan ringan</p>
            </div>
            <div class="border-l-4 border-purple-500 pl-4 py-2 bg-purple-50">
              <p class="font-semibold">Kamis - Upper Body Hypertrophy</p>
              <p class="text-sm text-gray-600">Incline Press, Cable Fly, Pull-ups, Dumbbell Row (4 set x 10 reps)</p>
            </div>
            <div class="border-l-4 border-purple-500 pl-4 py-2 bg-purple-50">
              <p class="font-semibold">Jumat - Lower Body Hypertrophy</p>
              <p class="text-sm text-gray-600">Front Squat, Romanian Deadlift, Lunges (4 set x 10 reps)</p>
            </div>
            <div class="border-l-4 border-purple-500 pl-4 py-2 bg-purple-50">
              <p class="font-semibold">Sabtu - Arms & Shoulders</p>
              <p class="text-sm text-gray-600">Bicep Curl, Tricep Extension, Lateral Raise (4 set x 12 reps)</p>
            </div>
            <div class="border-l-4 border-gray-400 pl-4 py-2 bg-gray-50">
              <p class="font-semibold">Minggu - Rest</p>
              <p class="text-sm text-gray-600">Istirahat penuh, fokus nutrisi</p>
            </div>
          </div>
          <?php
          break;

        case "Atletis":
          ?>
          <div class="space-y-3">
            <div class="border-l-4 border-orange-500 pl-4 py-2 bg-orange-50">
              <p class="font-semibold">Senin - HIIT Cardio</p>
              <p class="text-sm text-gray-600">Sprint intervals 30 detik on/30 detik off x 10 rounds</p>
            </div>
            <div class="border-l-4 border-orange-500 pl-4 py-2 bg-orange-50">
              <p class="font-semibold">Selasa - Strength Training</p>
              <p class="text-sm text-gray-600">Squat, Deadlift, Push-up, Pull-up (3 set x 8-10 reps)</p>
            </div>
            <div class="border-l-4 border-orange-500 pl-4 py-2 bg-orange-50">
              <p class="font-semibold">Rabu - Endurance Run</p>
              <p class="text-sm text-gray-600">Lari steady pace 5-7 km</p>
            </div>
            <div class="border-l-4 border-orange-500 pl-4 py-2 bg-orange-50">
              <p class="font-semibold">Kamis - Agility & Plyometrics</p>
              <p class="text-sm text-gray-600">Box jumps, Burpees, Ladder drills, Jump rope (4 set x 15 reps)</p>
            </div>
            <div class="border-l-4 border-orange-500 pl-4 py-2 bg-orange-50">
              <p class="font-semibold">Jumat - Circuit Training</p>
              <p class="text-sm text-gray-600">Full body circuit: 5 exercises x 4 rounds, minimal rest</p>
            </div>
            <div class="border-l-4 border-green-500 pl-4 py-2 bg-green-50">
              <p class="font-semibold">Sabtu - Active Recovery</p>
              <p class="text-sm text-gray-600">Yoga, swimming, atau bersepeda santai</p>
            </div>
            <div class="border-l-4 border-gray-400 pl-4 py-2 bg-gray-50">
              <p class="font-semibold">Minggu - Rest</p>
              <p class="text-sm text-gray-600">Istirahat atau stretching ringan</p>
            </div>
          </div>
          <?php
          break;

        case "Lean":
          ?>
          <div class="space-y-3">
            <div class="border-l-4 border-teal-500 pl-4 py-2 bg-teal-50">
              <p class="font-semibold">Senin - Cardio Steady State</p>
              <p class="text-sm text-gray-600">Lari atau bersepeda 40-45 menit, intensity sedang</p>
            </div>
            <div class="border-l-4 border-teal-500 pl-4 py-2 bg-teal-50">
              <p class="font-semibold">Selasa - Upper Body Strength</p>
              <p class="text-sm text-gray-600">Push-ups, Pull-ups, Dumbbell Press (3 set x 12-15 reps)</p>
            </div>
            <div class="border-l-4 border-teal-500 pl-4 py-2 bg-teal-50">
              <p class="font-semibold">Rabu - HIIT Cardio</p>
              <p class="text-sm text-gray-600">20-25 menit interval training</p>
            </div>
            <div class="border-l-4 border-teal-500 pl-4 py-2 bg-teal-50">
              <p class="font-semibold">Kamis - Lower Body Strength</p>
              <p class="text-sm text-gray-600">Squat, Lunges, Leg Curls (3 set x 12-15 reps)</p>
            </div>
            <div class="border-l-4 border-teal-500 pl-4 py-2 bg-teal-50">
              <p class="font-semibold">Jumat - Cardio + Core</p>
              <p class="text-sm text-gray-600">30 menit cardio + 15 menit abs workout</p>
            </div>
            <div class="border-l-4 border-teal-500 pl-4 py-2 bg-teal-50">
              <p class="font-semibold">Sabtu - Full Body Circuit</p>
              <p class="text-sm text-gray-600">Circuit training 30-40 menit, fokus fat burning</p>
            </div>
            <div class="border-l-4 border-gray-400 pl-4 py-2 bg-gray-50">
              <p class="font-semibold">Minggu - Rest / Yoga</p>
              <p class="text-sm text-gray-600">Istirahat aktif atau yoga</p>
            </div>
          </div>
          <?php
          break;

        default:
          ?>
          <div class="text-center py-8 text-gray-500">
            <p>Silakan pilih goal terlebih dahulu untuk melihat jadwal olahraga yang sesuai.</p>
          </div>
          <?php
          break;
      }
      ?>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-md mb-6">
      <h3 class="text-xl font-semibold mb-3">Tracking Harian</h3>

      <div class="grid md:grid-cols-2 gap-6">
        <div>
          <h4 class="font-semibold mb-2">📸 Upload Bukti Olahraga</h4>
          <input type="file" class="w-full border p-2 rounded-lg" required />
        </div>

        <div>
          <h4 class="font-semibold mb-2">🍱 Upload Foto Makanan</h4>
          <input type="file" class="w-full border p-2 rounded-lg" required />
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Tanggal</label>
          <input type="date" class="w-full border rounded px-3 py-2" required />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Berat (kg)</label>
          <input type="number" class="w-full border rounded px-3 py-2" required />
        </div>

        <div class="md:col-span-2 text-right">
          <button class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700">Tambah Aktivitas Hari Ini</button>
        </div>
      </div>
    </div>
  </section>

  <footer class="bg-gray-900 text-white py-6 text-center mt-10">
    <p>© 2025 Dietly. Semua Hak Dilindungi.</p>
  </footer>

</body>
</html>
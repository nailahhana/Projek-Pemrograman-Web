<?php
require_once 'koneksi.php';

// Jika sudah login, redirect ke index
if (isset($_SESSION['sudah_login']) && $_SESSION['sudah_login'] === true) {
    redirect('index.php');
}

// Cek apakah kuisioner sudah pernah dilengkapi (dari cookie)
$kuisioner_selesai = isset($_COOKIE['kuisioner_selesai']) && $_COOKIE['kuisioner_selesai'] == '1';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome to Dietly</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; }
    
    @keyframes fadeInScale {
      0% { opacity: 0; transform: scale(0.5); }
      100% { opacity: 1; transform: scale(1); }
    }
    
    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.05); }
    }
    
    .splash-logo {
      animation: fadeInScale 1s ease-out, pulse 2s ease-in-out infinite 1s;
    }
    
    .progress-step {
      transition: all 0.3s ease;
    }
    
    .progress-step.active {
      background: linear-gradient(135deg, #10b981, #059669);
      color: white;
    }
    
    @keyframes slideInRight {
      from { opacity: 0; transform: translateX(50px); }
      to { opacity: 1; transform: translateX(0); }
    }
    
    .question-slide {
      animation: slideInRight 0.4s ease-out;
    }
  </style>
</head>
<body class="bg-gradient-to-br from-emerald-50 via-white to-blue-50">

  <!-- SPLASH SCREEN (Mobile Only) -->
  <div id="splashScreen" class="md:hidden fixed inset-0 bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center z-50">
    <div class="text-center">
      <div class="splash-logo">
        <div class="w-32 h-32 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-2xl">
          <span class="text-5xl font-bold text-emerald-600">D</span>
        </div>
        <h1 class="text-4xl font-bold text-white mb-2">Dietly</h1>
        <p class="text-emerald-100">Your Health Journey Starts Here</p>
      </div>
      <div class="mt-8">
        <div class="w-48 h-1 bg-white/30 rounded-full mx-auto overflow-hidden">
          <div id="splashProgress" class="h-full bg-white rounded-full transition-all duration-1000" style="width: 0%"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- MAIN QUIZ CONTAINER -->
  <div id="quizContainer" class="hidden min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-2xl">
      
      <!-- Progress Indicator -->
      <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-bold text-slate-800">Setup Profil Kamu</h2>
          <span class="text-sm text-slate-600"><span id="currentStep">1</span>/7</span>
        </div>
        <div class="flex gap-2">
          <div id="progress1" class="progress-step flex-1 h-2 bg-slate-200 rounded-full active"></div>
          <div id="progress2" class="progress-step flex-1 h-2 bg-slate-200 rounded-full"></div>
          <div id="progress3" class="progress-step flex-1 h-2 bg-slate-200 rounded-full"></div>
          <div id="progress4" class="progress-step flex-1 h-2 bg-slate-200 rounded-full"></div>
          <div id="progress5" class="progress-step flex-1 h-2 bg-slate-200 rounded-full"></div>
          <div id="progress6" class="progress-step flex-1 h-2 bg-slate-200 rounded-full"></div>
          <div id="progress7" class="progress-step flex-1 h-2 bg-slate-200 rounded-full"></div>
        </div>
      </div>

      <!-- Quiz Form -->
      <form method="POST" action="proses_kuisioner.php" id="formKuisioner">
        <div class="bg-white rounded-3xl shadow-2xl p-8">
          
          <!-- Step 1: Nama -->
          <div id="step1" class="quiz-step question-slide">
            <div class="text-center mb-6">
              <div class="text-6xl mb-4">👋</div>
              <h3 class="text-2xl font-bold text-slate-800 mb-2">Halo! Siapa nama kamu?</h3>
              <p class="text-slate-600">Mari kita kenalan dulu</p>
            </div>
            <input type="text" name="nama" id="inputNama" placeholder="Masukkan nama lengkap" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-emerald-500 focus:outline-none text-center text-lg" required autofocus>
          </div>

          <!-- Step 2: Usia & Gender -->
          <div id="step2" class="quiz-step hidden">
            <div class="text-center mb-6">
              <div class="text-6xl mb-4">🎂</div>
              <h3 class="text-2xl font-bold text-slate-800 mb-2">Berapa usia kamu?</h3>
              <p class="text-slate-600">Dan jenis kelamin kamu?</p>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
              <input type="number" name="usia" id="inputUsia" placeholder="Usia" min="10" max="100" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-emerald-500 focus:outline-none text-center text-lg" required>
              <select name="jenis_kelamin" id="inputJenisKelamin" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-emerald-500 focus:outline-none text-center text-lg" required>
                <option value="">Pilih Gender</option>
                <option value="laki-laki">Laki-laki</option>
                <option value="perempuan">Perempuan</option>
                <option value="lainnya">Lainnya</option>
              </select>
            </div>
          </div>

          <!-- Step 3: Tinggi & Berat -->
          <div id="step3" class="quiz-step hidden">
            <div class="text-center mb-6">
              <div class="text-6xl mb-4">📏</div>
              <h3 class="text-2xl font-bold text-slate-800 mb-2">Data Fisik</h3>
              <p class="text-slate-600">Tinggi dan berat badan kamu saat ini</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Tinggi (cm)</label>
                <input type="number" name="tinggi_badan" id="inputTinggi" placeholder="170" min="100" max="250" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-emerald-500 focus:outline-none text-center text-lg" required>
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Berat (kg)</label>
                <input type="number" name="berat_badan" id="inputBerat" placeholder="70" min="30" max="300" step="0.1" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-emerald-500 focus:outline-none text-center text-lg" required>
              </div>
            </div>
          </div>

          <!-- Step 4: Target Berat -->
          <div id="step4" class="quiz-step hidden">
            <div class="text-center mb-6">
              <div class="text-6xl mb-4">🎯</div>
              <h3 class="text-2xl font-bold text-slate-800 mb-2">Target Berat Ideal</h3>
              <p class="text-slate-600">Berapa berat badan yang ingin kamu capai?</p>
            </div>
            <input type="number" name="berat_target" id="inputTargetBerat" placeholder="65" min="30" max="300" step="0.1" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-emerald-500 focus:outline-none text-center text-lg" required>
            <div id="hasilBMI" class="mt-4 p-4 bg-emerald-50 rounded-xl text-center hidden">
              <p class="text-sm text-slate-600 mb-1">BMI Kamu:</p>
              <p class="text-3xl font-bold text-emerald-600" id="nilaiBMI">0</p>
              <p class="text-sm text-slate-600 mt-1" id="kategoriBMI">-</p>
            </div>
          </div>

          <!-- Step 5: Aktivitas -->
          <div id="step5" class="quiz-step hidden">
            <div class="text-center mb-6">
              <div class="text-6xl mb-4">🏃</div>
              <h3 class="text-2xl font-bold text-slate-800 mb-2">Level Aktivitas</h3>
              <p class="text-slate-600">Seberapa aktif kamu sehari-hari?</p>
            </div>
            <input type="hidden" name="level_aktivitas" id="inputAktivitas">
            <div class="space-y-3">
              <button type="button" onclick="pilihAktivitas('sedentary')" class="btn-aktivitas w-full p-4 border-2 border-slate-200 rounded-xl hover:border-emerald-500 hover:bg-emerald-50 transition text-left">
                <p class="font-semibold text-slate-800">🛋️ Sedentary - Jarang bergerak</p>
                <p class="text-sm text-slate-600">Duduk hampir sepanjang hari</p>
              </button>
              <button type="button" onclick="pilihAktivitas('light')" class="btn-aktivitas w-full p-4 border-2 border-slate-200 rounded-xl hover:border-emerald-500 hover:bg-emerald-50 transition text-left">
                <p class="font-semibold text-slate-800">🚶 Light - Aktivitas ringan</p>
                <p class="text-sm text-slate-600">Olahraga ringan 1-3x seminggu</p>
              </button>
              <button type="button" onclick="pilihAktivitas('moderate')" class="btn-aktivitas w-full p-4 border-2 border-slate-200 rounded-xl hover:border-emerald-500 hover:bg-emerald-50 transition text-left">
                <p class="font-semibold text-slate-800">🏃 Moderate - Cukup aktif</p>
                <p class="text-sm text-slate-600">Olahraga 3-5x seminggu</p>
              </button>
              <button type="button" onclick="pilihAktivitas('active')" class="btn-aktivitas w-full p-4 border-2 border-slate-200 rounded-xl hover:border-emerald-500 hover:bg-emerald-50 transition text-left">
                <p class="font-semibold text-slate-800">💪 Active - Sangat aktif</p>
                <p class="text-sm text-slate-600">Olahraga intensif 6-7x seminggu</p>
              </button>
            </div>
          </div>

          <!-- Step 6: Goal -->
          <div id="step6" class="quiz-step hidden">
            <div class="text-center mb-6">
              <div class="text-6xl mb-4">🎖️</div>
              <h3 class="text-2xl font-bold text-slate-800 mb-2">Tujuan Diet</h3>
              <p class="text-slate-600">Apa yang ingin kamu capai?</p>
            </div>
            <input type="hidden" name="tujuan_diet" id="inputTujuan">
            <div class="space-y-3">
              <button type="button" onclick="pilihTujuan('lose')" class="btn-tujuan w-full p-4 border-2 border-slate-200 rounded-xl hover:border-emerald-500 hover:bg-emerald-50 transition text-left">
                <p class="font-semibold text-slate-800">📉 Lose Weight - Turunkan berat badan</p>
                <p class="text-sm text-slate-600">Fokus pada defisit kalori</p>
              </button>
              <button type="button" onclick="pilihTujuan('maintain')" class="btn-tujuan w-full p-4 border-2 border-slate-200 rounded-xl hover:border-emerald-500 hover:bg-emerald-50 transition text-left">
                <p class="font-semibold text-slate-800">⚖️ Maintain Weight - Pertahankan berat</p>
                <p class="text-sm text-slate-600">Jaga pola makan seimbang</p>
              </button>
              <button type="button" onclick="pilihTujuan('gain')" class="btn-tujuan w-full p-4 border-2 border-slate-200 rounded-xl hover:border-emerald-500 hover:bg-emerald-50 transition text-left">
                <p class="font-semibold text-slate-800">📈 Gain Weight - Naikkan berat badan</p>
                <p class="text-sm text-slate-600">Fokus pada surplus kalori & protein</p>
              </button>
            </div>
          </div>

          <!-- Step 7: Lokasi -->
          <div id="step7" class="quiz-step hidden">
            <div class="text-center mb-6">
              <div class="text-6xl mb-4">📍</div>
              <h3 class="text-2xl font-bold text-slate-800 mb-2">Hampir Selesai!</h3>
              <p class="text-slate-600">Dari mana kamu?</p>
            </div>
            <input type="text" name="lokasi" id="inputLokasi" placeholder="Contoh: Jakarta, Indonesia" class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-emerald-500 focus:outline-none text-center text-lg" required>
            
            <div class="mt-6 p-4 bg-blue-50 rounded-xl">
              <p class="text-sm text-slate-700 text-center">
                ✨ Data kamu akan disimpan dengan aman dan hanya digunakan untuk personalisasi pengalaman kamu di Dietly
              </p>
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="mt-8 flex gap-3">
            <button type="button" id="btnPrev" onclick="prevStep()" class="px-6 py-3 border-2 border-slate-300 text-slate-700 rounded-xl font-semibold hover:bg-slate-50 transition hidden">
              ← Kembali
            </button>
            <button type="button" id="btnNext" onclick="nextStep()" class="flex-1 px-6 py-3 bg-emerald-600 text-white rounded-xl font-semibold hover:bg-emerald-700 transition">
              Lanjut →
            </button>
          </div>

        </div>
      </form>

      <!-- Admin Button -->
      <div class="mt-6 text-center">
        <a href="admin-login.php" class="text-sm text-slate-500 hover:text-emerald-600 transition">
          🔐 Admin Login
        </a>
      </div>

    </div>
  </div>

  <script>
    var currentStepNum = 1;
    var totalSteps = 7;

    window.addEventListener('load', function() {
      var isMobile = window.innerWidth < 768;
      
      if (isMobile) {
        setTimeout(function() {
          document.getElementById('splashProgress').style.width = '100%';
          
          setTimeout(function() {
            document.getElementById('splashScreen').classList.add('hidden');
            document.getElementById('quizContainer').classList.remove('hidden');
          }, 1000);
        }, 1500);
      } else {
        document.getElementById('splashScreen').classList.add('hidden');
        document.getElementById('quizContainer').classList.remove('hidden');
      }
    });

    function nextStep() {
      if (!validateCurrentStep()) {
        return;
      }

      document.getElementById('step' + currentStepNum).classList.add('hidden');
      currentStepNum++;
      
      if (currentStepNum > totalSteps) {
        document.getElementById('formKuisioner').submit();
        return;
      }
      
      document.getElementById('step' + currentStepNum).classList.remove('hidden');
      updateProgress();
      
      document.getElementById('btnPrev').classList.remove('hidden');
      
      if (currentStepNum === totalSteps) {
        document.getElementById('btnNext').textContent = 'Selesai 🎉';
      }
    }

    function prevStep() {
      if (currentStepNum <= 1) return;
      
      document.getElementById('step' + currentStepNum).classList.add('hidden');
      currentStepNum--;
      document.getElementById('step' + currentStepNum).classList.remove('hidden');
      
      updateProgress();
      
      if (currentStepNum === 1) {
        document.getElementById('btnPrev').classList.add('hidden');
      }
      
      document.getElementById('btnNext').textContent = 'Lanjut →';
    }

    function updateProgress() {
      document.getElementById('currentStep').textContent = currentStepNum;
      
      for (var i = 1; i <= totalSteps; i++) {
        var progressEl = document.getElementById('progress' + i);
        if (i <= currentStepNum) {
          progressEl.classList.add('active');
        } else {
          progressEl.classList.remove('active');
        }
      }
    }

    function validateCurrentStep() {
      var isValid = true;
      var errorMsg = '';

      switch(currentStepNum) {
        case 1:
          if (!document.getElementById('inputNama').value.trim()) {
            errorMsg = 'Mohon masukkan nama kamu!';
            isValid = false;
          }
          break;
        case 2:
          if (!document.getElementById('inputUsia').value || !document.getElementById('inputJenisKelamin').value) {
            errorMsg = 'Mohon lengkapi usia dan jenis kelamin!';
            isValid = false;
          }
          break;
        case 3:
          if (!document.getElementById('inputTinggi').value || !document.getElementById('inputBerat').value) {
            errorMsg = 'Mohon masukkan tinggi dan berat badan!';
            isValid = false;
          }
          break;
        case 4:
          if (!document.getElementById('inputTargetBerat').value) {
            errorMsg = 'Mohon masukkan target berat badan!';
            isValid = false;
          } else {
            hitungBMI();
          }
          break;
        case 5:
          if (!document.getElementById('inputAktivitas').value) {
            errorMsg = 'Mohon pilih level aktivitas!';
            isValid = false;
          }
          break;
        case 6:
          if (!document.getElementById('inputTujuan').value) {
            errorMsg = 'Mohon pilih tujuan diet!';
            isValid = false;
          }
          break;
        case 7:
          if (!document.getElementById('inputLokasi').value.trim()) {
            errorMsg = 'Mohon masukkan lokasi kamu!';
            isValid = false;
          }
          break;
      }

      if (!isValid) {
        alert(errorMsg);
      }

      return isValid;
    }

    function hitungBMI() {
      var tinggi = parseFloat(document.getElementById('inputTinggi').value);
      var berat = parseFloat(document.getElementById('inputBerat').value);
      
      var tinggiMeter = tinggi / 100;
      var bmi = berat / (tinggiMeter * tinggiMeter);
      bmi = Math.round(bmi * 10) / 10;
      
      document.getElementById('nilaiBMI').textContent = bmi;
      
      var kategori = '';
      if (bmi < 18.5) kategori = 'Underweight';
      else if (bmi < 25) kategori = 'Normal';
      else if (bmi < 30) kategori = 'Overweight';
      else kategori = 'Obese';
      
      document.getElementById('kategoriBMI').textContent = kategori;
      document.getElementById('hasilBMI').classList.remove('hidden');
    }

    function pilihAktivitas(level) {
      document.getElementById('inputAktivitas').value = level;
      
      var buttons = document.querySelectorAll('.btn-aktivitas');
      buttons.forEach(function(btn) {
        btn.classList.remove('border-emerald-500', 'bg-emerald-50');
      });
      event.target.closest('button').classList.add('border-emerald-500', 'bg-emerald-50');
      
      setTimeout(nextStep, 300);
    }

    function pilihTujuan(tujuan) {
      document.getElementById('inputTujuan').value = tujuan;
      
      var buttons = document.querySelectorAll('.btn-tujuan');
      buttons.forEach(function(btn) {
        btn.classList.remove('border-emerald-500', 'bg-emerald-50');
      });
      event.target.closest('button').classList.add('border-emerald-500', 'bg-emerald-50');
      
      setTimeout(nextStep, 300);
    }

    document.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        nextStep();
      }
    });
  </script>

</body>
</html>
// dietly.js - Main JavaScript Functions

// ========== UTILITY FUNCTIONS ==========
function showNotification(message, type = 'info') {
  // Simple notification (bisa diganti dengan library seperti toastr)
  const notification = document.createElement('div');
  notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg ${
    type === 'success' ? 'bg-green-500' : 
    type === 'error' ? 'bg-red-500' : 
    'bg-blue-500'
  } text-white`;
  notification.textContent = message;
  document.body.appendChild(notification);
  
  setTimeout(() => {
    notification.remove();
  }, 3000);
}

function formatNumber(num) {
  return new Intl.NumberFormat('id-ID').format(num);
}

function formatDate(dateString) {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('id-ID', options);
}

// ========== AUTH FUNCTIONS ==========
async function handleRegister(e) {
  e.preventDefault();
  
  const email = document.querySelector('input[type="email"]').value;
  const password = document.querySelector('input[type="password"]').value;
  const nama = email.split('@')[0]; // Simple name extraction
  
  try {
    const response = await fetch('auth_api.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ action: 'register', email, password, nama })
    });
    
    const data = await response.json();
    
    if (data.success) {
      showNotification(data.message, 'success');
      setTimeout(() => {
        window.location.href = data.redirect;
      }, 1000);
    } else {
      showNotification(data.message, 'error');
    }
  } catch (error) {
    showNotification('Terjadi kesalahan: ' + error.message, 'error');
  }
}

async function handleLogin(e) {
  e.preventDefault();
  
  const email = document.querySelector('input[type="email"]').value;
  const password = document.querySelector('input[type="password"]').value;
  
  try {
    const response = await fetch('auth_api.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ action: 'login', email, password })
    });
    
    const data = await response.json();
    
    if (data.success) {
      showNotification(data.message, 'success');
      setTimeout(() => {
        window.location.href = data.redirect;
      }, 1000);
    } else {
      showNotification(data.message, 'error');
    }
  } catch (error) {
    showNotification('Terjadi kesalahan: ' + error.message, 'error');
  }
}

async function handleLogout() {
  try {
    const response = await fetch('auth_api.php?action=logout');
    const data = await response.json();
    
    if (data.success) {
      showNotification(data.message, 'success');
      setTimeout(() => {
        window.location.href = data.redirect;
      }, 1000);
    }
  } catch (error) {
    showNotification('Terjadi kesalahan: ' + error.message, 'error');
  }
}

async function checkSession() {
  try {
    const response = await fetch('auth_api.php?action=check_session');
    const data = await response.json();
    
    if (data.success && data.logged_in) {
      updateUIWithUserData(data);
    }
    
    return data.logged_in;
  } catch (error) {
    console.error('Session check failed:', error);
    return false;
  }
}

function updateUIWithUserData(userData) {
  // Update nama pengguna di UI
  const namaElements = document.querySelectorAll('[data-user-name]');
  namaElements.forEach(el => {
    el.textContent = userData.nama || 'User';
  });
  
  // Update foto profil
  if (userData.foto) {
    const fotoElements = document.querySelectorAll('[data-user-photo]');
    fotoElements.forEach(el => {
      el.src = userData.foto;
    });
  }
}

// ========== ONBOARDING FUNCTIONS ==========
async function saveOnboardingData(formData) {
  try {
    const response = await fetch('onboard_api.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({
        action: 'save_onboarding',
        ...formData
      })
    });
    
    const data = await response.json();
    
    if (data.success) {
      showNotification(data.message, 'success');
      return data;
    } else {
      showNotification(data.message, 'error');
      return null;
    }
  } catch (error) {
    showNotification('Terjadi kesalahan: ' + error.message, 'error');
    return null;
  }
}

// ========== PROFILE FUNCTIONS ==========
async function loadProfileData() {
  try {
    const response = await fetch('onboard_api.php?action=get_profile');
    const data = await response.json();
    
    if (data.success) {
      populateProfileForm(data.data);
    }
  } catch (error) {
    console.error('Failed to load profile:', error);
  }
}

function populateProfileForm(profileData) {
  // Populate form fields with profile data
  const fields = {
    'nama': profileData.nama,
    'berat': profileData.berat,
    'tinggi': profileData.tinggi,
    // Add more fields as needed
  };
  
  for (const [name, value] of Object.entries(fields)) {
    const input = document.querySelector(`input[name="${name}"]`);
    if (input && value) {
      input.value = value;
    }
  }
  
  // Display BMI info
  if (profileData.bmi) {
    const bmiElement = document.querySelector('[data-bmi]');
    if (bmiElement) {
      bmiElement.textContent = profileData.bmi;
    }
    
    const bmiCategoryElement = document.querySelector('[data-bmi-category]');
    if (bmiCategoryElement) {
      bmiCategoryElement.textContent = profileData.bmi_category;
    }
  }
  
  // Display calorie target
  if (profileData.kalori_target) {
    const calorieElement = document.querySelector('[data-calorie-target]');
    if (calorieElement) {
      calorieElement.textContent = formatNumber(profileData.kalori_target);
    }
  }
}

async function updateProfile(formData) {
  try {
    const response = await fetch('profile_api.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({
        action: 'update_profile',
        ...formData
      })
    });
    
    const data = await response.json();
    
    if (data.success) {
      showNotification(data.message, 'success');
      return data;
    } else {
      showNotification(data.message, 'error');
      return null;
    }
  } catch (error) {
    showNotification('Terjadi kesalahan: ' + error.message, 'error');
    return null;
  }
}

async function uploadProfilePhoto(fileInput) {
  const file = fileInput.files[0];
  if (!file) return;
  
  const formData = new FormData();
  formData.append('action', 'upload_photo');
  formData.append('photo', file);
  
  try {
    const response = await fetch('profile_api.php', {
      method: 'POST',
      body: formData
    });
    
    const data = await response.json();
    
    if (data.success) {
      showNotification(data.message, 'success');
      // Update image preview
      const preview = document.getElementById('avatarPreview');
      if (preview) {
        preview.src = data.photo_url;
      }
    } else {
      showNotification(data.message, 'error');
    }
  } catch (error) {
    showNotification('Terjadi kesalahan: ' + error.message, 'error');
  }
}

// ========== PROGRESS FUNCTIONS ==========
async function addWeight(tanggal, berat, catatan = '') {
  try {
    const response = await fetch('progress_api.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({
        action: 'add_weight',
        tanggal,
        berat,
        catatan
      })
    });
    
    const data = await response.json();
    
    if (data.success) {
      showNotification(data.message, 'success');
      return data;
    } else {
      showNotification(data.message, 'error');
      return null;
    }
  } catch (error) {
    showNotification('Terjadi kesalahan: ' + error.message, 'error');
    return null;
  }
}

async function addActivity(activityData) {
  try {
    const response = await fetch('progress_api.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({
        action: 'add_activity',
        ...activityData
      })
    });
    
    const data = await response.json();
    
    if (data.success) {
      showNotification(data.message, 'success');
      return data;
    } else {
      showNotification(data.message, 'error');
      return null;
    }
  } catch (error) {
    showNotification('Terjadi kesalahan: ' + error.message, 'error');
    return null;
  }
}

async function loadProgressSummary() {
  try {
    const response = await fetch('progress_api.php?action=get_progress_summary');
    const data = await response.json();
    
    if (data.success) {
      displayProgressSummary(data.summary);
    }
  } catch (error) {
    console.error('Failed to load progress summary:', error);
  }
}

function displayProgressSummary(summary) {
  // Update progress percentage
  const progressBar = document.querySelector('[data-progress-bar]');
  if (progressBar) {
    progressBar.style.width = summary.progress_percentage + '%';
  }
  
  const progressText = document.querySelector('[data-progress-text]');
  if (progressText) {
    progressText.textContent = `Progress: ${summary.progress_percentage}% – ${summary.hari_tracking} hari tracking`;
  }
  
  // Update weight info
  const beratAwalEl = document.querySelector('[data-berat-awal]');
  if (beratAwalEl) {
    beratAwalEl.textContent = summary.berat_awal + ' kg';
  }
  
  const beratSekarangEl = document.querySelector('[data-berat-sekarang]');
  if (beratSekarangEl) {
    beratSekarangEl.textContent = summary.berat_sekarang + ' kg';
  }
  
  const targetBeratEl = document.querySelector('[data-target-berat]');
  if (targetBeratEl) {
    targetBeratEl.textContent = summary.target_berat + ' kg';
  }
}

// ========== RECIPE FUNCTIONS ==========
async function loadRecipes(kategori = '', limit = 10) {
  try {
    const params = new URLSearchParams({
      action: 'get_recipes',
      kategori,
      limit
    });
    
    const response = await fetch('resep_api.php?' + params);
    const data = await response.json();
    
    if (data.success) {
      displayRecipes(data.recipes);
    }
  } catch (error) {
    console.error('Failed to load recipes:', error);
  }
}

function displayRecipes(recipes) {
  const container = document.querySelector('[data-recipes-container]');
  if (!container) return;
  
  container.innerHTML = recipes.map(recipe => `
    <div class="bg-white p-5 rounded-2xl shadow hover:shadow-lg transition flex flex-col">
      <h3 class="font-semibold text-lg text-slate-800">${recipe.nama_resep}</h3>
      <div class="mt-3 flex justify-start">
        ${recipe.foto_resep ? `<img src="${recipe.foto_resep}" alt="${recipe.nama_resep}" class="w-20 h-17 object-cover rounded-xl shadow-sm">` : ''}
        <p class="text-sm text-slate-600 ml-3 mt-3 text-left">
          ${recipe.deskripsi || 'Resep lezat dan sehat'}
        </p>
      </div>
      <div class="mt-4 flex items-center justify-between text-xs text-slate-500">
        <div>Kalori ~ ${recipe.kalori} kcal</div>
        <div><a href="detailresep.php?id=${recipe.id}" class="text-emerald-600 font-medium">Lihat Resep</a></div>
      </div>
    </div>
  `).join('');
}

async function searchRecipes(query) {
  try {
    const response = await fetch(`resep_api.php?action=search_recipes&q=${encodeURIComponent(query)}`);
    const data = await response.json();
    
    if (data.success) {
      displayRecipes(data.recipes);
    }
  } catch (error) {
    console.error('Search failed:', error);
  }
}

// ========== INITIALIZATION ==========
document.addEventListener('DOMContentLoaded', async function() {
  // Check session on page load
  const isLoggedIn = await checkSession();
  
  // Setup auth form handlers if present
  const registerBtn = document.querySelector('[data-register-btn]');
  if (registerBtn) {
    registerBtn.addEventListener('click', handleRegister);
  }
  
  const loginBtn = document.querySelector('[data-login-btn]');
  if (loginBtn) {
    loginBtn.addEventListener('click', handleLogin);
  }
  
  const logoutBtns = document.querySelectorAll('[data-logout-btn]');
  logoutBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      if (confirm('Yakin ingin logout?')) {
        handleLogout();
      }
    });
  });
  
  // Load profile data if on profile page
  if (document.querySelector('[data-profile-page]') && isLoggedIn) {
    loadProfileData();
  }
  
  // Load progress summary if on progress page
  if (document.querySelector('[data-progress-page]') && isLoggedIn) {
    loadProgressSummary();
  }
  
  // Load recipes if on recipe page
  if (document.querySelector('[data-recipes-container]')) {
    loadRecipes();
  }
  
  // Setup search functionality
  const searchInput = document.querySelector('[data-search-input]');
  if (searchInput) {
    let searchTimeout;
    searchInput.addEventListener('input', (e) => {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        if (e.target.value.length >= 3) {
          searchRecipes(e.target.value);
        } else if (e.target.value.length === 0) {
          loadRecipes();
        }
      }, 300);
    });
  }
});
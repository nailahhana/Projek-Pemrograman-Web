<?php
// auth_api.php - Handle authentication
require_once 'config.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch($action) {
    case 'register':
        register();
        break;
    case 'login':
        login();
        break;
    case 'logout':
        logout();
        break;
    case 'check_session':
        checkSession();
        break;
    default:
        jsonResponse(['success' => false, 'message' => 'Invalid action'], 400);
}

function register() {
    global $conn;
    
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $nama = $_POST['nama'] ?? '';
    
    if (empty($email) || empty($password)) {
        jsonResponse(['success' => false, 'message' => 'Email dan password harus diisi']);
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(['success' => false, 'message' => 'Format email tidak valid']);
    }
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT email FROM pengguna WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        jsonResponse(['success' => false, 'message' => 'Email sudah terdaftar']);
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $conn->prepare("
        INSERT INTO pengguna (email, nama, password, created_at) 
        VALUES (?, ?, ?, NOW())
    ");
    
    try {
        $stmt->execute([$email, $nama ?: 'User', $hashedPassword]);
        
        $_SESSION['user_email'] = $email;
        $_SESSION['user_nama'] = $nama ?: 'User';
        
        jsonResponse([
            'success' => true, 
            'message' => 'Registrasi berhasil',
            'redirect' => 'cobasplash.php'
        ]);
    } catch(PDOException $e) {
        jsonResponse(['success' => false, 'message' => 'Gagal mendaftar: ' . $e->getMessage()]);
    }
}

function login() {
    global $conn;
    
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        jsonResponse(['success' => false, 'message' => 'Email dan password harus diisi']);
    }
    
    $stmt = $conn->prepare("SELECT * FROM pengguna WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($password, $user['password'])) {
        jsonResponse(['success' => false, 'message' => 'Email atau password salah']);
    }
    
    // Set session
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_nama'] = $user['nama'];
    $_SESSION['user_foto'] = $user['foto_profil'];
    
    // Check if user has completed onboarding
    $hasOnboarding = !empty($user['bb']) && !empty($user['tb']);
    
    jsonResponse([
        'success' => true,
        'message' => 'Login berhasil',
        'redirect' => $hasOnboarding ? 'index.php' : 'cobasplash.php'
    ]);
}

function logout() {
    session_destroy();
    jsonResponse(['success' => true, 'message' => 'Logout berhasil', 'redirect' => 'index.php']);
}

function checkSession() {
    if (isLoggedIn()) {
        global $conn;
        $stmt = $conn->prepare("SELECT nama, foto_profil FROM pengguna WHERE email = ?");
        $stmt->execute([getUserEmail()]);
        $user = $stmt->fetch();
        
        jsonResponse([
            'success' => true,
            'logged_in' => true,
            'email' => getUserEmail(),
            'nama' => $user['nama'] ?? 'User',
            'foto' => $user['foto_profil'] ?? ''
        ]);
    } else {
        jsonResponse(['success' => true, 'logged_in' => false]);
    }
}
?>
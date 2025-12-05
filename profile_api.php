<?php
// profile_api.php - Profile management
require_once 'config.php';

requireLogin();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch($action) {
    case 'update_profile':
        updateProfile();
        break;
    case 'upload_photo':
        uploadPhoto();
        break;
    case 'update_goal':
        updateGoal();
        break;
    case 'get_plans':
        getPlans();
        break;
    default:
        jsonResponse(['success' => false, 'message' => 'Invalid action'], 400);
}

function updateProfile() {
    global $conn;
    
    $email = getUserEmail();
    $nama = $_POST['nama'] ?? '';
    $tanggalLahir = $_POST['tanggal_lahir'] ?? '';
    $berat = (float)($_POST['berat'] ?? 0);
    $tinggi = (float)($_POST['tinggi'] ?? 0);
    $goal = $_POST['goal'] ?? '';
    
    if (empty($nama)) {
        jsonResponse(['success' => false, 'message' => 'Nama harus diisi']);
    }
    
    // Calculate age from birth date if provided
    $umur = null;
    if ($tanggalLahir) {
        $birthDate = new DateTime($tanggalLahir);
        $today = new DateTime();
        $umur = $today->diff($birthDate)->y;
    }
    
    // Get current user data for calculations
    $stmt = $conn->prepare("SELECT * FROM pengguna WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    // Use current values if not provided
    $berat = $berat ?: $user['bb'];
    $tinggi = $tinggi ?: $user['tb'];
    $umur = $umur ?: $user['umur'];
    $goal = $goal ?: $user['goal'];
    
    // Recalculate BMI and calories if weight/height changed
    $bmi = calculateBMI($berat, $tinggi);
    $calorieTarget = calculateCalorieTarget(
        $berat, $tinggi, $umur, 
        $user['jenis_kelamin'], 
        $user['aktivitas_level'], 
        $goal
    );
    
    // Calculate macros
    $protein = round($berat * 2);
    $carbs = round($calorieTarget * 0.4 / 4);
    $fat = round($calorieTarget * 0.3 / 9);
    
    // Update database
    $updateFields = [];
    $params = [];
    
    if ($nama) {
        $updateFields[] = "nama = ?";
        $params[] = $nama;
    }
    if ($umur) {
        $updateFields[] = "umur = ?";
        $params[] = $umur;
    }
    if ($berat) {
        $updateFields[] = "bb = ?";
        $params[] = $berat;
    }
    if ($tinggi) {
        $updateFields[] = "tb = ?";
        $params[] = $tinggi;
    }
    if ($goal) {
        $updateFields[] = "goal = ?";
        $params[] = $goal;
    }
    
    $updateFields[] = "bmi = ?";
    $params[] = $bmi;
    $updateFields[] = "kalori_target = ?";
    $params[] = $calorieTarget;
    $updateFields[] = "protein_target = ?";
    $params[] = $protein;
    $updateFields[] = "karbohidrat_target = ?";
    $params[] = $carbs;
    $updateFields[] = "lemak_target = ?";
    $params[] = $fat;
    $updateFields[] = "updated_at = NOW()";
    
    $params[] = $email;
    
    $sql = "UPDATE pengguna SET " . implode(", ", $updateFields) . " WHERE email = ?";
    $stmt = $conn->prepare($sql);
    
    try {
        $stmt->execute($params);
        
        // Update progress if weight changed
        if ($berat && $berat != $user['bb']) {
            $stmtProgress = $conn->prepare("
                INSERT INTO progres (email, tanggal, berat, bmi, created_at)
                VALUES (?, CURDATE(), ?, ?, NOW())
                ON DUPLICATE KEY UPDATE berat = ?, bmi = ?
            ");
            $stmtProgress->execute([$email, $berat, $bmi, $berat, $bmi]);
        }
        
        jsonResponse([
            'success' => true,
            'message' => 'Profil berhasil diupdate',
            'data' => [
                'bmi' => $bmi,
                'bmi_category' => getBMICategory($bmi),
                'kalori_target' => $calorieTarget
            ]
        ]);
    } catch(PDOException $e) {
        jsonResponse(['success' => false, 'message' => 'Gagal update profil: ' . $e->getMessage()]);
    }
}

function uploadPhoto() {
    global $conn;
    
    if (!isset($_FILES['photo'])) {
        jsonResponse(['success' => false, 'message' => 'No file uploaded']);
    }
    
    $result = uploadImage($_FILES['photo'], 'uploads/profiles/');
    
    if (!$result['success']) {
        jsonResponse($result);
    }
    
    // Update database
    $email = getUserEmail();
    $stmt = $conn->prepare("UPDATE pengguna SET foto_profil = ?, updated_at = NOW() WHERE email = ?");
    
    try {
        $stmt->execute([$result['path'], $email]);
        $_SESSION['user_foto'] = $result['path'];
        
        jsonResponse([
            'success' => true,
            'message' => 'Foto berhasil diupload',
            'photo_url' => $result['path']
        ]);
    } catch(PDOException $e) {
        jsonResponse(['success' => false, 'message' => 'Gagal menyimpan foto: ' . $e->getMessage()]);
    }
}

function updateGoal() {
    global $conn;
    
    $email = getUserEmail();
    $goal = $_POST['goal'] ?? '';
    
    if (empty($goal)) {
        jsonResponse(['success' => false, 'message' => 'Goal harus dipilih']);
    }
    
    // Get user data for calorie recalculation
    $stmt = $conn->prepare("SELECT * FROM pengguna WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    // Recalculate calories based on new goal
    $calorieTarget = calculateCalorieTarget(
        $user['bb'], $user['tb'], $user['umur'],
        $user['jenis_kelamin'], $user['aktivitas_level'], $goal
    );
    
    // Update goal and calorie target
    $stmt = $conn->prepare("
        UPDATE pengguna 
        SET goal = ?, kalori_target = ?, updated_at = NOW()
        WHERE email = ?
    ");
    
    try {
        $stmt->execute([$goal, $calorieTarget, $email]);
        
        jsonResponse([
            'success' => true,
            'message' => 'Goal berhasil diupdate',
            'kalori_target' => $calorieTarget
        ]);
    } catch(PDOException $e) {
        jsonResponse(['success' => false, 'message' => 'Gagal update goal: ' . $e->getMessage()]);
    }
}

function getPlans() {
    global $conn;
    
    $stmt = $conn->query("SELECT * FROM plan ORDER BY id");
    $plans = $stmt->fetchAll();
    
    jsonResponse([
        'success' => true,
        'plans' => $plans
    ]);
}
?>
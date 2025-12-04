<?php
// onboard_api.php - Handle onboarding data
require_once 'config.php';

requireLogin();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch($action) {
    case 'save_onboarding':
        saveOnboarding();
        break;
    case 'get_profile':
        getProfile();
        break;
    default:
        jsonResponse(['success' => false, 'message' => 'Invalid action'], 400);
}

function saveOnboarding() {
    global $conn;
    
    $email = getUserEmail();
    $nama = $_POST['name'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $age = (int)($_POST['age'] ?? 0);
    $height = (float)($_POST['height'] ?? 0);
    $weight = (float)($_POST['weight'] ?? 0);
    $activity = $_POST['active'] ?? 'sedang';
    $goal = $_POST['goal'] ?? 'menjaga_berat';
    
    // Validate data
    if (empty($nama) || $age < 10 || $height < 100 || $weight < 30) {
        jsonResponse(['success' => false, 'message' => 'Data tidak lengkap atau tidak valid']);
    }
    
    // Map activity level from form to database
    $activityMap = [
        'low' => 'ringan',
        'moderate' => 'sedang',
        'high' => 'berat'
    ];
    $activity = $activityMap[$activity] ?? 'sedang';
    
    // Map goal from form to database
    $goalMap = [
        'lose' => 'menurunkan_berat',
        'maintain' => 'menjaga_berat',
        'gain' => 'menaikkan_berat',
        'other' => 'menjaga_berat'
    ];
    $goal = $goalMap[$goal] ?? 'menjaga_berat';
    
    // Map gender
    $genderMap = [
        'male' => 'pria',
        'female' => 'wanita',
        'other' => 'lainnya'
    ];
    $gender = $genderMap[$gender] ?? 'lainnya';
    
    // Calculate BMI
    $bmi = calculateBMI($weight, $height);
    
    // Calculate calorie target
    $calorieTarget = calculateCalorieTarget($weight, $height, $age, $gender, $activity, $goal);
    
    // Calculate macros (simple formula)
    $protein = round($weight * 2); // 2g per kg body weight
    $carbs = round($calorieTarget * 0.4 / 4); // 40% of calories
    $fat = round($calorieTarget * 0.3 / 9); // 30% of calories
    
    // Determine target weight based on goal
    $targetWeight = $weight;
    if ($goal == 'menurunkan_berat') {
        $targetWeight = $weight * 0.9; // 10% reduction
    } elseif ($goal == 'menaikkan_berat' || $goal == 'muscle_gain') {
        $targetWeight = $weight * 1.1; // 10% increase
    }
    
    // Update user data
    $stmt = $conn->prepare("
        UPDATE pengguna SET 
            nama = ?,
            umur = ?,
            jenis_kelamin = ?,
            bb = ?,
            tb = ?,
            bmi = ?,
            goal = ?,
            target_berat = ?,
            aktivitas_level = ?,
            kalori_target = ?,
            protein_target = ?,
            karbohidrat_target = ?,
            lemak_target = ?,
            updated_at = NOW()
        WHERE email = ?
    ");
    
    try {
        $stmt->execute([
            $nama, $age, $gender, $weight, $height, $bmi, $goal, 
            $targetWeight, $activity, $calorieTarget, $protein, $carbs, $fat, $email
        ]);
        
        // Update session
        $_SESSION['user_nama'] = $nama;
        
        // Create initial progress entry
        $stmtProgress = $conn->prepare("
            INSERT INTO progres (email, tanggal, berat, bmi, created_at)
            VALUES (?, CURDATE(), ?, ?, NOW())
            ON DUPLICATE KEY UPDATE berat = ?, bmi = ?
        ");
        $stmtProgress->execute([$email, $weight, $bmi, $weight, $bmi]);
        
        jsonResponse([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'data' => [
                'bmi' => $bmi,
                'bmi_category' => getBMICategory($bmi),
                'calorie_target' => $calorieTarget,
                'protein' => $protein,
                'carbs' => $carbs,
                'fat' => $fat
            ]
        ]);
    } catch(PDOException $e) {
        jsonResponse(['success' => false, 'message' => 'Gagal menyimpan data: ' . $e->getMessage()]);
    }
}

function getProfile() {
    global $conn;
    
    $email = getUserEmail();
    $stmt = $conn->prepare("SELECT * FROM pengguna WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        jsonResponse(['success' => false, 'message' => 'User not found']);
    }
    
    // Get latest weight from progress
    $stmtProgress = $conn->prepare("
        SELECT berat FROM progres 
        WHERE email = ? 
        ORDER BY tanggal DESC 
        LIMIT 1
    ");
    $stmtProgress->execute([$email]);
    $latestProgress = $stmtProgress->fetch();
    
    $currentWeight = $latestProgress['berat'] ?? $user['bb'];
    $currentBMI = $user['tb'] ? calculateBMI($currentWeight, $user['tb']) : $user['bmi'];
    
    jsonResponse([
        'success' => true,
        'data' => [
            'nama' => $user['nama'],
            'email' => $user['email'],
            'umur' => $user['umur'],
            'jenis_kelamin' => $user['jenis_kelamin'],
            'berat' => $currentWeight,
            'tinggi' => $user['tb'],
            'bmi' => $currentBMI,
            'bmi_category' => getBMICategory($currentBMI),
            'goal' => $user['goal'],
            'target_berat' => $user['target_berat'],
            'aktivitas_level' => $user['aktivitas_level'],
            'kalori_target' => $user['kalori_target'],
            'protein_target' => $user['protein_target'],
            'karbohidrat_target' => $user['karbohidrat_target'],
            'lemak_target' => $user['lemak_target'],
            'foto_profil' => $user['foto_profil']
        ]
    ]);
}
?>
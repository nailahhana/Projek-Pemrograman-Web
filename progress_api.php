<?php
// progress_api.php - Progress tracking
require_once 'config.php';

requireLogin();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch($action) {
    case 'add_weight':
        addWeight();
        break;
    case 'add_activity':
        addActivity();
        break;
    case 'upload_workout_photo':
        uploadWorkoutPhoto();
        break;
    case 'upload_food_photo':
        uploadFoodPhoto();
        break;
    case 'get_progress':
        getProgress();
        break;
    case 'get_progress_summary':
        getProgressSummary();
        break;
    default:
        jsonResponse(['success' => false, 'message' => 'Invalid action'], 400);
}

function addWeight() {
    global $conn;
    
    $email = getUserEmail();
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    $berat = (float)($_POST['berat'] ?? 0);
    $catatan = $_POST['catatan'] ?? '';
    
    if ($berat <= 0) {
        jsonResponse(['success' => false, 'message' => 'Berat tidak valid']);
    }
    
    // Get user height for BMI calculation
    $stmt = $conn->prepare("SELECT tb FROM pengguna WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    $bmi = calculateBMI($berat, $user['tb']);
    
    // Insert or update weight entry
    $stmt = $conn->prepare("
        INSERT INTO diary_berat (email, tanggal, berat, catatan, created_at)
        VALUES (?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE berat = ?, catatan = ?
    ");
    
    try {
        $stmt->execute([$email, $tanggal, $berat, $catatan, $berat, $catatan]);
        
        // Update progress table
        $stmtProgress = $conn->prepare("
            INSERT INTO progres (email, tanggal, berat, bmi, created_at)
            VALUES (?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE berat = ?, bmi = ?
        ");
        $stmtProgress->execute([$email, $tanggal, $berat, $bmi, $berat, $bmi]);
        
        // Update user's current weight
        $stmtUser = $conn->prepare("UPDATE pengguna SET bb = ?, bmi = ? WHERE email = ?");
        $stmtUser->execute([$berat, $bmi, $email]);
        
        jsonResponse([
            'success' => true,
            'message' => 'Berat berhasil dicatat',
            'bmi' => $bmi,
            'category' => getBMICategory($bmi)
        ]);
    } catch(PDOException $e) {
        jsonResponse(['success' => false, 'message' => 'Gagal menyimpan berat: ' . $e->getMessage()]);
    }
}

function addActivity() {
    global $conn;
    
    $email = getUserEmail();
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    $waktu = $_POST['waktu'] ?? date('H:i:s');
    $aktivitasId = $_POST['aktivitas_id'] ?? null;
    $namaAktivitas = $_POST['nama_aktivitas'] ?? '';
    $durasiMenit = (int)($_POST['durasi_menit'] ?? 0);
    $kaloriTerbakar = (float)($_POST['kalori_terbakar'] ?? 0);
    $catatan = $_POST['catatan'] ?? '';
    
    if ($durasiMenit <= 0) {
        jsonResponse(['success' => false, 'message' => 'Durasi harus lebih dari 0']);
    }
    
    // If activity ID provided, get calories from database
    if ($aktivitasId && !$kaloriTerbakar) {
        $stmt = $conn->prepare("SELECT kalori_per_jam FROM aktivitas WHERE id = ?");
        $stmt->execute([$aktivitasId]);
        $aktivitas = $stmt->fetch();
        
        if ($aktivitas) {
            $kaloriTerbakar = ($aktivitas['kalori_per_jam'] / 60) * $durasiMenit;
        }
    }
    
    $stmt = $conn->prepare("
        INSERT INTO diary_aktivitas 
        (email, tanggal, waktu, aktivitas_id, nama_aktivitas, durasi_menit, kalori_terbakar, catatan, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    try {
        $stmt->execute([
            $email, $tanggal, $waktu, $aktivitasId, $namaAktivitas, 
            $durasiMenit, $kaloriTerbakar, $catatan
        ]);
        
        // Update progress table
        $stmtProgress = $conn->prepare("
            UPDATE progres 
            SET kalori_terbakar = COALESCE(kalori_terbakar, 0) + ?,
                durasi_olahraga_menit = COALESCE(durasi_olahraga_menit, 0) + ?
            WHERE email = ? AND tanggal = ?
        ");
        $stmtProgress->execute([$kaloriTerbakar, $durasiMenit, $email, $tanggal]);
        
        jsonResponse([
            'success' => true,
            'message' => 'Aktivitas berhasil dicatat',
            'kalori_terbakar' => $kaloriTerbakar
        ]);
    } catch(PDOException $e) {
        jsonResponse(['success' => false, 'message' => 'Gagal menyimpan aktivitas: ' . $e->getMessage()]);
    }
}

function uploadWorkoutPhoto() {
    global $conn;
    
    if (!isset($_FILES['photo'])) {
        jsonResponse(['success' => false, 'message' => 'No file uploaded']);
    }
    
    $result = uploadImage($_FILES['photo'], 'uploads/workout/');
    
    if (!$result['success']) {
        jsonResponse($result);
    }
    
    jsonResponse([
        'success' => true,
        'message' => 'Foto berhasil diupload',
        'photo_url' => $result['path']
    ]);
}

function uploadFoodPhoto() {
    global $conn;
    
    if (!isset($_FILES['photo'])) {
        jsonResponse(['success' => false, 'message' => 'No file uploaded']);
    }
    
    $result = uploadImage($_FILES['photo'], 'uploads/food/');
    
    if (!$result['success']) {
        jsonResponse($result);
    }
    
    // Could integrate AI scanning here in the future
    
    jsonResponse([
        'success' => true,
        'message' => 'Foto berhasil diupload',
        'photo_url' => $result['path']
    ]);
}

function getProgress() {
    global $conn;
    
    $email = getUserEmail();
    $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
    $endDate = $_GET['end_date'] ?? date('Y-m-d');
    
    $stmt = $conn->prepare("
        SELECT * FROM progres
        WHERE email = ? AND tanggal BETWEEN ? AND ?
        ORDER BY tanggal DESC
    ");
    $stmt->execute([$email, $startDate, $endDate]);
    $progress = $stmt->fetchAll();
    
    jsonResponse([
        'success' => true,
        'progress' => $progress
    ]);
}

function getProgressSummary() {
    global $conn;
    
    $email = getUserEmail();
    
    // Get user data
    $stmt = $conn->prepare("SELECT * FROM pengguna WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    // Get initial weight (first progress entry)
    $stmtFirst = $conn->prepare("
        SELECT berat FROM progres 
        WHERE email = ? 
        ORDER BY tanggal ASC 
        LIMIT 1
    ");
    $stmtFirst->execute([$email]);
    $firstEntry = $stmtFirst->fetch();
    $beratAwal = $firstEntry['berat'] ?? $user['bb'];
    
    // Get latest weight
    $stmtLatest = $conn->prepare("
        SELECT berat, tanggal FROM progres 
        WHERE email = ? 
        ORDER BY tanggal DESC 
        LIMIT 1
    ");
    $stmtLatest->execute([$email]);
    $latestEntry = $stmtLatest->fetch();
    $beratSekarang = $latestEntry['berat'] ?? $user['bb'];
    
    // Calculate progress
    $perubahanBerat = $beratSekarang - $beratAwal;
    $targetBerat = $user['target_berat'] ?? $beratSekarang;
    
    $totalChange = abs($targetBerat - $beratAwal);
    $currentChange = abs($beratSekarang - $beratAwal);
    $progressPercentage = $totalChange > 0 ? round(($currentChange / $totalChange) * 100, 1) : 0;
    
    // Calculate days tracking
    if ($firstEntry) {
        $firstDate = new DateTime($firstEntry['tanggal'] ?? date('Y-m-d'));
        $today = new DateTime();
        $daysTracking = $today->diff($firstDate)->days;
    } else {
        $daysTracking = 0;
    }
    
    // Get total calories and activities in last 7 days
    $stmt7Days = $conn->prepare("
        SELECT 
            SUM(kalori_konsumsi) as total_kalori,
            SUM(durasi_olahraga_menit) as total_olahraga,
            COUNT(*) as hari_tracking
        FROM progres
        WHERE email = ? AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    ");
    $stmt7Days->execute([$email]);
    $summary7Days = $stmt7Days->fetch();
    
    jsonResponse([
        'success' => true,
        'summary' => [
            'berat_awal' => $beratAwal,
            'berat_sekarang' => $beratSekarang,
            'target_berat' => $targetBerat,
            'perubahan_berat' => $perubahanBerat,
            'progress_percentage' => $progressPercentage,
            'hari_tracking' => $daysTracking,
            'goal' => $user['goal'],
            'kalori_target' => $user['kalori_target'],
            'last_7_days' => [
                'rata_kalori' => $summary7Days['hari_tracking'] > 0 
                    ? round($summary7Days['total_kalori'] / $summary7Days['hari_tracking']) 
                    : 0,
                'total_olahraga' => $summary7Days['total_olahraga'] ?? 0
            ]
        ]
    ]);
}
?>
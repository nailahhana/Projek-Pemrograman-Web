<?php
// config.php - Database Configuration
session_start();

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'cobacoba');

// Create connection
try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user_email']);
}

function getUserEmail() {
    return $_SESSION['user_email'] ?? null;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: index.php#auth");
        exit();
    }
}

function calculateBMI($weight, $height) {
    // height in cm, convert to m
    $heightInMeters = $height / 100;
    return round($weight / ($heightInMeters * $heightInMeters), 2);
}

function getBMICategory($bmi) {
    if ($bmi < 18.5) return "Underweight";
    if ($bmi < 25) return "Normal";
    if ($bmi < 30) return "Overweight";
    return "Obese";
}

function calculateCalorieTarget($weight, $height, $age, $gender, $activityLevel, $goal) {
    // BMR calculation (Mifflin-St Jeor)
    if ($gender == 'pria') {
        $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age) + 5;
    } else {
        $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age) - 161;
    }
    
    // Activity multiplier
    $activityMultipliers = [
        'sangat_ringan' => 1.2,
        'ringan' => 1.375,
        'sedang' => 1.55,
        'berat' => 1.725,
        'sangat_berat' => 1.9
    ];
    
    $tdee = $bmr * ($activityMultipliers[$activityLevel] ?? 1.55);
    
    // Adjust based on goal
    if ($goal == 'menurunkan_berat') {
        $tdee -= 500; // deficit 500 cal
    } elseif ($goal == 'menaikkan_berat' || $goal == 'muscle_gain') {
        $tdee += 300; // surplus 300 cal
    }
    
    return round($tdee);
}

function uploadImage($file, $targetDir = 'uploads/') {
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($imageFileType, $allowedTypes)) {
        return ['success' => false, 'message' => 'Only JPG, JPEG, PNG, GIF & WEBP files are allowed'];
    }
    
    if ($file["size"] > 5000000) { // 5MB
        return ['success' => false, 'message' => 'File is too large (max 5MB)'];
    }
    
    $fileName = uniqid() . '_' . time() . '.' . $imageFileType;
    $targetFile = $targetDir . $fileName;
    
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        return ['success' => true, 'path' => $targetFile];
    }
    
    return ['success' => false, 'message' => 'Failed to upload file'];
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}
?>
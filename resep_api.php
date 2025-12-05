<?php
// resep_api.php - Recipe management
require_once 'config.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch($action) {
    case 'get_recipes':
        getRecipes();
        break;
    case 'get_recipe_detail':
        getRecipeDetail();
        break;
    case 'search_recipes':
        searchRecipes();
        break;
    case 'add_favorite':
        addFavorite();
        break;
    case 'get_favorites':
        getFavorites();
        break;
    default:
        jsonResponse(['success' => false, 'message' => 'Invalid action'], 400);
}

function getRecipes() {
    global $conn;
    
    $kategori = $_GET['kategori'] ?? '';
    $limit = (int)($_GET['limit'] ?? 10);
    $offset = (int)($_GET['offset'] ?? 0);
    
    $sql = "SELECT * FROM resep WHERE 1=1";
    $params = [];
    
    if ($kategori) {
        $sql .= " AND kategori = ?";
        $params[] = $kategori;
    }
    
    // If user is logged in, filter based on their plan
    if (isLoggedIn()) {
        $stmtUser = $conn->prepare("SELECT plan_id, goal FROM pengguna WHERE email = ?");
        $stmtUser->execute([getUserEmail()]);
        $user = $stmtUser->fetch();
        
        if ($user['plan_id']) {
            $sql .= " AND (plan_id = ? OR plan_id IS NULL)";
            $params[] = $user['plan_id'];
        }
    }
    
    $sql .= " ORDER BY rating DESC, created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $recipes = $stmt->fetchAll();
    
    jsonResponse([
        'success' => true,
        'recipes' => $recipes
    ]);
}

function getRecipeDetail() {
    global $conn;
    
    $id = (int)($_GET['id'] ?? 0);
    
    if (!$id) {
        jsonResponse(['success' => false, 'message' => 'Recipe ID required']);
    }
    
    $stmt = $conn->prepare("SELECT * FROM resep WHERE id = ?");
    $stmt->execute([$id]);
    $recipe = $stmt->fetch();
    
    if (!$recipe) {
        jsonResponse(['success' => false, 'message' => 'Recipe not found']);
    }
    
    // Check if favorited by current user
    $isFavorite = false;
    if (isLoggedIn()) {
        $stmtFav = $conn->prepare("
            SELECT COUNT(*) as count 
            FROM makanan_favorit 
            WHERE email = ? AND makanan_id = ?
        ");
        $stmtFav->execute([getUserEmail(), $id]);
        $isFavorite = $stmtFav->fetch()['count'] > 0;
    }
    
    $recipe['is_favorite'] = $isFavorite;
    
    jsonResponse([
        'success' => true,
        'recipe' => $recipe
    ]);
}

function searchRecipes() {
    global $conn;
    
    $query = $_GET['q'] ?? '';
    $limit = (int)($_GET['limit'] ?? 10);
    
    if (empty($query)) {
        jsonResponse(['success' => false, 'message' => 'Search query required']);
    }
    
    $stmt = $conn->prepare("
        SELECT * FROM resep 
        WHERE nama_resep LIKE ? 
           OR bahan LIKE ? 
           OR diet_tags LIKE ?
        ORDER BY rating DESC
        LIMIT ?
    ");
    
    $searchTerm = "%{$query}%";
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $limit]);
    $recipes = $stmt->fetchAll();
    
    jsonResponse([
        'success' => true,
        'recipes' => $recipes
    ]);
}

function addFavorite() {
    global $conn;
    
    requireLogin();
    
    $resepId = (int)($_POST['resep_id'] ?? 0);
    
    if (!$resepId) {
        jsonResponse(['success' => false, 'message' => 'Recipe ID required']);
    }
    
    $email = getUserEmail();
    
    // Check if already favorited
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM makanan_favorit 
        WHERE email = ? AND makanan_id = ?
    ");
    $stmt->execute([$email, $resepId]);
    
    if ($stmt->fetch()['count'] > 0) {
        // Remove from favorites
        $stmtDelete = $conn->prepare("
            DELETE FROM makanan_favorit 
            WHERE email = ? AND makanan_id = ?
        ");
        $stmtDelete->execute([$email, $resepId]);
        
        jsonResponse([
            'success' => true,
            'message' => 'Dihapus dari favorit',
            'is_favorite' => false
        ]);
    } else {
        // Add to favorites
        $stmtInsert = $conn->prepare("
            INSERT INTO makanan_favorit (email, makanan_id, created_at)
            VALUES (?, ?, NOW())
        ");
        $stmtInsert->execute([$email, $resepId]);
        
        jsonResponse([
            'success' => true,
            'message' => 'Ditambahkan ke favorit',
            'is_favorite' => true
        ]);
    }
}

function getFavorites() {
    global $conn;
    
    requireLogin();
    
    $email = getUserEmail();
    
    $stmt = $conn->prepare("
        SELECT r.* 
        FROM resep r
        JOIN makanan_favorit mf ON r.id = mf.makanan_id
        WHERE mf.email = ?
        ORDER BY mf.created_at DESC
    ");
    $stmt->execute([$email]);
    $favorites = $stmt->fetchAll();
    
    jsonResponse([
        'success' => true,
        'favorites' => $favorites
    ]);
}
?>
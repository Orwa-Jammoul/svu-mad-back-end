<?php
// menus.php
$menu_action = $segments[1] ?? '';
$menu_param = $segments[2] ?? '';

// Get menu by restaurant
if ($method === 'GET' && $menu_action === 'restaurant' && is_numeric($menu_param)) {
    try {
        // First check if restaurant exists
        $stmt = $db->prepare("SELECT restaurant_id FROM restaurants WHERE restaurant_id = ?");
        $stmt->execute([$menu_param]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(["error" => "Restaurant not found"]);
            exit();
        }
        
        $stmt = $db->prepare("
            SELECT * FROM menus 
            WHERE restaurant_id = ? 
            AND (availability_status = 'available' OR availability_status IS NULL)
            ORDER BY item_name
        ");
        $stmt->execute([$menu_param]);
        $menu = $stmt->fetchAll();
        
        echo json_encode([
            "success" => true,
            "data" => $menu,
            "count" => count($menu)
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
    exit();
}

// Get single menu item
if ($method === 'GET' && is_numeric($menu_action)) {
    try {
        $stmt = $db->prepare("SELECT * FROM menus WHERE menu_id = ?");
        $stmt->execute([$menu_action]);
        $item = $stmt->fetch();
        
        if (!$item) {
            http_response_code(404);
            echo json_encode(["error" => "Menu item not found"]);
            exit();
        }
        
        echo json_encode([
            "success" => true,
            "data" => $item
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
    exit();
}

// If no matching endpoint
http_response_code(404);
echo json_encode(["error" => "Endpoint not found. Available: /menus/restaurant/{id}, /menus/{id}"]);
?>
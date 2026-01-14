<?php
// restaurants.php
$restaurant_id = $segments[1] ?? null;

// Get all restaurants
if ($method === 'GET' && $restaurant_id === null) {
    try {
        $query = "SELECT * FROM restaurants WHERE 1=1";
        $params = [];
        
        // Filter by cuisine type
        if (isset($_GET['cuisine'])) {
            $query .= " AND cuisine_type = ?";
            $params[] = $_GET['cuisine'];
        }
        
        // Filter by rating
        if (isset($_GET['min_rating'])) {
            $query .= " AND rating >= ?";
            $params[] = $_GET['min_rating'];
        }
        
        $query .= " ORDER BY name";
        
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $restaurants = $stmt->fetchAll();
        
        echo json_encode([
            "success" => true,
            "data" => $restaurants,
            "count" => count($restaurants)
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
    exit();
}

// Get single restaurant
if ($method === 'GET' && is_numeric($restaurant_id)) {
    try {
        $stmt = $db->prepare("
            SELECT r.*, 
                   COALESCE(AVG(rev.rating), 0) as average_rating,
                   COUNT(rev.review_id) as review_count
            FROM restaurants r
            LEFT JOIN reviews rev ON r.restaurant_id = rev.restaurant_id
            WHERE r.restaurant_id = ?
            GROUP BY r.restaurant_id
        ");
        $stmt->execute([$restaurant_id]);
        $restaurant = $stmt->fetch();
        
        if (!$restaurant) {
            http_response_code(404);
            echo json_encode(["error" => "Restaurant not found"]);
            exit();
        }
        
        echo json_encode([
            "success" => true,
            "data" => $restaurant
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
    exit();
}

// If no matching endpoint
http_response_code(405);
echo json_encode(["error" => "Method not allowed"]);
?>
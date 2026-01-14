<?php
// reviews.php
$review_action = $segments[1] ?? '';
$review_param = $segments[2] ?? '';

// Submit review
if ($method === 'POST' && empty($review_action)) {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $required = ['user_id', 'restaurant_id', 'order_id', 'rating'];
    foreach ($required as $field) {
        if (!isset($data[$field])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing required field: $field"]);
            exit();
        }
    }
    
    // Validate rating
    if ($data['rating'] < 1 || $data['rating'] > 5) {
        http_response_code(400);
        echo json_encode(["error" => "Rating must be between 1 and 5"]);
        exit();
    }
    
    try {
        // Check if review already exists for this order
        $stmt = $db->prepare("SELECT review_id FROM reviews WHERE order_id = ?");
        $stmt->execute([$data['order_id']]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(["error" => "Review already submitted for this order"]);
            exit();
        }
        
        $stmt = $db->prepare("
            INSERT INTO reviews (user_id, restaurant_id, order_id, rating, comment, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $data['user_id'],
            $data['restaurant_id'],
            $data['order_id'],
            $data['rating'],
            $data['comment'] ?? ''
        ]);
        
        $review_id = $db->lastInsertId();
        
        // Update restaurant average rating
        $stmt = $db->prepare("
            UPDATE restaurants r
            SET rating = (
                SELECT AVG(rating) 
                FROM reviews 
                WHERE restaurant_id = ?
            )
            WHERE restaurant_id = ?
        ");
        $stmt->execute([$data['restaurant_id'], $data['restaurant_id']]);
        
        echo json_encode([
            "success" => true,
            "review_id" => $review_id,
            "message" => "Review submitted successfully"
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
    exit();
}

// Get restaurant reviews
if ($method === 'GET' && $review_action === 'restaurant' && is_numeric($review_param)) {
    try {
        $stmt = $db->prepare("
            SELECT r.*, u.name as user_name, u.email as user_email
            FROM reviews r
            JOIN users u ON r.user_id = u.user_id
            WHERE r.restaurant_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$review_param]);
        $reviews = $stmt->fetchAll();
        
        // Calculate average rating
        $avg_stmt = $db->prepare("
            SELECT AVG(rating) as average_rating, COUNT(*) as total_reviews 
            FROM reviews 
            WHERE restaurant_id = ?
        ");
        $avg_stmt->execute([$review_param]);
        $stats = $avg_stmt->fetch();
        
        echo json_encode([
            "success" => true,
            "data" => $reviews,
            "stats" => $stats,
            "count" => count($reviews)
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
    exit();
}

// If no matching endpoint
http_response_code(404);
echo json_encode(["error" => "Endpoint not found. Available: POST /reviews, GET /reviews/restaurant/{id}"]);
?>
<?php
// delivery.php
$delivery_action = $segments[1] ?? '';
$delivery_param = $segments[2] ?? '';

// Get delivery tracking for order
if ($method === 'GET' && $delivery_action === 'order' && is_numeric($delivery_param)) {
    try {
        $stmt = $db->prepare("
            SELECT d.*, dr.name as driver_name, dr.phone as driver_phone, dr.vehicle_type,
                   o.order_status, o.total_amount, r.name as restaurant_name
            FROM delivery d
            LEFT JOIN drivers dr ON d.driver_id = dr.driver_id
            JOIN orders o ON d.order_id = o.order_id
            JOIN restaurants r ON o.restaurant_id = r.restaurant_id
            WHERE d.order_id = ?
        ");
        $stmt->execute([$delivery_param]);
        $delivery = $stmt->fetch();
        
        if (!$delivery) {
            http_response_code(404);
            echo json_encode(["error" => "Delivery not found for this order"]);
            exit();
        }
        
        echo json_encode([
            "success" => true,
            "data" => $delivery
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
    exit();
}


// If no matching endpoint
http_response_code(404);
echo json_encode(["error" => "Endpoint not found. Available: GET /delivery/order/{id}, PUT /delivery/location/{order_id}"]);
?>
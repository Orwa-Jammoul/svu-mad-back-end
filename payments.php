<?php
// payments.php
$payment_action = $segments[1] ?? '';

// Create payment
if ($method === 'POST' && empty($payment_action)) {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $required = ['order_id', 'payment_method', 'amount'];
    foreach ($required as $field) {
        if (!isset($data[$field])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing required field: $field"]);
            exit();
        }
    }
    
    try {
        // Check if order exists
        $stmt = $db->prepare("SELECT order_id FROM orders WHERE order_id = ?");
        $stmt->execute([$data['order_id']]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(["error" => "Order not found"]);
            exit();
        }
        
        $stmt = $db->prepare("
            INSERT INTO payments (order_id, payment_method, payment_status, amount, paid_at)
            VALUES (?, ?, 'completed', ?, NOW())
        ");
        $stmt->execute([$data['order_id'], $data['payment_method'], $data['amount']]);
        $payment_id = $db->lastInsertId();
        
        // Update order status to confirmed
        $stmt = $db->prepare("UPDATE orders SET order_status = 'confirmed' WHERE order_id = ?");
        $stmt->execute([$data['order_id']]);
        
        echo json_encode([
            "success" => true,
            "payment_id" => $payment_id,
            "message" => "Payment recorded successfully"
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
    exit();
}

// Get payment by order ID
if ($method === 'GET' && $payment_action === 'order' && is_numeric($segments[2] ?? '')) {
    try {
        $stmt = $db->prepare("
            SELECT * FROM payments WHERE order_id = ?
        ");
        $stmt->execute([$segments[2]]);
        $payment = $stmt->fetch();
        
        if (!$payment) {
            http_response_code(404);
            echo json_encode(["error" => "Payment not found for this order"]);
            exit();
        }
        
        echo json_encode([
            "success" => true,
            "data" => $payment
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
    exit();
}

// If no matching endpoint
http_response_code(404);
echo json_encode(["error" => "Endpoint not found. Available: POST /payments, GET /payments/order/{id}"]);
?>
<?php
// orders.php
$order_id = $segments[1] ?? '';
$order_action = $segments[2] ?? '';

// Create new order with automatic delivery assignment
if ($method === 'POST' && empty($order_id)) {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid JSON data"]);
        exit();
    }
    
    $required = ['user_id', 'restaurant_id', 'items'];
    foreach ($required as $field) {
        if (!isset($data[$field])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing required field: $field"]);
            exit();
        }
    }
    
    try {
        $db->beginTransaction();
        
        // Calculate total amount
        $total = 0;
        $items_data = [];
        
        foreach ($data['items'] as $item) {
            if (!isset($item['menu_id']) || !isset($item['quantity'])) {
                throw new Exception("Each item must have menu_id and quantity");
            }
            
            $stmt = $db->prepare("SELECT price FROM menus WHERE menu_id = ?");
            $stmt->execute([$item['menu_id']]);
            $menu_item = $stmt->fetch();
            
            if (!$menu_item) {
                throw new Exception("Menu item not found: " . $item['menu_id']);
            }
            
            $item_total = $menu_item['price'] * $item['quantity'];
            $total += $item_total;
            
            $items_data[] = [
                'menu_id' => $item['menu_id'],
                'quantity' => $item['quantity'],
                'price' => $menu_item['price'],
                'item_total' => $item_total
            ];
        }
        
        // Create order
        $stmt = $db->prepare("
            INSERT INTO orders (user_id, restaurant_id, total_amount, order_status, created_at)
            VALUES (?, ?, ?, 'pending', NOW())
        ");
        $stmt->execute([$data['user_id'], $data['restaurant_id'], $total]);
        $order_id = $db->lastInsertId();
        
        // Add order items
        foreach ($items_data as $item) {
            $stmt = $db->prepare("
                INSERT INTO order_items (order_id, menu_id, quantity, price)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$order_id, $item['menu_id'], $item['quantity'], $item['price']]);
        }
        
        // Find an available driver
        $stmt = $db->prepare("
            SELECT driver_id, name, vehicle_type 
            FROM drivers 
            WHERE availability_status = 'available' 
            LIMIT 1
        ");
        $stmt->execute();
        $driver = $stmt->fetch();
        
        $driver_id = null;
        if ($driver) {
            $driver_id = $driver['driver_id'];
            
            // Update driver status to busy
            $stmt = $db->prepare("
                UPDATE drivers 
                SET availability_status = 'busy' 
                WHERE driver_id = ?
            ");
            $stmt->execute([$driver_id]);
        }
        
        // Create delivery record
        $estimated_time = 30; // Default 30 minutes
        
        $stmt = $db->prepare("
            INSERT INTO delivery (order_id, driver_id, delivery_status, estimated_time)
            VALUES (?, ?, 'assigned', ?)
        ");
        $stmt->execute([$order_id, $driver_id, $estimated_time]);
        $delivery_id = $db->lastInsertId();
        
        $db->commit();
        
        $response = [
            "success" => true,
            "order_id" => $order_id,
            "delivery_id" => $delivery_id,
            "total_amount" => $total,
            "estimated_delivery_time" => $estimated_time,
            "message" => "Order created successfully"
        ];
        
        if ($driver) {
            $response['driver'] = [
                "driver_id" => $driver['driver_id'],
                "name" => $driver['name'],
                "vehicle_type" => $driver['vehicle_type']
            ];
        } else {
            $response['message'] .= " (No available drivers at the moment)";
        }
        
        echo json_encode($response);
        
    } catch (Exception $e) {
        $db->rollBack();
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit();
}

// Get user orders
if ($method === 'GET' && $order_id === 'user' && is_numeric($order_action)) {
    try {
        $stmt = $db->prepare("
            SELECT o.*, r.name as restaurant_name, r.address as restaurant_address
            FROM orders o
            JOIN restaurants r ON o.restaurant_id = r.restaurant_id
            WHERE o.user_id = ?
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$order_action]);
        $orders = $stmt->fetchAll();
        
        // Get order items for each order
        foreach ($orders as &$order) {
            $stmt = $db->prepare("
                SELECT oi.*, m.item_name, m.image_url
                FROM order_items oi
                JOIN menus m ON oi.menu_id = m.menu_id
                WHERE oi.order_id = ?
            ");
            $stmt->execute([$order['order_id']]);
            $order['items'] = $stmt->fetchAll();
            
            // Get delivery data for each order
            $stmt = $db->prepare("
                SELECT d.*, dr.name as driver_name, dr.phone as driver_phone, dr.vehicle_type
                FROM delivery d
                LEFT JOIN drivers dr ON d.driver_id = dr.driver_id
                WHERE d.order_id = ?
            ");
            $stmt->execute([$order['order_id']]);
            $order['delivery'] = $stmt->fetch();
        }
        
        echo json_encode([
            "success" => true,
            "data" => $orders,
            "count" => count($orders)
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
    exit();
}

// Get single order with delivery and driver data
if ($method === 'GET' && is_numeric($order_id)) {
    try {
        // Get order details with user and restaurant info
        $stmt = $db->prepare("
            SELECT 
                o.*, 
                r.name as restaurant_name, 
                r.address as restaurant_address,
                r.phone as restaurant_phone,
                r.cuisine_type as restaurant_cuisine,
                r.rating as restaurant_rating,
                u.name as user_name, 
                u.email as user_email, 
                u.phone as user_phone,
                u.address as user_address
            FROM orders o
            JOIN restaurants r ON o.restaurant_id = r.restaurant_id
            JOIN users u ON o.user_id = u.user_id
            WHERE o.order_id = ?
        ");
        $stmt->execute([$order_id]);
        $order = $stmt->fetch();
        
        if (!$order) {
            http_response_code(404);
            echo json_encode(["error" => "Order not found"]);
            exit();
        }
        
        // Get order items
        $stmt = $db->prepare("
            SELECT 
                oi.*, 
                m.item_name, 
                m.image_url, 
                m.description,
                (oi.quantity * oi.price) as item_total
            FROM order_items oi
            JOIN menus m ON oi.menu_id = m.menu_id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$order_id]);
        $order['items'] = $stmt->fetchAll();
        
        // Get delivery and driver data
        $stmt = $db->prepare("
            SELECT 
                d.*,
                dr.name as driver_name,
                dr.phone as driver_phone,
                dr.vehicle_type
            FROM delivery d
            LEFT JOIN drivers dr ON d.driver_id = dr.driver_id
            WHERE d.order_id = ?
        ");
        $stmt->execute([$order_id]);
        $delivery_data = $stmt->fetch();
        
        if ($delivery_data) {
            // Calculate delivery progress based on status
            switch ($delivery_data['delivery_status']) {
                case 'assigned':
                    $delivery_data['delivery_progress'] = 25;
                    break;
                case 'on the way':
                    $delivery_data['delivery_progress'] = 50;
                    break;
                case 'delivered':
                    $delivery_data['delivery_progress'] = 100;
                    break;
                default:
                    $delivery_data['delivery_progress'] = 0;
            }
            
            // Calculate estimated delivery time
            if ($delivery_data['delivery_status'] != 'delivered' && $delivery_data['estimated_time']) {
                $order_time = strtotime($order['created_at']);
                $estimated_time = date('Y-m-d H:i:s', $order_time + ($delivery_data['estimated_time'] * 60));
                $delivery_data['estimated_delivery_time'] = $estimated_time;
            }
            
            // Calculate minutes since order was created
            $order_time = new DateTime($order['created_at']);
            $current_time = new DateTime();
            $interval = $current_time->diff($order_time);
            $minutes_since_order = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
            $delivery_data['minutes_since_order'] = $minutes_since_order;
            
            // Add driver info
            if ($delivery_data['driver_id']) {
                $delivery_data['driver'] = [
                    'id' => $delivery_data['driver_id'],
                    'name' => $delivery_data['driver_name'],
                    'phone' => $delivery_data['driver_phone'],
                    'vehicle_type' => $delivery_data['vehicle_type']
                ];
            }
            
            $order['delivery'] = $delivery_data;
        } else {
            $order['delivery'] = null;
        }
        
        // Get payment data if exists
        $stmt = $db->prepare("
            SELECT 
                payment_id,
                payment_method,
                payment_status,
                amount,
                paid_at,
                transaction_id
            FROM payments 
            WHERE order_id = ?
        ");
        $stmt->execute([$order_id]);
        $payment_data = $stmt->fetch();
        
        if ($payment_data) {
            $order['payment'] = $payment_data;
        } else {
            $order['payment'] = null;
        }
        
        // Get review data if exists
        $stmt = $db->prepare("
            SELECT 
                review_id,
                rating,
                comment,
                created_at as review_date
            FROM reviews 
            WHERE order_id = ?
        ");
        $stmt->execute([$order_id]);
        $review_data = $stmt->fetch();
        
        if ($review_data) {
            $order['review'] = $review_data;
        } else {
            $order['review'] = null;
        }
        
        echo json_encode([
            "success" => true,
            "data" => $order
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
    exit();
}

// Update order status with delivery and driver status updates
if ($method === 'PUT' && is_numeric($order_id) && $order_action === 'status') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['status'])) {
        http_response_code(400);
        echo json_encode(["error" => "Status is required"]);
        exit();
    }
    
    $valid_statuses = ['pending', 'confirmed', 'delivered', 'canceled'];
    if (!in_array($data['status'], $valid_statuses)) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid status. Valid: pending, confirmed, delivered, canceled"]);
        exit();
    }
    
    try {
        $db->beginTransaction();
        
        // Get current order status and delivery info
        $stmt = $db->prepare("
            SELECT o.order_status, d.driver_id, d.delivery_status
            FROM orders o
            LEFT JOIN delivery d ON o.order_id = d.order_id
            WHERE o.order_id = ?
        ");
        $stmt->execute([$order_id]);
        $current_data = $stmt->fetch();
        
        if (!$current_data) {
            http_response_code(404);
            echo json_encode(["error" => "Order not found"]);
            exit();
        }
        
        $current_status = $current_data['order_status'];
        $driver_id = $current_data['driver_id'];
        $current_delivery_status = $current_data['delivery_status'];
        
        // Update order status
        $stmt = $db->prepare("
            UPDATE orders SET order_status = ? WHERE order_id = ?
        ");
        $stmt->execute([$data['status'], $order_id]);
        
        $delivery_updated = false;
        $driver_updated = false;
        
        // Handle delivery status and driver availability based on order status
        switch ($data['status']) {
            case 'confirmed':
                // Order confirmed
                if (!$current_delivery_status || $current_delivery_status === 'assigned') {
                    $stmt = $db->prepare("
                        UPDATE delivery SET delivery_status = 'assigned' WHERE order_id = ?
                    ");
                    $stmt->execute([$order_id]);
                    $delivery_updated = true;
                }
                break;
                
            case 'delivered':
                // Order delivered
                $stmt = $db->prepare("
                    UPDATE delivery 
                    SET delivery_status = 'delivered', actual_time = NOW() 
                    WHERE order_id = ?
                ");
                $stmt->execute([$order_id]);
                $delivery_updated = true;
                
                // Make driver available again
                if ($driver_id) {
                    $stmt = $db->prepare("
                        UPDATE drivers 
                        SET availability_status = 'available' 
                        WHERE driver_id = ?
                    ");
                    $stmt->execute([$driver_id]);
                    $driver_updated = true;
                }
                break;
                
            case 'canceled':
                // Order canceled
                $stmt = $db->prepare("
                    UPDATE delivery 
                    SET delivery_status = 'canceled' 
                    WHERE order_id = ?
                ");
                $stmt->execute([$order_id]);
                $delivery_updated = true;
                
                // Make driver available if order was assigned
                if ($driver_id) {
                    $stmt = $db->prepare("
                        UPDATE drivers 
                        SET availability_status = 'available' 
                        WHERE driver_id = ?
                    ");
                    $stmt->execute([$driver_id]);
                    $driver_updated = true;
                }
                break;
        }
        
        // If order is being confirmed for the first time and has no driver assigned
        if ($current_status === 'pending' && $data['status'] === 'confirmed' && !$driver_id) {
            // Try to find an available driver
            $stmt = $db->prepare("
                SELECT driver_id 
                FROM drivers 
                WHERE availability_status = 'available' 
                LIMIT 1
            ");
            $stmt->execute();
            $new_driver = $stmt->fetch();
            
            if ($new_driver) {
                $driver_id = $new_driver['driver_id'];
                
                // Update driver status to busy
                $stmt = $db->prepare("
                    UPDATE drivers 
                    SET availability_status = 'busy' 
                    WHERE driver_id = ?
                ");
                $stmt->execute([$driver_id]);
                $driver_updated = true;
                
                // Update delivery with driver assignment
                $stmt = $db->prepare("
                    UPDATE delivery 
                    SET driver_id = ?, delivery_status = 'assigned' 
                    WHERE order_id = ?
                ");
                $stmt->execute([$driver_id, $order_id]);
                $delivery_updated = true;
            }
        }
        
        // Special case: if order is confirmed and going out for delivery
        if ($data['status'] === 'confirmed' && isset($data['out_for_delivery']) && $data['out_for_delivery'] === true) {
            $stmt = $db->prepare("
                UPDATE delivery 
                SET delivery_status = 'on the way' 
                WHERE order_id = ?
            ");
            $stmt->execute([$order_id]);
            $delivery_updated = true;
        }
        
        $db->commit();
        
        $response = [
            "success" => true,
            "message" => "Order status updated to " . $data['status'],
            "order_id" => $order_id,
            "new_status" => $data['status']
        ];
        
        if ($delivery_updated) {
            $response['delivery_updated'] = true;
        }
        if ($driver_updated) {
            $response['driver_availability_updated'] = true;
        }
        
        echo json_encode($response);
        
    } catch (PDOException $e) {
        $db->rollBack();
        http_response_code(500);
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
    exit();
}

// If no matching endpoint
http_response_code(404);
echo json_encode(["error" => "Endpoint not found. Available: POST /orders, GET /orders/user/{id}, GET /orders/{id}, PUT /orders/{id}/status"]);
?>
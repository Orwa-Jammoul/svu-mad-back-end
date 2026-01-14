<?php
// drivers.php
$driver_action = $segments[1] ?? '';

// Get available drivers
if ($method === 'GET' && $driver_action === 'available') {
    try {
        $stmt = $db->prepare("
            SELECT * FROM drivers 
            WHERE availability_status = 'available'
            ORDER BY name
        ");
        $stmt->execute();
        $drivers = $stmt->fetchAll();
        
        echo json_encode([
            "success" => true,
            "data" => $drivers,
            "count" => count($drivers)
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
    exit();
}

// Get all drivers
if ($method === 'GET' && empty($driver_action)) {
    try {
        $stmt = $db->prepare("SELECT * FROM drivers ORDER BY name");
        $stmt->execute();
        $drivers = $stmt->fetchAll();
        
        echo json_encode([
            "success" => true,
            "data" => $drivers,
            "count" => count($drivers)
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
    exit();
}

// Get single driver
if ($method === 'GET' && is_numeric($driver_action)) {
    try {
        $stmt = $db->prepare("SELECT * FROM drivers WHERE driver_id = ?");
        $stmt->execute([$driver_action]);
        $driver = $stmt->fetch();
        
        if (!$driver) {
            http_response_code(404);
            echo json_encode(["error" => "Driver not found"]);
            exit();
        }
        
        echo json_encode([
            "success" => true,
            "data" => $driver
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
    exit();
}

// If no matching endpoint
http_response_code(404);
echo json_encode(["error" => "Endpoint not found. Available: GET /drivers, GET /drivers/available, GET /drivers/{id}"]);
?>
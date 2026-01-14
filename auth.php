<?php
// auth.php
$endpoint = $segments[1] ?? '';

// Helper function to verify password
function verifyPassword($input, $hash) {
    return password_verify($input, $hash);
}

// Login endpoint
if ($method === 'POST' && $endpoint === 'login') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['email']) || !isset($data['password'])) {
        http_response_code(400);
        echo json_encode(["error" => "Email and password are required"]);
        exit();
    }
    
    try {
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        $user = $stmt->fetch();
        
        if (!$user || !verifyPassword($data['password'], $user['password_hash'])) {
            http_response_code(401);
            echo json_encode(["error" => "Invalid credentials"]);
            exit();
        }
        
        // Remove password from response
        unset($user['password_hash']);
        
        echo json_encode([
            "success" => true,
            "user" => $user,
            "message" => "Login successful"
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
    exit();
}

// Register endpoint
if ($method === 'POST' && $endpoint === 'register') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $required = ['name', 'email', 'password', 'phone'];
    foreach ($required as $field) {
        if (!isset($data[$field])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing required field: $field"]);
            exit();
        }
    }
    
    try {
        // Check if email exists
        $stmt = $db->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(["error" => "Email already registered"]);
            exit();
        }
        
        // Hash password
        $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Insert user
        $stmt = $db->prepare("
            INSERT INTO users (name, email, phone, password_hash, address, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $data['name'],
            $data['email'],
            $data['phone'],
            $password_hash,
            $data['address'] ?? ''
        ]);
        
        $user_id = $db->lastInsertId();
        
        // Get the created user
        $stmt = $db->prepare("SELECT user_id, name, email, phone, address, created_at FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $new_user = $stmt->fetch();
        
        echo json_encode([
            "success" => true,
            "user" => $new_user,
            "message" => "Registration successful"
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
    exit();
}

// If no matching endpoint
http_response_code(404);
echo json_encode(["error" => "Endpoint not found. Available: /auth/login, /auth/register"]);
?>
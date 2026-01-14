<?php
// api.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once 'config.php';

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

// Get request method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

// Split path into segments
$segments = explode('/', trim($path, '/'));


// Remove everything up to and including 'api.php'
$remaining = preg_replace('/.*api\.php\//', '', $path);

// Split the remaining parts by '/'
$segments = explode('/', $remaining);
$segments = array_filter($segments);
$segments = array_values($segments);

// Route the request
try {
    switch ($segments[0]) {
        case 'auth':
            require_once 'auth.php';
            break;
        case 'restaurants':
            require_once 'restaurants.php';
            break;
        case 'menus':
            require_once 'menus.php';
            break;
        case 'orders':
            require_once 'orders.php';
            break;
        case 'payments':
            require_once 'payments.php';
            break;
        case 'delivery':
            require_once 'delivery.php';
            break;
        case 'reviews':
            require_once 'reviews.php';
            break;
        case 'drivers':
            require_once 'drivers.php';
            break;
        default:
            http_response_code(404);
            echo json_encode([
              "error" => "Endpoint not found",
              "method" => $method,
              "path" => $path,
              "segments" => $segments,
            ]);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
<?php
// test_api.php - Complete API Testing Suite
header("Content-Type: text/html; charset=UTF-8");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DishDrop API Testing Suite</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 2.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .subtitle {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 20px;
        }

        .stats {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
        }

        .stat-box {
            background: #f8f9fa;
            padding: 15px 25px;
            border-radius: 10px;
            text-align: center;
            min-width: 150px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
            display: block;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #666;
        }

        /* Tabs */
        .tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 15px;
        }

        .tab {
            padding: 12px 24px;
            background: #f8f9fa;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            color: #555;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .tab:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }

        .tab.active {
            background: #667eea;
            color: white;
            border-color: #764ba2;
        }

        /* Content Areas */
        .tab-content {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .tab-content.active {
            display: block;
        }

        /* API Sections */
        .api-section {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            border-left: 5px solid #667eea;
        }

        .api-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .api-title {
            font-size: 1.4rem;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .api-title i {
            font-size: 1.2em;
        }

        /* Endpoint Cards */
        .endpoint-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            border: 1px solid #e9ecef;
        }

        .endpoint-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .method {
            padding: 6px 15px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .GET {
            background: #28a745;
            color: white;
        }

        .POST {
            background: #007bff;
            color: white;
        }

        .PUT {
            background: #ffc107;
            color: black;
        }

        .DELETE {
            background: #dc3545;
            color: white;
        }

        .endpoint-path {
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 1rem;
            color: #333;
            background: white;
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid #ddd;
        }

        .endpoint-desc {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        /* Code Blocks */
        .code-container {
            margin: 15px 0;
        }

        .code-header {
            background: #2d3748;
            color: white;
            padding: 10px 15px;
            border-radius: 8px 8px 0 0;
            font-family: 'Consolas', monospace;
            font-size: 0.9rem;
            display: flex;
            justify-content: space-between;
        }

        .language {
            color: #a0aec0;
        }

        .copy-btn {
            background: #4a5568;
            border: none;
            color: white;
            padding: 3px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: background 0.3s;
        }

        .copy-btn:hover {
            background: #718096;
        }

        pre {
            background: #2d3748;
            color: #e2e8f0;
            padding: 20px;
            border-radius: 0 0 8px 8px;
            overflow-x: auto;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 0.9rem;
            line-height: 1.5;
            margin: 0;
            tab-size: 4;
        }

        /* Test Controls */
        .test-controls {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .test-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .test-btn.primary {
            background: #667eea;
            color: white;
        }

        .test-btn.primary:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
        }

        .test-btn.secondary {
            background: #6c757d;
            color: white;
        }

        .test-btn.secondary:hover {
            background: #5a6268;
        }

        /* Result Area */
        .result-container {
            margin-top: 20px;
            animation: slideIn 0.5s ease;
        }

        .result-header {
            background: #2d3748;
            color: white;
            padding: 12px 15px;
            border-radius: 8px 8px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .result-status {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-success {
            background: #28a745;
        }

        .status-error {
            background: #dc3545;
        }

        .status-loading {
            background: #ffc107;
            animation: pulse 1.5s infinite;
        }

        .result-pre {
            background: #1a202c;
            color: #e2e8f0;
            padding: 20px;
            border-radius: 0 0 8px 8px;
            max-height: 400px;
            overflow-y: auto;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 0.9rem;
            line-height: 1.5;
            margin: 0;
        }

        /* Loading animation */
        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            h1 {
                font-size: 2rem;
            }

            .stats {
                flex-direction: column;
                align-items: center;
            }

            .stat-box {
                width: 100%;
            }

            .tabs {
                justify-content: center;
            }

            .api-header {
                flex-direction: column;
                gap: 15px;
            }
        }

        /* JSON Syntax Highlighting */
        .json-key {
            color: #63b3ed;
        }

        .json-string {
            color: #68d391;
        }

        .json-number {
            color: #fbb6ce;
        }

        .json-boolean {
            color: #f6ad55;
        }

        .json-null {
            color: #a0aec0;
        }

        /* Custom Tester Styles */
        .custom-tester-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
        }

        @media (max-width: 768px) {
            .custom-tester-grid {
                grid-template-columns: 1fr;
            }
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ddd;
            font-family: 'Consolas', 'Monaco', monospace;
        }

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-hamburger"></i> DishDrop API Testing Suite</h1>
            <p class="subtitle">Complete testing interface for all API endpoints with code examples</p>
            <div class="stats">
                <div class="stat-box">
                    <span class="stat-number" id="totalEndpoints">14</span>
                    <span class="stat-label">Endpoints</span>
                </div>
                <div class="stat-box">
                    <span class="stat-number" id="testedEndpoints">0</span>
                    <span class="stat-label">Tested</span>
                </div>
                <div class="stat-box">
                    <span class="stat-number" id="successRate">0%</span>
                    <span class="stat-label">Success Rate</span>
                </div>
            </div>
        </header>

        <div class="tabs">
            <div class="tab active" data-tab="all"><i class="fas fa-globe"></i> All APIs</div>
            <div class="tab" data-tab="auth"><i class="fas fa-user-lock"></i> Authentication</div>
            <div class="tab" data-tab="restaurants"><i class="fas fa-store"></i> Restaurants</div>
            <div class="tab" data-tab="orders"><i class="fas fa-shopping-cart"></i> Orders</div>
            <div class="tab" data-tab="delivery"><i class="fas fa-motorcycle"></i> Delivery</div>
            <div class="tab" data-tab="reviews"><i class="fas fa-star"></i> Reviews</div>
            <div class="tab" data-tab="code"><i class="fas fa-code"></i> Code Examples</div>
        </div>

        <!-- All APIs Tab -->
        <div id="all" class="tab-content active">
            <div class="api-section">
                <div class="api-header">
                    <h2 class="api-title"><i class="fas fa-bolt"></i> Quick Test All Endpoints</h2>
                    <button class="test-btn primary" onclick="runAllTests()">
                        <i class="fas fa-play"></i> Run All Tests
                    </button>
                </div>
                <div class="test-controls">
                    <button class="test-btn primary" onclick="testAuthEndpoints()">
                        <i class="fas fa-user-lock"></i> Test Authentication
                    </button>
                    <button class="test-btn primary" onclick="testRestaurantEndpoints()">
                        <i class="fas fa-store"></i> Test Restaurants
                    </button>
                    <button class="test-btn primary" onclick="testOrderEndpoints()">
                        <i class="fas fa-shopping-cart"></i> Test Orders
                    </button>
                    <button class="test-btn secondary" onclick="clearResults()">
                        <i class="fas fa-trash"></i> Clear Results
                    </button>
                </div>
                <div id="quick-results"></div>
            </div>

            <!-- Results will be populated here -->
            <div id="all-results"></div>
        </div>

        <!-- Authentication Tab -->
        <div id="auth" class="tab-content">
            <div class="api-section">
                <div class="api-header">
                    <h2 class="api-title"><i class="fas fa-user-lock"></i> Authentication Endpoints</h2>
                </div>

                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <span class="method POST">POST</span>
                        <span class="endpoint-path">/auth/login</span>
                    </div>
                    <div class="endpoint-desc">User login with email and password. Returns user data on success.</div>
                    <div class="code-container">
                        <div class="code-header">
                            <span>JavaScript Fetch Example</span>
                            <button class="copy-btn" data-target="login-code">Copy</button>
                        </div>
                        <pre id="login-code">// User Login
fetch('api.php/auth/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        email: 'john@example.com',
        password: 'password123'
    })
})
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.error('Error:', error));</pre>
                    </div>
                    <div class="test-controls">
                        <button class="test-btn primary" onclick="testEndpoint('POST', 'auth/login', {
                            email: 'john@example.com',
                            password: 'password123'
                        })">
                            <i class="fas fa-play"></i> Test Login
                        </button>
                        <button class="test-btn secondary" onclick="showResult('login', 'Click Test Login to see results')">
                            <i class="fas fa-eye"></i> View Expected Response
                        </button>
                    </div>
                    <div id="login-result" class="result-container" style="display: none;"></div>
                </div>

                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <span class="method POST">POST</span>
                        <span class="endpoint-path">/auth/register</span>
                    </div>
                    <div class="endpoint-desc">Register a new user account. All fields are required.</div>
                    <div class="code-container">
                        <div class="code-header">
                            <span>JavaScript Fetch Example</span>
                            <button class="copy-btn" data-target="register-code">Copy</button>
                        </div>
                        <pre id="register-code">// User Registration
fetch('api.php/auth/register', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        name: 'John Doe',
        email: 'john@example.com',
        password: 'securepassword123',
        phone: '+1234567890',
        address: '123 Main Street, City'
    })
})
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.error('Error:', error));</pre>
                    </div>
                    <div class="test-controls">
                        <button class="test-btn primary" onclick="testEndpoint('POST', 'auth/register', {
                            name: 'Test User ' + Date.now(),
                            email: 'test' + Date.now() + '@example.com',
                            password: 'password123',
                            phone: '+1234567890',
                            address: 'Test Address'
                        })">
                            <i class="fas fa-play"></i> Test Registration
                        </button>
                    </div>
                    <div id="register-result" class="result-container" style="display: none;"></div>
                </div>
            </div>
        </div>

        <!-- Restaurants Tab -->
        <div id="restaurants" class="tab-content">
            <div class="api-section">
                <div class="api-header">
                    <h2 class="api-title"><i class="fas fa-store"></i> Restaurant Endpoints</h2>
                </div>

                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <span class="method GET">GET</span>
                        <span class="endpoint-path">/restaurants</span>
                    </div>
                    <div class="endpoint-desc">Get all restaurants. Can filter by cuisine type.</div>
                    <div class="code-container">
                        <div class="code-header">
                            <span>JavaScript Fetch Example</span>
                            <button class="copy-btn" data-target="restaurants-code">Copy</button>
                        </div>
                        <pre id="restaurants-code">// Get all restaurants
fetch('api.php/restaurants')
    .then(response => response.json())
    .then(data => console.log(data))
    .catch(error => console.error('Error:', error));

// Get restaurants by cuisine
fetch('api.php/restaurants?cuisine=Italian')
    .then(response => response.json())
    .then(data => console.log(data))
    .catch(error => console.error('Error:', error));</pre>
                    </div>
                    <div class="test-controls">
                        <button class="test-btn primary" onclick="testEndpoint('GET', 'restaurants')">
                            <i class="fas fa-play"></i> Test Get All
                        </button>
                        <button class="test-btn primary" onclick="testEndpoint('GET', 'restaurants?cuisine=Italian')">
                            <i class="fas fa-filter"></i> Test with Filter
                        </button>
                    </div>
                    <div id="restaurants-result" class="result-container" style="display: none;"></div>
                </div>

                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <span class="method GET">GET</span>
                        <span class="endpoint-path">/restaurants/{id}</span>
                    </div>
                    <div class="endpoint-desc">Get single restaurant by ID with average rating.</div>
                    <div class="code-container">
                        <div class="code-header">
                            <span>JavaScript Fetch Example</span>
                            <button class="copy-btn" data-target="restaurant-single-code">Copy</button>
                        </div>
                        <pre id="restaurant-single-code">// Get single restaurant
fetch('api.php/restaurants/1')
    .then(response => response.json())
    .then(data => console.log(data))
    .catch(error => console.error('Error:', error));</pre>
                    </div>
                    <div class="test-controls">
                        <button class="test-btn primary" onclick="testEndpoint('GET', 'restaurants/1')">
                            <i class="fas fa-play"></i> Test Get Single
                        </button>
                    </div>
                    <div id="restaurant-single-result" class="result-container" style="display: none;"></div>
                </div>

                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <span class="method GET">GET</span>
                        <span class="endpoint-path">/menus/restaurant/{id}</span>
                    </div>
                    <div class="endpoint-desc">Get menu items for a specific restaurant.</div>
                    <div class="code-container">
                        <div class="code-header">
                            <span>JavaScript Fetch Example</span>
                            <button class="copy-btn" data-target="menus-code">Copy</button>
                        </div>
                        <pre id="menus-code">// Get restaurant menu
fetch('api.php/menus/restaurant/1')
    .then(response => response.json())
    .then(data => console.log(data))
    .catch(error => console.error('Error:', error));</pre>
                    </div>
                    <div class="test-controls">
                        <button class="test-btn primary" onclick="testEndpoint('GET', 'menus/restaurant/1')">
                            <i class="fas fa-play"></i> Test Menu
                        </button>
                    </div>
                    <div id="menus-result" class="result-container" style="display: none;"></div>
                </div>
            </div>
        </div>

        <!-- Orders Tab -->
        <div id="orders" class="tab-content">
            <div class="api-section">
                <div class="api-header">
                    <h2 class="api-title"><i class="fas fa-shopping-cart"></i> Order Endpoints</h2>
                </div>

                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <span class="method POST">POST</span>
                        <span class="endpoint-path">/orders</span>
                    </div>
                    <div class="endpoint-desc">Create a new order. Requires user_id, restaurant_id, and items array.</div>
                    <div class="code-container">
                        <div class="code-header">
                            <span>JavaScript Fetch Example</span>
                            <button class="copy-btn" data-target="create-order-code">Copy</button>
                        </div>
                        <pre id="create-order-code">// Create new order
fetch('api.php/orders', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        user_id: 1,
        restaurant_id: 1,
        items: [
            { menu_id: 1, quantity: 2 },
            { menu_id: 2, quantity: 1 }
        ]
    })
})
.then(response => response.json())
.then(data => {
    console.log('Order created:', data);
    // Save order_id for payment
    localStorage.setItem('last_order_id', data.order_id);
})
.catch(error => console.error('Error:', error));</pre>
                    </div>
                    <div class="test-controls">
                        <button class="test-btn primary" onclick="testEndpoint('POST', 'orders', {
                            user_id: 1,
                            restaurant_id: 1,
                            items: [{ menu_id: 1, quantity: 2 }]
                        })">
                            <i class="fas fa-play"></i> Create Test Order
                        </button>
                    </div>
                    <div id="create-order-result" class="result-container" style="display: none;"></div>
                </div>

                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <span class="method GET">GET</span>
                        <span class="endpoint-path">/orders/user/{id}</span>
                    </div>
                    <div class="endpoint-desc">Get all orders for a specific user.</div>
                    <div class="code-container">
                        <div class="code-header">
                            <span>JavaScript Fetch Example</span>
                            <button class="copy-btn" data-target="user-orders-code">Copy</button>
                        </div>
                        <pre id="user-orders-code">// Get user orders
fetch('api.php/orders/user/1')
    .then(response => response.json())
    .then(data => {
        console.log('User orders:', data);
        if (data.data && Array.isArray(data.data)) {
            data.data.forEach(order => {
                console.log(`Order ${order.order_id}: ${order.total_amount}`);
            });
        }
    })
    .catch(error => console.error('Error:', error));</pre>
                    </div>
                    <div class="test-controls">
                        <button class="test-btn primary" onclick="testEndpoint('GET', 'orders/user/1')">
                            <i class="fas fa-play"></i> Test User Orders
                        </button>
                    </div>
                    <div id="user-orders-result" class="result-container" style="display: none;"></div>
                </div>

                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <span class="method GET">GET</span>
                        <span class="endpoint-path">/orders/{id}</span>
                    </div>
                    <div class="endpoint-desc">Get detailed information about a specific order.</div>
                    <div class="code-container">
                        <div class="code-header">
                            <span>JavaScript Fetch Example</span>
                            <button class="copy-btn" data-target="single-order-code">Copy</button>
                        </div>
                        <pre id="single-order-code">// Get single order details
fetch('api.php/orders/1')
    .then(response => response.json())
    .then(data => {
        console.log('Order details:', data);
        if (data.data && data.data.items) {
            console.log('Items:', data.data.items);
        }
    })
    .catch(error => console.error('Error:', error));</pre>
                    </div>
                    <div class="test-controls">
                        <button class="test-btn primary" onclick="testEndpoint('GET', 'orders/1')">
                            <i class="fas fa-play"></i> Test Order Details
                        </button>
                    </div>
                    <div id="single-order-result" class="result-container" style="display: none;"></div>
                </div>

                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <span class="method PUT">PUT</span>
                        <span class="endpoint-path">/orders/{id}/status</span>
                    </div>
                    <div class="endpoint-desc">Update order status. Valid statuses: pending, confirmed, delivered, cancelled.</div>
                    <div class="code-container">
                        <div class="code-header">
                            <span>JavaScript Fetch Example</span>
                            <button class="copy-btn" data-target="update-status-code">Copy</button>
                        </div>
                        <pre id="update-status-code">// Update order status
fetch('api.php/orders/1/status', {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        status: 'confirmed'
    })
})
.then(response => response.json())
.then(data => console.log('Status updated:', data))
.catch(error => console.error('Error:', error));</pre>
                    </div>
                    <div class="test-controls">
                        <button class="test-btn primary" onclick="testEndpoint('PUT', 'orders/1/status', {
                            status: 'confirmed'
                        })">
                            <i class="fas fa-play"></i> Test Status Update
                        </button>
                    </div>
                    <div id="update-status-result" class="result-container" style="display: none;"></div>
                </div>
            </div>
        </div>

        <!-- Delivery Tab -->
        <div id="delivery" class="tab-content">
            <div class="api-section">
                <div class="api-header">
                    <h2 class="api-title"><i class="fas fa-motorcycle"></i> Delivery Endpoints</h2>
                </div>

                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <span class="method GET">GET</span>
                        <span class="endpoint-path">/delivery/order/{id}</span>
                    </div>
                    <div class="endpoint-desc">Get delivery tracking information for an order.</div>
                    <div class="code-container">
                        <div class="code-header">
                            <span>JavaScript Fetch Example</span>
                            <button class="copy-btn" data-target="delivery-tracking-code">Copy</button>
                        </div>
                        <pre id="delivery-tracking-code">// Get delivery tracking
fetch('api.php/delivery/order/1')
    .then(response => response.json())
    .then(data => {
        console.log('Delivery info:', data);
        if (data.data) {
            console.log(`Driver: ${data.data.driver_name}`);
            console.log(`Status: ${data.data.delivery_status}`);
        }
    })
    .catch(error => console.error('Error:', error));</pre>
                    </div>
                    <div class="test-controls">
                        <button class="test-btn primary" onclick="testEndpoint('GET', 'delivery/order/1')">
                            <i class="fas fa-play"></i> Test Delivery Tracking
                        </button>
                    </div>
                    <div id="delivery-tracking-result" class="result-container" style="display: none;"></div>
                </div>
            </div>
        </div>

        <!-- Reviews Tab -->
        <div id="reviews" class="tab-content">
            <div class="api-section">
                <div class="api-header">
                    <h2 class="api-title"><i class="fas fa-star"></i> Review Endpoints</h2>
                </div>

                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <span class="method POST">POST</span>
                        <span class="endpoint-path">/reviews</span>
                    </div>
                    <div class="endpoint-desc">Submit a review for an order. Rating must be between 1-5.</div>
                    <div class="code-container">
                        <div class="code-header">
                            <span>JavaScript Fetch Example</span>
                            <button class="copy-btn" data-target="submit-review-code">Copy</button>
                        </div>
                        <pre id="submit-review-code">// Submit review
fetch('api.php/reviews', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        user_id: 1,
        restaurant_id: 1,
        order_id: 1,
        rating: 5,
        comment: 'Excellent food and service!'
    })
})
.then(response => response.json())
.then(data => console.log('Review submitted:', data))
.catch(error => console.error('Error:', error));</pre>
                    </div>
                    <div class="test-controls">
                        <button class="test-btn primary" onclick="testEndpoint('POST', 'reviews', {
                            user_id: 1,
                            restaurant_id: 1,
                            order_id: 1,
                            rating: 5,
                            comment: 'Great experience!'
                        })">
                            <i class="fas fa-play"></i> Test Submit Review
                        </button>
                    </div>
                    <div id="submit-review-result" class="result-container" style="display: none;"></div>
                </div>

                <div class="endpoint-card">
                    <div class="endpoint-header">
                        <span class="method GET">GET</span>
                        <span class="endpoint-path">/reviews/restaurant/{id}</span>
                    </div>
                    <div class="endpoint-desc">Get all reviews for a restaurant with average rating.</div>
                    <div class="code-container">
                        <div class="code-header">
                            <span>JavaScript Fetch Example</span>
                            <button class="copy-btn" data-target="restaurant-reviews-code">Copy</button>
                        </div>
                        <pre id="restaurant-reviews-code">// Get restaurant reviews
fetch('api.php/reviews/restaurant/1')
    .then(response => response.json())
    .then(data => {
        console.log('Restaurant reviews:', data);
        if (data.stats) {
            console.log(`Average rating: ${data.stats.average_rating}`);
            console.log(`Total reviews: ${data.stats.total_reviews}`);
        }
    })
    .catch(error => console.error('Error:', error));</pre>
                    </div>
                    <div class="test-controls">
                        <button class="test-btn primary" onclick="testEndpoint('GET', 'reviews/restaurant/1')">
                            <i class="fas fa-play"></i> Test Restaurant Reviews
                        </button>
                    </div>
                    <div id="restaurant-reviews-result" class="result-container" style="display: none;"></div>
                </div>
            </div>
        </div>

        <!-- Code Examples Tab -->
        <div id="code" class="tab-content">
            <div class="api-section">
                <div class="api-header">
                    <h2 class="api-title"><i class="fas fa-code"></i> Complete API Integration Examples</h2>
                </div>

                <div class="endpoint-card">
                    <div class="code-container">
                        <div class="code-header">
                            <span>Complete User Journey Example</span>
                            <button class="copy-btn" data-target="complete-journey">Copy</button>
                        </div>
                        <pre id="complete-journey">// DishDrop Complete User Journey Example
class DishDropAPI {
    constructor() {
        this.baseURL = 'api.php/';
        this.token = localStorage.getItem('auth_token');
    }
    
    // 1. Login
    async login(email, password) {
        try {
            const response = await fetch(this.baseURL + 'auth/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            const data = await response.json();
            
            if (data.success) {
                this.token = data.user.user_id;
                localStorage.setItem('auth_token', this.token);
                localStorage.setItem('user', JSON.stringify(data.user));
                return data.user;
            } else {
                throw new Error(data.error);
            }
        } catch (error) {
            console.error('Login failed:', error);
            throw error;
        }
    }
    
    // 2. Get Restaurants
    async getRestaurants(cuisine = null) {
        try {
            let url = 'restaurants';
            if (cuisine) url += `?cuisine=${cuisine}`;
            
            const response = await fetch(this.baseURL + url);
            return await response.json();
        } catch (error) {
            console.error('Failed to get restaurants:', error);
            throw error;
        }
    }
    
    // 3. Get Restaurant Menu
    async getMenu(restaurantId) {
        try {
            const response = await fetch(this.baseURL + `menus/restaurant/${restaurantId}`);
            return await response.json();
        } catch (error) {
            console.error('Failed to get menu:', error);
            throw error;
        }
    }
    
    // 4. Create Order
    async createOrder(userId, restaurantId, items) {
        try {
            const response = await fetch(this.baseURL + 'orders', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    user_id: userId,
                    restaurant_id: restaurantId,
                    items: items
                })
            });
            const data = await response.json();
            
            if (data.success) {
                // Save order ID for payment and tracking
                localStorage.setItem('current_order_id', data.order_id);
                return data;
            } else {
                throw new Error(data.error);
            }
        } catch (error) {
            console.error('Failed to create order:', error);
            throw error;
        }
    }
    
    // 5. Process Payment
    async processPayment(orderId, paymentMethod, amount) {
        try {
            const response = await fetch(this.baseURL + 'payments', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    order_id: orderId,
                    payment_method: paymentMethod,
                    amount: amount
                })
            });
            return await response.json();
        } catch (error) {
            console.error('Payment failed:', error);
            throw error;
        }
    }
    
    // 6. Track Delivery
    async trackDelivery(orderId) {
        try {
            const response = await fetch(this.baseURL + `delivery/order/${orderId}`);
            return await response.json();
        } catch (error) {
            console.error('Failed to track delivery:', error);
            throw error;
        }
    }
    
    // 7. Submit Review
    async submitReview(userId, restaurantId, orderId, rating, comment = '') {
        try {
            const response = await fetch(this.baseURL + 'reviews', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    user_id: userId,
                    restaurant_id: restaurantId,
                    order_id: orderId,
                    rating: rating,
                    comment: comment
                })
            });
            return await response.json();
        } catch (error) {
            console.error('Failed to submit review:', error);
            throw error;
        }
    }
    
    // Example usage:
    async completeOrderJourney() {
        try {
            // Step 1: Login
            const user = await this.login('john@example.com', 'password123');
            console.log('Logged in as:', user.name);
            
            // Step 2: Browse restaurants
            const restaurants = await this.getRestaurants('Italian');
            console.log('Found restaurants:', restaurants.data.length);
            
            // Step 3: View menu
            const menu = await this.getMenu(1);
            console.log('Menu items:', menu.data.length);
            
            // Step 4: Create order
            const order = await this.createOrder(user.user_id, 1, [
                { menu_id: 1, quantity: 2 },
                { menu_id: 2, quantity: 1 }
            ]);
            console.log('Order created:', order.order_id);
            
            // Step 5: Process payment
            const payment = await this.processPayment(order.order_id, 'card', order.total_amount);
            console.log('Payment processed:', payment.success);
            
            // Step 6: Track delivery (polling example)
            setInterval(async () => {
                const tracking = await this.trackDelivery(order.order_id);
                console.log('Delivery status:', tracking.data.delivery_status);
                
                if (tracking.data.delivery_status === 'delivered') {
                    console.log('Order delivered!');
                    // Step 7: Submit review
                    const review = await this.submitReview(
                        user.user_id,
                        1,
                        order.order_id,
                        5,
                        'Excellent service!'
                    );
                    console.log('Review submitted:', review.success);
                }
            }, 30000); // Poll every 30 seconds
            
        } catch (error) {
            console.error('Order journey failed:', error);
        }
    }
}

// Initialize and run example
const api = new DishDropAPI();
// api.completeOrderJourney();</pre>
                    </div>
                </div>

                <div class="endpoint-card">
                    <div class="code-container">
                        <div class="code-header">
                            <span>PHP cURL Examples</span>
                            <button class="copy-btn" data-target="php-curl">Copy</button>
                        </div>
                        <pre id="php-curl"><?php
                                            // PHP cURL Examples for DishDrop API

                                            // Function to call API
                                            function callDishDropAPI($endpoint, $method = 'GET', $data = null)
                                            {
                                                $url = "http://localhost/DishDrop/api.php/" . $endpoint;

                                                $ch = curl_init();
                                                curl_setopt($ch, CURLOPT_URL, $url);
                                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

                                                if ($method == 'POST' || $method == 'PUT') {
                                                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                                                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                                                        'Content-Type: application/json',
                                                        'Content-Length: ' . strlen(json_encode($data))
                                                    ]);
                                                }

                                                $response = curl_exec($ch);
                                                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                                curl_close($ch);

                                                return [
                                                    'status' => $httpCode,
                                                    'data' => json_decode($response, true)
                                                ];
                                            }

                                            // Example 1: Get all restaurants
                                            $restaurants = callDishDropAPI('restaurants');
                                            if ($restaurants['status'] == 200) {
                                                $count = isset($restaurants['data']['data']) ? count($restaurants['data']['data']) : 0;
                                                echo "Total restaurants: " . $count . "\n";
                                            }

                                            // Example 2: User login
                                            $loginData = [
                                                'email' => 'john@example.com',
                                                'password' => 'password123'
                                            ];
                                            $loginResult = callDishDropAPI('auth/login', 'POST', $loginData);
                                            if ($loginResult['status'] == 200 && isset($loginResult['data']['success']) && $loginResult['data']['success']) {
                                                $user = $loginResult['data']['user'];
                                                echo "Logged in as: " . $user['name'] . "\n";
                                            }

                                            // Example 3: Create order
                                            $orderData = [
                                                'user_id' => 1,
                                                'restaurant_id' => 1,
                                                'items' => [
                                                    ['menu_id' => 1, 'quantity' => 2],
                                                    ['menu_id' => 2, 'quantity' => 1]
                                                ]
                                            ];
                                            $orderResult = callDishDropAPI('orders', 'POST', $orderData);
                                            if ($orderResult['status'] == 200 && isset($orderResult['data']['success']) && $orderResult['data']['success']) {
                                                echo "Order created with ID: " . $orderResult['data']['order_id'] . "\n";
                                            }

                                            // Example 4: Get user orders
                                            $userOrders = callDishDropAPI('orders/user/1');
                                            if ($userOrders['status'] == 200 && isset($userOrders['data']['data'])) {
                                                $count = is_array($userOrders['data']['data']) ? count($userOrders['data']['data']) : 0;
                                                echo "User has " . $count . " orders\n";
                                            }
                                            ?></pre>
                    </div>
                </div>

                <div class="endpoint-card">
                    <div class="code-container">
                        <div class="code-header">
                            <span>Python Requests Example</span>
                            <button class="copy-btn" data-target="python-example">Copy</button>
                        </div>
                        <pre id="python-example"># Python DishDrop API Client
import requests
import json

class DishDropClient:
    def __init__(self, base_url="http://localhost/DishDrop/api.php"):
        self.base_url = base_url
        self.session = requests.Session()
    
    def call_api(self, endpoint, method='GET', data=None):
        url = f"{self.base_url}/{endpoint}"
        headers = {'Content-Type': 'application/json'}
        
        try:
            if method.upper() == 'GET':
                response = self.session.get(url, headers=headers)
            elif method.upper() == 'POST':
                response = self.session.post(url, headers=headers, json=data)
            elif method.upper() == 'PUT':
                response = self.session.put(url, headers=headers, json=data)
            elif method.upper() == 'DELETE':
                response = self.session.delete(url, headers=headers)
            
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            print(f"Request failed: {e}")
            return {"error": str(e)}
    
    def login(self, email, password):
        """User login"""
        data = {'email': email, 'password': password}
        return self.call_api('auth/login', 'POST', data)
    
    def get_restaurants(self, cuisine=None):
        """Get restaurants with optional cuisine filter"""
        endpoint = 'restaurants'
        if cuisine:
            endpoint += f'?cuisine={cuisine}'
        return self.call_api(endpoint)
    
    def create_order(self, user_id, restaurant_id, items):
        """Create new order"""
        data = {
            'user_id': user_id,
            'restaurant_id': restaurant_id,
            'items': items
        }
        return self.call_api('orders', 'POST', data)
    
    def track_delivery(self, order_id):
        """Track order delivery"""
        return self.call_api(f'delivery/order/{order_id}')

# Example usage
if __name__ == "__main__":
    client = DishDropClient()
    
    try:
        # Login
        login_result = client.login('john@example.com', 'password123')
        if login_result.get('success'):
            print(f"Logged in as: {login_result['user']['name']}")
        
        # Get restaurants
        restaurants = client.get_restaurants('Italian')
        count = restaurants.get('count', 0) if isinstance(restaurants, dict) else 0
        print(f"Found {count} Italian restaurants")
        
        # Create test order
        order_data = {
            'user_id': 1,
            'restaurant_id': 1,
            'items': [
                {'menu_id': 1, 'quantity': 2},
                {'menu_id': 2, 'quantity': 1}
            ]
        }
        order_result = client.create_order(**order_data)
        if order_result.get('success'):
            print(f"Order created: ID {order_result['order_id']}")
            
            # Track delivery
            delivery_info = client.track_delivery(order_result['order_id'])
            delivery_status = delivery_info.get('data', {}).get('delivery_status', 'unknown') if isinstance(delivery_info, dict) else 'unknown'
            print(f"Delivery status: {delivery_status}")
            
    except Exception as e:
        print(f"API Error: {e}")</pre>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom Tester -->
        <div class="api-section" style="margin-top: 30px;">
            <div class="api-header">
                <h2 class="api-title"><i class="fas fa-terminal"></i> Custom API Tester</h2>
            </div>
            <div class="custom-tester-grid">
                <div>
                    <div class="form-group">
                        <label for="customMethod">Method:</label>
                        <select id="customMethod" class="form-control">
                            <option value="GET">GET</option>
                            <option value="POST">POST</option>
                            <option value="PUT">PUT</option>
                            <option value="DELETE">DELETE</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="customEndpoint">Endpoint:</label>
                        <input type="text" id="customEndpoint" placeholder="restaurants/1" class="form-control">
                    </div>
                    <button class="test-btn primary" onclick="runCustomTest()" style="width: 100%;">
                        <i class="fas fa-play"></i> Run Custom Test
                    </button>
                </div>
                <div>
                    <div class="form-group">
                        <label for="customBody">Request Body (JSON):</label>
                        <textarea id="customBody" placeholder='{"key": "value"}' class="form-control"></textarea>
                    </div>
                    <div class="test-controls">
                        <button class="test-btn secondary" onclick="formatJSON()">
                            <i class="fas fa-align-left"></i> Format JSON
                        </button>
                        <button class="test-btn secondary" onclick="clearCustom()">
                            <i class="fas fa-trash"></i> Clear
                        </button>
                    </div>
                </div>
            </div>
            <div id="custom-result" class="result-container" style="display: none;"></div>
        </div>
    </div>

    <script>
        // Test statistics
        let testStats = {
            total: 14,
            tested: 0,
            successful: 0
        };

        // Update statistics display
        function updateStats() {
            document.getElementById('totalEndpoints').textContent = testStats.total;
            document.getElementById('testedEndpoints').textContent = testStats.tested;
            const successRate = testStats.tested > 0 ? Math.round((testStats.successful / testStats.tested) * 100) : 0;
            document.getElementById('successRate').textContent = successRate + '%';
        }

        // Tab navigation
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });

            // Show selected tab
            const tabElement = document.getElementById(tabName);
            if (tabElement) {
                tabElement.classList.add('active');
            }

            // Activate clicked tab
            if (event && event.currentTarget) {
                event.currentTarget.classList.add('active');
            }
        }

        // Copy code to clipboard
        function copyCode(elementId) {
            const codeElement = document.getElementById(elementId);
            if (!codeElement) return;

            const textArea = document.createElement('textarea');
            textArea.value = codeElement.textContent;
            document.body.appendChild(textArea);
            textArea.select();

            try {
                document.execCommand('copy');

                // Show copied notification
                const btn = event.target;
                const originalText = btn.textContent;
                btn.textContent = 'Copied!';
                btn.style.background = '#28a745';
                setTimeout(() => {
                    btn.textContent = 'Copy';
                    btn.style.background = '';
                }, 2000);
            } catch (err) {
                console.error('Failed to copy text:', err);
            } finally {
                document.body.removeChild(textArea);
            }
        }

        // Format JSON in textarea
        function formatJSON() {
            const textarea = document.getElementById('customBody');
            if (!textarea || !textarea.value.trim()) return;

            try {
                const obj = JSON.parse(textarea.value);
                textarea.value = JSON.stringify(obj, null, 2);
            } catch (e) {
                alert('Invalid JSON: ' + e.message);
            }
        }

        // Clear custom test fields
        function clearCustom() {
            document.getElementById('customEndpoint').value = '';
            document.getElementById('customBody').value = '';
            const resultDiv = document.getElementById('custom-result');
            if (resultDiv) {
                resultDiv.style.display = 'none';
            }
        }

        // Clear all results
        function clearResults() {
            document.querySelectorAll('.result-container').forEach(div => {
                div.style.display = 'none';
                div.innerHTML = '';
            });
            const allResults = document.getElementById('all-results');
            if (allResults) allResults.innerHTML = '';
            const quickResults = document.getElementById('quick-results');
            if (quickResults) quickResults.innerHTML = '';

            testStats.tested = 0;
            testStats.successful = 0;
            updateStats();
        }

        // Show result in a specific container
        function showResult(endpointId, message) {
            const resultDiv = document.getElementById(endpointId + '-result');
            if (!resultDiv) return;

            resultDiv.innerHTML = `
                <div class="result-header">
                    <div class="result-status">
                        <span class="status-indicator status-success"></span>
                        <span>Expected Response</span>
                    </div>
                </div>
                <pre class="result-pre">${escapeHTML(message)}</pre>
            `;
            resultDiv.style.display = 'block';
            highlightJSON(resultDiv.querySelector('.result-pre'));
        }

        // Escape HTML special characters
        function escapeHTML(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Test a single endpoint
        async function testEndpoint(method, endpoint, data = null, resultContainer = null) {
            const url = `api.php/${endpoint}`;

            // Create result container if not provided
            if (!resultContainer) {
                const endpointId = endpoint.replace(/[\/?=]/g, '-');
                resultContainer = document.getElementById(endpointId + '-result') ||
                    document.getElementById('custom-result');
                if (!resultContainer) {
                    resultContainer = document.createElement('div');
                    resultContainer.className = 'result-container';
                    const allResults = document.getElementById('all-results');
                    if (allResults) {
                        allResults.appendChild(resultContainer);
                    }
                }
            }

            // Show loading state
            resultContainer.innerHTML = `
                <div class="result-header">
                    <div class="result-status">
                        <span class="status-indicator status-loading"></span>
                        <span>${method} ${endpoint}</span>
                    </div>
                    <span>Loading...</span>
                </div>
                <pre class="result-pre">Sending request...</pre>
            `;
            resultContainer.style.display = 'block';

            // Update stats
            testStats.tested++;
            updateStats();

            try {
                const options = {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json'
                    }
                };

                if (data && (method === 'POST' || method === 'PUT')) {
                    options.body = JSON.stringify(data);
                }

                const startTime = Date.now();
                const response = await fetch(url, options);
                const responseTime = Date.now() - startTime;

                let result;
                try {
                    result = await response.json();
                } catch (e) {
                    const rawText = await response.text();
                    result = {
                        error: 'Invalid JSON response',
                        raw: rawText
                    };
                }

                const statusClass = response.ok ? 'status-success' : 'status-error';
                const statusText = response.ok ? 'Success' : 'Error';

                if (response.ok) {
                    testStats.successful++;
                }

                resultContainer.innerHTML = `
                    <div class="result-header">
                        <div class="result-status">
                            <span class="status-indicator ${statusClass}"></span>
                            <span>${method} ${endpoint} - ${statusText} (${response.status})</span>
                        </div>
                        <span>${responseTime}ms</span>
                    </div>
                    <pre class="result-pre">${escapeHTML(JSON.stringify(result, null, 2))}</pre>
                `;

                // Highlight JSON syntax
                highlightJSON(resultContainer.querySelector('.result-pre'));

            } catch (error) {
                testStats.tested--;
                resultContainer.innerHTML = `
                    <div class="result-header">
                        <div class="result-status">
                            <span class="status-indicator status-error"></span>
                            <span>${method} ${endpoint} - Network Error</span>
                        </div>
                    </div>
                    <pre class="result-pre">Error: ${escapeHTML(error.message)}</pre>
                `;
            }

            updateStats();
            resultContainer.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }

        // Run custom test
        async function runCustomTest() {
            const method = document.getElementById('customMethod').value;
            const endpoint = document.getElementById('customEndpoint').value;
            const bodyText = document.getElementById('customBody').value;

            if (!endpoint.trim()) {
                alert('Please enter an endpoint');
                return;
            }

            let data = null;
            if (bodyText.trim()) {
                try {
                    data = JSON.parse(bodyText);
                } catch (e) {
                    alert('Invalid JSON: ' + e.message);
                    return;
                }
            }

            await testEndpoint(method, endpoint, data);
        }

        // Run all tests
        async function runAllTests() {
            const tests = [{
                    method: 'GET',
                    endpoint: 'restaurants',
                    data: null
                },
                {
                    method: 'GET',
                    endpoint: 'restaurants/1',
                    data: null
                },
                {
                    method: 'GET',
                    endpoint: 'menus/restaurant/1',
                    data: null
                },
                {
                    method: 'POST',
                    endpoint: 'auth/login',
                    data: {
                        email: 'john@example.com',
                        password: 'password123'
                    }
                },
                {
                    method: 'GET',
                    endpoint: 'orders/user/1',
                    data: null
                }
            ];

            const quickResults = document.getElementById('quick-results');
            if (quickResults) {
                quickResults.innerHTML = '<div class="loading">Starting tests...</div>';
            }

            for (const test of tests) {
                const resultDiv = document.createElement('div');
                resultDiv.className = 'result-container';
                if (quickResults) {
                    quickResults.appendChild(resultDiv);
                }
                await testEndpoint(test.method, test.endpoint, test.data, resultDiv);
                await new Promise(resolve => setTimeout(resolve, 500)); // Delay between tests
            }
        }

        // Test specific endpoint groups
        async function testAuthEndpoints() {
            const tests = [{
                    method: 'POST',
                    endpoint: 'auth/login',
                    data: {
                        email: 'john@example.com',
                        password: 'password123'
                    }
                },
                {
                    method: 'POST',
                    endpoint: 'auth/register',
                    data: {
                        name: 'Test User ' + Date.now(),
                        email: 'test' + Date.now() + '@example.com',
                        password: 'password123',
                        phone: '+1234567890',
                        address: 'Test Address'
                    }
                }
            ];

            await runTestGroup('Authentication Tests', tests);
        }

        async function testRestaurantEndpoints() {
            const tests = [{
                    method: 'GET',
                    endpoint: 'restaurants',
                    data: null
                },
                {
                    method: 'GET',
                    endpoint: 'restaurants?cuisine=Italian',
                    data: null
                },
                {
                    method: 'GET',
                    endpoint: 'restaurants/1',
                    data: null
                },
                {
                    method: 'GET',
                    endpoint: 'menus/restaurant/1',
                    data: null
                }
            ];

            await runTestGroup('Restaurant Tests', tests);
        }

        async function testOrderEndpoints() {
            const tests = [{
                    method: 'GET',
                    endpoint: 'orders/user/1',
                    data: null
                },
                {
                    method: 'GET',
                    endpoint: 'orders/1',
                    data: null
                }
            ];

            await runTestGroup('Order Tests', tests);
        }

        async function runTestGroup(groupName, tests) {
            const groupDiv = document.createElement('div');
            groupDiv.className = 'api-section';
            groupDiv.innerHTML = `<h3>${groupName}</h3>`;
            const allResults = document.getElementById('all-results');
            if (allResults) {
                allResults.appendChild(groupDiv);
            }

            for (const test of tests) {
                const resultDiv = document.createElement('div');
                resultDiv.className = 'result-container';
                groupDiv.appendChild(resultDiv);
                await testEndpoint(test.method, test.endpoint, test.data, resultDiv);
                await new Promise(resolve => setTimeout(resolve, 300));
            }
        }

        // JSON syntax highlighting
        function highlightJSON(preElement) {
            if (!preElement || !preElement.textContent) return;

            let jsonString = preElement.textContent;
            try {
                const jsonObj = JSON.parse(jsonString);
                jsonString = JSON.stringify(jsonObj, null, 2);

                // Escape HTML entities
                jsonString = jsonString
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');

                // Apply syntax highlighting
                jsonString = jsonString.replace(/("(\\"|[^"])*")(\s*:)?/g, function(match, p1, p2, p3) {
                        let cls = 'json-key';
                        if (p3) {
                            return '<span class="' + cls + '">' + p1 + p3 + '</span>';
                        } else {
                            cls = 'json-string';
                            return '<span class="' + cls + '">' + p1 + '</span>';
                        }
                    })
                    .replace(/\b(true|false)\b/g, '<span class="json-boolean">$&</span>')
                    .replace(/\b(null)\b/g, '<span class="json-null">$&</span>')
                    .replace(/\b-?\d+(\.\d+)?([eE][+-]?\d+)?\b/g, '<span class="json-number">$&</span>');

                preElement.innerHTML = jsonString;
            } catch (e) {
                // Not valid JSON, leave as is with escaped HTML
                preElement.innerHTML = preElement.textContent
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateStats();

            // Add event listeners to tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabName = this.getAttribute('data-tab');
                    showTab(tabName);
                });
            });

            // Add event listeners to copy buttons
            document.querySelectorAll('.copy-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    if (targetId) {
                        copyCode(targetId);
                    }
                });
            });

            // Auto-format JSON in custom body
            const customBody = document.getElementById('customBody');
            if (customBody) {
                customBody.addEventListener('input', function() {
                    try {
                        if (this.value.trim()) {
                            JSON.parse(this.value);
                            this.style.borderColor = '#28a745';
                        } else {
                            this.style.borderColor = '#ddd';
                        }
                    } catch (e) {
                        this.style.borderColor = '#dc3545';
                    }
                });
            }

            // Load sample endpoints
            const sampleEndpoints = [
                'restaurants',
                'restaurants/1',
                'menus/restaurant/1',
                'orders/user/1',
                'delivery/order/1',
                'reviews/restaurant/1'
            ];

            const endpointSelect = document.getElementById('customEndpoint');
            if (endpointSelect) {
                sampleEndpoints.forEach(endpoint => {
                    const option = document.createElement('option');
                    option.value = endpoint;
                    option.textContent = endpoint;
                    endpointSelect.appendChild(option);
                });
            }

            // Add keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl+Enter to run custom test
                if (e.ctrlKey && e.key === 'Enter') {
                    const activeElement = document.activeElement;
                    if (activeElement.id === 'customBody' || activeElement.id === 'customEndpoint') {
                        runCustomTest();
                    }
                }
            });
        });
    </script>
</body>

</html>
<?php
/**
 * Funciones principales del sistema de chocolates
 */

// Conectar a la base de datos
function getDBConnection() {
    static $connection = null;
    
    if ($connection === null) {
        if (!defined('DB_HOST')) {
            require_once 'config.php';
        }
        
        $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($connection->connect_error) {
            die("Error de conexión: " . $connection->connect_error);
        }
        
        $connection->set_charset("utf8mb4");
    }
    
    return $connection;
}

// Función para sanitizar entrada
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Función para generar número de pedido único
function generateOrderNumber() {
    $prefix = defined('ORDER_PREFIX') ? ORDER_PREFIX : 'CHO';
    return $prefix . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

// Función para formatear precio
function formatPrice($price) {
    $symbol = defined('CURRENCY_SYMBOL') ? CURRENCY_SYMBOL : '$';
    return $symbol . number_format($price, 2);
}

// Función para obtener configuración del sistema
function getSetting($key, $default = '') {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['setting_value'];
    }
    
    return $default;
}

// Función para calcular costo de envío
function calculateShippingCost($postalCode, $total) {
    $db = getDBConnection();
    $stmt = $db->prepare("
        SELECT delivery_cost, min_order_amount 
        FROM delivery_zones 
        WHERE active = 1 AND (postal_codes LIKE ? OR postal_codes LIKE ?)
        ORDER BY min_order_amount ASC 
        LIMIT 1
    ");
    
    $pattern1 = "%$postalCode%";
    $pattern2 = "%" . substr($postalCode, 0, 2) . "%";
    $stmt->bind_param("ss", $pattern1, $pattern2);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $freeShippingAmount = getSetting('free_shipping_amount', 500);
        if ($total >= $freeShippingAmount) {
            return 0;
        }
        return $row['delivery_cost'];
    }
    
    return 100; // Costo por defecto
}

// Función para enviar email
function sendEmail($to, $subject, $message, $isHTML = true) {
    $headers = array();
    $headers[] = 'From: ' . getSetting('email_from', 'noreply@chocolates.com');
    
    if ($isHTML) {
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=UTF-8';
    }
    
    return mail($to, $subject, $message, implode("\r\n", $headers));
}

// Función para verificar si el usuario está logueado
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Función para verificar si es administrador
function isAdmin() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

// Función para redireccionar
function redirect($url) {
    header("Location: $url");
    exit;
}

// Función para mostrar mensaje flash
function setFlashMessage($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

// Función para obtener productos
function getProducts($limit = null, $category = null, $featured = null) {
    $db = getDBConnection();
    $sql = "SELECT * FROM products WHERE active = 1";
    $params = [];
    $types = "";
    
    if ($category) {
        $sql .= " AND category = ?";
        $params[] = $category;
        $types .= "s";
    }
    
    if ($featured !== null) {
        $sql .= " AND featured = ?";
        $params[] = $featured;
        $types .= "i";
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT ?";
        $params[] = $limit;
        $types .= "i";
    }
    
    $stmt = $db->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Función para obtener un producto por ID
function getProduct($id) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM products WHERE id = ? AND active = 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

// Función para agregar al carrito
function addToCart($productId, $quantity = 1) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }
}

// Función para obtener el carrito
function getCart() {
    if (!isset($_SESSION['cart'])) {
        return [];
    }
    
    $cart = [];
    $db = getDBConnection();
    
    foreach ($_SESSION['cart'] as $productId => $quantity) {
        $product = getProduct($productId);
        if ($product) {
            $product['quantity'] = $quantity;
            $product['subtotal'] = $product['price'] * $quantity;
            $cart[] = $product;
        }
    }
    
    return $cart;
}

// Función para calcular total del carrito
function getCartTotal() {
    $cart = getCart();
    $total = 0;
    
    foreach ($cart as $item) {
        $total += $item['subtotal'];
    }
    
    return $total;
}

// Función para limpiar el carrito
function clearCart() {
    unset($_SESSION['cart']);
}

// Inicializar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
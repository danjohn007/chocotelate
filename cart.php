<?php
require_once 'includes/functions.php';

// Si no existe config.php, redireccionar al instalador
if (!file_exists('config.php')) {
    header('Location: installer.php');
    exit;
}

require_once 'config.php';

// Procesar acciones del carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            $productId = (int)($_POST['product_id'] ?? 0);
            $quantity = (int)($_POST['quantity'] ?? 1);
            
            if ($productId > 0 && $quantity > 0) {
                $product = getProduct($productId);
                if ($product) {
                    addToCart($productId, $quantity);
                    setFlashMessage("Producto agregado al carrito exitosamente", 'success');
                } else {
                    setFlashMessage("Producto no encontrado", 'error');
                }
            }
            break;
            
        case 'update':
            $productId = (int)($_POST['product_id'] ?? 0);
            $quantity = (int)($_POST['quantity'] ?? 0);
            
            if ($productId > 0) {
                if ($quantity > 0) {
                    $_SESSION['cart'][$productId] = $quantity;
                    setFlashMessage("Cantidad actualizada", 'success');
                } else {
                    unset($_SESSION['cart'][$productId]);
                    setFlashMessage("Producto eliminado del carrito", 'info');
                }
            }
            break;
            
        case 'remove':
            $productId = (int)($_POST['product_id'] ?? 0);
            if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
                unset($_SESSION['cart'][$productId]);
                setFlashMessage("Producto eliminado del carrito", 'info');
            }
            break;
            
        case 'clear':
            clearCart();
            setFlashMessage("Carrito vaciado", 'info');
            break;
    }
    
    // Redireccionar para evitar reenvío de formulario
    header('Location: cart.php');
    exit;
}

$cart = getCart();
$cartTotal = getCartTotal();
$siteName = SITE_NAME;
$flashMessage = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - <?php echo htmlspecialchars($siteName); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --chocolate-dark: #3c2415;
            --chocolate-medium: #8b4513;
            --chocolate-light: #d2b48c;
            --cream: #f5f5dc;
        }
        
        body {
            font-family: 'Georgia', serif;
            background-color: var(--cream);
        }
        
        .navbar {
            background-color: var(--chocolate-dark) !important;
        }
        
        .btn-chocolate {
            background-color: var(--chocolate-medium);
            border-color: var(--chocolate-medium);
            color: white;
        }
        
        .btn-chocolate:hover {
            background-color: var(--chocolate-dark);
            border-color: var(--chocolate-dark);
            color: white;
        }
        
        .cart-item {
            border-bottom: 1px solid #eee;
            padding: 1rem 0;
        }
        
        .quantity-input {
            width: 70px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                🍫 <?php echo htmlspecialchars($siteName); ?>
            </a>
            
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-arrow-left"></i> Volver a la Tienda
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if ($flashMessage): ?>
            <div class="alert alert-<?php echo $flashMessage['type'] === 'error' ? 'danger' : $flashMessage['type']; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($flashMessage['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-shopping-cart"></i> Mi Carrito de Compras</h4>
                    </div>
                    <div class="card-body">
                        <?php if (empty($cart)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                                <h5>Tu carrito está vacío</h5>
                                <p>Agrega algunos deliciosos chocolates a tu carrito.</p>
                                <a href="index.php" class="btn btn-chocolate">
                                    <i class="fas fa-shopping-bag"></i> Ir de Compras
                                </a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($cart as $item): ?>
                                <div class="cart-item">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 80px;">
                                                <?php if (!empty($item['image_url']) && file_exists($item['image_url'])): ?>
                                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                                         alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                                         class="img-fluid" style="max-height: 70px;">
                                                <?php else: ?>
                                                    <i class="fas fa-cookie-bite fa-2x text-secondary"></i>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <h6><?php echo htmlspecialchars($item['name']); ?></h6>
                                            <small class="text-muted"><?php echo formatPrice($item['price']); ?> c/u</small>
                                        </div>
                                        <div class="col-md-2">
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                                <input type="number" 
                                                       name="quantity" 
                                                       value="<?php echo $item['quantity']; ?>" 
                                                       min="0" 
                                                       max="99"
                                                       class="form-control quantity-input"
                                                       onchange="this.form.submit()">
                                            </form>
                                        </div>
                                        <div class="col-md-3">
                                            <strong><?php echo formatPrice($item['subtotal']); ?></strong>
                                        </div>
                                        <div class="col-md-1">
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="action" value="remove">
                                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('¿Eliminar este producto?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <div class="mt-3">
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="action" value="clear">
                                    <button type="submit" class="btn btn-outline-secondary" 
                                            onclick="return confirm('¿Vaciar todo el carrito?')">
                                        <i class="fas fa-trash"></i> Vaciar Carrito
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($cart)): ?>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Resumen del Pedido</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span><?php echo formatPrice($cartTotal); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Envío:</span>
                                <span class="text-muted">Calcular en checkout</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total:</strong>
                                <strong><?php echo formatPrice($cartTotal); ?></strong>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <a href="checkout.php" class="btn btn-chocolate btn-lg">
                                    <i class="fas fa-credit-card"></i> Proceder al Pago
                                </a>
                                <a href="index.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-shopping-bag"></i> Continuar Comprando
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
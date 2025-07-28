<?php
require_once 'includes/functions.php';

// Si existe config.php, incluirlo
if (file_exists('config.php')) {
    require_once 'config.php';
} else {
    // Si no hay configuración, redireccionar al instalador
    if (file_exists('installer.php')) {
        header('Location: installer.php');
        exit;
    } else {
        die('Sistema no configurado. Contacte al administrador.');
    }
}

$siteName = defined('SITE_NAME') ? SITE_NAME : 'Chocolates Artesanales';
$featuredProducts = getProducts(6, null, 1);
$flashMessage = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($siteName); ?> - Deliciosos chocolates artesanales</title>
    <meta name="description" content="<?php echo getSetting('site_description', 'Deliciosos chocolates artesanales con envío a domicilio'); ?>">
    
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
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .hero-section {
            background: linear-gradient(rgba(60, 36, 21, 0.7), rgba(60, 36, 21, 0.7)),
                        url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 600"><rect fill="%23d2b48c" width="1200" height="600"/><circle fill="%238b4513" cx="200" cy="150" r="30"/><circle fill="%238b4513" cx="400" cy="250" r="25"/><circle fill="%238b4513" cx="600" cy="180" r="35"/><circle fill="%238b4513" cx="800" cy="300" r="20"/><circle fill="%238b4513" cx="1000" cy="200" r="30"/></svg>');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        
        .product-card {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            background: white;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
        }
        
        .product-img {
            height: 250px;
            object-fit: cover;
            background-color: var(--chocolate-light);
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
        
        .section-title {
            color: var(--chocolate-dark);
            font-weight: bold;
            margin-bottom: 2rem;
        }
        
        .footer {
            background-color: var(--chocolate-dark);
            color: white;
        }
        
        .cart-badge {
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.8rem;
            position: absolute;
            top: -5px;
            right: -5px;
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
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">Nosotros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contacto</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item position-relative">
                        <a class="nav-link" href="cart.php">
                            <i class="fas fa-shopping-cart"></i> Carrito
                            <?php 
                            $cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
                            if ($cartCount > 0): ?>
                                <span class="cart-badge"><?php echo $cartCount; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="account.php">Mi Cuenta</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Salir</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Iniciar Sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Registrarse</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <?php if ($flashMessage): ?>
        <div class="alert alert-<?php echo $flashMessage['type'] === 'error' ? 'danger' : $flashMessage['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($flashMessage['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <h1 class="display-4 mb-4">Deliciosos Chocolates Artesanales</h1>
                    <p class="lead mb-4">
                        Descubre nuestra exquisita selección de chocolates hechos a mano con los mejores ingredientes.
                        Perfectos para regalar o darte un capricho especial.
                    </p>
                    <a href="products.php" class="btn btn-chocolate btn-lg">Ver Productos</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-5">
        <div class="container">
            <h2 class="section-title text-center">Productos Destacados</h2>
            
            <?php if (empty($featuredProducts)): ?>
                <div class="row">
                    <div class="col-12 text-center">
                        <p class="lead">No hay productos destacados disponibles.</p>
                        <a href="products.php" class="btn btn-chocolate">Ver Todos los Productos</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($featuredProducts as $product): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card product-card h-100">
                                <div class="product-img card-img-top d-flex align-items-center justify-content-center" style="height: 250px; background-color: var(--chocolate-light);">
                                    <?php if (!empty($product['image_url']) && file_exists($product['image_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                             class="img-fluid" style="max-height: 100%; max-width: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <i class="fas fa-cookie-bite fa-4x text-white"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <p class="card-text flex-grow-1">
                                        <?php echo htmlspecialchars($product['short_description'] ?: substr($product['description'], 0, 100) . '...'); ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="h5 mb-0 text-success"><?php echo formatPrice($product['price']); ?></span>
                                        <form method="post" action="cart.php" class="d-inline">
                                            <input type="hidden" name="action" value="add">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <button type="submit" class="btn btn-chocolate btn-sm">
                                                <i class="fas fa-cart-plus"></i> Agregar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="text-center mt-4">
                    <a href="products.php" class="btn btn-outline-secondary btn-lg">Ver Todos los Productos</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 text-center mb-4">
                    <i class="fas fa-truck fa-3x text-success mb-3"></i>
                    <h4>Envío a Domicilio</h4>
                    <p>Entregamos tus chocolates favoritos directamente en tu puerta.</p>
                </div>
                <div class="col-lg-4 text-center mb-4">
                    <i class="fas fa-heart fa-3x text-danger mb-3"></i>
                    <h4>Hecho con Amor</h4>
                    <p>Cada chocolate es elaborado artesanalmente con ingredientes de primera calidad.</p>
                </div>
                <div class="col-lg-4 text-center mb-4">
                    <i class="fas fa-gift fa-3x text-warning mb-3"></i>
                    <h4>Perfect para Regalar</h4>
                    <p>Presentaciones especiales ideales para cualquier ocasión especial.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer py-4">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h5><?php echo htmlspecialchars($siteName); ?></h5>
                    <p>Deliciosos chocolates artesanales con envío a domicilio.</p>
                </div>
                <div class="col-lg-6 text-lg-end">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteName); ?>. Todos los derechos reservados.</p>
                    <?php if (file_exists('admin/index.php')): ?>
                        <a href="admin/" class="text-light text-decoration-none">
                            <i class="fas fa-cog"></i> Panel de Administración
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
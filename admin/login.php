<?php
require_once '../includes/functions.php';

// Verificar si existe configuración
if (!file_exists('../config.php')) {
    header('Location: ../installer.php');
    exit;
}

require_once '../config.php';

// Si ya está logueado, redireccionar al dashboard
if (isAdmin()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Por favor ingresa email y contraseña.';
    } else {
        try {
            $db = getDBConnection();
            $stmt = $db->prepare("SELECT id, email, password, name FROM admin_users WHERE email = ? AND active = 1");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($user = $result->fetch_assoc()) {
                if (password_verify($password, $user['password'])) {
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_email'] = $user['email'];
                    $_SESSION['admin_name'] = $user['name'];
                    
                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'Credenciales incorrectas.';
                }
            } else {
                $error = 'Credenciales incorrectas.';
            }
        } catch (Exception $e) {
            $error = 'Error de conexión. Intenta nuevamente.';
        }
    }
}

$siteName = defined('SITE_NAME') ? SITE_NAME : 'Chocolates Artesanales';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Panel de Administración</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --chocolate-dark: #3c2415;
            --chocolate-medium: #8b4513;
            --chocolate-light: #d2b48c;
        }
        
        body {
            background: linear-gradient(135deg, var(--chocolate-light) 0%, var(--chocolate-medium) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .login-card {
            max-width: 400px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            border: none;
            border-radius: 15px;
        }
        
        .login-header {
            background-color: var(--chocolate-dark);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 2rem;
            text-align: center;
        }
        
        .btn-chocolate {
            background-color: var(--chocolate-medium);
            border-color: var(--chocolate-medium);
            color: white;
            padding: 0.75rem;
            font-weight: bold;
        }
        
        .btn-chocolate:hover {
            background-color: var(--chocolate-dark);
            border-color: var(--chocolate-dark);
            color: white;
        }
        
        .form-control:focus {
            border-color: var(--chocolate-medium);
            box-shadow: 0 0 0 0.2rem rgba(139, 69, 19, 0.25);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card login-card">
                    <div class="login-header">
                        <i class="fas fa-cog fa-3x mb-3"></i>
                        <h3>Panel de Administración</h3>
                        <p class="mb-0"><?php echo htmlspecialchars($siteName); ?></p>
                    </div>
                    
                    <div class="card-body p-4">
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="post">
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i> Email
                                </label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                       required 
                                       autofocus>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i> Contraseña
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       required>
                            </div>
                            
                            <button type="submit" class="btn btn-chocolate w-100">
                                <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                            </button>
                        </form>
                        
                        <div class="text-center mt-4">
                            <a href="../index.php" class="text-decoration-none">
                                <i class="fas fa-arrow-left"></i> Volver a la Tienda
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Información del instalador si existe -->
                <?php if (file_exists('../installer.php')): ?>
                    <div class="alert alert-warning mt-3 text-center">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Nota de Seguridad:</strong> Recuerda eliminar el archivo installer.php después de la instalación.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
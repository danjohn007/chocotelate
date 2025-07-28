<?php
/**
 * Instalador Web para Sistema de Venta de Chocolates
 * Web Installer for Chocolate Sales System
 * Compatible con Apache/cPanel hosting
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('INSTALLER_VERSION', '1.0.0');

class ChocolateInstaller {
    
    private $steps = [
        1 => 'Verificación de Requisitos',
        2 => 'Configuración de Base de Datos', 
        3 => 'Creación de Tablas',
        4 => 'Configuración del Sistema',
        5 => 'Usuario Administrador',
        6 => 'Finalización'
    ];
    
    private $errors = [];
    private $config = [];
    
    public function __construct() {
        $this->config = [
            'db_host' => 'localhost',
            'db_name' => '',
            'db_user' => '',
            'db_pass' => '',
            'admin_email' => '',
            'admin_password' => '',
            'site_name' => 'Chocolates Artesanales'
        ];
    }
    
    public function run() {
        $step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost($step);
        }
        
        $this->showHeader();
        $this->showNavigation($step);
        
        switch($step) {
            case 1: $this->stepRequirements(); break;
            case 2: $this->stepDatabase(); break;
            case 3: $this->stepTables(); break;
            case 4: $this->stepConfig(); break;
            case 5: $this->stepAdmin(); break;
            case 6: $this->stepFinish(); break;
            default: $this->stepRequirements(); break;
        }
        
        $this->showFooter();
    }
    
    private function handlePost($step) {
        switch($step) {
            case 2:
                $this->config['db_host'] = $_POST['db_host'] ?? 'localhost';
                $this->config['db_name'] = $_POST['db_name'] ?? '';
                $this->config['db_user'] = $_POST['db_user'] ?? '';
                $this->config['db_pass'] = $_POST['db_pass'] ?? '';
                
                if ($this->testDatabaseConnection()) {
                    $_SESSION['config'] = $this->config;
                    header('Location: installer.php?step=3');
                    exit;
                }
                break;
                
            case 3:
                if ($this->createTables()) {
                    header('Location: installer.php?step=4');
                    exit;
                }
                break;
                
            case 4:
                $this->config['site_name'] = $_POST['site_name'] ?? 'Chocolates Artesanales';
                if ($this->createConfigFile()) {
                    $_SESSION['config'] = $this->config;
                    header('Location: installer.php?step=5');
                    exit;
                }
                break;
                
            case 5:
                $this->config['admin_email'] = $_POST['admin_email'] ?? '';
                $this->config['admin_password'] = $_POST['admin_password'] ?? '';
                
                if ($this->createAdminUser()) {
                    header('Location: installer.php?step=6');
                    exit;
                }
                break;
        }
    }
    
    private function stepRequirements() {
        $checks = $this->checkRequirements();
        
        echo '<div class="card">';
        echo '<h2>🔍 Verificación de Requisitos del Sistema</h2>';
        
        foreach ($checks as $check) {
            $icon = $check['status'] ? '✅' : '❌';
            $class = $check['status'] ? 'success' : 'error';
            echo "<p class='$class'>$icon {$check['name']}: {$check['message']}</p>";
        }
        
        $allPassed = array_reduce($checks, function($carry, $item) {
            return $carry && $item['status'];
        }, true);
        
        if ($allPassed) {
            echo '<div class="success">¡Todos los requisitos se cumplen! Puedes continuar con la instalación.</div>';
            echo '<a href="installer.php?step=2" class="btn btn-primary">Continuar →</a>';
        } else {
            echo '<div class="error">Algunos requisitos no se cumplen. Por favor, contacta a tu proveedor de hosting.</div>';
        }
        
        echo '</div>';
    }
    
    private function stepDatabase() {
        if (isset($_SESSION['config'])) {
            $this->config = array_merge($this->config, $_SESSION['config']);
        }
        
        echo '<div class="card">';
        echo '<h2>🗄️ Configuración de Base de Datos</h2>';
        
        if (!empty($this->errors)) {
            echo '<div class="error">' . implode('<br>', $this->errors) . '</div>';
        }
        
        echo '<form method="post">';
        echo '<div class="form-group">';
        echo '<label>Servidor de Base de Datos:</label>';
        echo '<input type="text" name="db_host" value="' . htmlspecialchars($this->config['db_host']) . '" required>';
        echo '<small>Generalmente "localhost" en cPanel</small>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label>Nombre de la Base de Datos:</label>';
        echo '<input type="text" name="db_name" value="' . htmlspecialchars($this->config['db_name']) . '" required>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label>Usuario de Base de Datos:</label>';
        echo '<input type="text" name="db_user" value="' . htmlspecialchars($this->config['db_user']) . '" required>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label>Contraseña de Base de Datos:</label>';
        echo '<input type="password" name="db_pass" value="' . htmlspecialchars($this->config['db_pass']) . '">';
        echo '</div>';
        
        echo '<button type="submit" class="btn btn-primary">Probar Conexión y Continuar</button>';
        echo '</form>';
        echo '</div>';
    }
    
    private function stepTables() {
        echo '<div class="card">';
        echo '<h2>📋 Creación de Tablas</h2>';
        echo '<p>Se crearán las tablas necesarias para el sistema de venta de chocolates.</p>';
        
        echo '<form method="post">';
        echo '<button type="submit" class="btn btn-primary">Crear Tablas de Base de Datos</button>';
        echo '</form>';
        echo '</div>';
    }
    
    private function stepConfig() {
        echo '<div class="card">';
        echo '<h2>⚙️ Configuración del Sistema</h2>';
        
        echo '<form method="post">';
        echo '<div class="form-group">';
        echo '<label>Nombre del Sitio:</label>';
        echo '<input type="text" name="site_name" value="' . htmlspecialchars($this->config['site_name']) . '" required>';
        echo '</div>';
        
        echo '<button type="submit" class="btn btn-primary">Guardar Configuración</button>';
        echo '</form>';
        echo '</div>';
    }
    
    private function stepAdmin() {
        if (isset($_SESSION['config'])) {
            $this->config = array_merge($this->config, $_SESSION['config']);
        }
        
        echo '<div class="card">';
        echo '<h2>👤 Usuario Administrador</h2>';
        
        if (!empty($this->errors)) {
            echo '<div class="error">' . implode('<br>', $this->errors) . '</div>';
        }
        
        echo '<form method="post">';
        echo '<div class="form-group">';
        echo '<label>Email del Administrador:</label>';
        echo '<input type="email" name="admin_email" value="' . htmlspecialchars($this->config['admin_email']) . '" required>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label>Contraseña del Administrador:</label>';
        echo '<input type="password" name="admin_password" required>';
        echo '<small>Mínimo 8 caracteres</small>';
        echo '</div>';
        
        echo '<button type="submit" class="btn btn-primary">Crear Usuario Administrador</button>';
        echo '</form>';
        echo '</div>';
    }
    
    private function stepFinish() {
        echo '<div class="card">';
        echo '<h2>🎉 ¡Instalación Completada!</h2>';
        echo '<div class="success">';
        echo '<p>El sistema de venta de chocolates ha sido instalado exitosamente.</p>';
        echo '<p><strong>⚠️ IMPORTANTE:</strong> Por seguridad, elimina el archivo installer.php del servidor.</p>';
        echo '</div>';
        
        echo '<h3>Próximos pasos:</h3>';
        echo '<ul>';
        echo '<li>Accede al panel de administración en: <a href="admin/">admin/</a></li>';
        echo '<li>Configura los productos de chocolate</li>';
        echo '<li>Configura las zonas de envío</li>';
        echo '<li>Personaliza el diseño de la tienda</li>';
        echo '</ul>';
        
        echo '<a href="index.php" class="btn btn-primary">Ir a la Tienda</a>';
        echo ' <a href="admin/" class="btn btn-secondary">Panel de Administración</a>';
        echo '</div>';
    }
    
    private function checkRequirements() {
        $checks = [];
        
        // PHP Version
        $phpVersion = phpversion();
        $checks[] = [
            'name' => 'PHP Version',
            'status' => version_compare($phpVersion, '7.4', '>='),
            'message' => "Versión actual: $phpVersion (requerida: 7.4+)"
        ];
        
        // MySQL Extension
        $checks[] = [
            'name' => 'MySQL/MySQLi Extension',
            'status' => extension_loaded('mysqli'),
            'message' => extension_loaded('mysqli') ? 'Disponible' : 'No disponible'
        ];
        
        // File Permissions
        $writable = is_writable('.');
        $checks[] = [
            'name' => 'Permisos de Escritura',
            'status' => $writable,
            'message' => $writable ? 'Directorio escribible' : 'Directorio no escribible'
        ];
        
        // Memory Limit
        $memoryLimit = ini_get('memory_limit');
        $checks[] = [
            'name' => 'Límite de Memoria PHP',
            'status' => true,
            'message' => "Actual: $memoryLimit"
        ];
        
        return $checks;
    }
    
    private function testDatabaseConnection() {
        try {
            $connection = new mysqli(
                $this->config['db_host'],
                $this->config['db_user'], 
                $this->config['db_pass'],
                $this->config['db_name']
            );
            
            if ($connection->connect_error) {
                $this->errors[] = "Error de conexión: " . $connection->connect_error;
                return false;
            }
            
            $connection->close();
            return true;
            
        } catch (Exception $e) {
            $this->errors[] = "Error: " . $e->getMessage();
            return false;
        }
    }
    
    private function createTables() {
        $config = $_SESSION['config'] ?? $this->config;
        
        try {
            $connection = new mysqli(
                $config['db_host'],
                $config['db_user'],
                $config['db_pass'], 
                $config['db_name']
            );
            
            if ($connection->connect_error) {
                $this->errors[] = "Error de conexión: " . $connection->connect_error;
                return false;
            }
            
            $sql = file_get_contents('database/schema.sql');
            
            if ($connection->multi_query($sql)) {
                do {
                    if ($result = $connection->store_result()) {
                        $result->free();
                    }
                } while ($connection->more_results() && $connection->next_result());
            }
            
            if ($connection->error) {
                $this->errors[] = "Error creando tablas: " . $connection->error;
                $connection->close();
                return false;
            }
            
            $connection->close();
            return true;
            
        } catch (Exception $e) {
            $this->errors[] = "Error: " . $e->getMessage();
            return false;
        }
    }
    
    private function createConfigFile() {
        $config = $_SESSION['config'] ?? $this->config;
        $config['site_name'] = $this->config['site_name'];
        
        $configContent = "<?php\n";
        $configContent .= "// Configuración del Sistema de Chocolates\n";
        $configContent .= "// Generado por el instalador el " . date('Y-m-d H:i:s') . "\n\n";
        $configContent .= "define('DB_HOST', '" . addslashes($config['db_host']) . "');\n";
        $configContent .= "define('DB_NAME', '" . addslashes($config['db_name']) . "');\n";
        $configContent .= "define('DB_USER', '" . addslashes($config['db_user']) . "');\n";
        $configContent .= "define('DB_PASS', '" . addslashes($config['db_pass']) . "');\n";
        $configContent .= "define('SITE_NAME', '" . addslashes($config['site_name']) . "');\n";
        $configContent .= "define('BASE_URL', 'http" . (isset($_SERVER['HTTPS']) ? 's' : '') . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/');\n";
        $configContent .= "\n// Configuración de seguridad\n";
        $configContent .= "define('SECRET_KEY', '" . bin2hex(random_bytes(32)) . "');\n";
        
        if (!file_put_contents('config.php', $configContent)) {
            $this->errors[] = "No se pudo crear el archivo de configuración";
            return false;
        }
        
        return true;
    }
    
    private function createAdminUser() {
        if (strlen($this->config['admin_password']) < 8) {
            $this->errors[] = "La contraseña debe tener al menos 8 caracteres";
            return false;
        }
        
        $config = $_SESSION['config'] ?? [];
        
        try {
            $connection = new mysqli(
                $config['db_host'],
                $config['db_user'],
                $config['db_pass'],
                $config['db_name']
            );
            
            if ($connection->connect_error) {
                $this->errors[] = "Error de conexión: " . $connection->connect_error;
                return false;
            }
            
            $email = $connection->real_escape_string($this->config['admin_email']);
            $password = password_hash($this->config['admin_password'], PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO admin_users (email, password, name, created_at) VALUES ('$email', '$password', 'Administrador', NOW())";
            
            if (!$connection->query($sql)) {
                $this->errors[] = "Error creando usuario: " . $connection->error;
                $connection->close();
                return false;
            }
            
            $connection->close();
            return true;
            
        } catch (Exception $e) {
            $this->errors[] = "Error: " . $e->getMessage();
            return false;
        }
    }
    
    private function showHeader() {
        echo '<!DOCTYPE html>';
        echo '<html lang="es">';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        echo '<title>Instalador - Sistema de Chocolates</title>';
        echo '<style>';
        echo 'body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; background: #f5f5f5; }';
        echo '.header { text-align: center; margin-bottom: 30px; }';
        echo '.card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }';
        echo '.form-group { margin-bottom: 20px; }';
        echo 'label { display: block; margin-bottom: 5px; font-weight: bold; }';
        echo 'input[type="text"], input[type="email"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }';
        echo 'small { color: #666; font-size: 0.9em; }';
        echo '.btn { display: inline-block; padding: 12px 24px; background: #007cba; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; margin: 5px; }';
        echo '.btn:hover { background: #005a87; }';
        echo '.btn-secondary { background: #6c757d; }';
        echo '.btn-secondary:hover { background: #545b62; }';
        echo '.success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin: 10px 0; }';
        echo '.error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin: 10px 0; }';
        echo '.progress { display: flex; margin-bottom: 30px; }';
        echo '.step { flex: 1; text-align: center; padding: 10px; background: #f8f9fa; border: 1px solid #dee2e6; }';
        echo '.step.active { background: #007cba; color: white; }';
        echo '.step.completed { background: #28a745; color: white; }';
        echo 'ul { margin: 15px 0; }';
        echo 'li { margin: 5px 0; }';
        echo '</style>';
        echo '</head>';
        echo '<body>';
        
        echo '<div class="header">';
        echo '<h1>🍫 Instalador del Sistema de Chocolates</h1>';
        echo '<p>Versión ' . INSTALLER_VERSION . ' - Compatible con Apache/cPanel</p>';
        echo '</div>';
    }
    
    private function showNavigation($currentStep) {
        echo '<div class="progress">';
        foreach ($this->steps as $num => $title) {
            $class = 'step';
            if ($num < $currentStep) $class .= ' completed';
            if ($num == $currentStep) $class .= ' active';
            
            echo "<div class='$class'>$num. $title</div>";
        }
        echo '</div>';
    }
    
    private function showFooter() {
        echo '<div style="text-align: center; margin-top: 30px; color: #666; font-size: 0.9em;">';
        echo 'Sistema de Venta de Chocolates &copy; ' . date('Y') . ' - Instalador Web';
        echo '</div>';
        echo '</body>';
        echo '</html>';
    }
}

// Ejecutar el instalador
$installer = new ChocolateInstaller();
$installer->run();
?>
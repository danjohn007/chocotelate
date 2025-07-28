-- Base de datos para Sistema de Venta de Chocolates
-- Creado por el instalador web
-- Compatible con MySQL/MariaDB en cPanel

-- Tabla de usuarios administradores
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de clientes registrados
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(20),
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de direcciones de entrega
CREATE TABLE IF NOT EXISTS `addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `street` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `country` varchar(100) DEFAULT 'México',
  `phone` varchar(20),
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de zonas de envío y tarifas
CREATE TABLE IF NOT EXISTS `delivery_zones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `postal_codes` text,
  `delivery_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `min_order_amount` decimal(10,2) DEFAULT 0.00,
  `delivery_time` varchar(50),
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de productos (chocolates)
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `short_description` varchar(500),
  `price` decimal(10,2) NOT NULL,
  `weight` decimal(8,2),
  `category` varchar(100),
  `chocolate_type` varchar(100), -- Bitter, Milk, White, etc.
  `filling` varchar(100), -- Nuts, Caramel, Fruit, etc.
  `presentation` varchar(100), -- Box, Individual, Gift, etc.
  `stock` int(11) DEFAULT 0,
  `min_stock` int(11) DEFAULT 5,
  `image_url` varchar(500),
  `images` text, -- JSON array of additional images
  `featured` tinyint(1) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_category` (`category`),
  INDEX `idx_active` (`active`),
  INDEX `idx_featured` (`featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de pedidos
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(20) NOT NULL UNIQUE,
  `user_id` int(11),
  `guest_email` varchar(255),
  `guest_name` varchar(200),
  `guest_phone` varchar(20),
  `status` enum('pending','confirmed','preparing','shipped','delivered','cancelled') DEFAULT 'pending',
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `payment_method` varchar(50),
  `subtotal` decimal(10,2) NOT NULL,
  `delivery_cost` decimal(10,2) DEFAULT 0.00,
  `discount` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `delivery_address` text NOT NULL,
  `delivery_notes` text,
  `tracking_number` varchar(100),
  `delivered_at` timestamp NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_order_number` (`order_number`),
  INDEX `idx_status` (`status`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de detalle de pedidos
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de historial de pagos
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `transaction_id` varchar(255),
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `gateway_response` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de códigos de descuento
CREATE TABLE IF NOT EXISTS `discount_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL UNIQUE,
  `description` varchar(255),
  `type` enum('percentage','fixed') DEFAULT 'percentage',
  `value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) DEFAULT 0.00,
  `max_uses` int(11),
  `used_count` int(11) DEFAULT 0,
  `valid_from` date,
  `valid_until` date,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de opiniones de productos
CREATE TABLE IF NOT EXISTS `product_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `user_id` int(11),
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `rating` tinyint(1) NOT NULL CHECK (rating >= 1 AND rating <= 5),
  `comment` text,
  `approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de configuración del sistema
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL UNIQUE,
  `setting_value` text,
  `description` varchar(255),
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar configuraciones iniciales
INSERT INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES
('site_name', 'Chocolates Artesanales', 'Nombre del sitio'),
('site_description', 'Deliciosos chocolates artesanales con envío a domicilio', 'Descripción del sitio'),
('currency', 'MXN', 'Moneda del sitio'),
('currency_symbol', '$', 'Símbolo de la moneda'),
('tax_rate', '16', 'Tasa de impuesto (IVA)'),
('free_shipping_amount', '500', 'Monto mínimo para envío gratis'),
('email_from', 'noreply@chocolates.com', 'Email remitente del sistema'),
('order_prefix', 'CHO', 'Prefijo de números de pedido'),
('maintenance_mode', '0', 'Modo de mantenimiento (0=inactivo, 1=activo)');

-- Insertar zona de envío por defecto
INSERT INTO `delivery_zones` (`name`, `postal_codes`, `delivery_cost`, `min_order_amount`, `delivery_time`, `active`) VALUES
('Zona Centro', '01000-09999', 50.00, 200.00, '1-2 días hábiles', 1),
('Zona Metropolitana', '10000-99999', 80.00, 300.00, '2-3 días hábiles', 1);

-- Insertar productos de ejemplo
INSERT INTO `products` (`name`, `description`, `short_description`, `price`, `weight`, `category`, `chocolate_type`, `filling`, `presentation`, `stock`, `image_url`, `featured`, `active`) VALUES
('Chocolate Amargo 70%', 'Delicioso chocolate amargo con 70% de cacao, perfecto para los amantes del sabor intenso.', 'Chocolate amargo premium con 70% de cacao', 120.00, 100.00, 'Chocolates', 'Amargo', 'Sin relleno', 'Barra', 50, 'images/chocolate-amargo.jpg', 1, 1),
('Trufas de Chocolate con Nuez', 'Exquisitas trufas de chocolate rellenas de nuez, cubiertas con cacao en polvo.', 'Trufas artesanales con nuez', 250.00, 200.00, 'Trufas', 'Leche', 'Nuez', 'Caja x12', 30, 'images/trufas-nuez.jpg', 1, 1),
('Chocolate Blanco con Fresas', 'Suave chocolate blanco con trozos de fresa deshidratada, una combinación perfecta.', 'Chocolate blanco con fresas naturales', 180.00, 150.00, 'Chocolates', 'Blanco', 'Fresa', 'Tableta', 25, 'images/chocolate-blanco-fresa.jpg', 0, 1);
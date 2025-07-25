# 🧾 Solicitud de desarrollo: Sistema de venta de chocolates con envío a domicilio

## 🎯 Objetivo

Desarrollar un sistema web completo para venta de chocolates artesanales con opción de envío a domicilio. El sistema debe incluir un frontend atractivo para clientes, backend de administración, procesamiento de pedidos, sistema de entregas y base de datos relacional.

---

## ✅ Requerimientos funcionales

### 1. Frontend del cliente (Tienda en línea)

- [ ] Página de inicio con presentación de productos
- [ ] Catálogo con filtros (tipo de chocolate, relleno, presentación, etc.)
- [ ] Vista individual del producto con imagen, descripción y precio
- [ ] Carrito de compras con modificación en tiempo real
- [ ] Checkout con formulario:
  - Datos personales
  - Dirección de envío
  - Selección de método de pago
- [ ] Registro e inicio de sesión de usuario
- [ ] Historial y seguimiento de pedidos por usuario
- [ ] Notificaciones por correo (confirmación de pedido y envío)

### 2. Backend (Panel de administración)

- [ ] Dashboard con KPIs: pedidos, ingresos, productos más vendidos
- [ ] CRUD de productos (nombre, precio, imagen, descripción, stock)
- [ ] Gestión de pedidos: cambio de estatus, impresión de guía
- [ ] Gestión de usuarios registrados
- [ ] Gestión de zonas de envío y costos
- [ ] Reportes de ventas exportables (PDF / Excel)

### 3. Base de datos MySQL (Esquema sugerido)

- `users`: clientes registrados  
- `admin_users`: usuarios del panel de administración  
- `products`: catálogo de chocolates  
- `orders`: pedidos realizados  
- `order_items`: detalle de productos por pedido  
- `addresses`: direcciones de entrega  
- `payments`: historial de pagos  
- `delivery_zones`: zonas y tarifas de envío  

### 4. Módulo de envío a domicilio

- [ ] Validación de dirección con código postal
- [ ] Cálculo dinámico de tarifas de envío
- [ ] Estatus del pedido: pendiente, en preparación, enviado, entregado
- [ ] (Opcional) Integración API con paquetería externa o UberDirect
- [ ] Confirmación de entrega por parte del repartidor

### 5. Funcionalidades adicionales

- [ ] Códigos de descuento / promociones
- [ ] Opiniones de productos
- [ ] Multilenguaje: español e inglés
- [ ] Modo catálogo (sin posibilidad de compra)
- [ ] Responsive para móviles y tablets
- [ ] SEO básico y enlaces compartibles

---

## 💻 Tecnologías sugeridas

- **Frontend:** HTML5, CSS3, JavaScript, Bootstrap o TailwindCSS  
- **Backend:** PHP puro o Laravel  
- **Base de datos:** MySQL  
- **Pasarelas de pago:** Stripe, PayPal  
- **Opcionales:** Vue.js o React para frontend dinámico, API WhatsApp para confirmaciones

---

## 🗂 Estructura de carpetas sugerida


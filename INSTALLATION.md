# 🍫 Instalador Web para Sistema de Chocolates

## Descripción

Este es un instalador web completo para implementar el Sistema de Venta de Chocolates en servidores Apache/cPanel. El instalador proporciona una interfaz web intuitiva que guía paso a paso la configuración del sistema.

## Características del Instalador

### ✅ Verificación de Requisitos
- Comprueba la versión de PHP (requiere 7.4+)
- Verifica extensiones MySQL/MySQLi
- Valida permisos de escritura
- Revisa límites de memoria

### 🗄️ Configuración de Base de Datos
- Interfaz amigable para configurar conexión MySQL
- Prueba de conectividad en tiempo real
- Compatible con cPanel y otros paneles de hosting
- Soporte para bases de datos remotas

### 📋 Creación de Estructura
- Creación automática de todas las tablas necesarias
- Inserción de datos iniciales
- Configuración de índices y relaciones
- Datos de ejemplo para pruebas

### ⚙️ Configuración del Sistema
- Generación automática de archivo de configuración
- Configuración de seguridad con claves secretas
- Detección automática de URL base
- Configuración de parámetros del sitio

### 👤 Usuario Administrador
- Creación de cuenta de administrador
- Encriptación segura de contraseñas
- Validación de fortaleza de contraseña
- Configuración de acceso al panel admin

## Instrucciones de Instalación

### Paso 1: Subir Archivos
1. Descarga todos los archivos del sistema
2. Súbelos a tu directorio web (public_html, www, etc.)
3. Asegúrate de que los permisos sean correctos (755 para directorios, 644 para archivos)

### Paso 2: Crear Base de Datos
1. Accede a tu panel de cPanel
2. Ve a "Bases de datos MySQL"
3. Crea una nueva base de datos
4. Crea un usuario y asígnalo a la base de datos
5. Otorga todos los permisos al usuario

### Paso 3: Ejecutar Instalador
1. Visita tu sitio web en el navegador
2. El instalador se ejecutará automáticamente
3. Sigue los 6 pasos del proceso de instalación:
   - ✅ Verificación de requisitos
   - 🗄️ Configuración de base de datos  
   - 📋 Creación de tablas
   - ⚙️ Configuración del sistema
   - 👤 Usuario administrador
   - 🎉 Finalización

### Paso 4: Seguridad Post-Instalación
1. **IMPORTANTE**: Elimina el archivo `installer.php` después de la instalación
2. Accede al panel de administración en `/admin/`
3. Configura productos, zonas de envío y personalización

## Requisitos del Servidor

- **PHP**: 7.4 o superior
- **MySQL**: 5.7 o superior (o MariaDB 10.2+)
- **Extensiones PHP requeridas**:
  - mysqli
  - session
  - json
- **Permisos**: Escritura en el directorio raíz
- **Memoria PHP**: Mínimo 128MB recomendado

## Estructura de Archivos Generados

```
/
├── installer.php          # ⚠️ ELIMINAR después de la instalación
├── index.php             # Tienda principal
├── config.php            # Configuración generada automáticamente
├── .htaccess             # Configuración de Apache
├── includes/
│   └── functions.php     # Funciones principales del sistema
├── admin/
│   ├── index.php         # Panel de administración
│   ├── login.php         # Login de administrador
│   └── logout.php        # Cerrar sesión
├── database/
│   └── schema.sql        # Estructura de base de datos
└── assets/               # Recursos estáticos (crear según necesidad)
    ├── css/
    ├── js/
    └── images/
```

## Funcionalidades Implementadas

### Frontend (Tienda)
- ✅ Página principal con productos destacados
- ✅ Diseño responsive con Bootstrap
- ✅ Tema chocolatero personalizado
- ✅ Sistema de carrito de compras
- ✅ Navegación intuitiva
- ✅ Redirección automática al instalador si no está configurado

### Backend (Admin)
- ✅ Dashboard con estadísticas
- ✅ Sistema de autenticación seguro
- ✅ Panel de control con navegación lateral
- ✅ Diseño consistente con tema chocolatero
- ✅ Estructura preparada para CRUD de productos, pedidos, etc.

### Base de Datos
- ✅ Esquema completo para e-commerce
- ✅ Tablas para productos, pedidos, usuarios, pagos
- ✅ Sistema de reviews y códigos de descuento
- ✅ Zonas de envío y configuración
- ✅ Datos de ejemplo incluidos

## Seguridad Implementada

- 🔒 Protección contra SQL Injection (prepared statements)
- 🔒 Encriptación de contraseñas con password_hash()
- 🔒 Validación y sanitización de entradas
- 🔒 Protección de archivos sensibles via .htaccess
- 🔒 Generación de claves secretas únicas
- 🔒 Sesiones seguras para autenticación

## Soporte y Personalización

El sistema está preparado para:
- ✨ Agregado de más productos
- ✨ Personalización de diseño
- ✨ Integración con pasarelas de pago
- ✨ Sistema de envíos
- ✨ Notificaciones por email
- ✨ Reportes y estadísticas avanzadas

## Tecnologías Utilizadas

- **Backend**: PHP 7.4+ con MySQLi
- **Frontend**: HTML5, CSS3, Bootstrap 5.1.3
- **Base de datos**: MySQL/MariaDB
- **Iconos**: Font Awesome 6.0
- **Servidor web**: Apache con mod_rewrite

## Problemas Comunes y Soluciones

### Error de conexión a base de datos
- Verifica las credenciales de la base de datos
- Asegúrate de que el usuario tenga permisos
- Confirma que el servidor MySQL esté funcionando

### Problemas de permisos
- Configura permisos 755 para directorios
- Configura permisos 644 para archivos
- Asegúrate de que el usuario web tenga acceso de escritura

### Error 500 después de la instalación
- Revisa los logs de error del servidor
- Verifica que el archivo config.php se haya creado correctamente
- Asegúrate de eliminar installer.php después de la instalación

---

🍫 **¡Disfruta tu nueva tienda de chocolates!** 🍫
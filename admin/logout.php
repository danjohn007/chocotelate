<?php
session_start();

// Destruir todas las variables de sesión de admin
unset($_SESSION['admin_id']);
unset($_SESSION['admin_email']);
unset($_SESSION['admin_name']);

// Redireccionar al login
header('Location: login.php');
exit;
?>
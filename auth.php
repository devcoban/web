<?php
// auth.php

// Deshabilitar caché en el navegador
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Fecha en el pasado

// Iniciar sesión solo si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir conexión a la base de datos
require_once 'db/db.php';

// Verificar si el usuario está logueado y tiene rol válido
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol'])) {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit;
}

// Verificar que el usuario sigue activo
$usuario_id = $_SESSION['usuario_id'];

$stmt = $conn->prepare("SELECT estado FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    // Usuario no encontrado, cerrar sesión
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit;
}

$usuario = $resultado->fetch_assoc();

if ($usuario['estado'] != 1) {
    // Usuario inactivo o estado diferente, cerrar sesión
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit;
}

// Cerrar recursos
$stmt->close();

function puedeVer($permiso)
{
    // Si es un Administrador, puede ver todo
    if ($_SESSION['rol'] === 'Administrador') {
        return true;
    }
    
    // Si es un Instructor, verificar si tiene el permiso específico
    if ($_SESSION['rol'] === 'Instructor') {
        return tienePermiso($_SESSION['usuario_id'], $permiso);
    }

    return false; // Si no es Admin ni Instructor con el permiso, no puede ver
}

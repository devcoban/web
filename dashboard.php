<?php
// Deshabilitar caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

require_once 'auth.php';
require_once 'db/db.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    
    <link href="images/gym.ico" rel="icon">
    
    <link href="assets/css/adminlte.min.css" rel="stylesheet">
    <link href="assets/css/all.min.css" rel="stylesheet">
    <link href="css/stylebarra.css" rel="stylesheet">
    <link href="assets/css/sweetalert2.min.css" rel="stylesheet">

    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/adminlte.min.js"></script>
    <script src="assets/js/sweetalert2.all.min.js"></script>
</head>
<body>

<!-- Barra lateral -->
<div class="sidebar">
    <h1>Total Fitness</h1>
    <a href="dashboard.php"><i class="fa fa-home"></i> <span>Inicio</span></a>

    <?php if (puedeVer('registrar_usuario') || puedeVer('ver_usuario')): ?>
        <ul class="menu">
            <li class="menu-item">
                <a href="#"><i class="fa fa-users"></i> <span>Usuarios</span></a>
                <ul class="submenu">
                    <?php if (puedeVer('registrar_usuario')): ?>
                        <li><a href="usuarios/registrar_usuario.php"><small><i class="fa fa-user-plus"></i>Registrar usuario</small></a></li>
                    <?php endif; ?>
                    <?php if (puedeVer('ver_usuario')): ?>
                        <li><a href="usuarios/ver_usuario.php"><small><i class="fa fa-list"></i>Ver usuarios</small></a></li>
                    <?php endif; ?>
                </ul>
            </li>
        </ul>
    <?php endif; ?>

    <?php if (puedeVer('registrar_socio') || puedeVer('ver_socio')): ?>
        <ul class="menu">
            <li class="menu-item">
                <a href="#"><i class="fa fa-user-friends"></i> <span>Socios</span></a>
                <ul class="submenu">
                    <?php if (puedeVer('registrar_socio')): ?>
                        <li><a href="socios/registrar_socio.php"><small><i class="fa fa-user-plus"></i>Registrar socio</small></a></li>
                    <?php endif; ?>
                    <?php if (puedeVer('ver_socio')): ?>
                        <li><a href="socios/ver_socio.php"><small><i class="fa fa-list"></i>Ver socios</small></a></li>
                    <?php endif; ?>
                </ul>
            </li>
        </ul>
    <?php endif; ?>

    <?php if (puedeVer('registrar_membresia') || puedeVer('ver_membresia') || puedeVer('asignar_membresia')): ?>
        <ul class="menu">
            <li class="menu-item">
                <a href="#"><i class="fa fa-id-card"></i> <span>Membresías</span></a>
                <ul class="submenu">
                    <?php if (puedeVer('registrar_membresia')): ?>
                        <li><a href="membresias/registrar_membresia.php"><small><i class="fa fa-plus"></i>Registrar Membresía</small></a></li>
                    <?php endif; ?>
                    <?php if (puedeVer('ver_membresia')): ?>
                        <li><a href="membresias/ver_membresia.php"><small><i class="fa fa-list"></i>Ver Membresías</small></a></li>
                    <?php endif; ?>
                    <?php if (puedeVer('asignar_membresia')): ?>
                        <li><a href="membresias/asignar_membresia.php"><small><i class="fa fa-id-badge"></i>Asignar Membresía</small></a></li>
                    <?php endif; ?>
                </ul>
            </li>
        </ul>
    <?php endif; ?>

    <?php if (puedeVer('registrar_pago')): ?>
        <ul class="menu">
            <li class="menu-item">
                <a href="#"><i class="fa fa-money-bill"></i> <span>Pagos</span></a>
                <ul class="submenu">
                    <li><a href="#"><small>Registrar Pago</small></a></li>
                </ul>
            </li>
        </ul>
    <?php endif; ?>

    <?php if (puedeVer('registrar_asistencia')): ?>
        <a href="asistencia/registrar_ingreso.php"><i class="fa fa-calendar-check"></i> <span>Asistencia</span></a>
    <?php endif; ?>

    <?php if (puedeVer('ver_asistencia') || puedeVer('ver_socios_membresias')): ?>
        <ul class="menu">
            <li class="menu-item">
                <a href="#"><i class="fa fa-chart-bar"></i> <span>Informes</span></a>
                <ul class="submenu">
                    <?php if (puedeVer('ver_socios_membresias')): ?>
                        <li><a href="informes/ver_socios_membresias.php"><small><i class="fa fa-user-plus"></i>Socios con Membresía</small></a></li>
                    <?php endif; ?>
                    <?php if (puedeVer('ver_asistencia')): ?>
                        <li><a href="informes/listar_asistencias.php"><small><i class="fa fa-list-ol"></i>Ver Asistencia</small></a></li>
                    <?php endif; ?>
                </ul>
            </li>
        </ul>
    <?php endif; ?>

    <?php if (puedeVer('asignar_permisos') || puedeVer('ver_permisos')): ?>
        <ul class="menu">
            <li class="menu-item">
                <a href="#"><i class="fa fa-cogs"></i> <span>Configuración</span></a>
                <ul class="submenu">
                    <?php if (puedeVer('asignar_permisos')): ?>
                        <li><a href="configuracion/asignar_permisos.php"><small><i class="fa fa-lock"></i>Asignar Permisos</small></a></li>
                    <?php endif; ?>
                    <?php if (puedeVer('ver_permisos')): ?>
                        <li><a href="configuracion/panel_permisos.php"><small><i class="fa fa-list-alt"></i>Permisos</small></a></li>
                    <?php endif; ?>
                </ul>
            </li>
        </ul>
    <?php endif; ?>

    <a href="perfil.php"><i class="fa fa-user"></i> <span>Perfil</span></a>
    <a href="logout.php" class="logout"><i class="fa fa-sign-out-alt"></i> <span>Cerrar sesión</span></a>
</div>

<!-- Contenido principal -->
<div class="main">
    <div class="header">
        <h2>Bienvenido, <?php echo $_SESSION['usuario_nombre']; ?>! 💪</h2>
        <button class="toggle-sidebar"><i class="fa fa-bars"></i></button>
    </div>

    <!-- Dashboard dinámico -->
    <div id="dashboard-cards" class="row mt-4 ml-2 mr-2">
        <!-- Se carga por AJAX -->
    </div>
</div>

<script>
    // Detectar F2 para redirigir al dashboard
    document.addEventListener('keydown', function(event) {
        // Si se presiona la tecla F2
        if (event.key === 'F2') {
            window.location.href = 'asistencia/registrar_ingreso.php'; // Redirigir al dashboard
        }
    });

    // Alternar barra lateral
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main');
    const toggleSidebarButton = document.querySelector('.toggle-sidebar');

    toggleSidebarButton.addEventListener('click', () => {
        sidebar.classList.toggle('hidden');
        mainContent.classList.toggle('hidden');
    });

    // Menú desplegable
    $('.menu-item > a').on('click', function(e) {
        e.preventDefault();
        var $li = $(this).parent();
        $('.menu-item').not($li).removeClass('active');
        $li.toggleClass('active');
    });

    // Cargar estadísticas con AJAX
    function cargarEstadisticas() {
        $.ajax({
            url: 'dashboard_datos.php',
            method: 'GET',
            success: function (data) {
                $('#dashboard-cards').html(data);
            }
        });
    }

    cargarEstadisticas(); // Al cargar
    setInterval(cargarEstadisticas, 30000); // Cada 30 segundos
</script>

</body>
</html>

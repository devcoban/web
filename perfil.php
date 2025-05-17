<?php
// Deshabilitar caché en el navegador
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Fecha en el pasado

session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
  header('Location: index.php');
  exit;
}

// Verificar que el rol esté presente en la sesión
if (!isset($_SESSION['rol'])) {
  // Si no hay rol, cerrar sesión por seguridad
  session_destroy();
  header('Location: index.php');
  exit;
}

date_default_timezone_set('America/Guatemala');

include 'db/db.php'; // Asegúrate de tener la conexión a la base de datos
require_once 'auth.php';

$mensaje = '';
$tipo_mensaje = '';

// Para mostrar mensajes si los hay
$mensaje = '';
$tipo_mensaje = '';

if (isset($_SESSION['mensaje'])) {
  $mensaje = $_SESSION['mensaje'];
  $tipo_mensaje = $_SESSION['tipo_mensaje'];
  unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);
}

// Obtener datos
$usuario_id = $_SESSION['usuario_id'];

$stmt = $conn->prepare("SELECT nombres, apellidos, usuario, rol FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Perfil - Total Fitness</title>
  <link href="images/gym.ico" rel="icon">

  <link href="assets/css/adminlte.min.css" rel="stylesheet">
  <link href="assets/css/all.min.css" rel="stylesheet">
  <link href="css/stylebarra.css" rel="stylesheet">

  <style>
    .mensaje {
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-weight: bold;
      animation: aparecer 0.5s ease;
    }

    .mensaje-exito {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .mensaje-error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    @keyframes aparecer {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .formulario input,
    .formulario button {
      display: block;
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 16px;
    }

    .formulario button {
      background-color: #007bff;
      color: white;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .formulario button:hover {
      background-color: #0056b3;
    }

    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
    }

    .modal-contenido {
      background: white;
      padding: 20px;
      margin: 15% auto;
      width: 90%;
      max-width: 400px;
      border-radius: 10px;
      text-align: center;
    }

    .modal-contenido button {
      margin: 10px;
      padding: 10px 20px;
      font-weight: bold;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .modal-contenido button:first-child {
      background-color: #28a745;
      color: white;
    }

    .modal-contenido button:last-child {
      background-color: #dc3545;
      color: white;
    }
  </style>

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

    <!-- Enlace de Cerrar Sesión -->
    <a href="logout.php" class="logout"><i class="fa fa-sign-out-alt"></i> <span>Cerrar sesión</span></a>
  </div>

  <!-- Contenido principal -->
  <div class="main">
    <div class="header">
      <h2>Perfil de <?php echo htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']); ?></h2>

      <!-- Botón hamburguesa -->
      <button class="toggle-sidebar"><i class="fa fa-bars"></i></button>
    </div>

    <div class="content">
      <div class="container-fluid">
        <?php if ($mensaje): ?>
          <div class="alert <?php echo $tipo_mensaje; ?>" role="alert">
            <?php echo $mensaje; ?>
          </div>
        <?php endif; ?>

        <div class="card">
          <h3>Datos Personales</h3>
          <p style="margin: 0;"><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombres']); ?></p>
          <p style="margin: 0;"><strong>Apellidos:</strong> <?php echo htmlspecialchars($usuario['apellidos']); ?></p>
          <p style="margin: 0;"><strong>Usuario:</strong> <?php echo htmlspecialchars($usuario['usuario']); ?></p>
          <p><strong>Rol:</strong> <?php echo htmlspecialchars($usuario['rol']); ?></p>
        </div>

        <div class="card">
          <h3>Cambiar Contraseña</h3>

          <form id="form-cambiar-password" action="cambiar_password.php" method="POST" class="formulario">
            <input type="password" name="password_actual" placeholder="Contraseña Actual" required>
            <input type="password" name="password_nueva" placeholder="Nueva Contraseña" required>
            <button type="button" onclick="confirmarCambio()">Actualizar Contraseña</button>
          </form>

          <!-- Modal de Confirmación -->
          <div id="modal-confirmacion" class="modal">
            <div class="modal-contenido">
              <p>¿Estás seguro de que quieres cambiar tu contraseña?</p>
              <button onclick="enviarFormulario()">Sí, cambiar</button>
              <button onclick="cerrarModal()">Cancelar</button>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <script src="assets/js/jquery-3.6.0.min.js"></script>
  <script src="assets/js/adminlte.min.js"></script>
  <script src="assets/js/sweetalert2.all.min.js"></script>

  <script>
    // Funcionalidad para ocultar/mostrar la barra lateral
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main');
    const toggleSidebarButton = document.querySelector('.toggle-sidebar');

    toggleSidebarButton.addEventListener('click', () => {
      sidebar.classList.toggle('hidden');
      mainContent.classList.toggle('hidden');
    });
  </script>

  <script>
    function confirmarCambio() {
      document.getElementById('modal-confirmacion').style.display = 'block';
    }

    function cerrarModal() {
      document.getElementById('modal-confirmacion').style.display = 'none';
    }

    function enviarFormulario() {
      document.getElementById('form-cambiar-password').submit();
    }
  </script>

  <script>
    $('.menu-item > a').on('click', function(e) {
      e.preventDefault(); // Evitar comportamiento predeterminado del enlace

      var $li = $(this).parent(); // El <li> actual

      // Cerrar todos los demás submenús
      $('.menu-item').not($li).removeClass('active');

      // Alternar (abrir/cerrar) el submenú del <li> actual
      $li.toggleClass('active');
    });
  </script>


</body>

</html>
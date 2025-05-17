<?php
session_start();
include 'db/db.php';

// Desactivar la caché del navegador para esta página
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Verificar si el usuario ya está logueado
if (isset($_SESSION['usuario_id'])) {
  header('Location: dashboard.php');  // Redirigir al dashboard si ya está logueado
  exit;
}

// Verificar si la tabla de usuarios está vacía
$stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios");
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

// Procesar el formulario de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $usuario = $_POST['usuario'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ?");
  $stmt->bind_param("s", $usuario);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();

    if ($usuario['estado'] != 1) {  // No es 1 => está inactivo
      $_SESSION['error'] = '⚠️ Usuario inactivo. Contacte al administrador.';
      header('Location: index.php');
      exit;
    }


    if (password_verify($password, $usuario['password'])) {
      $_SESSION['usuario_id'] = $usuario['id'];
      $_SESSION['usuario_nombre'] = $usuario['nombres'];
      $_SESSION['rol'] = $usuario['rol'];

      // 🔥 NUEVO: cargar permisos y guardarlos en sesión
      $stmt_permisos = $conn->prepare("SELECT permiso FROM permisosInstructor WHERE usuario_id = ? AND estado = 1");
      $stmt_permisos->bind_param("i", $usuario['id']);
      $stmt_permisos->execute();
      $resultado_permisos = $stmt_permisos->get_result();

      $permisos = [];
      while ($permiso = $resultado_permisos->fetch_assoc()) {
        $permisos[] = $permiso['permiso'];
      }
      $_SESSION['permisos'] = $permisos; // 🔥 guardar permisos en la sesión

      $stmt_permisos->close();

      // Redirigir al dashboard después de un inicio de sesión exitoso
      header('Location: dashboard.php');
      exit;
    } else {
      $_SESSION['error'] = '⚠️ Contraseña incorrecta.';
      header('Location: index.php');
      exit;
    }
  } else {
    $_SESSION['error'] = '⚠️ Usuario no encontrado.';
    header('Location: index.php');
    exit;
  }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Total Fitness</title>
  <link href="images/gym.ico" rel="icon">

  <link href="assets/css/adminlte.min.css" rel="stylesheet">
  <link href="assets/css/all.min.css" rel="stylesheet">

  <style>
    body {
      background: url('images/11366_2000.png') no-repeat center center fixed;
      background-size: cover;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Arial', sans-serif;
      margin: 0;
      overflow: hidden;
    }

    .login-box {
      width: 400px;
      background: rgba(255, 255, 255, 0.8);
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      position: relative;
      z-index: 1;
    }

    .login-logo a {
      font-size: 30px;
      font-weight: bold;
      color: #007bff;
    }

    .form-control {
      border-radius: 20px;
      box-shadow: none;
    }

    .btn-primary {
      border-radius: 20px;
      width: 100%;
    }

    .alert {
      margin-top: 10px;
    }

    .login-box-msg {
      font-weight: bold;
      color: #333;
    }

    .container {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 100%;
      max-width: 500px;
      z-index: 2;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="login-box">
      <div class="login-logo">
        <a href="#">Total Fitness</a>
      </div>
      <div class="card">
        <div class="card-body login-card-body">
          <p class="login-box-msg">Iniciar sesión para comenzar</p>

          <form method="POST">
            <div class="input-group mb-3">
              <input type="text" name="usuario" class="form-control" placeholder="Usuario" required>
              <div class="input-group-append">
                <div class="input-group-text"><i class="fa fa-user"></i></div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" required>
              <div class="input-group-append">
                <div class="input-group-text"><i class="fa fa-lock"></i></div>
              </div>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="showPassword">
              <label class="form-check-label" for="showPassword">Mostrar Contraseña</label>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Entrar</button>
          </form>

          <?php if ($count == 0): ?>
            <p class="mb-1">
              <a href="register.php">Registrar usuario Administrador</a>
            </p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <script src="assets/js/jquery-3.6.0.min.js"></script>
  <script src="assets/js/adminlte.min.js"></script>
  <script src="assets/js/sweetalert2.all.min.js"></script>

  <script>
    // Script para mostrar/ocultar la contraseña
    document.getElementById('showPassword').addEventListener('change', function() {
      const passwordField = document.getElementById('password');
      if (this.checked) {
        passwordField.type = 'text';
        confirmPasswordField.type = 'text';
      } else {
        passwordField.type = 'password';
        confirmPasswordField.type = 'password';
      }
    });
  </script>

  <!-- Mostrar errores con SweetAlert2 -->
  <?php if (isset($_SESSION['error'])): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: '<?= $_SESSION['error']; ?>',
          confirmButtonColor: '#d33'
        }).then((result) => {
          <?php if (isset($_SESSION['redirect'])): ?>
            window.location.href = '<?= $_SESSION['redirect']; ?>';
          <?php endif; ?>
        });
      });
    </script>
    <?php unset($_SESSION['error']); ?>
    <?php unset($_SESSION['redirect']); ?>
  <?php endif; ?>
</body>

</html>
<?php
session_start();

include 'db/db.php';

// Verificamos si es el primer registro en la base de datos
$stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios");
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

// Si ya existen usuarios, validar sesión
if ($count > 0 && !isset($_SESSION['usuario_id'])) {
  header('Location: index.php');
  exit;
}

// Asignamos el rol dependiendo de si es el primer registro
$rol = ($count == 0) ? 'Administrador' : 'Instructor';

// Procesar el formulario de registro
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $nombre = $_POST['nombres'];
  $apellidos = $_POST['apellidos'];
  $usuario = strtok($nombre, " ");  // Obtener el primer nombre del usuario
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];

  // Validar que las contraseñas coincidan
  if ($password !== $confirm_password) {
    $_SESSION['error'] = '❌ Las contraseñas no coinciden.';
  } else {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $estado = 1;  // Estado 1 por defecto (activo)

    // Insertar el nuevo usuario en la base de datos
    $sql = "INSERT INTO usuarios (nombres, apellidos, usuario, password, rol, estado) 
                VALUES ('$nombre', '$apellidos', '$usuario', '$password_hash', '$rol', '$estado')";

    if ($conn->query($sql) === TRUE) {
      // Guardamos el mensaje de éxito en la sesión
      $_SESSION['success'] = '✅ Registro exitoso.';
    } else {
      // Guardamos el mensaje de error en la sesión
      $_SESSION['error'] = '❌ Error: ' . $conn->error;
    }
  }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro - Total Fitness</title>
  <link href="images/gym.ico" rel="icon">

  <!-- AdminLTE CSS -->
  <link href="assets/css/adminlte.min.css" rel="stylesheet">
  <link href="assets/css/all.min.css" rel="stylesheet">

  <style>
    /* Fondo de pantalla */
    body {
      background: url('images/11366_2000.png') no-repeat center center fixed;
      background-size: cover;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Arial', sans-serif;
    }

    .login-box {
      width: 400px;
      background: rgba(255, 255, 255, 0.8);
      /* Fondo blanco semi-transparente */
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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

    .input-group-text {
      font-size: 20px;
    }

    .input-group-append .input-group-text {
      background-color: #f1f1f1;
    }
  </style>
</head>

<body>
  <div class="login-box">
    <div class="login-logo">
      <a href="#">Total Fitness</a>
    </div>
    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg">Registrar nuevo usuario</p>

        <form method="POST">
          <div class="input-group mb-3">
            <input type="text" name="nombres" class="form-control" placeholder="Nombre completo" required>
            <div class="input-group-append">
              <div class="input-group-text"><i class="fa fa-user"></i></div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="text" name="apellidos" class="form-control" placeholder="Apellidos" required>
            <div class="input-group-append">
              <div class="input-group-text"><i class="fa fa-user"></i></div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" name="password" class="form-control" id="password" placeholder="Contraseña" required>
            <div class="input-group-append">
              <div class="input-group-text"><i class="fa fa-lock"></i></div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="Confirmar Contraseña" required>
            <div class="input-group-append">
              <div class="input-group-text"><i class="fa fa-lock"></i></div>
            </div>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="showPassword">
            <label class="form-check-label" for="showPassword">Mostrar Contraseña</label>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Registrar</button>
        </form>

        <p class="mb-1">
          <a href="index.php">¿Ya tienes cuenta? Iniciar sesión</a>
        </p>
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
      const confirmPasswordField = document.getElementById('confirm_password');
      if (this.checked) {
        passwordField.type = 'text';
        confirmPasswordField.type = 'text';
      } else {
        passwordField.type = 'password';
        confirmPasswordField.type = 'password';
      }
    });
  </script>

  <!-- Script para mostrar el modal con el mensaje de éxito o error si existe -->
  <?php if (isset($_SESSION['success'])): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
          icon: 'success',
          title: '¡Éxito!',
          html: '<?= $_SESSION['success']; ?>',
          confirmButtonColor: '#3085d6'
        }).then((result) => {
          // Si el usuario hace clic en 'Ok', redirigir a index.php
          if (result.isConfirmed) {
            window.location.href = 'index.php'; // Redirige al login
          }
        });
      });
    </script>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['error'])): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
          icon: 'error',
          title: '¡Error!',
          text: '<?= $_SESSION['error']; ?>',
          confirmButtonColor: '#d33'
        });
      });
    </script>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>
</body>

</html>
<?php
session_start();
include 'db/db.php';

// Asegurarse de que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['usuario_id'];
    $password_actual = trim($_POST['password_actual']);
    $password_nueva = trim($_POST['password_nueva']);

    // Buscar la contraseña actual del usuario
    $stmt = $conn->prepare("SELECT password FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    if ($usuario && password_verify($password_actual, $usuario['password'])) {
        $password_nueva_hash = password_hash($password_nueva, PASSWORD_DEFAULT);

        // Actualizar la contraseña
        $stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $password_nueva_hash, $usuario_id);
        $stmt->execute();

        $mensaje = "✅ Contraseña actualizada correctamente.";
        $tipo_mensaje = "exito";
    } else {
        $mensaje = "⚠️ Contraseña actual incorrecta.";
        $tipo_mensaje = "error";
    }
} else {
    header('Location: perfil.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Cambiando Contraseña...</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            text-align: center;
            padding: 50px;
        }

        .mensaje {
            padding: 20px;
            margin: 20px auto;
            border-radius: 10px;
            font-size: 20px;
            font-weight: bold;
            width: fit-content;
            animation: aparecer 0.5s ease;
            opacity: 1;
            transition: opacity 1s ease;
        }

        .mensaje-exito {
            background-color: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
        }

        .mensaje-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 2px solid #f5c6cb;
        }

        .loader {
            border: 6px solid #f3f3f3;
            border-top: 6px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 30px auto;
            transition: opacity 1s ease;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes aparecer {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
    <script>
        // Al cargar la página, empieza el proceso de desaparecer y redirigir
        window.onload = function() {
            setTimeout(function() {
                // Fade out del loader y el mensaje
                document.querySelector('.loader').style.opacity = '0';
                document.querySelector('.mensaje').style.opacity = '0';
            }, 200); // Espera 2 segundos antes de empezar a desvanecer

            setTimeout(function() {
                // Redirige a perfil.php después de que terminó el fade out
                window.location.href = 'perfil.php';
            }, 500); // Redirige después de 3.5 segundos en total
        };
    </script>
</head>

<body>

    <div class="loader"></div>

    <div class="mensaje <?php echo ($tipo_mensaje == 'exito') ? 'mensaje-exito' : 'mensaje-error'; ?>">
        <?php echo $mensaje; ?>
    </div>

    <p>Redirigiendo a tu perfil...</p>

</body>

</html>
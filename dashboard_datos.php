<?php
require_once 'db/db.php';

// Total socios
$result_socios = $conn->query("SELECT COUNT(*) AS total FROM socios");
$total_socios = $result_socios->fetch_assoc()['total'];

// Total usuarios
$result_usuarios = $conn->query("SELECT COUNT(*) AS total FROM usuarios");
$total_usuarios = $result_usuarios->fetch_assoc()['total'];

// Socios inactivos (estado = 0)
$result_inactivos = $conn->query("SELECT COUNT(*) AS total FROM socios WHERE estado = 0");
$total_socios_inactivos = $result_inactivos->fetch_assoc()['total'];

// Asistencia de hoy
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM ingresos WHERE DATE(fecha_hora_ingreso) = CURDATE()");
$stmt->execute();
$result_asistencia = $stmt->get_result();
$asistencia_hoy = $result_asistencia->fetch_assoc()['total'];

?>

<!-- Tarjetas -->
<div class="col-md-3">
    <div class="small-box bg-info">
        <div class="inner">
            <h3><?php echo $total_socios; ?></h3>
            <p>Total Socios</p>
        </div>
        <div class="icon">
            <i class="fa fa-user-friends"></i>
        </div>
    </div>
</div>

<div class="col-md-3">
    <div class="small-box bg-success">
        <div class="inner">
            <h3><?php echo $total_usuarios; ?></h3>
            <p>Total Instructores</p>
        </div>
        <div class="icon">
            <i class="fa fa-users"></i>
        </div>
    </div>
</div>

<div class="col-md-3">
    <div class="small-box bg-warning">
        <div class="inner">
            <h3><?php echo $total_socios_inactivos; ?></h3>
            <p>Socios Inactivos</p>
        </div>
        <div class="icon">
            <i class="fa fa-user-slash"></i>
        </div>
    </div>
</div>

<div class="col-md-3">
    <div class="small-box bg-danger">
        <div class="inner">
            <h3><?php echo $asistencia_hoy; ?></h3>
            <p>Asistencias Hoy</p>
        </div>
        <div class="icon">
            <i class="fa fa-calendar-check"></i>
        </div>
    </div>
</div>

<link href="css/bootstrap.min.css" rel="stylesheet">

<div class="row mt-4">
    <div class="col-12" style="text-align: center;">
        <img src="images/gym.png" alt="Imagen del Dashboard" class="centrado">
    </div>
</div>

<!-- Sección para mostrar el reloj y la fecha -->
<div class="row mt-4">
    <div class="col-12" style="text-align: center;">
        <h2 id="reloj"></h2>
    </div>
</div>

<style>
    .centrado {
        display: block;
        margin-left: auto;
        margin-right: auto;
        width: 308px;
        height: auto;
    }
</style>

<script>
// Función para actualizar el reloj y la fecha cada segundo
function actualizarReloj() {
    const fecha = new Date();
    
    // Formato de la fecha (Ejemplo: 12 MAYO 2025)
    const dia = String(fecha.getDate()).padStart(2, '0');
    const mes = fecha.toLocaleString('default', { month: 'long' }).toUpperCase(); // Mes en mayúsculas
    const anio = fecha.getFullYear();
    
    // Formato de la hora (Ejemplo: 14:30:45)
    const horas = String(fecha.getHours()).padStart(2, '0');
    const minutos = String(fecha.getMinutes()).padStart(2, '0');
    const segundos = String(fecha.getSeconds()).padStart(2, '0');
    
    // Mostrar la fecha y la hora
    document.getElementById('reloj').textContent = `${dia} DE ${mes} DEL ${anio} - ${horas}:${minutos}:${segundos}`;
}

// Llamamos a la función cada segundo
setInterval(actualizarReloj, 1000);

// Inicializamos el reloj al cargar la página
actualizarReloj();
</script>

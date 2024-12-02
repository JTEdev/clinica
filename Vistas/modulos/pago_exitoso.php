<?php

// Iniciar sesión si no está activa
if ($status == 'approved') {
    // Datos necesarios para registrar la cita
    $userId = $_SESSION['id']; // ID del usuario desde la sesión
    $doctorId = $_SESSION['doctor_id']; // Supongamos que el doctor fue seleccionado previamente
    $fechaHora = $_SESSION['fecha_hora']; // Fecha y hora seleccionadas
    $consultorioId = $_SESSION['consultorio_id']; // ID del consultorio

    // Crear instancia de la clase y enviar cita
    $enviarC = new CitasC();
    $resultado = $enviarC->EnviarCitaC($userId, $doctorId, $fechaHora, $consultorioId);

    if ($resultado) {
        echo '<h1>Pago exitoso y cita registrada correctamente.</h1>';
    } else {
        echo '<h1>Pago exitoso, pero ocurrió un error al registrar la cita.</h1>';
    }
}
?>

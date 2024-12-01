<?php
// Validar sesión
if (!isset($_SESSION["id"])) {
    echo '<script>
            alert("Sesión no válida. Por favor, inicia sesión.");
            window.location = "http://localhost/clinica/inicio";
          </script>';
    exit();
}

// Validar parámetros de la URL
if (!isset($_GET["url"]) || !is_numeric($_GET["url"])) {
    echo '<script>
            alert("Solicitud no válida. Regresando al historial.");
            window.location = "http://localhost/clinica/historial/' . $_SESSION["id"] . '";
          </script>';
    exit();
}

$idCita = intval($_GET["url"]);
// $cita = CitasC::VerCitaPorIdC($idCita);

// Verificar si la cita existe
if (!$cita || $_SESSION["documento"] != $cita["documento"]) {
    echo '<script>
            alert("Cita no encontrada o no tienes permiso para editarla.");
            window.location = "http://localhost/clinica/historial/' . $_SESSION["id"] . '";
          </script>';
    exit();
}

?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Editar Cita</h1>
        <h2>Doctor: <?php echo htmlspecialchars($cita["nombre_doctor"] . " " . $cita["apellido_doctor"]); ?></h2>
        <h3>Consultorio: <?php echo htmlspecialchars($cita["nombre_consultorio"]); ?></h3>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-body">
                <form method="post">
                    <div class="form-group">
                        <h2>Fecha Actual:</h2>
                        <input type="text" class="form-control" value="<?php echo date("d/m/Y", strtotime($cita["inicio"])); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <h2>Hora Actual:</h2>
                        <input type="text" class="form-control" value="<?php echo date("H:i", strtotime($cita["inicio"])); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <h2>Seleccionar Nueva Fecha:</h2>
                        <input type="date" class="form-control" name="nuevaFecha" value="<?php echo date("Y-m-d", strtotime($cita["inicio"])); ?>" required>
                    </div>

                    <div class="form-group">
                        <h2>Seleccionar Nueva Hora:</h2>
                        <input type="time" class="form-control" name="nuevaHora" value="<?php echo date("H:i", strtotime($cita["inicio"])); ?>" required>
                    </div>

                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </section>
</div>

<?php
// // Manejar la actualización de la cita
// if ($_SERVER["REQUEST_METHOD"] === "POST") {
//     $nuevaFechaHora = $_POST["nuevaFecha"] . " " . $_POST["nuevaHora"] . ":00";

//     // Validar nueva fecha y hora
//     if (strtotime($nuevaFechaHora) === false) {
//         echo '<script>alert("Fecha u hora no válida.");</script>';
//     } else {
//         $resultado = CitasC::($idCita, $nuevaFechaHora);

//         if ($resultado) {
//             echo '<script>
//                     alert("Cita actualizada con éxito.");
//                     window.location = "http://localhost/clinica/historial/' . $_SESSION["id"] . '";
//                   </script>';
//         } else {
//             echo '<script>alert("Error al actualizar la cita.");</script>';
//         }
//     }
// }
// ?>

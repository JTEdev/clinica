





<?php


if ($_SESSION["id"] != substr($_GET["url"], 10)) {
    echo '<script>window.location = "inicio";</script>';
    return;
}

// Variable para mensajes
$mensaje = "";

if (isset($_POST["eliminarCita"])) {
    $idCita = $_POST["id_cita"];
    
    // Llamar al modelo o controlador para eliminar
    $resultado = CitasC::EliminarCitaC($idCita);
    
    if ($resultado) {
        $mensaje = "Cita eliminada correctamente.";
    } else {
        $mensaje = "Error al eliminar la cita.";
    }
}




?>

<div class="content-wrapper">

    <section class="content-header">
        <h1>Su Historial de Citas Médicas</h1>
    </section>

    <section class="content">

        <div class="box">

            <div class="box-body">

                <table class="table table-bordered table-hover table-striped DT">

                    <thead>
                        <tr>
                            <th>Fecha y Hora</th>
                            <th>Doctor</th>
                            <th>Consultorio</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php
                        $resultado = CitasC::VerCitasC();

                        foreach ($resultado as $key => $value) {
                            if ($_SESSION["documento"] == $value["documento"]) {
                                echo '<tr>
                                    <td>' . $value["inicio"] . '</td>';

                                $columna = "id";
                                $valor = $value["id_doctor"];
                                $doctor = DoctoresC::DoctorC($columna, $valor);
                                echo '<td>' . $doctor["apellido"] . ' ' . $doctor["nombre"] . '</td>';

                                $valor = $value["id_consultorio"];
                                $consultorio = ConsultoriosC::VerConsultoriosC($columna, $valor);
                                echo '<td>' . $consultorio["nombre"] . '</td>';

                                echo '<td>
                                <form method="POST" action="">
                                    <input type="hidden" name="id_cita" value="' . $value["id"] . '">
                                    <button type="submit" name="eliminarCita" class="btn btn-danger">Eliminar</button>
                                </form>

                                <form method="POST" action="http://localhost/clinica/editarCita/">
                                    <input type="hidden" name="id_cita" value="' . $value["id"] . '">
                                    <button type="submit" name="editarCita" class="btn btn-warning">Editar</button>
                                </form>
                            </td>';
                            }
                        }

                 
                        
                    



                        ?>

                    </tbody>

                </table>

            </div>

        </div>

    </section>

</div>

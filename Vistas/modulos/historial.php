<?php
require_once "Controladores/citasC.php";
require_once "Modelos/citasM.php";

if ($_SESSION["id"] != substr($_GET["url"], 10)) {

    echo '<script>

	window.location = "inicio";
	</script>';

    return;
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


                                $columna = "id";
                                $valor = $value["id_consultorio"];

                                $consultorio = ConsultoriosC::VerConsultoriosC($columna, $valor);

                                echo '<td>' . $consultorio["nombre"] . '</td>';

                                echo '</tr>';

                                // Botones de edición y eliminación
                                echo '<td>
  <a href="http://localhost/clinica/editarCita/' . htmlspecialchars($value["id"]) . '">
<button class="btn btn-warning">Editar</button>
</a>
<a href="http://localhost/clinica/historial/' . $_SESSION["id"] . '?idCita=' . htmlspecialchars($value["id"]) . '">
<button class="btn btn-danger">Eliminar</button>
                  </a>
</td>
</tr>';
                            }
                        }

                        ?>


                    </tbody>

                </table>

            </div>

        </div>

    </section>

</div>
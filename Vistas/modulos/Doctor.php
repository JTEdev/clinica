<?php
if ($_SESSION["rol"] != "Paciente") {
    echo '<script>
    window.location = "inicio";
    </script>';
    return;
}
?>

<div class="content-wrapper">

    <section class="content-header">

        <?php
        $columna = "id";
        $valor = substr($_GET["url"], 7);

        $resultado = DoctoresC::DoctorC($columna, $valor);

        if ($resultado["sexo"] == "Femenino") {
            echo '<h1>Doctora: ' . $resultado["apellido"] . ' ' . $resultado["nombre"] . '</h1>';
        } else {
            echo '<h1>Doctor: ' . $resultado["apellido"] . ' ' . $resultado["nombre"] . '</h1>';
        }

        $columna = "id";
        $valor = $resultado["id_consultorio"];

        $consultorio = ConsultoriosC::VerConsultoriosC($columna, $valor);

        echo '<br>
        <h1>Consultorio de: ' . $consultorio["nombre"] . '</h1>';
        ?>

    </section>

    <section class="content">

        <div class="box">

            <div class="box-body">

                <form method="post">

                    <div class="modal-body">

                        <div class="box-body">

                            <?php
                            $columna = "id";
                            $valor = substr($_GET["url"], 7);

                            $resultado = DoctoresC::DoctorC($columna, $valor);

                            echo '<div class="form-group">
                                <h2>Nombre del Paciente:</h2>
                                <input type="text" class="form-control input-lg" name="nyaC" value="' . $_SESSION["nombre"] . ' ' . $_SESSION["apellido"] . '" readonly>
                                <input type="hidden" name="Did" value="' . $resultado["id"] . '">
                                <input type="hidden" name="Pid" id="pidInput" value="' . $_SESSION["id"] . '">
                            </div>

                            <div class="form-group">
                                <h2>Documento:</h2>
                                <input type="text" class="form-control input-lg" name="documentoC" value="' . $_SESSION["documento"] . '" readonly>
                            </div>';

                            $columna = "id";
                            $valor = $resultado["id_consultorio"];

                            $consultorio = ConsultoriosC::VerConsultoriosC($columna, $valor);

                            echo '<div class="form-group">
                                <input type="hidden" name="Cid" value="' . $consultorio["id"] . '">
                            </div>';
                            ?>

                            <!-- Selección de Fecha -->
                            <div class="form-group">
                                <h2>Seleccionar Fecha:</h2>
                                <input type="date" id="fechaInput" class="form-control input-lg" name="fechaC" min="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <!-- Selección de Hora -->
                            <div class="form-group">
                                <h2>Seleccionar Hora:</h2>
                                <select id="horaSelect" class="form-control input-lg" name="fyhIC" required>
                                    <option value="">Seleccione una hora...</option>
                                </select>
                            </div>

                            <!-- Fecha de Fin -->
                            <div class="form-group">
                                <h2>Fecha de Fin:</h2>
                                <input type="text" id="fechaFinInput" class="form-control input-lg" name="fyhFC" readonly>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Pedir Cita</button>
                            <button type="button" class="btn btn-danger">Cancelar</button>
                        </div>

                        <?php
                        $enviarC = new CitasC();
                        $enviarC->EnviarCitaC();
                        ?>
                </form>

            </div>

        </div>

    </section>

</div>

<script>
const citasOcupadas = <?php echo json_encode(CitasC::VerTodosLosHorariosOcupados()); ?>;
const diasOcupadosDoctor = <?php echo json_encode(array_column(CitasC::VerDisponibilidadC($resultado["id"])["ocupados"], "inicio")); ?>;
const horarioE = "<?php echo CitasC::VerDisponibilidadC($resultado["id"])["horario"]["horarioE"]; ?>";
const horarioS = "<?php echo CitasC::VerDisponibilidadC($resultado["id"])["horario"]["horarioS"]; ?>";
const DURACION_CITA_MINUTOS = 15;

// Elementos HTML
const fechaInput = document.getElementById("fechaInput");
const horaSelect = document.getElementById("horaSelect");
const fechaFinInput = document.getElementById("fechaFinInput");

// Obtener la hora actual en Perú (UTC-5)
function getHoraActualPeru() {
    const ahoraUTC = new Date();
    const ahoraPeru = new Date(ahoraUTC.setHours(ahoraUTC.getHours() - 5)); // Ajustar UTC-5
    return ahoraPeru;
}

// Validar si la fecha es válida (día actual o futuro)
function esDiaValido(fecha) {
    const hoy = getHoraActualPeru();
    const fechaSeleccionada = new Date(`${fecha}T00:00:00`);
    return fechaSeleccionada >= new Date(hoy.setHours(0, 0, 0, 0)); // Comparar días
}

// Evento para manejar el cambio de fecha
fechaInput.addEventListener("change", function () {
    const pacienteId = document.getElementById("pidInput").value;

    if (!pacienteId) {
        alert("Por favor, selecciona un paciente antes de elegir la fecha.");
        fechaInput.value = '';
        return;
    }

    const fechaSeleccionada = this.value;
    if (!esDiaValido(fechaSeleccionada)) {
        alert("Día inválido. Por favor selecciona un día válido.");
        this.value = '';
        return;
    }

    horaSelect.innerHTML = "<option value=''>Seleccione una hora...</option>";

    const ahoraPeru = getHoraActualPeru();
    const inicio = new Date(`1970-01-01T${horarioE}`);
    const fin = new Date(`1970-01-01T${horarioS}`);

    for (let hora = new Date(inicio); hora < fin; hora.setMinutes(hora.getMinutes() + DURACION_CITA_MINUTOS)) {
        const horaStr = hora.toTimeString().slice(0, 5);
        const fechaHoraStr = `${fechaSeleccionada} ${horaStr}:00`;

        // Validar si el horario es del pasado en el día actual
        const fechaHoraCompleta = new Date(`${fechaSeleccionada}T${horaStr}:00`);
        if (fechaSeleccionada === ahoraPeru.toISOString().split("T")[0] && fechaHoraCompleta < ahoraPeru) {
            continue; // Omitir horarios pasados
        }

        const option = document.createElement("option");
        let estado = "disponible"; // Estado inicial de la opción
        let mensaje = horaStr;     // Texto predeterminado para la opción

        // Verificar si está reservado por otro paciente o si hay cruce
        if (diasOcupadosDoctor.includes(fechaHoraStr)) {
            estado = "reservado";
            mensaje = "Reservado";
        } else {
            citasOcupadas.forEach(cita => {
                if (cita.inicio === fechaHoraStr && String(cita.id_paciente) === pacienteId) {
                    estado = "cruce";
                    mensaje = cita.id_doctor !== "<?php echo $resultado['id']; ?>"
                        ? "Cruce con otro doctor"
                        : "Cruce con este doctor";
                }
            });
        }

        // Establecer estilo y estado del horario
        switch (estado) {
            case "reservado":
                option.textContent = mensaje;
                option.style.color = "green";
                option.disabled = true;
                break;
            case "cruce":
                option.textContent = mensaje;
                option.style.color = "red";
                option.disabled = true;
                break;
            default:
                option.value = fechaHoraStr;
                option.textContent = mensaje;
                option.style.color = "black";
                option.disabled = false;
                break;
        }

        horaSelect.appendChild(option);
    }
});

// Evento para manejar el cambio de hora
horaSelect.addEventListener("change", function () {
    const horaInicio = this.value;
    if (horaInicio) {
        const inicioDate = new Date(horaInicio);
        inicioDate.setMinutes(inicioDate.getMinutes() + DURACION_CITA_MINUTOS);

        const year = inicioDate.getFullYear();
        const month = String(inicioDate.getMonth() + 1).padStart(2, '0');
        const day = String(inicioDate.getDate()).padStart(2, '0');
        const hours = String(inicioDate.getHours()).padStart(2, '0');
        const minutes = String(inicioDate.getMinutes()).padStart(2, '0');

        fechaFinInput.value = `${year}-${month}-${day} ${hours}:${minutes}:00`;
    } else {
        fechaFinInput.value = "";
    }
});



</script>
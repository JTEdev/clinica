<?php
// Verificar si el usuario es paciente
if ($_SESSION["rol"] != "Paciente") {
    echo '<script>
    window.location = "inicio";
    </script>';
    return;
}

date_default_timezone_set('America/Lima');



// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nuevaFecha"])) {
    $idCita = $_POST["id_cita"];
    $nuevaFecha = $_POST["nuevaFecha"];

    // Actualizar la fecha de la cita
    $resultado = CitasC::ActualizarFechaCita($idCita, $nuevaFecha);

    // Mostrar el mensaje correspondiente
    $mensaje = $resultado ? "Fecha actualizada correctamente." : "Error al actualizar la fecha.";
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Editar Cita</h1>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-body">
                <!-- Mostrar mensajes -->
                <?php if (!empty($mensaje)): ?>
                    <div class="alert alert-info"><?= $mensaje ?></div>
                <?php endif; ?>

                <!-- Mostrar formulario si se ha seleccionado una cita -->
                <?php if (isset($_POST["id_cita"])): ?>
                    <?php
                    // Obtener la cita seleccionada
                    $idCita = $_POST["id_cita"];
                    $cita = CitasC::ObtenerCitaPorId($idCita);

                    // Obtener información del consultorio si está disponible
                    if (isset($cita['id_consultorio'])) {
                        $columna = "id"; // Campo de búsqueda en la tabla de consultorios
                        $valor = $cita['id_consultorio'];
                        $consultorio = ConsultoriosC::VerConsultoriosC($columna, $valor);
                    }

                    // Obtener información del doctor si está disponible
                    if (isset($cita['id_doctor'])) {
                        $columna = "id"; // Campo de búsqueda en la tabla de doctores
                        $valor = $cita['id_doctor'];
                        $doctor = DoctoresC::DoctorC($columna, $valor);
                    }

                    // Obtener horarios ocupados y disponibilidad del doctor
                    $citasOcupadas = CitasC::VerTodosLosHorariosOcupados();
                    $disponibilidad = CitasC::VerDisponibilidadC($doctor["id"]);
                    $diasOcupadosDoctor = array_column($disponibilidad["ocupados"], "inicio");

                    ?>

                    <?php if ($cita): ?>
                        <form method="POST">
                            <!-- Información del doctor -->
                            <div class="form-group">
                                <h2>Doctor:</h2>
                                <input type="text" class="form-control input-lg"
                                    value="<?= isset($doctor) ?
                                                (($doctor["sexo"] == "Femenino" ? '' : '') . ' ' . $doctor["apellido"] . ' ' . $doctor["nombre"])
                                                : 'No disponible' ?>" readonly>
                            </div>

                            <!-- Información del consultorio -->
                            <div class="form-group">
                                <h2>Consultorio:</h2>
                                <input type="text" class="form-control input-lg"
                                    value="<?= isset($consultorio['nombre']) ? $consultorio['nombre'] : 'No disponible' ?>" readonly>
                            </div>

                            <!-- Información de la cita original (Fecha y Hora) -->
                            <div class="form-group">
                                <h2>Fecha y Hora Actual:</h2>
                                <input type="text" class="form-control input-lg"
                                    value="<?= isset($cita['inicio']) ? date('d/m/Y H:i', strtotime($cita['inicio'])) : 'No disponible' ?>" readonly>
                            </div>

                            <!-- Selección de nueva fecha y hora -->
                            <div class="form-group">
                                <h2>Seleccionar Nueva Fecha:</h2>
                                <input type="date" id="fechaInput" class="form-control input-lg" name="nuevaFecha" min="<?= date('Y-m-d'); ?>" required>
                            </div>
                            <!-- Selección de Hora -->
                            <div class="form-group">
                                <h2>Seleccionar Nueva Hora:</h2>
                                <select id="horaSelect" class="form-control input-lg" name="nuevaFecha" required>
                                    <option value="">Seleccione una hora...</option>
                                    <!-- Se llenará dinámicamente con JavaScript -->
                                </select>
                            </div>

                            <!-- Fecha de Fin -->
                            <div class="form-group">
                                <h2>Fecha de Fin:</h2>
                                <input type="text" id="fechaFinInput" class="form-control input-lg" name="fyhFC" readonly>
                            </div>

                            <input type="hidden" name="id_cita" value="<?= $cita['id'] ?>">

                            <button type="submit" class="btn btn-success">Guardar Cambios</button>
                            <a href="http://localhost/clinica/historial" class="btn btn-secondary">Cancelar</a>
                        </form>
                    <?php else: ?>
                        <p class="text-danger">No se encontró la cita para editar.</p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<script>
    // Obtención de los datos necesarios desde PHP a JavaScript
    const citasOcupadas = <?php echo json_encode($citasOcupadas); ?>;
    const diasOcupadosDoctor = <?php echo json_encode($diasOcupadosDoctor); ?>;
    const horarioE = "<?php echo $disponibilidad['horario']['horarioE']; ?>";
    const horarioS = "<?php echo $disponibilidad['horario']['horarioS']; ?>";

    const DURACION_CITA_MINUTOS = 15;

    const fechaInput = document.getElementById("fechaInput");
    const horaSelect = document.getElementById("horaSelect");
    const fechaFinInput = document.getElementById("fechaFinInput");

    fechaInput.addEventListener("change", function() {
        const fecha = this.value;
        horaSelect.innerHTML = "<option value=''>Seleccione una hora...</option>";

        const inicio = new Date(`1970-01-01T${horarioE}`);
        const fin = new Date(`1970-01-01T${horarioS}`);

        for (let hora = inicio; hora < fin; hora.setMinutes(hora.getMinutes() + DURACION_CITA_MINUTOS)) {
            const horaStr = hora.toTimeString().slice(0, 5);
            const fechaHoraStr = `${fecha} ${horaStr}:00`;

            const option = document.createElement("option");

            if (diasOcupadosDoctor.includes(fechaHoraStr)) {
                option.textContent = "Reservado";
                option.style.color = "green";
                option.disabled = true;
            } else {
                let cruceEncontrado = false;

                citasOcupadas.forEach(cita => {
                    if (cita.inicio === fechaHoraStr) {
                        cruceEncontrado = true;
                        option.textContent = "Hora ocupada(Cruce)";
                        option.style.color = "red";
                        option.disabled = true;
                    }
                });

                if (!cruceEncontrado) {
                    option.value = fechaHoraStr;
                    option.textContent = horaStr;
                    option.disabled = false;
                }
            }
            horaSelect.appendChild(option);
        }
    });

    horaSelect.addEventListener("change", function() {
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
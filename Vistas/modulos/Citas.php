<div class="content-wrapper">
    <!-- Encabezado de la sección -->
    <section class="content-header">
        <?php
        $columna = "id";
        $valor = substr($_GET["url"], 6);
        $resultado = DoctoresC::DoctorC($columna, $valor);

        echo '<h1>' . ($resultado["sexo"] == "Femenino" ? 'Doctora' : 'Doctor') . ': ' . $resultado["apellido"] . ' ' . $resultado["nombre"] . '</h1>';

        $columna = "id";
        $valor = $resultado["id_consultorio"];
        $consultorio = ConsultoriosC::VerConsultoriosC($columna, $valor);
        echo '<h1>Consultorio de: ' . $consultorio["nombre"] . '</h1>';

        date_default_timezone_set('America/Lima');
        ?>
    </section>

    <!-- Contenido principal -->
    <section class="content">
        <div class="box">
            <div class="box-body">
                <form method="post">
                    <div class="modal-body">
                        <div class="box-body">

                            <!-- Campos ocultos -->
                            <input type="hidden" name="Did" value="<?php echo $resultado["id"]; ?>">
                            <input type="hidden" name="Cid" value="<?php echo $consultorio["id"]; ?>">

                            <!-- Selección de Paciente -->
                            <div class="form-group">
                                <h2>Seleccionar Paciente:</h2>
                                <select id="pacienteSelect" class="form-control input-lg" name="nombreP" required>
                                    <option value="">Paciente...</option>
                                    <?php
                                    $resultadoPacientes = PacientesC::VerPacientesC(null, null);
                                    foreach ($resultadoPacientes as $paciente) {
                                        echo '<option value="' . $paciente["nombre"] . ' ' . $paciente["apellido"] . '" data-documento="' . $paciente["documento"] . '" data-id="' . $paciente["id"] . '">' . $paciente["apellido"] . ' ' . $paciente["nombre"] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Documento del Paciente -->
                            <div class="form-group">
                                <h2>Documento:</h2>
                                <input type="text" id="documentoInput" class="form-control input-lg" name="documentoP" readonly>
                            </div>
                            <input type="hidden" id="pidInput" name="Pid" value="">

                            <!-- Script para manejo de selección de paciente -->
                            <script>
                                document.getElementById("pacienteSelect").addEventListener("change", function() {
                                    const selectedOption = this.options[this.selectedIndex];
                                    const documento = selectedOption.getAttribute("data-documento");
                                    const pacienteId = selectedOption.getAttribute("data-id");

                                    // Asignar valores a los campos de documento y paciente ID
                                    document.getElementById("documentoInput").value = documento || '';
                                    document.getElementById("pidInput").value = pacienteId || '';

                                    // Restablecer la selección de fecha y hora
                                    document.getElementById("fechaInput").value = '';
                                    const horaSelect = document.getElementById("horaSelect");
                                    horaSelect.innerHTML = "<option value=''>Seleccione una hora...</option>"; // Limpiar opciones

                                    // Limpiar campo de fecha de fin
                                    document.getElementById("fechaFinInput").value = '';
                                });
                            </script>


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
                            <script>
                                const citasOcupadas = <?php echo json_encode(CitasC::VerTodosLosHorariosOcupados()); ?>;
                                const diasOcupadosDoctor = <?php echo json_encode(array_column(CitasC::VerDisponibilidadC($resultado["id"])["ocupados"], "inicio")); ?>;
                                const horarioE = "<?php echo CitasC::VerDisponibilidadC($resultado["id"])["horario"]["horarioE"]; ?>";
                                const horarioS = "<?php echo CitasC::VerDisponibilidadC($resultado["id"])["horario"]["horarioS"]; ?>";
                                const DURACION_CITA_MINUTOS = 30;

                                const fechaInput = document.getElementById("fechaInput");
                                const horaSelect = document.getElementById("horaSelect");
                                const fechaFinInput = document.getElementById("fechaFinInput");

                                // Obtener la hora actual en Perú (UTC-5)
                                function getHoraActualPeru() {
                                    const ahoraUTC = new Date();
                                    const ahoraPeru = new Date(ahoraUTC.setHours(ahoraUTC.getHours() + 0)); // Ajustar UTC-5
                                    return ahoraPeru;
                                }

                                // Validar si la fecha es válida (día actual o futuro)
                                function esDiaValido(fecha) {
                                    const hoy = getHoraActualPeru();
                                    const fechaSeleccionada = new Date(`${fecha}T00:00:00`);
                                    return fechaSeleccionada >= new Date(hoy.setHours(0, 0, 0, 0)); // Comparar días
                                }

                                fechaInput.addEventListener("change", function() {
                                    const pacienteId = document.getElementById("pidInput").value;

                                    if (!pacienteId) {
                                        alert("Por favor, selecciona un paciente antes de elegir la fecha.");
                                        fechaInput.value = '';
                                        return;
                                    }

                                    const fecha = this.value;

                                    if (!esDiaValido(fecha)) {
                                        alert("Día inválido. Por favor selecciona un día válido.");
                                        this.value = '';
                                        return;
                                    }

                                    horaSelect.innerHTML = "<option value=''>Seleccione una hora...</option>";

                                    const inicio = new Date(`1970-01-01T${horarioE}`);
                                    const fin = new Date(`1970-01-01T${horarioS}`);

                                    console.log("Paciente ID seleccionado:", pacienteId);
                                    console.log("Fecha seleccionada:", fecha);

                                    for (let hora = inicio; hora < fin; hora.setMinutes(hora.getMinutes() + DURACION_CITA_MINUTOS)) {
                                        const horaStr = hora.toTimeString().slice(0, 5);
                                        const fechaHoraStr = `${fecha} ${horaStr}:00`;

                                        const option = document.createElement("option");

                                        // Validar si el horario es del pasado en el día actual
                                        const fechaHoraCompleta = new Date(`${fecha}T${horaStr}:00`);
                                        const ahoraPeru = getHoraActualPeru();
                                        if (fecha === ahoraPeru.toISOString().split("T")[0] && fechaHoraCompleta < ahoraPeru) {
                                            continue; // Omitir horarios pasados
                                        }

                                        if (diasOcupadosDoctor.includes(fechaHoraStr)) {
                                            option.textContent = "Reservado";
                                            option.style.color = "green";
                                            option.disabled = true;
                                        } else {
                                            let cruceEncontrado = false;

                                            citasOcupadas.forEach(cita => {
                                                console.log("Comparando:", cita.inicio, "con", fechaHoraStr);
                                                console.log("Paciente en cita:", cita.id_paciente, "vs seleccionado:", pacienteId);

                                                if (cita.inicio === fechaHoraStr && String(cita.id_paciente) === pacienteId) {
                                                    cruceEncontrado = true;
                                                    const esOtroDoctor = cita.id_doctor !== "<?php echo $resultado['id']; ?>";
                                                    option.textContent = esOtroDoctor ? "Cruce con otro doctor" : "Cruce con este doctor";
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


                        </div>
                    </div>

                    <!-- Botón para enviar el formulario -->
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Pedir Cita</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<?php
$enviarC = new CitasC();
$enviarC->PedirCitaDoctorC();
?>
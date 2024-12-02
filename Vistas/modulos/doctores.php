<?php

if ($_SESSION["rol"] != "Secretaria" && $_SESSION["rol"] != "Administrador") {

	echo '<script>

	window.location = "inicio";
	</script>';

	return;
}


?>

<div class="content-wrapper">

	<section class="content-header">

		<h1>Gestor de Doctores</h1>

	</section>

	<section class="content">

		<div class="box">

			<div class="box-header">

				<button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#CrearDoctor">Crear Doctor</button>

			</div>


			<div class="box-body">

				<table class="table table-bordered table-hover table-striped DT">

					<thead>

						<tr>

							<th>N°</th>
							<th>Apellido</th>
							<th>Nombre</th>
							<th>Foto</th>
							<th>Consultorio</th>
							<th>Usuario</th>
							<th>Contraseña</th>
							<th>Editar / Borrar</th>

						</tr>

					</thead>

					<tbody>

						<?php

						$columna = null;
						$valor = null;

						$resultado = DoctoresC::VerDoctoresC($columna, $valor);

						foreach ($resultado as $key => $value) {

							echo '<tr>
							
									<td>' . ($key + 1) . '</td>
									<td>' . $value["apellido"] . '</td>
									<td>' . $value["nombre"] . '</td>';

							if ($value["foto"] == "") {

								echo '<td><img src="Vistas/img/defecto.png" width="40px"></td>';
							} else {

								echo '<td><img src="' . $value["foto"] . '" width="40px"></td>';
							}


							$columna = "id";
							$valor = $value["id_consultorio"];

							$consultorio = ConsultoriosC::VerConsultoriosC($columna, $valor);

							echo '<td>' . $consultorio["nombre"] . '</td>

									<td>' . $value["usuario"] . '</td>

									
									<td style="-webkit-text-security: disc; text-security: disc;"><?php echo $value["clave"]; ?></td>


									<td>
										
										<div class="btn-group">
											
											
											<button class="btn btn-success EditarDoctor" Did="' . $value["id"] . '" data-toggle="modal" data-target="#EditarDoctor"><i class="fa fa-pencil"></i> Editar</button>
											
											<button class="btn btn-danger EliminarDoctor" Did="' . $value["id"] . '" imgD="' . $value["foto"] . '"><i class="fa fa-times"></i> Borrar</button>
											

										</div>

									</td>

								</tr>';
						}


						?>



					</tbody>

				</table>

			</div>

		</div>

	</section>

</div>



<div class="modal fade" rol="dialog" id="CrearDoctor">

	<div class="modal-dialog">

		<div class="modal-content">

			<form method="post" role="form">

				<div class="modal-body">

					<div class="box-body">

						<div class="form-group">

							<h2>Apellido:</h2>

							<input type="text" class="form-control input-lg" name="apellido" required>

							<input type="hidden" name="rolD" value="Doctor">

						</div>

						<div class="form-group">

							<h2>Nombre:</h2>

							<input type="text" class="form-control input-lg" name="nombre" required>

						</div>


						<div class="form-group">

							<h2>Sexo:</h2>

							<select class="form-control input-lg" name="sexo">

								<option>Seleccionar...</option>

								<option value="Masculino">Masculino</option>
								<option value="Femenino">Femenino</option>

							</select>

						</div>


						<div class="form-group">

							<h2>Consultorio:</h2>

							<select class="form-control input-lg" name="consultorio">

								<option>Seleccionar...</option>

								<?php

								$columna = null;
								$valor = null;

								$resultado = ConsultoriosC::VerConsultoriosC($columna, $valor);

								foreach ($resultado as $key => $value) {

									echo '<option value="' . $value["id"] . '">' . $value["nombre"] . '</option>';
								}

								?>

							</select>

						</div>


						<div class="form-group">

							<h2>Usuario:</h2>

							<input type="text" class="form-control input-lg" name="usuario" required>

						</div>

						<div class="form-group">

							<h2>Contraseña:</h2>

							<input type="password" class="form-control input-lg" name="clave" required>

						</div>

					</div>

				</div>


				<div class="modal-footer">

					<button type="submit" class="btn btn-primary">Crear</button>

					<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>

				</div>


				<script>
					document.querySelector('#formPaciente').addEventListener('submit', function(event) {
						let form = event.target;
						let valid = true;

						// Función para eliminar espacios al inicio y al final de los valores
						function removeSpaces(value) {
							return value.replace(/\s+/g, ''); // Elimina todos los espacios
						}

						// Función para mostrar mensajes de error debajo de los campos
						function showError(message, field) {
							let errorMessageContainer = field.parentElement.querySelector('.error-message');
							if (!errorMessageContainer) {
								errorMessageContainer = document.createElement('div');
								errorMessageContainer.classList.add('error-message');
								field.parentElement.appendChild(errorMessageContainer);
							}
							errorMessageContainer.textContent = message;
							errorMessageContainer.style.color = 'red';
						}

						// Función para limpiar mensajes de error
						function clearErrors() {
							form.querySelectorAll('.error-message').forEach(function(error) {
								error.remove();
							});
						}

						// Prevención de espacios al escribir en los campos
						form.querySelectorAll('input[type="text"], input[type="password"]').forEach(function(input) {
							input.addEventListener('input', function() {
								input.value = removeSpaces(input.value); // Elimina los espacios mientras escribe
								clearErrors(); // Limpiar los mensajes de error cuando el usuario empieza a escribir
							});
						});

						// Función para capitalizar la primera letra de cada palabra (Nombre y Apellido)
						function capitalizeName(name) {
							return name.split(' ').map(function(word) {
								return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
							}).join(' ');
						}

						// Validación del campo 'Apellido'
						let apellido = form.querySelector('[name="apellido"]');
						apellido.value = removeSpaces(apellido.value); // Elimina espacios
						if (!apellido.value) {
							valid = false;
							showError('El campo Apellido no puede estar vacío ni contener solo espacios.', apellido);
						} else if (/\d/.test(apellido.value)) { // El apellido no puede contener números
							valid = false;
							showError('El campo Apellido no puede contener números.', apellido);
						} else {
							apellido.value = capitalizeName(apellido.value); // Capitalizar el apellido
						}

						// Validación del campo 'Nombre' (solo letras, sin caracteres especiales)
						let nombre = form.querySelector('[name="nombre"]');
						nombre.value = removeSpaces(nombre.value); // Elimina espacios
						if (!nombre.value) {
							valid = false;
							showError('El campo Nombre no puede estar vacío ni contener solo espacios.', nombre);
						} else if (/[^a-zA-Z\s]/.test(nombre.value)) { // El nombre no puede contener caracteres especiales ni números
							valid = false;
							showError('El campo Nombre solo puede contener letras, sin caracteres especiales.', nombre);
						} else {
							nombre.value = capitalizeName(nombre.value); // Capitalizar el nombre
						}

						// Validación del campo 'Documento'
						let documento = form.querySelector('[name="documento"]');
						documento.value = removeSpaces(documento.value); // Elimina espacios
						if (!documento.value) {
							valid = false;
							showError('El campo Documento no puede estar vacío ni contener solo espacios.', documento);
						} else if (!/^\d{8,15}$/.test(documento.value)) { // El documento debe tener entre 8 y 15 dígitos
							valid = false;
							showError('El campo Documento debe contener entre 8 y 15 dígitos.', documento);
						}

						// Validación del campo 'Usuario'
						let usuario = form.querySelector('[name="usuario"]');
						if (usuario) {
							usuario.value = removeSpaces(usuario.value); // Elimina espacios

							// Comprobar si el campo está vacío
							if (!usuario.value) {
								valid = false;
								showError('El campo Usuario no puede estar vacío ni contener solo espacios.', usuario);
							}
							// Comprobar que solo contiene letras y números
							else if (!/^[a-zA-Z0-9]+$/.test(usuario.value)) {
								valid = false;
								showError('El campo Usuario solo puede contener letras y números, sin caracteres especiales.', usuario);
							}
							// Comprobar que tiene exactamente una letra mayúscula
							else if (!/^([a-z]*[A-Z][a-z]*)$/.test(usuario.value)) {
								valid = false;
								showError('El campo Usuario debe contener exactamente una letra mayúscula.', usuario);
							}
							// Comprobar la longitud del campo (entre 5 y 20 caracteres)
							else if (usuario.value.length < 5 || usuario.value.length > 20) {
								valid = false;
								showError('El campo Usuario debe contener entre 5 y 20 caracteres.', usuario);
							}
						} else {
							console.error('El campo "usuario" no fue encontrado en el formulario.');
						}

						// Validación del campo 'Contraseña'
						let clave = form.querySelector('[name="clave"]');
						clave.value = removeSpaces(clave.value); // Elimina espacios
						if (!clave.value) {
							valid = false;
							showError('El campo Contraseña no puede estar vacío ni contener solo espacios.', clave);
						} else if (clave.value.length < 8) { // Contraseña debe tener al menos 8 caracteres
							valid = false;
							showError('La Contraseña debe tener al menos 8 caracteres.', clave);
						} else if (!/[A-Z]/.test(clave.value) || !/[0-9]/.test(clave.value)) { // Contraseña debe tener al menos una mayúscula y un número
							valid = false;
							showError('La Contraseña debe contener al menos una mayúscula y un número.', clave);
						}

						// Validación de campos requeridos vacíos
						form.querySelectorAll('[required]').forEach(function(input) {
							input.value = removeSpaces(input.value); // Elimina espacios
							if (!input.value) {
								valid = false;
								showError('Por favor, completa todos los campos obligatorios correctamente.', input);
							}
						});

						// Si algún campo es inválido, evitar el envío del formulario
						if (!valid) {
							event.preventDefault();
						}
					});
				</script>

				<?php

				$crear = new DoctoresC();
				$crear->CrearDoctorC();

				?>

			</form>

		</div>

	</div>

</div>


<div class="modal fade" rol="dialog" id="EditarDoctor">

	<div class="modal-dialog">

		<div class="modal-content">

			<form method="post" role="form">

				<div class="modal-body">

					<div class="box-body">

						<div class="form-group">

							<h2>Apellido:</h2>

							<input type="text" class="form-control input-lg" id="apellidoE" name="apellidoE" required>

							<input type="hidden" id="Did" name="Did">

						</div>

						<div class="form-group">

							<h2>Nombre:</h2>

							<input type="text" class="form-control input-lg" id="nombreE" name="nombreE" required>

						</div>


						<div class="form-group">

							<h2>Sexo:</h2>

							<select class="form-control input-lg" name="sexoE" required="">

								<option id="sexoE"></option>

								<option value="Masculino">Masculino</option>
								<option value="Femenino">Femenino</option>

							</select>

						</div>



						<div class="form-group">

							<h2>Usuario:</h2>

							<input type="text" class="form-control input-lg" id="usuarioE" name="usuarioE" required>

						</div>

						<div class="form-group">

							<h2>Contraseña:</h2>

							<input type="password" class="form-control input-lg" id="claveE" name="claveE" required>

						</div>

					</div>

				</div>


				<div class="modal-footer">

					<button type="submit" class="btn btn-success">Guardar Cambios</button>

					<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>

				</div>

				<?php

				$actualizar = new DoctoresC();
				$actualizar->ActualizarDoctorC();

				?>

			</form>

		</div>

	</div>

</div>


<?php

$borrarD = new DoctoresC();
$borrarD->BorrarDoctorC();

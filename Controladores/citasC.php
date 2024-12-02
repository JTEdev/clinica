<?php

class CitasC
{

	//Pedir Cita Paciente
	public function EnviarCitaC()
	{

		if (isset($_POST["Did"])) {

			$tablaBD = "citas";

			$Did = substr($_GET["url"], 7);

			$datosC = array("Did" => $_POST["Did"], "Pid" => $_POST["Pid"], "nyaC" => $_POST["nyaC"], "Cid" => $_POST["Cid"], "documentoC" => $_POST["documentoC"], "fyhIC" => $_POST["fyhIC"], "fyhFC" => $_POST["fyhFC"]);

			$resultado = CitasM::EnviarCitaM($tablaBD, $datosC);

			if ($resultado == true) {

				echo '<script>

				window.location = "Doctor/"' . $Did . ';
				</script>';
			}
		}
	}

 

	


	//Mostrar Citas
	public static function VerCitasC()
	{

		$tablaBD = "citas";

		$resultado = CitasM::VerCitasM($tablaBD);

		return $resultado;
	}




	// Mostrar días y horas disponibles para un doctor
	public static function VerDisponibilidadC($idDoctor)
	{
		$tablaBD = "citas";
		$tablaDoctor = "doctores";

		// Obtener días ocupados del doctor
		$ocupados = CitasM::ObtenerCitasDoctorM($tablaBD, $idDoctor);

		// Obtener horario del doctor
		$horario = CitasM::ObtenerHorarioDoctorM($tablaDoctor, $idDoctor);

		return ["ocupados" => $ocupados, "horario" => $horario];
	}


	public static function VerTodosLosHorariosOcupados()
	{
		$tabla = "citas";
		$respuesta = CitasM::ObtenerTodosLosHorariosOcupados($tabla);

		// Aquí agregamos un filtro para separar las citas por paciente, doctor, y fecha.
		$horariosOcupados = [];
		foreach ($respuesta as $cita) {
			$horariosOcupados[] = [
				"inicio" => $cita["inicio"],
				"id_paciente" => $cita["id_paciente"] // Guardar el paciente que ocupa ese horario
			];
		}

		return $horariosOcupados;
	}


	public static function EliminarCitaC($idCita) {
		$tabla = "citas";
		return CitasM::EliminarCitaM($tabla, $idCita);
	}

	
		public static function ObtenerCitaPorId($id) {
		   $tabla = "citas";
		   return CitasM::ObtenerCitaPorId($tabla, $id);
		}
	 
		public static function ActualizarFechaCita($id, $nuevaFecha) {
		   $tabla = "citas";
		   $datos = ["id" => $id, "inicio" => $nuevaFecha];
		   return CitasM::ActualizarCita($tabla, $datos);
		}
	 
	 

	


	// Programar cita desde la perspectiva del doctor
	public function PedirCitaDoctorC()
	{
		if (isset($_POST["Did"])) {
			$tablaBD = "citas";

			$datosC = [
				"Did" => $_POST["Did"],
				"Cid" => $_POST["Cid"],
				"Pid" => $_POST["Pid"],
				"nombreP" => $_POST["nombreP"],
				"documentoP" => $_POST["documentoP"],
				"fyhIC" => $_POST["fyhIC"],
				"fyhFC" => $_POST["fyhFC"]
			];

			$resultado = CitasM::PedirCitaDoctorM($tablaBD, $datosC);

			if ($resultado == true) {
				echo '<script>
                window.location = "Citas/" . $_POST["Did"];
                </script>';
			}
		}
	}
}

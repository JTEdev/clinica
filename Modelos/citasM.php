    <?php

    require_once "ConexionBD.php";

    class CitasM extends ConexionBD
    {

        //Pedir Cita Paciente
        static public function EnviarCitaM($tablaBD, $datosC)
        {

            $pdo = ConexionBD::cBD()->prepare("INSERT INTO $tablaBD (id_doctor, id_consultorio, id_paciente, nyaP, documento, inicio, fin) VALUES (:id_doctor, :id_consultorio, :id_paciente, :nyaP, :documento, :inicio, :fin)");

            $pdo->bindParam(":id_doctor", $datosC["Did"], PDO::PARAM_INT);
            $pdo->bindParam(":id_consultorio", $datosC["Cid"], PDO::PARAM_INT);
            $pdo->bindParam(":id_paciente", $datosC["Pid"], PDO::PARAM_INT);

            $pdo->bindParam(":nyaP", $datosC["nyaC"], PDO::PARAM_STR);
            $pdo->bindParam(":documento", $datosC["documentoC"], PDO::PARAM_STR);
            $pdo->bindParam(":inicio", $datosC["fyhIC"], PDO::PARAM_STR);
            $pdo->bindParam(":fin", $datosC["fyhFC"], PDO::PARAM_STR);

            if ($pdo->execute()) {
                return true;
            }

            //$pdo -> close();
            $pdo = null;
        }


        //Mostrar Citas
        static public function VerCitasM($tablaBD)
        {

            $pdo = ConexionBD::cBD()->prepare("SELECT * FROM $tablaBD");

            $pdo->execute();

            return $pdo->fetchAll();

            $pdo->close();
            $pdo = null;
        }

        // Obtener las citas existentes de un doctor
        static public function ObtenerCitasDoctorM($tablaBD, $idDoctor)
        {
            $pdo = ConexionBD::cBD()->prepare("SELECT inicio FROM $tablaBD WHERE id_doctor = :id_doctor");
            $pdo->bindParam(":id_doctor", $idDoctor, PDO::PARAM_INT);
            $pdo->execute();

            return $pdo->fetchAll(PDO::FETCH_ASSOC);
        }

        // Obtener el horario de un doctor
        static public function ObtenerHorarioDoctorM($tablaDoctor, $idDoctor)
        {
            $pdo = ConexionBD::cBD()->prepare("SELECT horarioE, horarioS FROM $tablaDoctor WHERE id = :id");
            $pdo->bindParam(":id", $idDoctor, PDO::PARAM_INT);
            $pdo->execute();

            return $pdo->fetch(PDO::FETCH_ASSOC);
        }

        // En el Modelo (CitasM)
        public static function ObtenerTodosLosHorariosOcupados($tabla)
        {
            $stmt = ConexionBD::cBD()->prepare("SELECT inicio, id_paciente FROM $tabla"); // Agregar id_paciente para identificar las citas
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devolver como un array asociativo
        }

        public static function EliminarCitaM($tabla, $idCita) {
            $stmt = ConexionBD::cBD()->prepare("DELETE FROM $tabla WHERE id = :id");
            $stmt->bindParam(":id", $idCita, PDO::PARAM_INT);
    
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
    
            $stmt = null; // Cerrar conexión
      }

        
      public static function ObtenerCitaPorId($tabla, $id) {
        $stmt = ConexionBD::cBD()->prepare("SELECT * FROM $tabla WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
     }
  
     public static function ActualizarCita($tabla, $datos) {
        $stmt = ConexionBD::cBD()->prepare("UPDATE $tabla SET inicio = :inicio WHERE id = :id");
        $stmt->bindParam(":inicio", $datos["inicio"], PDO::PARAM_STR);
        $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
  
        if ($stmt->execute()) {
           return true;
        } else {
           return false;
        }
     }
     public static function IncrementarEdicionesCita($idCita) {
        $tabla = "citas";
        $sql = "UPDATE $tabla SET inicio = inicio + 1 WHERE id = :id";
        $stmt = ConexionBD::cBD()->prepare($sql);
        $stmt->bindParam(":id", $idCita, PDO::PARAM_INT);
    
        return $stmt->execute();
    }
    
      
        // Guardar nueva cita
        static public function PedirCitaDoctorM($tablaBD, $datosC)
        {
            $pdo = ConexionBD::cBD()->prepare(
                "INSERT INTO $tablaBD (id_doctor, id_consultorio, id_paciente, nyaP, documento, inicio, fin) 
                VALUES (:id_doctor, :id_consultorio, :id_paciente,  :nyaP, :documento, :inicio, :fin)"
            );

            $pdo->bindParam(":id_doctor", $datosC["Did"], PDO::PARAM_INT);
            $pdo->bindParam(":id_consultorio", $datosC["Cid"], PDO::PARAM_INT);
            $pdo->bindParam(":id_paciente", $datosC["Pid"], PDO::PARAM_INT);
            $pdo->bindParam(":nyaP", $datosC["nombreP"], PDO::PARAM_STR);
            $pdo->bindParam(":documento", $datosC["documentoP"], PDO::PARAM_STR);
            $pdo->bindParam(":inicio", $datosC["fyhIC"], PDO::PARAM_STR);
            $pdo->bindParam(":fin", $datosC["fyhFC"], PDO::PARAM_STR);

            if ($pdo->execute()) {
                return true;
            }

            $pdo = null;
        }
    }

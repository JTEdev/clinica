<?php

class CitasC{

    //pedir citas pacientes
    public function EnviarCitaC(){

        if (isset($_POST["Did"])) {
            $tablaBD = "citas";

            $Did = substr($_GET["url"], 7);


            $datosC = array("Did"=>$_POST["Did"], "Pid"=>$_POST["Pid"], "nyaC"=>$_POST["nyaC"],
                             "Cid"=>$_POST["Cid"], "documentoC"=>$_POST["documentoC"],
                             "fyhIC"=>$_POST["fyhIC"], "fyhFC"=>$_POST["fyhFC"],);

            $resultado = CitasM::EnviarCitaM($tablaBD, $datosC);

            if ($resultado == TRUE) {
                echo'"todo correcto"
                
                
                ';
            }
        }
    }

}


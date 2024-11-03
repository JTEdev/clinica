<?php

require_once "Controladores/plantillaC.php";

require_once "Controladores/secretariasC.php";
require_once "Modelos/secretariasM.php";


require_once "Controladores/consultoriosC.php";
require_once "Modelos/consultoriosM.php";

require_once "Controladores/doctoresC.php";
require_once "Modelos/doctoresM.php";

require_once "Controladores/pacientesC.php";
require_once "Modelos/pacientesM.php";


$plantilla = new Plantilla();
$plantilla -> LlamarPlantilla();

<?php
   // require_once "modelo.php";
   require_once "vista.php";

   class Contolador {
        public function __construct()
        {

        }

        public function Inicia() {
            Vista::MuestraLanding();
        }
   
    }
    $programa = new Contolador();
    $programa->Inicia();
?>


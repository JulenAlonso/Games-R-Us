<?php
    require_once BASE_PATH . '/src/vistas/vista.php';

    class Controlador {
        public function __construct() {
            // Puedes inicializar aquí si es necesario
        }

        public function Inicia() {
            // Verifica si hay una solicitud POST
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->procesaFormulario();
            } else {
                Vista::MuestraLanding(); // Carga la vista por defecto
            }
        }

        private function procesaFormulario() {
            // Verifica qué botón fue presionado
            if (isset($_POST['loginbuton'])) {
                Vista::MuestraLogin();
            } elseif (isset($_POST['homeButton'])) {
                // Acción para el botón "Home" (Ejemplo si lo agregas)
                echo "Se presionó el botón Home";
            } elseif (isset($_POST['offersButton'])) {
                // Acción para el botón "Offers"
                echo "Se presionó el botón Offers";
            } else {
                echo "Ningún botón reconocido fue presionado.";
            }
        }
}
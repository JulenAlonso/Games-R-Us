<?php
// Ruta base del proyecto
define('BASE_PATH', realpath(__DIR__ . '/../'));

// Autoload de clases (recomendado usar Composer)
require_once BASE_PATH . '/src/controladores/Controlador.php';

// Crear instancia del controlador
$controlador = new Controlador();

// Procesar la solicitud dependiendo del método HTTP
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        // Pasa la acción al controlador
        $accion = $_POST['accion'];

        // Llama al método correspondiente basado en la acción
        switch ($accion) {
            case 'listadoLanding':
                $controlador->listadoLanding();
                break;
            case 'listadoJuegos':
                $controlador->listadoJuegos();
                break;
            case 'listadoUsers':
                $controlador->admn_listarUsers();
                break;
            // Puedes agregar otros casos aquí si es necesario
            default:
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Acción no válida.'
                ]);
                break;
        }
    } else {
        $controlador->Inicia();
    }
} else {
    // Procesa solicitudes GET o muestra la vista predeterminada
    $controlador->Inicia();
}
?>
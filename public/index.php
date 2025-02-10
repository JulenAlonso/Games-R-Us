<?php
// Ruta base del proyecto
define('BASE_PATH', realpath(__DIR__ . '/../'));

// Autoload de clases (recomendado usar Composer)
require_once BASE_PATH . '/src/controladores/Controlador.php';
require_once BASE_PATH . '/src/controladores/api.php';

// Crear instancia del controlador
$controlador = new Controlador();
$api = new Api();

// Procesar la solicitud dependiendo del método HTTP
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Depurar los datos que llegan al servidor
    error_log("POST: " . print_r($_POST, true));
    error_log("FILES: " . print_r($_FILES, true));

    // Capturar el parámetro 'accion'
    $accion = $_POST['accion'] ?? ($_SERVER['HTTP_X_ACCION'] ?? null);
    
    if ($accion) {
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
            case 'editarUsuario':
                $controlador->editarUsuario();
                break;
            case 'editarJuego':
                $controlador->editarJuego();
                break;
            case 'eliminarUsuario':
                $controlador->eliminarUsuario();
                break;
            case 'eliminarJuego':
                $controlador->eliminarJuego();
                break;
            case 'cargarUsuario':
                $controlador->procesarUsuario();
                break;
            case 'listadoGeneros':
                $controlador->listadoGeneros();
                break;
            case 'listadoSistemas':
                $controlador->listadoSistemas();
                break;
            case 'submitUserImg':
                $controlador->EditarDatosUsuario();
                break;
            case 'editarGenero':
                $controlador->editarGenero();
                break;
            case 'eliminarGenero':
                $controlador->eliminarGenero();
                break;
            case 'editarSistema':
                $controlador->editarSistema();
                break;
            case 'eliminarSistema': 
                $controlador->eliminarSistema();
                break;
            case 'crearSistema':
                $controlador->crearSistema();
                break;
            case 'crearGenero':
                $controlador->crearGenero();
                break;
            case 'listadoRoles':
                $controlador->listadoRoles();
                break;
            case 'agregarUsuario':
                $controlador->agregarUsuario();
                break;
            case 'agregarJuego':
                $controlador->agregarJuego();
                break;
            case 'listarcesta':
                $controlador->listarcesta();
                break;
            case 'eliminarJuegoCesta':
                $controlador->eliminarjuegocesta();
                break;
            case 'buscarJuego':
                $api->buscarJuego();
                break;
            case 'importarJuegoJSON':
                $controlador->importarJuegoJSON();
                break;
            case 'EditarDatosUsuario':
                $controlador->EditarDatosUsuario();
                break;
            case 'regalarJuego':
                $controlador->regalarJuego();
                break;
            case 'comprarJuego':
                $controlador->comprarJuego();
                break;
            case 'listadoBiblioteca':
                $controlador->listadoBiblioteca();
                break;


                
            default:
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Acción no válida. PENE'
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
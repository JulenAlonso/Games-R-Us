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
            case 'agregarJuego':
                //$controlador->agregarJuego();
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
            case 'submitProfileForm':
                print_r($_REQUEST);
                //$controlador->cambiarAvatar();
                $archivo = $_FILES['cambiarImagen'];
                print_r($archivo);
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
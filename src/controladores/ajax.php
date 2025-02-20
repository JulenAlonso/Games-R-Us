<?php
/**
 * Controlador Ajax para manejar solicitudes relacionadas con las últimas inserciones.
 *
 * PHP version 8.1
 *
 * @category  Controller
 * @package   AjaxController
 * @author    Tu Nombre
 * @license   MIT
 * @version   1.0
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../modelos/modelo.php"; // Asegúrate de que el archivo existe y está en la ruta correcta

/**
 * Clase AjaxController para manejar solicitudes AJAX.
 */
class AjaxController {

    /**
     * Obtiene las últimas inserciones desde el modelo y devuelve los datos en formato JSON.
     *
     * @return void
     */
    public function getUltimasInserciones() {
        // Instanciar el modelo
        $modelo = new Modelo();

        // Obtener las últimas inserciones (por ejemplo, las 5 más recientes)
        $ultimosJuegos = $modelo->obtenerUltimasInserciones(2);

        // Verificar que los datos sean correctos antes de enviarlos
        if (!$ultimosJuegos) {
            echo json_encode(["error" => "No se encontraron juegos"]);
            return;
        }

        // Forzar la cabecera JSON y devolver la respuesta
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($ultimosJuegos);
    }
}

// Manejar la petición AJAX
if (isset($_GET['action']) && $_GET['action'] == 'getUltimasInserciones') {
    $controller = new AjaxController();
    $controller->getUltimasInserciones();
}

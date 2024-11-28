<?php
// Ruta base del proyecto
define('BASE_PATH', realpath(__DIR__ . '/../'));

// Autoload de clases (recomendado usar Composer)
require_once BASE_PATH . '/src/controladores/Controlador.php';

// Crear instancia del controlador y procesar la acción
$controlador = new Controlador();
$controlador->Inicia();

?>
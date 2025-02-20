<?php
/**
 * Servicio SOAP para validación de números de tarjeta de crédito.
 *
 * Este script implementa un servidor SOAP en PHP que proporciona una función
 * para validar números de tarjeta de crédito utilizando el algoritmo de Luhn.
 *
 * @category  SOAP_Server
 * @package   PaymentValidationService
 * @author    Tu Nombre
 * @license   MIT
 * @version   1.0
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Permitir acceso desde cualquier origen (ajustar en producción)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, SOAPAction, X-Requested-With");
    header("Access-Control-Allow-Origin: http://localhost");
    header("Access-Control-Allow-Credentials: true");
    http_response_code(200);
    exit;
}

// Definir la URI base para el servicio SOAP
$uri = "http://localhost/Games-R-Us/soap";

/**
 * Valida un número de tarjeta de crédito utilizando el algoritmo de Luhn.
 *
 * Esta función toma un número de tarjeta como entrada, elimina los espacios en blanco
 * y aplica el algoritmo de Luhn para verificar si el número es válido.
 *
 * @param string $numeroTarjeta El número de tarjeta de crédito a validar.
 * @return bool Devuelve `true` si el número de tarjeta es válido, `false` en caso contrario.
 */
function comprobarTarjeta($numeroTarjeta) {
    // Eliminar espacios en blanco
    $numeroTarjeta = str_replace(' ', '', $numeroTarjeta);
    $longitud = strlen($numeroTarjeta);
    $suma = 0;
    $par = false; 

    for ($i = $longitud - 1; $i >= 0; $i--) {
        $digito = intval($numeroTarjeta[$i]);
        if ($par) {
            $digito *= 2;
            if ($digito > 9) {
                $digito -= 9;
            }
        }
        $suma += $digito;
        $par = !$par;
    }

    return ($suma % 10) == 0;
}

try {
    $server = new SoapServer(null, [
        'uri' => "http://localhost/Games-R-Us/soap",
        'soap_version' => SOAP_1_1
    ]);
    $server->addFunction('comprobarTarjeta');
    //file_put_contents("log_soap_request.xml", file_get_contents("php://input"));
    $server->handle();
} catch (Exception $e) {
    error_log("Error en el servidor SOAP: " . $e->getMessage());
    echo "Error en el servidor SOAP: " . $e->getMessage();
}

?>
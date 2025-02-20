<?php

/**
 * Clase Api para realizar búsquedas de juegos en la API de MobyGames.
 *
 * Esta clase carga variables de entorno y permite realizar consultas 
 * a la API de MobyGames para obtener información sobre juegos según su título.
 *
 * @category  API
 * @package   ApiClient
 * @author    Tu Nombre
 * @license   MIT
 * @version   1.0
 */

require_once __DIR__ . '../../../vendor/autoload.php';
use Dotenv\Dotenv;

/**
 * Clase Api encargada de la gestión de consultas a la API de MobyGames.
 */
class Api {

    /**
     * Constructor de la clase.
     *
     * Carga las variables de entorno desde el archivo .env.
     */
    public function __construct() {
        // Cargar las variables de entorno
        $dotenv = Dotenv::createImmutable(__DIR__ . '/');
        $dotenv->load();
    }

    /**
     * Busca un juego en la API de MobyGames a partir del título proporcionado.
     *
     * Este método obtiene el título desde una solicitud POST, construye una consulta 
     * a la API de MobyGames y devuelve los resultados en formato JSON.
     *
     * @return void
     */
    public function buscarJuego() {
        if (isset($_POST['title'])) {
            $title = htmlspecialchars($_POST['title']);

            // URL del API de MobyGames con la clave de API
            $apiKey = $_ENV['API_KEY'];
            $apiUrl = "https://api.mobygames.com/v1/games?api_key={$apiKey}&title=" . urlencode($title);

            try {
                // Realizar la solicitud a la API
                $response = file_get_contents($apiUrl);
                if (!$response) {
                    throw new Exception("No se pudo conectar a la API.");
                }

                $data = json_decode($response, true);

                // Verificar si hay resultados
                if (!isset($data['games']) || empty($data['games'])) {
                    echo json_encode(['success' => false, 'message' => 'No se encontraron juegos']);
                    return;
                }

                // Extraer información relevante
                $games = [];
                foreach ($data['games'] as $game) {
                    $games[] = [
                        'id' => $game['game_id'] ?? null,
                        'titulo' => $game['title'] ?? "Sin título",
                        'desarrollador' => isset($game['developers']) ? implode(", ", $game['developers']) : "Desconocido",
                        'distribuidor' => isset($game['publishers']) ? implode(", ", $game['publishers']) : "Desconocido",
                        'anio' => isset($game['first_release_date']) ? date("Y", strtotime($game['first_release_date'])) : "Desconocido",
                        'portada' => $game['sample_cover']['image'] ?? 'https://cdn-icons-png.flaticon.com/512/5260/5260498.png',
                        'plataformas' => isset($game['platforms']) ? array_column($game['platforms'], 'platform_name') : [],
                        'url' => $game['moby_url'] ?? '#'
                    ];
                }

                // Devolver los datos en JSON
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'data' => $games]);

            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error al consultar la API: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Parámetro "title" faltante']);
        }
    }
}
?>

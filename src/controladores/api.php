<?php

require_once __DIR__ . '../../../vendor/autoload.php';
use Dotenv\Dotenv;

class Api {

    public function __construct() {
        // Cargar las variables de entorno
        $dotenv = Dotenv::createImmutable(__DIR__ . '/');
        $dotenv->load();
    }

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

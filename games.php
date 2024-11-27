<?php
// Configuración: Carpeta donde están los juegos
$gamesDir = __DIR__ . '/src/games';

// Validar si la carpeta de juegos existe
if (!is_dir($gamesDir)) {
    die("La carpeta de juegos no existe. Por favor, verifica la ruta: $gamesDir");
}

// Obtener la lista de juegos ZIP
$games = array_filter(scandir($gamesDir), function ($file) use ($gamesDir) {
    return pathinfo($file, PATHINFO_EXTENSION) === 'zip' && is_file($gamesDir . '/' . $file);
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emulador de Juegos DOS</title>
    <!-- Cargar js-dos desde CDN (API v8) -->
    <link rel="stylesheet" href="https://v8.js-dos.com/latest/js-dos.css">
    <script src="https://v8.js-dos.com/latest/js-dos.js"></script>
    <style>
        body { font-family: Arial, sans-serif; }
        .game-list { margin-bottom: 20px; }
        .game-list a { text-decoration: none; color: blue; }
        .game-list a:hover { text-decoration: underline; }
        #dosbox-container { width: 100%; max-width: 800px; height: 600px; border: 1px solid #ccc; margin: auto; }
    </style>
</head>
<body>
    <h1>Emulador de Juegos DOS</h1>
    <p>Selecciona un juego para jugar directamente en tu navegador:</p>

    <div class="game-list">
        <ul>
            <?php if (empty($games)): ?>
                <li>No se encontraron juegos en la carpeta <code>src/games</code>.</li>
            <?php else: ?>
                <?php foreach ($games as $game): ?>
                    <li>
                        <a href="?game=<?= urlencode($game) ?>"><?= htmlspecialchars($game) ?></a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

    <div id="dosbox-container">
        <?php if (isset($_GET['game']) && in_array($_GET['game'], $games)): ?>
            <script>
                (async () => {
                    const dosboxContainer = document.getElementById('dosbox-container');
                    const zipUrl = "./src/games/<?= htmlspecialchars($_GET['game']) ?>";

                    try {
                        // Cargar el archivo ZIP desde la URL y configurarlo como bundle
                        const response = await fetch(zipUrl);
                        if (!response.ok) {
                            throw new Error(`Error al cargar el archivo: ${response.statusText}`);
                        }
                        const bundle = await response.blob();

                        // Inicializa el emulador con el archivo cargado
                        await Dos(dosboxContainer, {
                            bundle,
                            wdosboxUrl: "https://v8.js-dos.com/latest/wdosbox.wasm"
                        });
                    } catch (error) {
                        // Mostrar errores en el contenedor
                        dosboxContainer.innerHTML = `<p>Error al cargar el juego. Verifica que el archivo contenga un ejecutable válido (START.EXE).</p>`;
                        console.error("Error al cargar el juego:", error);
                    }
                })();
            </script>
        <?php else: ?>
            <p>Selecciona un juego de la lista para empezar a jugar.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Juegos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 8px;
            font-weight: bold;
        }
        input, select, textarea, button {
            margin-bottom: 20px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #007BFF;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Agregar Juego</h1>
        <form method="POST" enctype="multipart/form-data">
            <label for="titulo">Título</label>
            <input type="text" id="titulo" name="titulo" required>
            
            <label for="titulo2">Título Secundario</label>
            <input type="text" id="titulo2" name="titulo2">

            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="4" required></textarea>

            <label for="categoria">Categoría</label>
            <select id="categoria" name="categoria" required>
                <!-- Opciones dinámicas -->
                <option value="">Seleccione una categoría</option>
                <option value="1">Acción</option>
                <option value="2">Aventura</option>
                <option value="3">Deportes</option>
                <option value="4">Estrategia</option>
                <option value="5">Carreras</option>
                <option value="6">+18</option>
            </select>

            <label for="precio">Precio</label>
            <input type="number" id="precio" name="precio" step="0.01" required>

            <label for="imagen">Imagen</label>
            <input type="file" id="imagen" name="imagen" accept="image/*" required>

            <label for="ruta">Subir ZIP</label>
            <input type="file" id="ruta" name="ruta" accept=".zip" required>

            <button type="submit" name="agregarJuegoButton">Agregar Juego</button>
        </form>
    </div>
</body>
</html>
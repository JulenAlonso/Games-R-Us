<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Datos del Perfil</title>
    <link rel="stylesheet" href="../public/css/editarDatosPerfil.css">
</head>

<body>
    <!-- Modal -->
    <div id="editProfileModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Editar Datos del Perfil</h2>
            <form method="POST" action="guardarDatosPerfil.php">
                <label for="nick">Nick:</label>
                <input type="text" id="nick" name="nick" value="<?php echo htmlspecialchars($_SESSION['user_nick']); ?>" required>
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" required>
                
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($_SESSION['nombre']); ?>">
                
                <label for="ape1">Primer Apellido:</label>
                <input type="text" id="ape1" name="ape1" value="<?php echo htmlspecialchars($_SESSION['ape1']); ?>">
                
                <label for="ape2">Segundo Apellido:</label>
                <input type="text" id="ape2" name="ape2" value="<?php echo htmlspecialchars($_SESSION['ape2']); ?>">
                
                <label for="direccion_tipo">Tipo de Vía:</label>
                <input type="text" id="direccion_tipo" name="direccion_tipo" value="<?php echo htmlspecialchars($_SESSION['user_direccion_tipo']); ?>">
                
                <label for="direccion_via">Nombre de la Vía:</label>
                <input type="text" id="direccion_via" name="direccion_via" value="<?php echo htmlspecialchars($_SESSION['user_direccion_via']); ?>">
                
                <label for="direccion_numero">Número:</label>
                <input type="text" id="direccion_numero" name="direccion_numero" value="<?php echo htmlspecialchars($_SESSION['user_direccion_numero']); ?>">
                
                <label for="direccion_ciudad">Ciudad:</label>
                <input type="text" id="direccion_ciudad" name="direccion_ciudad" value="<?php echo htmlspecialchars($_SESSION['user_direccion_ciudad']); ?>">
                
                <label for="direccion_provincia">Provincia:</label>
                <input type="text" id="direccion_provincia" name="direccion_provincia" value="<?php echo htmlspecialchars($_SESSION['user_direccion_provincia']); ?>">
                
                <label for="direccion_cp">Código Postal:</label>
                <input type="text" id="direccion_cp" name="direccion_cp" value="<?php echo htmlspecialchars($_SESSION['user_direccion_cp']); ?>">
                
                <label for="direccion_pais">País:</label>
                <input type="text" id="direccion_pais" name="direccion_pais" value="<?php echo htmlspecialchars($_SESSION['user_direccion_pais']); ?>">
                
                <button type="submit" name="save_profile">Guardar Cambios</button>
            </form>
        </div>
    </div>
    <button onclick="window.location.href='perfilUsuario.php'">Aceptar</button>
    <script>
        // Open the modal
        function openModal() {
            document.getElementById('editProfileModal').style.display = 'block';
        }

        // Close the modal
        function closeModal() {
            document.getElementById('editProfileModal').style.display = 'none';
        }

        // Open the modal when the page loads
        window.onload = openModal;
    </script>
</body>

</html>
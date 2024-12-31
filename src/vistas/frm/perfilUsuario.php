<?php
require_once BASE_PATH . '/src/vistas/vista.php';

// AL ENTRAR AKI YA TENEMOS LA SESIÓN INICIADA
if (!isset($_SESSION['user_nick'])) {
    exit;
}
// Manejo de subida de imagen
if (isset($_POST['update_image']) && isset($_FILES['profile_image'])) {
    $image = $_FILES['profile_image'];
    $targetDir = BASE_PATH . '/public/uploads/profile/';
    $targetFile = $targetDir . basename($image['name']);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Validar tipo de archivo
    $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($imageFileType, $validExtensions) && $image['size'] <= 500000) { // Tamaño máximo 500KB
        if (move_uploaded_file($image['tmp_name'], $targetFile)) {
            $_SESSION['user_image'] = basename($image['name']);
        } else {
            echo "Error al subir la imagen.";
        }
    } else {
        echo "Formato de imagen no válido o archivo demasiado grande.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="../public/css/nav.css">
    <link rel="stylesheet" href="../public/css/nav_landing.css">
    <link rel="stylesheet" href="../public/css/library.css">
    <link rel="stylesheet" href="../public/css/perfilUsuario.css">
</head>

<body>
    <!-- Navbar -->
    <nav>
        <div>
            <div class="svg-container">
                <img src="../public/logo.png">
            </div>
            <div>
                <p onclick="document.getElementById('nav_iniciobutton').click();">Golden Age Games</p>
                <form method="POST">
                    <input type="submit" id="nav_iniciobutton" name="nav_iniciobutton" hidden>
                </form>
            </div>
        </div>
        <div>
            <div>
                <p onclick="document.getElementById('nav_iniciobutton').click();">Home</p>
                <form method="POST">
                    <input type="submit" id="nav_iniciobutton" name="nav_iniciobutton" hidden>
                </form>
            </div>
            <div class="<?php echo basename($_SERVER['PHP_SELF']) == 'perfilUsuario.php' ? 'active' : ''; ?>">
                <p onclick="document.getElementById('nav_TiendaButton').click();">Tienda</p>
                <form method="POST">
                    <input type="submit" id="nav_TiendaButton" name="nav_TiendaButton" hidden>
                </form>
            </div>
            <div class="<?php echo basename($_SERVER['PHP_SELF']) == 'perfilUsuario.php' ? 'active' : ''; ?>">
                <p onclick="document.getElementById('nav_bibliotecaButton').click();">Biblioteca</p>
                <form method="POST">
                    <input type="submit" id="nav_bibliotecaButton" name="nav_bibliotecaButton" hidden>
                </form>
            </div>
            <div class="svg-container">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </div>
            <!-- User Menu -->
            <div class="svg-container profile-container" onclick="toggleProfileMenu()">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z"
                        clip-rule="evenodd" />
                </svg>
                <div class="profile-menu hidden" id="profileMenu">
                    <?php if (!isset($_SESSION['user_nick'])): ?>
                        <p onclick="document.getElementById('nav_loginButton').click();">Login</p>
                        <form method="POST">
                            <input type="submit" id="nav_loginButton" name="nav_loginButton" hidden>
                        </form>
                        <p onclick="document.getElementById('nav_RegistroButton').click();">Register</p>
                        <form method="POST">
                            <input type="submit" id="nav_RegistroButton" name="nav_RegistroButton" hidden>
                        </form>
                    <?php else: ?>
                        <p onclick="document.getElementById('nav_LogoutButton').click();">Cerrar Sesión</p>
                        <form method="POST">
                            <input type="submit" id="nav_LogoutButton" name="nav_LogoutButton" hidden>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <main class="perfil-usuario">
        <section class="layout">
            <div class="grow1">
                <div class="profile-container">
                    <h1>Perfil de Usuario</h1>
                    <div class="profile-picture-section">
                        <div class="picture-frame">
                            <img src="../public/uploads/profile/<?php echo $_SESSION['user_image'] ?? 'default.png'; ?>"
                                class="profile-picture">
                        </div>
                        <p>
                        <form method="POST" enctype="multipart/form-data">
                            <label for="profileImageInput" class="button">Cambiar imagen</label>
                            <input type="file" id="profileImageInput" name="profile_image" accept="image/*" hidden>
                            <button type="submit" name="update_image">Guardar</button>
                        </form>
                    </div>
                </div>
                <hr>
                <div class="datosPersonales">
                    <center>Datos personales</center>
                    <p></p>
                    <div class="direcciones">
                        <div class="dir1">
                            <div class="profile-details">
                                <p><strong>Nick:</strong>
                                    <?php echo htmlspecialchars($_SESSION['user_nick']); ?>
                                </p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                                <p><strong>Rol:</strong>
                                    <?php echo $_SESSION['user_role'] == 2 ? 'Administrador' : 'Usuario'; ?></p>
                            </div>
                        </div>
                        <div class="dir2">
                            <p><strong>DATOS DE USUARIO</strong>
                                <!-- Aqui irian los campos nombre, apellidos 1 y 2 -->
                            <p><strong>Nombre de usuario:</strong>
                                <?php echo htmlspecialchars($_SESSION['user_nombre']); ?>
                            <p><strong>Primer apellido de usuario:</strong>
                                <?php echo htmlspecialchars($_SESSION['user_ape1']); ?>
                            <p><strong>Segundo apellido de usuario:</strong>
                                <?php echo htmlspecialchars($_SESSION['user_ape2']); ?>
                        </div>
                        <div class="dir3">
                            <!-- Aqui irian los campos de direccion -->
                            <p><strong>DIRECCIÓN</strong>
                            <p><strong>· </strong>
                                <?php echo htmlspecialchars($_SESSION['user_direccion_tipo']); ?>
                            </p>
                            <p><strong>· </strong>
                                <?php echo htmlspecialchars($_SESSION['user_direccion_via']); ?>
                            </p>
                            <p><strong>Número:</strong>
                                <?php echo htmlspecialchars($_SESSION['user_direccion_numero']); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            <hr>
            <div class="grow3">
                Editar datos<br>
                <form method="POST" action="editar_perfil.php">
                    <button type="submit" name="edit_profile">Editar Datos</button>
                </form>
            </div>
        </section>
    </main>
</body>

</html>

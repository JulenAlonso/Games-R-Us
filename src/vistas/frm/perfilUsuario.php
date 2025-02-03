<?php // AL ENTRAR AKI YA TENEMOS LA SESIÓN INICIADA -->
if (!isset($_SESSION['user_nick'])) {
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/nav.css">
    <link rel="stylesheet" href="../public/css/nav_landing.css">
    <link rel="stylesheet" href="../public/css/library.css">
    <link rel="stylesheet" href="../public/css/perfilUsuario.css">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <title>Perfil de Usuario</title>
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
            <div>
                <p onclick="document.getElementById('nav_TiendaButton').click();">Tienda</p>
                <form method="POST">
                    <input type="submit" id="nav_TiendaButton" name="nav_TiendaButton" hidden>
                </form>
            </div>
            <div>
                <p onclick="document.getElementById('nav_bibliotecaButton').click();">Biblioteca</p>
                <form method="POST">
                    <input type="submit" id="nav_bibliotecaButton" name="nav_bibliotecaButton" hidden>
                </form>
            </div>
            <!-- CARRITO  -->
            <div>
                <?php if (isset($_SESSION['user_nick'])): ?>
                    <!-- Mostrar botón solo para usuarios autenticados -->
                    <p onclick="document.getElementById('nav_carritoButton').click();">Carrito</p>
                    <form method="POST">
                        <input type="submit" id="nav_carritoButton" name="nav_carritoButton" hidden>
                    </form>
                <?php endif; ?>
            </div>
            <!-- -------------------------------- -->
            <div class="profile-menu hidden" id="profileMenu">
                <?php if (!isset($_SESSION['user_nick'])): ?>
                    <!-- Opciones para usuarios no autenticados -->
                    <p onclick="document.getElementById('nav_loginButton').click();">Login</p>
                    <form method="POST">
                        <input type="submit" id="nav_loginButton" name="nav_loginButton" hidden>
                    </form>
                    <p onclick="document.getElementById('nav_RegistroButton').click();">Register</p>
                    <form method="POST">
                        <input type="submit" id="nav_RegistroButton" name="nav_RegistroButton" hidden>
                    </form>
                <?php else: ?>
                    <!-- Opciones para usuarios autenticados -->
                    <p onclick="document.getElementById('nav_ProfileButton').click();">Perfil</p>
                    <form method="POST">
                        <input type="submit" id="nav_ProfileButton" name="nav_ProfileButton" hidden>
                    </form>
                    <?php if ($_SESSION['user_role'] == 2): ?>
                        <!-- Opción para administradores -->
                        <p onclick="document.getElementById('nav_AdminButton').click();">Admin Zone</p>
                        <form method="POST">
                            <input type="submit" id="nav_AdminButton" name="nav_AdminButton" hidden>
                        </form>
                    <?php endif; ?>
                    <p onclick="document.getElementById('nav_LogoutButton').click();">Cerrar Sesión</p>
                    <form method="POST">
                        <input type="submit" id="nav_LogoutButton" name="nav_LogoutButton" hidden>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        </div>
    </nav>
    <div class="perfil-usuario">
        <section class="layout">
            <div class="grow1">
                <div class="profile-container">
                    <h1>Perfil de Usuario</h1>
                    <div class="profile-picture-section">
                        <div class="picture-frame">
                            <img class="profile-picture" id="user_avatar" src="../../uploads/image/avatar/default.png"
                                alt="Avatar de usuario">
                        </div>
                        <form method="POST" enctype="multipart/form-data">
                            <input type="file" id="user_avatar" name="user_avatar" accept="image/*" required>
                            <input type="submit" id="submitProfileForm" name="submitProfileForm">Actualizar
                            Foto</input>
                            <input type="hidden" name="nick_user" id="nick_user" value="">
                            <input type="hidden" name="id_A" id="id_A" value="userFormImg">
                            <input type="text" name="accion" value="submitUserImg" hidden>
                        </form>
                    </div>
                </div>
            </div>
            <hr>
            <form method="POST">
                <div class="datosPersonales">
                    <center>
                        <h1>DATOS PERSONALES</h1>
                    </center>
                    <div class="direcciones">
                        <div class="dir1">
                            <div class="profile-details">
                                <strong>Nick: </strong>
                                <p id="user_nick"> </p> <!-- Campo no editable -->
                                <strong>Email: </strong>
                                <p id="user_email"> </p> <!-- Campo no editable -->
                                <strong>Rol: </strong>
                                <p id="user_role"> </p> <!-- Campo no editable -->
                            </div>
                        </div>
                        <div class="dir2">
                            <p><strong>DATOS DE USUARIO</strong></p>
                            <strong>Nombre de usuario: </strong><br>
                            <input type="text" id="user_nombre" name="user_nombre"><br>
                            <strong>Primer apellido: </strong><br>
                            <input type="text" id="user_ape1" name="user_ape1"><br>
                            <strong>Segundo apellido: </strong><br>
                            <input type="text" id="user_ape2" name="user_ape2"><br>
                            <strong>Teléfono: </strong><br>
                            <input type="tel" id="user_tlf" name="user_tlf"><br>
                        </div>
                        <div class="dir3">
                            <p><strong>DIRECCIÓN</strong></p>
                            <strong>Tipo de vía: </strong><br>
                            <input type="text" id="user_direccion_tipo" name="user_direccion_tipo"><br>
                            <strong>Nombre de la vía: </strong><br>
                            <input type="text" id="user_direccion_via" name="user_direccion_via"><br>
                            <strong>Número: </strong><br>
                            <input type="text" id="user_direccion_numero" name="user_direccion_numero"><br>
                            <strong>Otros datos de dirección: </strong><br>
                            <input type="text" id="user_direccion_otros" name="user_direccion_otros"><br>
                        </div>
                    </div>
                    <input type="hidden" id="accion" name="accion" value="EditarDatosUsuario">
                    <input type="hidden" id="nick_user" name="nick_user" value="">
                    <input type="hidden" name="id_A" id="id_A" value="userFormDatos">
                    <center><button type="submit">Guardar</button></center>
                </div>
    </div>
    <hr>
    <div class="grow3">
    </div>
    </section>
    </form>
    </main>
    <script src="../public/js/nav.js"></script>
    <script src="../public/js/perfilUsuario.js"></script>
    <script>
        const user = '<?php echo $_SESSION['user_nick']; ?>';
        cargarUsuario(user);
    </script>
</body>

</html>
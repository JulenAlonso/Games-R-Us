<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/nav.css">
    <link rel="stylesheet" href="../public/css/tienda.css">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <title>Tienda</title>
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
        <div id="lista-juegos-ajax"></div>
        <div>
            <div>
                <p onclick="document.getElementById('nav_iniciobutton').click();">Home</p>
                <form method="POST">
                    <input type="submit" id="nav_iniciobutton" name="nav_iniciobutton" hidden>
                </form>
            </div>
            <div class="active">
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
            <div class="svg-container profile-container" onclick="toggleProfileMenu()">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z"
                        clip-rule="evenodd" />
                </svg>
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

    <div style="display: flex;">
        <!-- Barra lateral para las categorías -->
        <aside id="sidebar"
            style="width: 20%; background-color: #2b2b2b; padding: 20px; height: 100vh; overflow-y: auto;">
            <h2 class="section-title" style="text-align: left; font-size: 1.4em;">Categorías</h2>
            <ul id="category-list" style="list-style: none; padding: 0; color: white; font-size: 1em; cursor: pointer;">
                <!-- Categorías dinámicas -->
            </ul>
        </aside>

        <!-- Contenedor de productos -->
        <main id="store-container" style="width: 80%; padding: 20px;">
            <!-- Los productos se cargarán aquí -->
        </main>
    </div>

    <!-- Contenedor para el modal -->
    <div id="modal-container"></div>

    <script src="../public/js/nav.js"></script>
    <script src="../public/js/tienda.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let userNick = 
            <?php 
                if (isset($_SESSION['user_nick'])){
                    echo json_encode($_SESSION['user_nick']);
                 } else 
                    echo json_encode("null");
            ?>;
            fetchData(userNick);
        });
    </script>
</body>

</html>
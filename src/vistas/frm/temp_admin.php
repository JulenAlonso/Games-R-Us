<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/nav.css">
    <link rel="stylesheet" href="../public/css/adminpage.css">
</head>

<body>
    <header>
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
                <div class="active">
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
                <div class="svg-container">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
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
    </header>
    <main class="container">
        <div class="grid">
            <div class="card">
                <h2>Manage Users</h2>
                <button class="button" onclick="UserData()">Go to Users</button>
                <button class="button" onclick="showUserForm()">Add User</button>
            </div>
            <div class="card">
                <h2>Manage Games</h2>
                <button class="button" onclick="GameData()">Go to Games</button>
                <button class="button" onclick="showGameForm()">Add Game</button>
            </div>
        </div>

        <!-- Formulario de agregar usuario -->
        <div id="userForm" class="user-list hidden">
            <h2>Add User</h2>
            <form id="addUserForm" onsubmit="addUser(event)">
                <input type="text" id="nick" placeholder="Nick" required>
                <input type="email" id="email" placeholder="Email" required>
                <input type="password" id="pass" placeholder="Contraseña" required>
                <input type="text" id="nombre" placeholder="Nombree" >
                <input type="text" id="ape1" placeholder="Apellido 1" >
                <input type="text" id="ape2" placeholder="Apellido 2" >
                <input type="text" id="tlf" placeholder="Numero de Telefono" >
                <input type="text" id="direccion" placeholder="Dirección">
                
                <!-- Role Selector -->
                <select id="rol" required>
                    <option value="" disabled selected>Selecciona Role</option>
                    <option value="1">User</option>
                    <option value="2">Admin</option>
                </select>

                <button type="submit">Añadir User</button>
            </form>
        </div>

        <!-- Formulario de agregar juego -->
        <div id="gameForm" class="user-list hidden">
            <h2>Add Game</h2>
            <form id="addGameForm" onsubmit="addGame(event)" enctype="multipart/form-data">
                <input type="text" id="titulo" placeholder="Titulo" required>
                <input type="text" id="desarrollador" placeholder="Desarrollador" required>
                <input type="text" id="distribuidor" placeholder="Bistribuidor" required>
                <input type="text" id="anio" placeholder="Año" required>

                <!-- Genre Selector -->
                <select id="genero" required>
                    <option value="" disabled selected>Selecciona Genero</option>
                    <option value="Acción">Acción</option>
                    <option value="Fantasia">Fantasia</option>
                </select>

                <!-- Systems Selector -->
                <select id="sistema" required>
                    <option value="" disabled selected>Selecciona Sistema</option>
                    <option value="DOS">DOS</option>
                    <option value="Windows Vista">Windows Vista</option>
                </select>

            <!-- Image Upload (Cover Image) -->
            <label for="coverImage">Imagen de Portada:</label>
            <input type="file" id="coverImage" name="coverImage" accept="image/*" required>

            <!-- ZIP Upload -->
            <label for="gameZip">Juego en Zip:</label>
            <input type="file" id="gameZip" name="gameZip" accept=".zip" required>


                <button type="submit">Add Game</button>
            </form>
        </div>



        <!-- Tabla de Usuarios -->
        <div id="userList" class="user-list hidden">
            <h2>User List</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nick</th>
                        <th>Email</th>
                        <th>Nombre</th>
                        <th>Apellido 1</th>
                        <th>Apellido 2</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>ID Rol</th>
                        <th>Acciones</th> <!-- Añadimos columna de acciones para editar -->
                    </tr>
                </thead>
                <tbody id="userTableBody">
                </tbody>
            </table>
        </div>

        <!-- Tabla de Juegos -->
        <div id="gameList" class="user-list hidden">
            <h2>Game List</h2>
            <table>
                <thead>
                    <tr>
                        <th>Titulo</th>
                        <th>Desarrollador</th>
                        <th>Distribuidor</th>
                        <th>Año</th>
                        <th>Genero</th>
                        <th>Sistema</th>
                        <th>Acciones</th> <!-- Añadimos columna de acciones para editar -->
                    </tr>
                </thead>
                <tbody id="gameTableBody">
                </tbody>
            </table>
        </div>

    </main>
    <script src="../public/js/adminpage.js"></script>
</body>
</html>

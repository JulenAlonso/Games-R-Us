<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/nav.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            background-color: #121212;
            color: #ffffff;
        }
        header {
            padding: 20px;
        }
        .container {
            display: flex;
            flex-direction: column;
            padding: 20px;
        }
        .card {
            background-color: #1f1f1f;
            border: 1px solid #272727;
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            border-color: #00bcd4;
        }
        .card h2 {
            color: #00bcd4;
        }
        .button {
            padding: 10px 15px;
            margin-top: 10px;
            background-color: #00bcd4;
            color: #000;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #0199a4;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .hidden {
            display: none;
        }
        .user-list {
            margin-top: 20px;
        }
        .user-list table {
            width: 100%;
            border-collapse: collapse;
        }
        .user-list th, .user-list td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #272727;
        }
        .user-list th {
            background-color: #1f1f1f;
            color: #00bcd4;
        }
    </style>
    <script>
        function loadUsers() {
            const userList = document.getElementById('userList');
            userList.classList.remove('hidden');
            // Mock data - Replace this with actual API call
            const users = [
                { nick: 'john_doe', email: 'john.doe@example.com', password: '1234', nombre: 'John', ape1: 'Doe', ape2: 'Smith', tlf: '123456789', direccion: '123 Main St', id_rol: 1 },
                { nick: 'jane_smith', email: 'jane.smith@example.com', password: '5678', nombre: 'Jane', ape1: 'Smith', ape2: 'Johnson', tlf: '987654321', direccion: '456 Elm St', id_rol: 2 },
                { nick: 'alice_johnson', email: 'alice.johnson@example.com', password: 'abcd', nombre: 'Alice', ape1: 'Johnson', ape2: 'Williams', tlf: '456789123', direccion: '789 Oak St', id_rol: 3 }
            ];

            const tableBody = document.getElementById('userTableBody');
            tableBody.innerHTML = '';
            users.forEach(user => {
                const row = `
                    <tr>
                        <td>${user.nick}</td>
                        <td>${user.email}</td>
                        <td>${user.password}</td>
                        <td>${user.nombre}</td>
                        <td>${user.ape1}</td>
                        <td>${user.ape2}</td>
                        <td>${user.tlf}</td>
                        <td>${user.direccion}</td>
                        <td>${user.id_rol}</td>
                    </tr>`;
                tableBody.innerHTML += row;
            });
        }
    </script>
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
                <p>View, edit, or delete user accounts. Keep track of your website's user activity.</p>
                <button class="button" onclick="loadUsers()">Go to Users</button>
            </div>
            <div class="card">
                <h2>Manage Games</h2>
                <p>Add, edit, or remove games from the website. Customize game details and availability.</p>
                <button class="button">Go to Games</button>
            </div>
        </div>
        <div id="userList" class="user-list hidden">
            <h2>User List</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nick</th>
                        <th>Email</th>
                        <th>Password</th>
                        <th>Nombre</th>
                        <th>Apellido 1</th>
                        <th>Apellido 2</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>ID Rol</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>

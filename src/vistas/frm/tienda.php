<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/nav.css">
    <link rel="stylesheet" href="../public/css/tienda.css">
    <title>Tienda</title>
</head>

<body>
    <nav>
        <div>
            <div class="svg-container">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
                </svg>
            </div>
            <div>
                <p onclick="document.getElementById('nav_iniciobutton').click();">Globe Express</p>
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
            <div class="active">
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
                    <?php if (!isset($_SESSION['user_id'])): ?>
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
    <h2 class="section-title">Acción</h2>
    <div class="container">
        <div class="product">
            <img src="https://localhost/Games-R-US/src/uploads/image/dos_batman-the-movie_1473.png" alt="Producto 1">
            <div class="divider"></div>
            <div class="product-content">
                <h3>Batman The Movie</h3>
                <p>Descripción breve del producto.</p>
                <div class="buttons">
                <button class="play-button">Comprar</button>
                <button>Regalar</button>
                </div>
            </div>
        </div>
        <div class="product">
            <img src="https://localhost/Games-R-US/src/uploads/image/BatmanTheCapedCrusader.jpg" alt="Producto 2">
            <div class="divider"></div>
            <div class="product-content">
                <h3>Batman The Caped Crusader</h3>
                <p>Descripción breve del producto.</p>
                <div class="buttons">
                    <button class="play-button">Comprar</button>
                    <button>Regalar</button>
                </div>
            </div>
        </div>
        <div class="product">
            <img src="https://localhost/Games-R-US/src/uploads/image/batman-foreverjpg.jpg" alt="Producto 3">
            <div class="divider"></div>
            <div class="product-content">
                <h3>Batman Forever</h3>
                <p>Descripción breve del producto.</p>
                <div class="buttons">
                    <button class="play-button">Comprar</button>
                    <button>Regalar</button>
                </div>
            </div>
        </div>
    </div>

    <h2 class="section-title">Aventuras</h2>
    <div class="container">
        <div class="product">
            <img src="https://via.placeholder.com/200" alt="Producto 1">
            <div class="divider"></div>
            <div class="product-content">
                <h3>Producto 1</h3>
                <p>Descripción breve del producto.</p>
                <div class="buttons">
                    <button class="play-button">Comprar</button>
                    <button>Regalar</button>
                </div>
            </div>
        </div>
        <div class="product">
            <img src="https://via.placeholder.com/200" alt="Producto 2">
            <div class="divider"></div>
            <div class="product-content">
                <h3>Producto 2</h3>
                <p>Descripción breve del producto.</p>
                <div class="buttons">
                    <button class="play-button">Comprar</button>
                    <button>Regalar</button>
                </div>
            </div>
        </div>
        <div class="product">
            <img src="https://via.placeholder.com/200" alt="Producto 3">
            <div class="divider"></div>
            <div class="product-content">
                <h3>Producto 3</h3>
                <p>Descripción breve del producto.</p>
                <div class="buttons">
                    <button class="play-button">Comprar</button>
                    <button>Regalar</button>
                </div>
            </div>
        </div>
        <div class="product">
            <img src="https://via.placeholder.com/200" alt="Producto 3">
            <div class="divider"></div>
            <div class="product-content">
                <h3>Producto 4</h3>
                <p>Descripción breve del producto.</p>
                <div class="buttons">
                    <button class="play-button">Comprar</button>
                    <button>Regalar</button>
                </div>
            </div>
        </div>
    </div>

    <h2 class="section-title">Carreras</h2>
    <div class="container">
        <div class="product">
            <img src="https://via.placeholder.com/200" alt="Producto 1">
            <div class="divider"></div>
            <div class="product-content">
                <h3>Producto 1</h3>
                <p>Descripción breve del producto.</p>
                <div class="buttons">
                    <button class="play-button">Comprar</button>
                    <button>Regalar</button>
                </div>
            </div>
        </div>
        <div class="product">
            <img src="https://via.placeholder.com/200" alt="Producto 2">
            <div class="divider"></div>
            <div class="product-content">
                <h3>Producto 2</h3>
                <p>Descripción breve del producto.</p>
                <div class="buttons">
                    <button class="play-button">Comprar</button>
                    <button>Regalar</button>
                </div>
            </div>
        </div>
        <div class="product">
            <img src="https://via.placeholder.com/200" alt="Producto 3">
            <div class="divider"></div>
            <div class="product-content">
                <h3>Producto 3</h3>
                <p>Descripción breve del producto.</p>
                <div class="buttons">
                    <button class="play-button">Comprar</button>
                    <button>Regalar</button>
                </div>
            </div>
        </div>
        <div class="product">
            <img src="https://via.placeholder.com/200" alt="Producto 3">
            <div class="divider"></div>
            <div class="product-content">
                <h3>Producto 4</h3>
                <p>Descripción breve del producto.</p>
                <div class="buttons">
                    <button class="play-button">Comprar</button>
                    <button>Regalar</button>
                </div>
            </div>
        </div>
    </div>
    <h2 class="section-title">Deportes</h2>
    <div class="container">
        <div class="product">
            <img src="https://via.placeholder.com/200" alt="Producto 1">
            <div class="divider"></div>
            <div class="product-content">
                <h3>Producto 1</h3>
                <p>Descripción breve del producto.</p>
                <div class="buttons">
                    <button class="play-button">Comprar</button>
                    <button>Regalar</button>
                </div>
            </div>
        </div>
        <div class="product">
            <img src="https://via.placeholder.com/200" alt="Producto 2">
            <div class="divider"></div>
            <div class="product-content">
                <h3>Producto 2</h3>
                <p>Descripción breve del producto.</p>
                <div class="buttons">
                    <button class="play-button">Comprar</button>
                    <button>Regalar</button>
                </div>
            </div>
        </div>
        <div class="product">
            <img src="https://via.placeholder.com/200" alt="Producto 3">
            <div class="divider"></div>
            <div class="product-content">
                <h3>Producto 3</h3>
                <p>Descripción breve del producto.</p>
                <div class="buttons">
                    <button class="play-button">Comprar</button>
                    <button>Regalar</button>
                </div>
            </div>
        </div>
        <div class="product">
            <img src="https://via.placeholder.com/200" alt="Producto 3">
            <div class="divider"></div>
            <div class="product-content">
                <h3>Producto 4</h3>
                <p>Descripción breve del producto.</p>
                <div class="buttons">
                    <button class="play-button">Comprar</button>
                    <button>Regalar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../public/js/nav.js"></script>
</body>
</html>
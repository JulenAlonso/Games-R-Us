<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- CSS dedicado al login -->
  <!-- ../public/css/login.css: cada '.', me saca de una carpeta -->
  <link rel="stylesheet" href="../public/css/nav.css">
  <link rel="stylesheet" href="../public/css/login.css">
  <!-- Spline Animation Script -->
  <script type="module" src="https://unpkg.com/@splinetool/viewer@1.9.46/build/spline-viewer.js"></script>
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <title>Login</title>
</head>

<body>
  <!-- Contenedor para el fondo animado -->
  <div class="background-animation">
    <spline-viewer url="https://prod.spline.design/9eH4GDnXXx0Da7it/scene.splinecode"></spline-viewer>
  </div>
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
      <?php if (isset($_SESSION['user_nick'])): ?>
        <!-- Mostrar botón solo para usuarios autenticados -->
        <p class="bi bi-bag" onclick="document.getElementById('nav_carritoButton').click();"></p>
        <form method="POST">
          <input type="submit" id="nav_carritoButton" name="nav_carritoButton" hidden>
        </form>
      <?php endif; ?>
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

  <!-- Formulario de login -->
  <form method="POST" class="login-form">
    <div class="login-container">
      <div class="login-card">
        <h1 class="login-title">Welcome Back</h1>
        <div class="input-group">
          <input type="text" name="nick" placeholder="Usuario">
        </div>
        <div class="input-group">
          <input type="password" name="password" placeholder="Password">
        </div>
        <div class="button-group">
          <input type="submit" name="loginButtonBut" class="login-button" value="Login">
          <input type="submit" name="RegisterButtonBut" class="signup-button" value="Sign Up">
        </div>
      </div>
    </div>
  </form>

  <!-- JavaScript dedicado al login -->
  <script src="../public/js/nav.js"></script>
</body>

</html>
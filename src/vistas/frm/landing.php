<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../public/css/landing.css">
  <link rel="stylesheet" href="../public/css/nav_landing.css">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <title>Document</title>
</head>

<body>
  <div class="indicator"></div>
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
      <p class="bi bi-bag"></p>
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

  <div id="demo"></div>

  <div class="details" id="details-even">
    <div class="place-box">
      <div class="text">Switzerland Alps</div>
    </div>
    <div class="title-box-1">
      <div class="title-1"> </div>
    </div>
    <div class="title-box-2">
      <div class="title-2">ANTONIEN</div>
    </div>
    <div class="desc">
      Tucked away in the Switzerland Alps, Saint Antönien offers an idyllic retreat for those seeking tranquility and
      adventure alike. </div>
  </div>

  <div class="details" id="details-odd">
    <div class="place-box">
      <div class="text">Switzerland Alps</div>
    </div>
    <div class="title-box-1">
      <div class="title-1">SAINT </div>
    </div>
    <div class="title-box-2">
      <div class="title-2">ANTONIEN</div>
    </div>
    <div class="desc">
      Tucked away in the Switzerland Alps, Saint Antönien offers an idyllic retreat for those seeking tranquility and
      adventure alike. </div>
  </div>
  </div>

  <div class="pagination" id="pagination">
    <div class="arrow arrow-left">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
      </svg>
    </div>
    <div class="arrow arrow-right">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
      </svg>
    </div>
    <div class="progress-sub-container">
      <div class="progress-sub-background">
        <div class="progress-sub-foreground"></div>
      </div>
    </div>
    <div class="slide-numbers" id="slide-numbers"></div>
  </div>

  <div class="cover">
  </div>
</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="../public/js/nav.js"></script>
<script src="../public/js/landing.js"></script>

</html>
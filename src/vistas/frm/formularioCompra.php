<?php
require_once BASE_PATH . '/src/vistas/vista.php';

// AL ENTRAR AKI YA TENEMOS LA SESIÓN INICIADA
if (!isset($_SESSION['user_id'])) {
    Vista::MuestraLogin();
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" sizes="32x32" href="../public/logo.png">
  <title>Frontend Mentor | Interactive card details form</title>
  <link rel="stylesheet" href="../public/css/nav.css">
  <link rel="stylesheet" href="../public/css/formularioCompra.css">
</head>
<body>

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
      <div class="active">
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

  <main class="wrapper">

    <div class="card">
      <div class="card-front">
        <img src="../public/img/bg-card-front.png" alt="Frente de la tarjeta">
        <div class="card-front__data">
          <img src="../public/img/card-logo.svg" alt="">
          <div>
            <p class="card-number" id="card-number">0000 0000 0000 0000</p>
            <div class="card-name-date">
              <p class="card-name" id="card-name">Jane Appleseed</p>
              <p class="card-date">
                <span id="card-month">00</span>/<span id="card-year">00</span>
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="card-back">
        <img src="../public/img/bg-card-back.png" alt="Dorso de la tarjeta">
        <div class="card-back__data">
          <p class="card-cvc" id="card-cvc">000</p>
        </div>
      </div>
    </div>

    <div class="form-wrapper">
      <form class="form" id="form">
        <div class="form-group">
          <label for="input-name" class="label">Cardholder Name</label>
          <input type="text" class="input" id="input-name" placeholder="e.g. Jane Appleseed" required>
        </div>
        <div class="form-group">
          <label for="input-number" class="label">Card Number</label>
          <input type="text" class="input" id="input-number" placeholder="e.g. 1234 5678 9123 0000" required>
        </div>
        <div class="form-group double">
          <div class="rows">
            <label for="input-month" class="label">Exp. Date (MM/YY)</label>
            <div class="columns">
              <input type="text" class="input" id="input-month" placeholder="MM" maxlength="2" required>
              <input type="text" class="input" id="input-year" placeholder="YY" maxlength="2" required>
            </div>
          </div>
          <div class="rows">
            <label for="input-cvc" class="label">CVC</label>
            <input type="text" class="input" id="input-cvc" placeholder="e.g. 123" maxlength="3" required>
          </div>
        </div>
        <div class="form-group">
          <button class="button" type="submit">Confirm</button>
        </div>
      </form>
      
      <div class="thank-you disabled" id="thank-you">
        <img src="../public/img/icon-complete.svg" alt="Ícono de completado">
        <p class="thank-you-title">Thank you!</p>
        <p class="thank-you-text">We've added your card details</p>
        <button class="button" id="continue">Continue</button>
      </div>
    </div>



  </main>



  <script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.0.2/cleave.min.js" integrity="sha512-SvgzybymTn9KvnNGu0HxXiGoNeOi0TTK7viiG0EGn2Qbeu/NFi3JdWrJs2JHiGA1Lph+dxiDv5F9gDlcgBzjfA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="../public/js/formularioCompra.js"></script>
  <script src="../public/js/nav.js"></script>
</body>
</html>
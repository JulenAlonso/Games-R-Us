<?php
require_once BASE_PATH . '/src/vistas/vista.php';

// AL ENTRAR AQUI YA TENEMOS LA SESIÓN INICIADA
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
  <link rel="stylesheet" href="../public/css/formularioRegalo.css">
</head>

<body>
  <!-- Contenedor para el fondo animado -->
  <div class="background-animation">
    <spline-viewer url="https://prod.spline.design/9eH4GDnXXx0Da7it/scene.splinecode"></spline-viewer>
  </div>

  <nav>
    <div>
      <div class="svg-container">
        <img src="../public/logo.png" alt="Golden Age Games Logo">
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
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-label="Buscar">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
        </svg>
      </div>
      <!-- User Menu -->
      <div class="svg-container profile-container" onclick="toggleProfileMenu()">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-label="Perfil">
          <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z" clip-rule="evenodd" />
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

  <main>
    <div class="wrapper">
      <div class="div1">
        <form action="" method="POST" id="gift-form">
          <fieldset>
            <legend><b>Formulario de Regalo</b></legend>
            <p></p>
            <div class="inputBox">
              <input type="text" name="gift_nick" id="gift_nick" class="inputUser" required>
              <label for="gift_nick" class="labelInput">Nick del destinatario</label>
            </div><br><br>

            <div class="inputBox">
              <input type="text" name="gift_name" id="gift_name" class="inputUser" required>
              <label for="gift_name" class="labelInput">Nombre del destinatario</label>
            </div><br><br>
            <div class="inputBox">
              <input type="email" name="gift_email" id="gift_email" class="inputUser" required>
              <label for="gift_email" class="labelInput">Email del destinatario</label>
            </div><br><br>
            <div class="inputBox">
              <textarea name="gift_message" id="gift_message" class="inputUser" placeholder="Escribe un mensaje para el destinatario" required></textarea>
              <label for="gift_message" class="labelInput">Mensaje de Regalo</label>
            </div><br><br>
            <div class="inputBox">
              <label for="product_list"><b>Lista de Productos a Regalar:</b></label>
              <textarea name="product_list" id="product_list" class="inputUser" rows="4" placeholder="Escribe los productos que deseas regalar, separados por comas" required></textarea>
            </div><br><br>
          </fieldset>
        </form>
      </div>

      <div class="box1">
        <form action="" method="POST" id="payment-form">
          <fieldset>
            <legend><b>Formulario de Pago</b></legend>
            <p>

              <!-- Información Personal -->
            <div class="inputBox">
              <input type="text" name="name" id="name" class="inputUser" required>
              <label for="name" class="labelInput">Nombre</label>
            </div><br><br>

            <div class="inputBox">
              <input type="text" name="ap1" id="ap1" class="inputUser" required>
              <label for="ap1" class="labelInput">Apellido 1</label>
            </div><br><br>

            <div class="inputBox">
              <input type="text" name="ap2" id="ap2" class="inputUser" required>
              <label for="ap2" class="labelInput">Apellido 2</label>
            </div><br><br>

            <div class="inputBox">
              <input type="email" name="email" id="email" class="inputUser" required>
              <label for="email" class="labelInput">Email</label>
            </div><br><br>

            <div class="inputBox">
              <input type="tel" name="tlf" id="tlf" class="inputUser" required>
              <label for="tlf" class="labelInput">Teléfono</label>
            </div><br><br>

            <div class="inputBox">
              <label for="fechaNac"><b>Fecha de Nacimiento:</b></label>
              <input type="date" name="fechaNac" id="fechaNac" class="fechaNac" required>
            </div><br><br>

            <div class="inputBox">
              <input type="text" name="calle" id="calle" class="inputUser" required>
              <label for="calle" class="labelInput">Calle</label>
            </div><br><br>

            <div class="inputBox">
              <input type="text" name="num" id="num" class="inputUser" required>
              <label for="num" class="labelInput">Número</label>
            </div><br><br>

            <div class="inputBox">
              <input type="text" name="piso" id="piso" class="inputUser" required>
              <label for="piso" class="labelInput">Piso</label>
            </div><br><br>

            <div class="inputBox">
              <input type="text" name="letra" id="letra" class="inputUser" required>
              <label for="letra" class="labelInput">Letra</label>
            </div><br><br>

            <div class="inputBox">
              <input type="text" name="cp" id="cp" class="inputUser" required>
              <label for="cp" class="labelInput">Código Postal</label>
            </div><br><br>

            <div class="inputBox">
              <input type="text" name="localidad" id="localidad" class="inputUser" required>
              <label for="localidad" class="labelInput">Localidad</label>
            </div><br><br>

            <div class="inputBox">
              <input type="text" name="pais" id="pais" class="inputUser" required>
              <label for="pais" class="labelInput">País</label>
            </div><br><br>
          </fieldset>
        </form>
      </div>

      <div class="box2">
        <form class="form" id="form">
          <div class="form-group">
            <label for="input-name" class="label">Titular de la tarjeta</label>
            <input type="text" class="input" id="input-name" placeholder="ej: Pepe Navarro" required>
          </div>

          <div class="form-group">
            <label for="input-number" class="label">Número de la tarjeta</label>
            <input type="text" class="input" id="input-number" placeholder="ej: 1234 5678 9123 0000" required>
          </div>

          <div class="form-group double">
            <div class="rows">
              <label for="input-month" class="label">Exp. Date (MES/AÑO)</label>
              <div class="columns">
                <input type="text" class="input" id="input-month" placeholder="MES" maxlength="2" required>
                <input type="text" class="input" id="input-year" placeholder="AÑO" maxlength="2" required>
              </div>
            </div>

            <div class="rows">
              <label for="input-cvc" class="label">CVC</label>
              <input type="text" class="input" id="input-cvc" placeholder="ej: 123" maxlength="3" required>
            </div>
          </div>

          <div class="form-wrapper">
            <div class="card">
              <div class="card-front">
                <img src="../public/img/bg-card-front.png" alt="Frente de la tarjeta">
                <div class="card-front__data">
                  <img src="../public/img/card-logo.svg" alt="">
                  <div>
                    <p class="card-number" id="card-number">0000 0000 0000 0000</p>
                    <div class="card-name-date">
                      <p class="card-name" id="card-name">Pepe Navarro</p>
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
          </div>

          <div class="thank-you disabled" id="thank-you">
            <img src="../public/img/icon-complete.svg" alt="Ícono de completado">
            <p class="thank-you-title">Thank you!</p>
            <p class="thank-you-text">We've added your card details</p>
            <button class="button" id="continue">Continue</button>
          </div>

          <input type="submit" name="submit" id="submit" value="Realizar Pago">
        </form>
      </div>
    </div>
    </div>
  </main>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.0.2/cleave.min.js" integrity="sha512-SvgzybymTn9KvnNGu0HxXiGoNeOi0TTK7viiG0EGn2Qbeu/NFi3JdWrJs2JHiGA1Lph+dxiDv5F9gDlcgBzjfA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="../public/js/formularioRegalo.js"></script>
  <script src="../public/js/nav.js"></script>
</body>

</html>

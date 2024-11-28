<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <!-- CSS dedicado al registro -->
  <link rel="stylesheet" href="../public/css/login.css">
  <!-- Spline Animation Script -->
  <script type="module" src="https://unpkg.com/@splinetool/viewer@1.9.46/build/spline-viewer.js"></script>
</head>

<body>
  <!-- Contenedor para el fondo animado -->
  <div class="background-animation">
    <spline-viewer url="https://prod.spline.design/LkNjBYNq90bJ4OpG/scene.splinecode"></spline-viewer>
  </div>

  <!-- Contenido principal -->
  <nav>
    <div>
      <div class="svg-container">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
          stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
        </svg>
      </div>
      <div>Globe Express</div>
    </div>
    <div>
      <div>Tienda</div>
      <div>Biblioteca</div>
      <div>Juegos</div>
      <!-- <div>Offers</div> -->
      <!-- <div>Contact</div> -->
      <div class="svg-container, active">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
          <path fill-rule="evenodd"
            d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z"
            clip-rule="evenodd" />
        </svg>
      </div>
    </div>
  </nav>
  <div class="login-container">
    <div class="login-card">
      <h1 class="login-title">Register</h1>
      <form method="POST" enctype="multipart/form-data" target="controlador.php">
        <div class="input-group">
          <input type="text" name="reg_username" placeholder="Username" required>
        </div>
        <div class="input-group">
          <input type="email" name="reg_email" placeholder="Email" required>
        </div>
        <div class="input-group">
          <input type="password" name="reg_password" placeholder="Password" required>
        </div>
        <div class="input-group">
          <input type="password" name="reg_confirm_password" placeholder="Confirm Password" required>
        </div>
        <div class="button-group">
          <input type="submit" name="reg_registerButton" class="login-button" value="Register"></input>
        </div>
      </form>
    </div>
  </div>

  <!-- JavaScript dedicado al registro -->
  <script src="../public/js/register.js"></script>
</body>

</html>
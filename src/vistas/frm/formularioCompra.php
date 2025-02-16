<?php
require_once BASE_PATH . '/src/vistas/vista.php';

// Verificar sesión
if (!isset($_SESSION['user_nick'])) {
    Vista::MuestraLogin();
    exit;
}

// **IMPORTANTE**: Asegúrate de que soap.php es accesible públicamente
// $url = "http://localhost/Games-R-Us/src/controladores/soap.php";
// $uri = "http://localhost/Games-R-Us/public/";

// try {
//     $cliente = new SoapClient(null, ['uri' => $uri, 'location' => $url]);
//     $resultado = $cliente->comprobarTarjeta("4111 1111 1111 1111");
//     echo "Resultado: " . ($resultado ? "Tarjeta válida" : "Tarjeta inválida");
// } catch (SoapFault $e) {
//     echo "Error SOAP: " . $e->getMessage();
// }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/nav.css">
    <link rel="stylesheet" href="../public/css/formularioCompra.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.0.2/cleave.min.js"></script>
    <title>Formulario de Pago</title>
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
            <p>Golden Age Games</p>
        </div>
    </nav>

    <main>
        <div class="wrapper">
            <!-- Formulario de Datos del Usuario -->
            <div class="box1">
                <form id="infoUserPagos">
                    <fieldset>
                        <legend><b>Datos del Usuario</b></legend>
                        
                        <div class="inputBox">
                            <input type="text" name="nick" id="nick" class="inputUser" value="<?php echo $_SESSION['user_nick']; ?>" readonly>
                            <label for="nick" class="labelInput">Nick</label>
                        </div>

                        <div class="inputBox">
                            <input type="text" name="name" id="name" class="inputUser" required>
                            <label for="name" class="labelInput">Nombre</label>
                        </div>

                        <div class="inputBox">
                            <input type="text" name="ap1" id="ap1" class="inputUser" required>
                            <label for="ap1" class="labelInput">Apellido 1</label>
                        </div>
                        
                        <div class="inputBox">
                            <input type="text" name="ap2" id="ap2" class="inputUser" required>
                            <label for="ap1" class="labelInput">Apellido 2</label>
                        </div>

                        <div class="inputBox">
                            <input type="email" name="email" id="email" class="inputUser" required>
                            <label for="email" class="labelInput">Email</label>
                        </div>

                        <div class="inputBox">
                            <input type="tel" name="tlf" id="tlf" class="inputUser" required>
                            <label for="tlf" class="labelInput">Teléfono</label>
                        </div>
                    </fieldset>
                    <br><br><br>
                    <!-- Nueva Sección de Facturación -->
                    <fieldset>
                        <legend><b>Datos de Facturación</b></legend>

                        <div class="inputBox">
                            <input type="text" name="direccion_tipo" id="direccion_tipo" class="inputUser" required>
                            <label for="direccion_tipo" class="labelInput">Tipo de Vía (Ej: Calle, Avenida, etc.)</label>
                        </div>

                        <div class="inputBox">
                            <input type="text" name="direccion_via" id="direccion_via" class="inputUser" required>
                            <label for="direccion_via" class="labelInput">Nombre de la Vía</label>
                        </div>

                        <div class="inputBox">
                            <input type="text" name="direccion_numero" id="direccion_numero" class="inputUser" required>
                            <label for="direccion_numero" class="labelInput">Número</label>
                        </div>

                        <div class="inputBox">
                            <input type="text" name="direccion_otros" id="direccion_otros" class="inputUser" required>
                            <label for="direccion_otros" class="labelInput">Otros detalles (piso, puerta, etc.)</label>
                        </div>


                    </fieldset>
                </form>
            </div>

            <!-- Formulario de Datos de Tarjeta -->
            <div class="box2">
                <form id="infoUserTarjeta">
                    <fieldset>
                        <legend><b>Datos de la Tarjeta</b></legend>

                        <div class="form-group">
                            <label for="input-name" class="label">Titular de la tarjeta</label>
                            <input type="text" class="input" id="input-name" name="input-name" placeholder="ej: Pepe Navarro" required>
                        </div>

                        <div class="form-group">
                            <label for="input-number" class="label">Número de la tarjeta</label>
                            <input type="text" class="input" id="input-number" name="input-number" placeholder="ej: 1234 5678 9123 0000" required>
                        </div>

                        <div class="form-group double">
                            <div class="rows">
                                <label for="input-month" class="label">Exp. Date (MES/AÑO)</label>
                                <div class="columns">
                                    <input type="text" class="input" id="input-month" name="input-month" placeholder="MM" maxlength="2" required>
                                    <input type="text" class="input" id="input-year" name="input-year" placeholder="YY" maxlength="2" required>
                                </div>
                            </div>

                            <div class="rows">
                                <label for="input-cvc" class="label">CVC</label>
                                <input type="text" class="input" id="input-cvc" name="input-cvc" placeholder="ej: 123" maxlength="3" required>
                            </div>
                        </div>
                        <br>
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
                    </fieldset>
                    <br>
                    <button type="submit" class="submit-btn">Realizar Pago</button>
                </form>
            </div>
        </div>
    </main>
    <script src="../public/js/formularioCompra.js"></script>
    <!-- <script src="../public/js/formularioCompra.js"></script>-->
</body>
</html>

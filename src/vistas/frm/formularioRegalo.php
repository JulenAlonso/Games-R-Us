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
    <title>Formulario de Regalo</title>
</head>

<body>

</body>

</html>
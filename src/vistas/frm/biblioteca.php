<?php
    require_once BASE_PATH . '/src/vistas/vista.php';

    // AL ENTRAR AKI YA TENEMOS LA SESIÓN INICIADA
    if (!isset($_SESSION['user_id'])) {
        Vista::MuestraLanding();
        exit;
    }

    echo "Bienvenido, " . $_SESSION['user_email'];
?>

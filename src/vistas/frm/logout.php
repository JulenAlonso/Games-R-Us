<?php
    require_once BASE_PATH . '/src/vistas/vista.php';

    session_start();
    session_destroy();
    header("Location: index.php");
    exit;
?>
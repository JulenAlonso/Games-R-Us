<?php
class Vista {
    public static function MuestraLanding() {
        include BASE_PATH . '/src/vistas/frm/landing.php';
    }

    public static function MuestraLogin() {
        include BASE_PATH . '/src/vistas/frm/login.php';
    }

    public static function MuestraBiblioteca() {
        include BASE_PATH . '/src/vistas/frm/biblioteca.php';
    }

    public static function MuestraRegistro() {
        include BASE_PATH . '/src/vistas/frm/register.php';
    }

    public static function MuestraLogOut() {
        include BASE_PATH . '/src/vistas/frm/logout.php';
    }
}

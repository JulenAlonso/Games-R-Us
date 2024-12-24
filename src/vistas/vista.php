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

    public static function MuestraAdmin() {
        include BASE_PATH . '/src/vistas/frm/temp_admin.php';
    }

    public static function MuestraTienda() {
        include BASE_PATH . '/src/vistas/frm/tienda.php';
    }

    public static function MuestraFormularioCompra() {
        include BASE_PATH . '/src/vistas/frm/formularioCompra.php';
    }
    public static function MuestraFormularioRegalo() {
        include BASE_PATH . '/src/vistas/frm/formularioRegalo.php';
    }

}

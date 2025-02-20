<?php
/**
 * Clase Vista
 *
 * Proporciona métodos estáticos para incluir archivos de vista en la aplicación.
 */
class Vista
{
    /**
     * Muestra la página de inicio (Landing Page).
     *
     * @return void
     */
    public static function MuestraLanding()
    {
        include BASE_PATH . '/src/vistas/frm/landing.php';
    }

    /**
     * Muestra la página de inicio de sesión.
     *
     * @return void
     */
    public static function MuestraLogin()
    {
        include BASE_PATH . '/src/vistas/frm/login.php';
    }

    /**
     * Muestra la página de la biblioteca de juegos del usuario.
     *
     * @return void
     */
    public static function MuestraBiblioteca()
    {
        include BASE_PATH . '/src/vistas/frm/biblioteca.php';
    }

    /**
     * Muestra la página de registro de usuario.
     *
     * @return void
     */
    public static function MuestraRegistro()
    {
        include BASE_PATH . '/src/vistas/frm/register.php';
    }

    /**
     * Muestra la página de cierre de sesión.
     *
     * @return void
     */
    public static function MuestraLogOut()
    {
        include BASE_PATH . '/src/vistas/frm/logout.php';
    }

    /**
     * Muestra el panel de administración.
     *
     * @return void
     */
    public static function MuestraAdmin()
    {
        include BASE_PATH . '/src/vistas/frm/adminZone.php';
    }

    /**
     * Muestra la tienda de juegos.
     *
     * @return void
     */
    public static function MuestraTienda()
    {
        include BASE_PATH . '/src/vistas/frm/tienda.php';
    }

    /**
     * Muestra el formulario de compra.
     *
     * @return void
     */
    public static function MuestraFormularioCompra()
    {
        include BASE_PATH . '/src/vistas/frm/formularioCompra.php';
    }

    /**
     * Muestra la página de perfil del usuario.
     *
     * @return void
     */
    public static function MuestraPerfilUsuario()
    {
        require_once BASE_PATH . '/src/vistas/frm/perfilUsuario.php';
    }
    
    /**
     * Muestra la página del carrito de compras.
     *
     * @return void
     */
    public static function MuestraCarrito()
    {
        require_once BASE_PATH . '/src/vistas/frm/carrito.php';
    }

}

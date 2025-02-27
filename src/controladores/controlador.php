<?php
/**
 * Clase Controlador
 *
 * Maneja la lógica del backend de la aplicación, incluyendo autenticación,
 * gestión de usuarios, manejo de la tienda, carritos de compra y administración.
 *
 * @category  Controlador
 * @package   Games-R-Us
 * @author    Tu Nombre
 * @license   MIT
 * @version   1.0
 */

if (!defined('BASE_PATH')) {
    define('BASE_PATH', realpath(__DIR__ . '/../..')); // Define BASE_PATH si no está definida
}

require_once BASE_PATH . '/src/vistas/vista.php';
require_once BASE_PATH . '/src/modelos/modelo.php';

/**
 * Clase Controlador
 *
 * Encargada de manejar la lógica de negocio y conectar la vista con el modelo.
 */
class Controlador
{
    /**
     * @var Modelo $modelo Instancia del modelo para manejar datos.
     */
    private $modelo;

    /**
     * Constructor de la clase.
     *
     * Inicializa la sesión y crea una instancia del modelo.
     */
    public function __construct() {
        $this->modelo = new Modelo();
        session_start(); // Asegura que las sesiones estén habilitadas en cada solicitud
    }

    /**
     * Punto de entrada principal del sistema.
     *
     * Maneja las solicitudes entrantes y redirige a las funciones correspondientes.
     *
     * @return void
     */
    //FUNCIONALIDAD DE TIENDA
    public function Inicia() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { //Primer filtrado que hacemos
            if (isset($_POST['accion']) && $_POST['accion'] === 'listadoJuegos') {
                $this->listadoJuegos();
            } else {
                $this->procesaNav();
                $this->procesaLogin();
                $this->procesaRegister();
                $this->procesarTienda();
                $this->procesarCarro();
            }
        } else {
            Vista::MuestraLanding(); // Carga la vista por defecto
        }
    }

    /**
     * Maneja la navegación de la aplicación a través de los botones de la barra de navegación.
     *
     * @return void
     */
    private function procesaNav()  {
        // Verifica qué botón fue presionado
        if (isset($_POST['nav_loginButton'])) {
            Vista::MuestraLogin();
        } elseif (isset($_POST['nav_bibliotecaButton'])) {
            if ($this->usuarioAutenticado()) {
                Vista::MuestraBiblioteca();
            } else {
                Vista::MuestraLogin(); // Redirige a login si no está autenticado
            }
        } elseif (isset($_POST['nav_iniciobutton'])) {
            Vista::MuestraLanding();
        } elseif (isset($_POST['nav_TiendaButton'])) {
            Vista::MuestraTienda();
        } elseif (isset($_POST['nav_LogoutButton'])) {
            Vista::MuestraLogOut();
        } elseif (isset($_POST['nav_RegistroButton'])) {
            Vista::MuestraRegistro();
        } elseif (isset($_POST['nav_ProfileButton'])) {
            Vista::MuestraPerfilUsuario();
        } elseif (isset($_POST['nav_AdminButton'])) {
            Vista::MuestraAdmin();
        } elseif (isset($_POST['nav_carritoButton'])) {
            Vista::MuestraCarrito();
        }
    }

    /**
     * Procesa la autenticación de los usuarios.
     *
     * Verifica credenciales y permite el acceso a la biblioteca del usuario.
     *
     * @return void
     */
    private function procesaLogin() {
        if (isset($_POST['loginButtonBut'])) {
            // Obtén los datos del formulario
            $input = $_POST['nick'] ?? ''; // Puede ser el nick o el correo
            $password = $_POST['password'] ?? '';

            // Verifica que la contraseña no esté vacía
            if (empty($password)) {
                echo "La contraseña no puede estar vacía.";
                return;
            }

            // Busca al usuario por nick o correo
            $user = $this->modelo->buscaUsuarioPorNick($input) ?: $this->modelo->buscaUsuarioPorMail($input);

            if ($user) {
                // Verifica la contraseña
                if (password_verify($password, $user['password'])) {
                    // Autenticación exitosa, guarda los datos en la sesión
                    $_SESSION['user_nick'] = $user['nick'];
                    $_SESSION['user_role'] = $user['id_rol']; // Asumiendo que el rol está incluido en el usuario
                    
                    // Redirige a la biblioteca
                    Vista::MuestraBiblioteca();
                    exit;
                } else {
                    echo "Contraseña incorrecta.";
                }
            } else {
                echo "Usuario no encontrado.";
            }
        } elseif (isset($_POST['RegisterButtonBut'])) {
            // Redirige al registro si se presiona el botón de registro
            Vista::MuestraRegistro();
        }
    }

    /**
     * Registra un nuevo usuario en la aplicación.
     *
     * @return void
     */
    private function procesaRegister() {
        if (isset($_POST['reg_registerButton'])) {
            $username = $_POST['reg_username'] ?? '';
            $email = $_POST['reg_email'] ?? '';
            $password = $_POST['reg_password'] ?? '';
            $confirm_password = $_POST['reg_confirm_password'] ?? '';

            // Validación básica
            if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
                echo "Todos los campos son obligatorios.";
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "Formato de email inválido.";
                return;
            }

            if ($password !== $confirm_password) {
                echo "Las contraseñas no coinciden.";
                return;
            }

            // Verifica si el email ya está registrado
            if ($this->modelo->buscaUsuarioPorNick($username)) {
                echo "El email ya está registrado.";
                return;
            }

            // Crea el usuario
            $this->modelo->creaUsuario($username, $email, $password);
            Vista::MuestraLogin();
        }
    }

    /**
     * Verifica si un usuario está autenticado.
     *
     * @return bool Devuelve `true` si hay una sesión activa, `false` en caso contrario.
     */
    private function usuarioAutenticado() {
        // Devuelve verdadero si hay una sesión activa
        return isset($_SESSION['user_nick']);
    }

    /**
     * Muestra el listado de juegos en el landing.
     *
     * @return void
     */
    public function listadoLanding() {

        try {
            // Obtener los juegos desde el modelo
            $juegos = $this->modelo->obtenerLanding();

            // Si no hay juegos, devolver un mensaje adecuado
            if (empty($juegos)) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'No se encontraron juegos.'
                ]);
                exit;
            }

            // Procesar los datos de los juegos
            $juegosProcesados = array_map(function ($juego) {
                $baseUrl = 'https://localhost/Games-r-us/src/uploads/image/portadas/';

                return [
                    'titulo' => htmlspecialchars($juego['titulo']), // Sanitiza para HTML
                    'ruta_imagen' => $baseUrl . $juego['ruta_imagen'],
                    'desarrollador' => htmlspecialchars($juego['desarrollador']),
                    'distribuidor' => htmlspecialchars($juego['distribuidor']),
                    'anio' => htmlspecialchars($juego['anio'])
                ];
            }, $juegos);


            // Devolver los datos como JSON
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $juegosProcesados
            ]);
            exit;
        } catch (Exception $e) {
            // Manejar cualquier error y devolver un mensaje adecuado
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener los juegos: ' . $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Muestra el listado de juegos en la tienda.
     *
     * @return void
     */
    public function listadoJuegos() {
        try {
            // Obtener los juegos desde el modelo
            $juegos = $this->modelo->obtenerJuegos();

            // Si no hay juegos, devolver un mensaje adecuado
            if (empty($juegos)) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'No se encontraron juegos.'
                ]);
                exit;
            }

            // Procesar los datos de los juegos
            $juegosProcesados = array_map(function ($juego) {
                $baseUrl = 'https://localhost/Games-r-us/src/uploads/image/portadas/';
                return [
                    'id' => htmlspecialchars($juego['id_juego']),
                    'titulo' => htmlspecialchars($juego['titulo']), // Sanitiza para HTML
                    'desarrollador' => htmlspecialchars($juego['desarrollador']),
                    'distribuidor' => htmlspecialchars($juego['distribuidor']),
                    'anio' => htmlspecialchars($juego['anio']),
                    'genero' => $this->modelo->obtenerCategoriasJuego($juego['id_juego']),
                    'sistema' => $this->modelo->obtenerSistemasJuego($juego['id_juego']),
                    'ruta' => $juego['ruta'],
                    'ruta_imagen' => $baseUrl . $juego['ruta_imagen'],
                    'precio' => $juego['precio'],
                ];
            }, $juegos);

            // Devolver los datos como JSON
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $juegosProcesados
            ]);
            exit;
        } catch (Exception $e) {
            // Manejar cualquier error y devolver un mensaje adecuado
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener los juegos: ' . $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Agrega un juego al carrito de compras del usuario.
     *
     * @return void
     */
    public function procesarTienda() {

        if (isset($_POST['tienda_comprar'])) {
            if (!empty($_POST['compra_gameid']) && !empty($_POST['compra_usuarionick'])) {
                $id_juego = $_POST['compra_gameid'];
                $nick = $_POST['compra_usuarionick'];
                $this->modelo->aniadirCarro($id_juego, $nick);
                Vista::MuestraCarrito();
            } else {
                echo "Faltan datos en el formulario.";
            }
        }
    }

    /**
     * Obtiene y lista todos los usuarios registrados en la base de datos.
     *
     * Recupera la lista de usuarios junto con sus roles y datos personales
     * para su gestión en el sistema administrativo.
     *
     * @return void Devuelve un JSON con la lista de usuarios o un mensaje de error.
     */
    public function admn_listarUsers() {
        try {
            // Obtener los usuarios del modelo
            $usuarios = $this->modelo->obtenerUsuarios();

            // Si no hay usuarios, devolver un mensaje adecuado
            if (empty($usuarios)) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'No se encontraron usuarios.'
                ]);
                exit;
            }

            $roles = $this->modelo->obtenerRoles();
            $RolesPorId = array_column($roles, 'nombre_rol', 'id_rol');

            // Procesar los datos de los usuarios
            $usuariosProcesados = array_map(function ($user) use ($RolesPorId) {

                return [
                    'nick' => htmlspecialchars($user['nick']),
                    'email' => htmlspecialchars($user['email']),
                    'nombre' => htmlspecialchars($user['nombre'] ?? 'Desconocido'),
                    'ape1' => htmlspecialchars($user['ape1'] ?? 'Desconocido'),
                    'ape2' => htmlspecialchars($user['ape2'] ?? 'Desconocido'),
                    'tlf' => htmlspecialchars($user['tlf'] ?? 'Desconocido'),
                    'avatar' => htmlspecialchars($user['avatar'] ?? 'default.png'),
                    'direccion_tipo' => htmlspecialchars($user['direccion_tipo'] ?? 'Desconocido'),
                    'direccion_via' => htmlspecialchars($user['direccion_via'] ?? 'Desconocido'),
                    'direccion_numero' => htmlspecialchars($user['direccion_numero'] ?? 'Desconocido'),
                    'direccion_otros' => htmlspecialchars($user['direccion_otros'] ?? 'Desconocido'),
                    'rol' => htmlspecialchars($RolesPorId[$user['id_rol']] ?? 'Desconocido'),
                ];
            }, $usuarios);

            // Devolver los datos como JSON
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $usuariosProcesados
            ]);
            exit;
        } catch (Exception $e) {
            // Manejar cualquier error y devolver un mensaje adecuado
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener los usuarios: ' . $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Edita los datos de un usuario en el sistema.
     *
     * @return void
     */
    public function editarUsuario() {
        if (isset($_POST['nick'], $_POST['nombre'], $_POST['ape1'], $_POST['ape2'], $_POST['tlf'])) {
            $nick = htmlspecialchars($_POST['nick']);
            $nombre = htmlspecialchars($_POST['nombre']);
            $ape1 = htmlspecialchars($_POST['ape1']);
            $ape2 = htmlspecialchars($_POST['ape2']);
            $tlf = htmlspecialchars($_POST['tlf']);
            $direccion_tipo = htmlspecialchars($_POST['direccion_tipo']);
            $direccion_via = htmlspecialchars($_POST['direccion_via']);
            $direccion_numero = htmlspecialchars($_POST['direccion_numero']);
            $direccion_otros = htmlspecialchars($_POST['direccion_otros']);
            $rol = htmlspecialchars($_POST['rol']);

            $result = $this->modelo->actualizarUsuario($nick, $nombre, $ape1, $ape2, $tlf, $direccion_tipo, $direccion_via, $direccion_numero, $direccion_otros, $rol);

            if ($result['success']) {
                echo json_encode($result); // Devolver respuesta como JSON
            } else {
                echo json_encode(['success' => false, 'message' => $result]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        }
    }

    /**
     * Edita los datos de un juego en la base de datos.
     *
     * @return void
     */
    public function editarJuego() {
        error_log("Datos recibidos: " . print_r($_POST, true)); // Depuración

        // Validar que se reciban los campos necesarios y que la acción sea "editarJuego"
        if (
            isset(
            $_POST['accion'],
            $_POST['id'],
            $_POST['titulo'],
            $_POST['desarrollador'],
            $_POST['distribuidor'],
            $_POST['anio'],
            $_POST['generos'],
            $_POST['sistemas']
        )
            && $_POST['accion'] === "editarJuego"
        ) {

            // Filtrar y procesar los datos recibidos
            $id = intval($_POST['id']);
            $titulo = htmlspecialchars($_POST['titulo']);
            $desarrollador = htmlspecialchars($_POST['desarrollador']);
            $distribuidor = htmlspecialchars($_POST['distribuidor']);
            $anio = htmlspecialchars($_POST['anio']);

            // Los géneros y sistemas se envían como cadenas JSON, así que hay que decodificarlos
            $generos = json_decode($_POST['generos'], true); // Convertir JSON a array
            $sistemas = json_decode($_POST['sistemas'], true); // Convertir JSON a array

            // Validar que los arrays decodificados sean válidos
            if (!is_array($generos) || !is_array($sistemas)) {
                echo json_encode(['success' => false, 'message' => 'Datos de géneros o sistemas inválidos']);
                return;
            }

            // Actualizar el juego en el modelo
            $result = $this->modelo->actualizarJuego($id, $titulo, $desarrollador, $distribuidor, $anio, $generos, $sistemas);

            // Responder según el resultado
            if ($result === true) {
                echo json_encode(['success' => true, 'message' => 'Juego actualizado correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar el juego', 'error' => $result]);
            }
        } else {
            // Responder si faltan campos obligatorios o si la acción no es válida
            error_log("Campos faltantes o acción inválida: " . print_r($_POST, true)); // Depuración
            echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios o acción inválida']);
        }
    }

    /**
     * Elimina un usuario de la base de datos.
     *
     * @return void
     */
    public function eliminarUsuario() {
        if (isset($_POST['nick'])) {
            $nick = htmlspecialchars($_POST['nick']);

            // Llamar al modelo para eliminar el usuario
            $result = $this->modelo->deleteUsuario($nick);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error deleting user']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        }
    }

    /**
     * Obtiene y devuelve la lista de géneros en formato JSON.
     *
     * @return void
     */
    public function listadoGeneros() {
        try {
            $result = $this->modelo->obtenerGeneros();

            echo json_encode([
                'success' => true,
                'data' => $result
            ]);
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener los géneros: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtiene y devuelve la lista de sistemas en formato JSON.
     *
     * Este método obtiene los sistemas disponibles en la base de datos y los devuelve 
     * en formato JSON. Si ocurre un error en la consulta, devuelve un mensaje de error.
     *
     * @return void
     */
    public function listadoSistemas() {
        try {
            $result = $this->modelo->obtenerSistemas();

            echo json_encode([
                'success' => true,
                'data' => $result
            ]);
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener los sistemas: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Elimina un juego de la base de datos.
     *
     * Este método recibe un ID de juego a través de una solicitud POST y lo elimina 
     * de la base de datos. Devuelve un mensaje en formato JSON indicando el éxito o 
     * fallo de la operación.
     *
     * @return void
     */
    public function eliminarJuego() {
        if (isset($_POST['id'])) {
            $id = intval($_POST['id']);

            // Llamar al método del modelo para eliminar el juego
            $result = $this->modelo->eliminarJuego($id);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Juego eliminado correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar el juego']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'ID de juego no proporcionado']);
        }
    }

    /**
     * Edita un género en la base de datos.
     *
     * Este método recibe un ID de género y un nuevo nombre a través de una solicitud POST, 
     * y actualiza el registro en la base de datos. Devuelve un mensaje JSON indicando 
     * el éxito o fallo de la operación.
     *
     * @return void
     */
    public function editarGenero()  {
        if (isset($_POST['id'], $_POST['nombre_genero'])) {
            $id = intval($_POST['id']);
            $nombre_genero = htmlspecialchars($_POST['nombre_genero']);

            $result = $this->modelo->actualizarGenero($id, $nombre_genero);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Género actualizado correctamente.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar el género.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios.']);
        }
    }

    /**
     * Elimina un género de la base de datos.
     *
     * Este método recibe un ID de género a través de una solicitud POST y lo elimina 
     * de la base de datos. Devuelve un mensaje JSON indicando el éxito o fallo de la operación.
     *
     * @return void
     */
    public function eliminarGenero()  {
        if (isset($_POST['id'])) {
            $id = intval($_POST['id']);

            $result = $this->modelo->eliminarGenero($id);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Género eliminado correctamente.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar el género.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios.']);
        }
    }

    /**
     * Edita un sistema en la base de datos.
     *
     * Este método recibe un ID de sistema y un nuevo nombre a través de una 
     * solicitud POST, y actualiza el registro en la base de datos.
     * Devuelve una respuesta JSON indicando el éxito o fallo de la operación.
     *
     * @return void
     */
    public function editarSistema()  {
        if (isset($_POST['id'], $_POST['nombre_sistema'])) {
            $id = intval($_POST['id']);
            $nombre_sistema = htmlspecialchars($_POST['nombre_sistema']);

            $result = $this->modelo->actualizarSistema($id, $nombre_sistema);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Sistema actualizado correctamente.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar el sistema.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios.']);
        }
    }

    /**
     * Elimina un sistema de la base de datos.
     *
     * Este método recibe un ID de sistema a través de una solicitud POST y lo elimina 
     * de la base de datos. Devuelve una respuesta JSON indicando el éxito o fallo de la operación.
     *
     * @return void
     */
    public function eliminarSistema() {
        if (isset($_POST['id'])) {
            $id = intval($_POST['id']);

            $result = $this->modelo->eliminarSistema($id);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Sistema eliminado correctamente.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar el sistema.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios.']);
        }
    }

    /**
     * Crea un nuevo sistema en la base de datos.
     *
     * Este método recibe el nombre de un nuevo sistema a través de una solicitud POST 
     * y lo guarda en la base de datos. Devuelve una respuesta JSON indicando el éxito 
     * o fallo de la operación.
     *
     * @return void
     */
    public function crearSistema()  {
        if (isset($_POST['nombre_sistema']) && !empty($_POST['nombre_sistema'])) {
            $nombreSistema = htmlspecialchars($_POST['nombre_sistema']);

            $result = $this->modelo->guardarSistema($nombreSistema);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Sistema creado correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al crear el sistema']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'El nombre del sistema es obligatorio']);
        }
    }

    /**
     * Crea un nuevo género en la base de datos.
     *
     * Este método recibe el nombre de un nuevo género a través de una solicitud POST 
     * y lo guarda en la base de datos. Devuelve una respuesta JSON indicando el éxito 
     * o fallo de la operación.
     *
     * @return void
     */
    public function crearGenero() {
        if (isset($_POST['nombre_genero']) && !empty($_POST['nombre_genero'])) {
            $nombreGenero = htmlspecialchars($_POST['nombre_genero']);

            $result = $this->modelo->guardarGenero($nombreGenero);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Género creado correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al crear el género']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'El nombre del género es obligatorio']);
        }
    }

    /**
     * Obtiene y devuelve la lista de roles en formato JSON.
     *
     * Este método obtiene los roles disponibles en la base de datos y los devuelve 
     * en formato JSON. Si ocurre un error en la consulta, devuelve un mensaje de error.
     *
     * @return void
     */
    public function listadoRoles() {
        try {
            $roles = $this->modelo->obtenerRoles();
            echo json_encode(['success' => true, 'data' => $roles]);
        } catch (Exception $e) {
            error_log("Error al listar roles: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al listar roles']);
        }
    }

    /**
     * Agrega un nuevo usuario a la base de datos.
     *
     * Este método recibe los datos de un nuevo usuario a través de una solicitud POST,
     * valida la información, y lo guarda en la base de datos. Devuelve una respuesta JSON 
     * indicando el éxito o fallo de la operación.
     *
     * @return void
     */
    public function agregarUsuario() {
        try {
            // Verificar que los campos obligatorios estén presentes
            if (isset($_POST['nick'], $_POST['email'], $_POST['rol'])) {
                $nick = htmlspecialchars($_POST['nick']);
                $email = htmlspecialchars($_POST['email']);
                $rol = intval($_POST['rol']);

                // Manejar campos opcionales
                $nombre = htmlspecialchars($_POST['nombre'] ?? '');
                $ape1 = htmlspecialchars($_POST['ape1'] ?? '');
                $ape2 = htmlspecialchars($_POST['ape2'] ?? '');
                $tlf = htmlspecialchars($_POST['tlf'] ?? '');
                $direccion_tipo = htmlspecialchars($_POST['direccion_tipo'] ?? '');
                $direccion_via = htmlspecialchars($_POST['direccion_via'] ?? '');
                $direccion_numero = htmlspecialchars($_POST['direccion_numero'] ?? '');
                $direccion_otros = htmlspecialchars($_POST['direccion_otros'] ?? '');

                // Verificar si el nick ya existe
                if ($this->modelo->usuarioExiste($nick)) {
                    echo json_encode(['success' => false, 'message' => 'El nick ya está en uso']);
                    return;
                }

                // Intentar agregar el usuario
                $result = $this->modelo->crearUsuario(
                    $nick,
                    $email,
                    $nombre,
                    $ape1,
                    $ape2,
                    $tlf,
                    $direccion_tipo,
                    $direccion_via,
                    $direccion_numero,
                    $direccion_otros,
                    $rol
                );

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Usuario creado correctamente']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al crear el usuario']);
                }
            } else {
                // Retornar error si faltan campos obligatorios
                echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios']);
            }
        } catch (Exception $e) {
            // Registrar el error y devolver un mensaje de error genérico
            echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }

    /**
     * Agrega un nuevo juego a la base de datos.
     *
     * Este método recibe los datos de un nuevo juego, incluyendo archivos de portada
     * y archivo del juego, los procesa y los guarda en la base de datos.
     * Devuelve una respuesta JSON indicando el éxito o fallo de la operación.
     *
     * @return void
     */
    public function agregarJuego() {
        if (isset($_POST['titulo'], $_FILES['portada'], $_FILES['archivo'])) {
            $titulo = htmlspecialchars($_POST['titulo']);
            $desarrollador = htmlspecialchars($_POST['desarrollador'] ?? '');
            $distribuidor = htmlspecialchars($_POST['distribuidor'] ?? '');
            $anio = intval($_POST['anio'] ?? 0);
            $generos = json_decode($_POST['generos'] ?? '[]', true);
            $sistemas = json_decode($_POST['sistemas'] ?? '[]', true);

            // Verificar y mover los archivos subidos
            if ($_FILES['portada']['error'] === UPLOAD_ERR_OK) {
                $portadaTmp = $_FILES['portada']['tmp_name'];
                $portadaNombre = $titulo;
                $portadaDestino = BASE_PATH . "/src/uploads/image/portadas/" . $portadaNombre . '.PNG';

                if (!move_uploaded_file($portadaTmp, $portadaDestino)) {
                    echo json_encode(['success' => false, 'message' => 'Error al guardar la portada']);
                    return;
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Error con el archivo de portada']);
                return;
            }

            if ($_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
                $archivoTmp = $_FILES['archivo']['tmp_name'];
                $archivoNombre = basename($_FILES['archivo']['name']);
                $archivoDestino = BASE_PATH . "/src/uploads/files/juegos/" . $archivoNombre;

                if (!move_uploaded_file($archivoTmp, $archivoDestino)) {
                    echo json_encode(['success' => false, 'message' => 'Error al guardar el archivo ZIP']);
                    return;
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Error con el archivo ZIP']);
                return;
            }

            // Intentar agregar el juego en la base de datos
            $result = $this->modelo->crearJuego($titulo, $desarrollador, $distribuidor, $anio, basename($portadaDestino), basename($archivoDestino), $generos, $sistemas);

            if ($result) {
                echo json_encode(['success' => true, 'message' => $result]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al crear el juego en la base de datos']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Campos obligatorios faltantes']);
        }
    }

    /**
     * Procesa los cambios en un usuario.
     *
     * Este método recibe un nombre de usuario en formato JSON y devuelve los datos del usuario
     * si existe en la base de datos. Si el usuario no existe, devuelve un mensaje de error.
     *
     * @return void
     */
    function procesarUsuario(){
        if (isset($_POST['accion']) && $_POST['accion'] === 'cargarUsuario') {
            // Decodificar el JSON recibido en el parámetro 'usuario'
            $userNick = json_decode($_POST['usuario'], true);

            // Validar que el parámetro no esté vacío
            if (empty($userNick)) {
                echo json_encode(['success' => false, 'message' => 'El usuario no fue proporcionado.']);
                return;
            }

            // Llamar al modelo para buscar el usuario
            $user = $this->modelo->buscaUsuarioPorNick($userNick);

            // Si el usuario no se encuentra, responder con un error
            if (!$user) {
                echo json_encode(['success' => false, 'message' => 'Usuario no encontrado.']);
                return;
            }

            // Roles disponibles (ejemplo, ajustar según tu contexto)
            $RolesPorId = [
                1 => 'Usuario',
                2 => 'Administrador',
                3 => 'Invitado',
            ];

            // Preparar la respuesta con los datos del usuario
            $result = [
                'nick' => htmlspecialchars($user['nick']),
                'email' => htmlspecialchars($user['email']),
                'rol' => htmlspecialchars($RolesPorId[$user['id_rol']]),
                'nombre' => htmlspecialchars($user['nombre'] ?? 'Desconocido'),
                'ape1' => htmlspecialchars($user['ape1'] ?? 'Desconocido'),
                'ape2' => htmlspecialchars($user['ape2'] ?? 'Desconocido'),
                'tlf' => htmlspecialchars($user['tlf'] ?? 'Desconocido'),
                'direccion_tipo' => htmlspecialchars($user['direccion_tipo'] ?? 'Desconocido'),
                'direccion_via' => htmlspecialchars($user['direccion_via'] ?? 'Desconocido'),
                'direccion_numero' => htmlspecialchars($user['direccion_numero'] ?? 'Desconocido'),
                'direccion_otros' => htmlspecialchars($user['direccion_otros'] ?? 'Desconocido'),
                'avatar' => htmlspecialchars($user['avatar'] ?? 'default.png'),
            ];
            // Responder con los datos del usuario
            echo json_encode(['success' => true, 'data' => $result]);
            return;
        }

        // Respuesta para solicitudes inválidas
        echo json_encode(['success' => false, 'message' => 'Solicitud inválida o falta de parámetros.']);
    }

    /**
     * Lista los juegos en la cesta de un usuario.
     *
     * Obtiene los juegos almacenados en la cesta del usuario y los devuelve en formato JSON.
     *
     * @return void
     */
    public function listarcesta(){
        $resultado = $this->modelo->cargarJuegosCestaUser($_POST['nick']);
        echo json_encode(['success' => true, 'message' => $resultado]);
    }

    /**
     * Elimina un juego de la cesta de un usuario.
     *
     * Recibe el ID del juego y el nick del usuario a través de una solicitud POST,
     * y lo elimina de la cesta. Devuelve una respuesta JSON indicando el éxito o fallo de la operación.
     *
     * @return void
     */
    public function eliminarjuegocesta(){
        $id_juego = $_POST['id_juego'];
        $nick = $_POST['nick'];

        $resultado = $this->modelo->eliminarJuegoCesta($id_juego, $nick);

        if ($resultado) {
            echo json_encode(['success' => true, 'message' => $resultado]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar el juego de la cesta']);
        }
    }

    /**
     * Procesa el pago de los juegos en la cesta del usuario.
     *
     * Calcula el precio total de los juegos en la cesta del usuario y almacena la información 
     * en cookies para el proceso de pago. Luego, muestra el formulario de compra.
     *
     * @return void
     */
    public function pagar() {

        // obtener nick
        $nick = $_SESSION['user_nick'];

        // obtener los juegos en su cesta
        $juegos = $this->modelo->cargarJuegosCestaUser($nick);

        // procesar precios y sumarlos
        $precioTotal = 0;
        foreach ($juegos as $juego) {
            $precioTotal += $juego['precio'];
        }

        // guardarlo en cookies para el proceso de pago
        setcookie('purchase_total', $precioTotal, time() + 1800, "/"); // Cookie de compra
        setcookie('purchase_user', $nick, time() + 1800, "/"); // Cookie de usuario
        
        Vista::MuestraFormularioCompra();
    }

    /**
     * Vacía el carrito de compras del usuario.
     *
     * Elimina todos los juegos del carrito de un usuario autenticado. 
     * Si ocurre un error, se captura y se muestra un mensaje.
     *
     * @return void
     */
    public function vaciarCarrito() {
        try {
            if (!$this->modelo->vaciarCarrito($_SESSION['user_nick'])) {
                throw new Exception("Error: No se pudo vaciar el carrito.");
            }
            Vista::MuestraCarrito();
        } catch (Exception $e) {
            // Captura cualquier error y lo muestra
            echo $e->getMessage();
        }
    }

    /**
     * Procesa las acciones relacionadas con el carrito de compras.
     *
     * Este método maneja las solicitudes de pago o de vaciar el carrito de compras,
     * llamando a los métodos correspondientes.
     *
     * @return void
     */
    public function procesarCarro(){
        if (isset($_POST['pagar'])) {
            $this->pagar();
        } else if (isset($_POST['vaciarCarrito'])) {
            $this->vaciarCarrito();
        }
    }

    /**
     * Importa juegos desde un archivo JSON.
     *
     * Este método recibe un JSON con información de juegos, lo procesa y almacena
     * en la base de datos. Devuelve una respuesta JSON indicando cuántos juegos se importaron
     * y si hubo errores.
     *
     * @return void
     */
    public function importarJuegoJSON() {
        if (!isset($_POST['jsonData'])) {
            echo json_encode(['success' => false, 'message' => 'No se ha recibido el JSON']);
            return;
        }
    
        // Convertir JSON a array
        $jsonData = json_decode($_POST['jsonData'], true);
    
        if (!$jsonData || !is_array($jsonData)) {
            echo json_encode(['success' => false, 'message' => 'Error al decodificar el JSON o formato incorrecto']);
            return;
        }
    
        $resultados = [
            'importados' => 0,
            'errores' => []
        ];
    
        foreach ($jsonData as $juego) {
            // Extraer datos del JSON
            $titulo = $juego['titulo'] ?? null;
            $desarrollador = $juego['desarrollador'] ?? null;
            $distribuidor = $juego['distribuidor'] ?? null;
            $anio = $juego['año'] ?? null;
            $urlPortada = $juego['portada'] ?? null;
            $urlJuego = $juego['ruta'] ?? null;
            //$generos = $juego['generos'] ?? [];
            //$sistemas = $juego['sistemas'] ?? [];
    
            // Llamar al modelo para guardar el juego en la base de datos
            $resultado = $this->modelo->crearJuego($titulo, $desarrollador, $distribuidor, $anio, $urlPortada, $urlJuego, null, null);
    
            if ($resultado === true) {
                $resultados['importados']++;
            } else {
                $resultados['errores'][] = "Error al guardar '$titulo': " . $resultado;
            }
        }
    
        echo json_encode([
            'success' => true,
            'message' => "Se importaron {$resultados['importados']} juegos.",
            'errores' => $resultados['errores']
        ]);
    } 

    /**
     * Edita los datos de un usuario o su avatar.
     *
     * Dependiendo del tipo de formulario enviado, actualiza los datos personales
     * o la imagen de perfil del usuario en la base de datos.
     *
     * @return void
     */
    public function EditarDatosUsuario(){
        if ($_POST['id_A'] == 'userFormDatos') {
            // Gestionamos los datos del usuario

            $nick = $_POST['nick_user'] ?? '';

            $nombre = $_POST['user_nombre'] ?? '';
            $ape1 = $_POST['user_ape1'] ?? '';
            $ape2 = $_POST['user_ape2'] ?? '';
            $tlf = $_POST['user_tlf'] ?? '';
            $direccion_tipo = $_POST['user_direccion_tipo'] ?? '';
            $direccion_via = $_POST['user_direccion_via'] ?? '';
            $direccion_numero = $_POST['user_direccion_numero'] ?? '';
            $direccion_otros = $_POST['user_direccion_otros'] ?? '';
            
            $result = $this->modelo->actualizarDatosUsuario($nick, $nombre, $ape1, $ape2, $tlf, 
                                                            $direccion_tipo, $direccion_via, $direccion_numero, $direccion_otros);
            if ($result['success']) {
                //echo json_encode(['success' => true, 'message' => 'Actualizado correctamente!']);
                Vista::MuestraPerfilUsuario();
            } else {
                echo json_encode(['success' => false, 'message' => $result['message']]);
            }
        } else if ($_POST['id_A'] == 'userFormImg' ) {
            //PASO 1: Capturamos la información de la imagen
            $imagen = $_FILES['user_avatar'] ?? null;

            // PASO 2: Obtenemos la extension del archivo
            $extension = pathinfo($imagen['name'], PATHINFO_EXTENSION);

            // PASO 3: Generamos el nombre con el nick
            $nick = $_POST['nick_user'] ?? '';
            $nombreImagen = $nick . '.' . $extension;

            // PASO 4: Indicamos la ruta de destino
            $destination = BASE_PATH ."\\src\\uploads\\image\\avatar\\" . $nombreImagen;
            
            //PASO 5: Movemos la imagen a la carpeta de avatares
            if (move_uploaded_file($imagen['tmp_name'], $destination)) {
                //PASO 6: Actualizamos la base de datos
                $result = $this->modelo->actualizarAvatarUsuario($nick, $nombreImagen);
                
                if ($result['success']) {
                    //echo json_encode(['success' => true, 'message' => 'Actualizado correctamente!']);
                    Vista::MuestraPerfilUsuario();
                } else {
                    echo json_encode(['success' => false, 'message' => $result['message']]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al subir la imagen']);
            }
        }
    }

    /**
     * Permite regalar un juego a otro usuario.
     *
     * Este método recibe los datos del destinatario y del juego a través de una
     * solicitud POST. Si el usuario existe, se guarda la información en cookies
     * y se muestra el formulario de compra.
     *
     * @return void
     */
    public function regalarJuego() {
        // Verificar que los datos requeridos existen en la solicitud
        if (!isset($_POST['gift_user']) || !isset($_POST['game_id'])) {
            echo json_encode(['success' => false, 'message' => 'Faltan datos en la solicitud']);
            return;
        }
    
        // Recoger datos del formulario
        $destinatarioNick = htmlspecialchars($_POST['gift_user']);
        $gameId = htmlspecialchars($_POST['game_id']);
    
        // Comprobar si el usuario destinatario existe
        if (!$this->modelo->usuarioExiste($destinatarioNick)) {
            echo json_encode(['success' => false, 'message' => 'El usuario destinatario no existe']);
            return;
        }
    
        // Guardar los datos en cookies (expiran en 30 minutos)
        setcookie('gift_user', $destinatarioNick, time() + 1800, "/"); // 1800 segundos = 30 minutos
        setcookie('gift_game', $gameId, time() + 1800, "/");
    
        // Mostrar la vista de compra
        Vista::MuestraFormularioCompra();
        return;
    }

    /**
     * Obtiene y devuelve la lista de juegos de la biblioteca de un usuario.
     *
     * Este método recibe el nombre de usuario a través de una solicitud POST y 
     * obtiene la lista de juegos que posee en su biblioteca. Devuelve una respuesta 
     * JSON con los datos o un mensaje de error si no se encuentran juegos.
     *
     * @return void
     */
    public function listadoBiblioteca() {
        if (!isset($_POST['nick'])) {
            echo json_encode(['success' => false, 'message' => 'Faltan datos en la solicitud']);
            return;
        }
    
        $nick = htmlspecialchars($_POST['nick']); // Sanitizar la entrada del usuario
        $result = $this->modelo->obtenerJuegosUsuario($nick);
    
        // Base URL de las imágenes
        $baseUrl = 'https://localhost/Games-r-us/src/uploads/image/portadas/';
    
        // Verificar si hay juegos y agregar la ruta completa de la imagen
        if (!empty($result)) {
            foreach ($result as &$game) {
                if (!empty($game['ruta_imagen'])) {
                    $game['ruta_imagen'] = $baseUrl . $game['ruta_imagen']; 
                }
            }
            unset($game); // Evita posibles referencias accidentales en el foreach
    
            echo json_encode(['success' => true, 'data' => $result]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontraron juegos en la biblioteca del usuario']);
        }
    }

    /**
     * Procesa el pago de los juegos en la cesta del usuario.
     *
     * Este método recibe los datos del usuario y su información de pago a través 
     * de una solicitud POST, actualiza su información en la base de datos y 
     * agrega los juegos comprados a su biblioteca. Luego, vacía la cesta del usuario.
     *
     * @return void
     */
    public function procesarPagoUser() {

        // Obtener los datos del formulario
        $nick = $_POST['nick'] ?? null;
        $name = $_POST['name'] ?? null;
        $ap1 = $_POST['ap1'] ?? null;
        $ap2 = $_POST['ap2'] ?? null;
        $tlf = $_POST['tlf'] ?? null;
    
        // Datos de facturación
        $direccion_tipo = $_POST['direccion_tipo'] ?? null;
        $direccion_via = $_POST['direccion_via'] ?? null;
        $direccion_numero = $_POST['direccion_numero'] ?? null;
        $direccion_otros = $_POST['direccion_otros'] ?? null;
    
        // Datos de la tarjeta
        $numero_tarjeta = $_POST['input-number'] ?? null;
        $exp_mes = $_POST['input-month'] ?? null;
        $exp_anio = $_POST['input-year'] ?? null;
    
        if (!$nick || !$name || !$ap1 || !$tlf || !$numero_tarjeta || !$exp_mes || !$exp_anio ) {
            echo json_encode(["success" => false, "message" => "Faltan datos obligatorios"]);
            return;
        }
    
        try {
            // Actualizar o insertar datos del usuario en el modelo
            $this->modelo->actualizarDatosUsuario($nick, $name, $ap1, $ap2, $tlf, 
                                                  $direccion_tipo, $direccion_via, $direccion_numero, $direccion_otros);
    
            // Guardar la tarjeta en la tabla Usuario_tarjeta
            $this->modelo->guardarTarjetaUsuario($nick, $numero_tarjeta, $exp_mes, $exp_anio);
    
            // Obtener los juegos en la cesta del usuario
            $juegosCesta = $this->modelo->cargarJuegosCestaUser($nick);

            // Agregamos los juegos a la biblioteca del usuario
            if (!empty($juegosCesta)) {
                // Agregar los juegos a la biblioteca del usuario
                foreach ($juegosCesta as $juego) {
                    $this->modelo->agregarJuegoBiblioteca($nick, $juego['id_juego']);
                }

                // Vaciar la cesta después de la compra
                $this->modelo->vaciarCarrito($nick);
            }
    
            echo json_encode(["success" => true, "message" => "Pago procesado con éxito y juegos añadidos a la biblioteca"]);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
        }
    }

    /**
     * Procesa el pago de un regalo y lo envía a otro usuario.
     *
     * Este método recibe los datos del usuario, su información de pago y los 
     * datos del destinatario a través de una solicitud POST. Luego, actualiza
     * la información del usuario y transfiere el juego al destinatario si no 
     * lo posee ya en su biblioteca.
     *
     * @return void
     */
    public function procesarRegaloUser() {

        // Obtener los datos del formulario
        $nick = $_POST['nick'] ?? null;
        $name = $_POST['name'] ?? null;
        $ap1 = $_POST['ap1'] ?? null;
        $ap2 = $_POST['ap2'] ?? null;
        $tlf = $_POST['tlf'] ?? null;
    
        // Datos de facturación
        $direccion_tipo = $_POST['direccion_tipo'] ?? null;
        $direccion_via = $_POST['direccion_via'] ?? null;
        $direccion_numero = $_POST['direccion_numero'] ?? null;
        $direccion_otros = $_POST['direccion_otros'] ?? null;
    
        // Datos de la tarjeta
        $numero_tarjeta = $_POST['input-number'] ?? null;
        $exp_mes = $_POST['input-month'] ?? null;
        $exp_anio = $_POST['input-year'] ?? null;

        //Datos de Regalo
        $destinatario = $_POST['gift_user'] ?? null;
        $juegoRegalo = $_POST['gift_game'] ?? null;
    
        if (!$nick || !$name || !$ap1 || !$tlf || !$numero_tarjeta || !$exp_mes || !$exp_anio ) {
            echo json_encode(["success" => false, "message" => "Faltan datos obligatorios"]);
            return;
        }
    
        try {
            // Actualizar o insertar datos del usuario en el modelo
            $this->modelo->actualizarDatosUsuario($nick, $name, $ap1, $ap2, $tlf, 
                                                  $direccion_tipo, $direccion_via, $direccion_numero, $direccion_otros);
    
            // Guardar la tarjeta en la tabla Usuario_tarjeta
            $this->modelo->guardarTarjetaUsuario($nick, $numero_tarjeta, $exp_mes, $exp_anio);
    
            // Buscar que no tenga el juego el usuario al que lo quere regalar
            $juegosUsuario = $this->modelo->obtenerJuegosUsuario($destinatario);
            foreach ($juegosUsuario as $juego) {
                if ($juego['id_juego'] == $juegoRegalo) {
                    echo json_encode(["success" => false, "message" => "El usuario ya tiene el juego que intentas regalar"]);
                    return;
                }
            }

            // Agregar el juego a la biblioteca del usuario
            $this->modelo->agregarJuegoBiblioteca($destinatario, $juegoRegalo);
    
            echo json_encode(["success" => true, "message" => "Pago procesado con éxito y juegos Regalados"]);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
        }
    }
    
}

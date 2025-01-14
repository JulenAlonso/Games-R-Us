<?php

use Dotenv\Parser\Value;

if (!defined('BASE_PATH')) {
    define('BASE_PATH', realpath(__DIR__ . '/../..')); // Define BASE_PATH si no está definida
}

require_once BASE_PATH . '/src/vistas/vista.php';
require_once BASE_PATH . '/src/modelos/modelo.php';

class Controlador
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new Modelo();
        session_start(); // Asegura que las sesiones estén habilitadas en cada solicitud
    }

    //FUNCIONALIDAD DE TIENDA
    public function Inicia()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { //Primer filtrado que hacemos
            if (isset($_POST['accion']) && $_POST['accion'] === 'listadoJuegos') {
                $this->listadoJuegos();
            } else {
                $this->procesaNav();
                $this->procesaLogin();
                $this->procesaRegister();
                $this->procesarTienda();
            }
        } else {
            Vista::MuestraLanding(); // Carga la vista por defecto
        }
    }

    private function procesaNav()
    {
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
        }
    }

    // Esto ejecuta TODO el login.php
    private function procesaLogin()
    {
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

    //Gestiona la pagina de  registro
    private function procesaRegister()
    {
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

    private function usuarioAutenticado()
    {
        // Devuelve verdadero si hay una sesión activa
        return isset($_SESSION['user_nick']);
    }

    public function listadoLanding()
    {

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
                $baseUrl = 'https://localhost/Games-r-us/src/uploads/image/';

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

    public function listadoJuegos()
    {

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
                $baseUrl = 'https://localhost/Games-r-us/src/uploads/image/';
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

    //Esto direccionaria las peticiones de "COMPRAR" y "REGALAR"
    public function procesarTienda()
    {
        if (isset($_POST['tienda_comprar'])) { //POST
            //echo $_POST['compra_gameid'];
            Vista::MuestraFormularioCompra(); //Aqui habria que poner la logica de "COMPRA"
        } else if (isset($_POST['tienda_regalar'])) {
            Vista::MuestraFormularioRegalo(); //Aqui habria que poner la logica de "REGALAR"
        }
    }

    public function admn_listarUsers()
    {
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
                    'avatar' => htmlspecialchars($user['avatar'] ?? 'Desconocido'),
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

    public function agregarUsuario()
    {
        try {
            // Verificar si los campos necesarios están presentes en el POST
            if (isset($_POST['nick'], $_POST['nombre'], $_POST['pass'], $_POST['ape1'], $_POST['ape2'], $_POST['tlf'], $_POST['email'], $_POST['rol'])) {

                // Filtrar y validar los datos (seguridad)
                $nick = htmlspecialchars($_POST['nick']);
                $nombre = htmlspecialchars($_POST['nombre']);
                $pass = $_POST['pass']; // El password debe ser manejado con precaución
                $ape1 = htmlspecialchars($_POST['ape1']);
                $ape2 = htmlspecialchars($_POST['ape2']);
                $tlf = htmlspecialchars($_POST['tlf']);
                $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
                $rol = intval($_POST['rol']); // Aseguramos que el rol sea un número entero

                // Validar que el email sea correcto
                if (!$email) {
                    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
                    return;
                }

                // Verificar si el usuario ya existe en la base de datos
                $usuarioExistente = $this->modelo->buscaUsuarioPorNick($nick);
                if ($usuarioExistente) {
                    echo json_encode(['success' => false, 'message' => 'Username already exists']);
                    return;
                }

                // Si el usuario no existe, proceder a insertar
                $result = $this->modelo->insertarUsuario($nick, $nombre, $pass, $ape1, $ape2, $tlf, $email, $rol, $avatar);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'User added successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error adding user']);
                }

            } else {
                echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            }
        } catch (PDOException $e) {
            // Log error and return response
            error_log("Error inserting user: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    public function agregarJuego()
    {
        try {
            if (isset($_POST['titulo'], $_POST['desarrollador'], $_POST['distribuidor'], $_POST['anio'], $_POST['genero'], $_POST['sistema'])) {
                // Filtrar y validar los datos
                $titulo = htmlspecialchars($_POST['titulo']);
                $desarrollador = htmlspecialchars($_POST['desarrollador']);
                $distribuidor = htmlspecialchars($_POST['distribuidor']);
                $anio = htmlspecialchars($_POST['anio']);
                $genero = explode(',', $_POST['genero']); // Convertir a array
                $sistema = explode(',', $_POST['sistema']); // Convertir a array

                // Verificar y mover los archivos subidos
                if (isset($_FILES['coverImage']) && $_FILES['coverImage']['error'] === UPLOAD_ERR_OK) {
                    $coverImage = $_FILES['coverImage'];
                    $coverImageName = $coverImage['name'];
                    $coverImageTmpName = $coverImage['tmp_name'];
                    $coverImageType = $coverImage['type'];

                    // Asegúrate de que el archivo sea una imagen
                    if (in_array($coverImageType, ['image/jpeg', 'image/png', 'image/gif'])) {
                        $coverImagePath = BASE_PATH . '/src/uploads/image/' . $coverImageName;
                        move_uploaded_file($coverImageTmpName, $coverImagePath); // Mover la imagen al servidor
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Invalid cover image format']);
                        return;
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Cover image is required']);
                    return;
                }

                if (isset($_FILES['gameZip']) && $_FILES['gameZip']['error'] === UPLOAD_ERR_OK) {
                    $gameZip = $_FILES['gameZip'];
                    $gameZipName = $gameZip['name'];
                    $gameZipTmpName = $gameZip['tmp_name'];

                    // Asegúrate de que el archivo sea un zip
                    $gameZipPath = BASE_PATH . '/src/uploads/files/' . $gameZipName;
                    move_uploaded_file($gameZipTmpName, $gameZipPath); // Mover el archivo zip al servidor

                } else {
                    echo json_encode(['success' => false, 'message' => 'Game ZIP is required']);
                    return;
                }

                // Llamar al modelo para agregar el juego
                $result = $this->modelo->insertarJuego($titulo, $desarrollador, $distribuidor, $anio, $genero, $sistema, $coverImagePath, $gameZipPath);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => $result]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error adding game']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    public function editarUsuario()
    {
        // Verificar que los campos necesarios estén presentes en el POST
        if (isset($_POST['nick'], $_POST['nombre'], $_POST['ape1'], $_POST['ape2'], $_POST['tlf'], $_POST['email'])) {
            // Filtrar y validar los datos (seguridad)
            $nick = htmlspecialchars($_POST['nick']);
            $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);  // Validar el email
            $nombre = htmlspecialchars($_POST['nombre']);
            $ape1 = htmlspecialchars($_POST['ape1']);
            $ape2 = htmlspecialchars($_POST['ape2']);
            $tlf = htmlspecialchars($_POST['tlf']);

            // Validar que el email sea correcto
            if (!$email) {
                echo json_encode(['success' => false, 'message' => 'Invalid email format']);
                return;
            }

            // Validar que los otros campos no estén vacíos
            if (empty($nick) || empty($nombre) || empty($ape1) || empty($ape2) || empty($tlf)) {
                echo json_encode(['success' => false, 'message' => 'All fields must be filled']);
                return;
            }

            // Llamar al modelo para actualizar el usuario
            $result = $this->modelo->actualizarUsuario($nick, $nombre, $ape1, $ape2, $tlf);

            // Verificar el resultado y responder
            if ($result) {
                //echo json_encode(['success' => true, 'message' => 'User updated successfully']);
                echo json_encode(['success' => false, 'message' => $result]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error updating user']);
            }
        } else {
            // Si faltan datos en el POST
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        }
    }
    public function editarJuego()
    {
        if (isset($_POST['id'], $_POST['titulo'], $_POST['desarrollador'], $_POST['distribuidor'], $_POST['anio'], $_POST['genero'], $_POST['sistema'])) {
            // Filtrar y validar los datos
            $id = intval($_POST['id']);
            $titulo = htmlspecialchars($_POST['titulo']);
            $desarrollador = htmlspecialchars($_POST['desarrollador']);
            $distribuidor = htmlspecialchars($_POST['distribuidor']);
            $anio = htmlspecialchars($_POST['anio']);
            $genero = explode(',', $_POST['genero']); // Convertir a array
            $sistema = explode(',', $_POST['sistema']); // Convertir a array

            // Llamar al modelo para actualizar el juego
            $result = $this->modelo->actualizarJuego($id, $titulo, $desarrollador, $distribuidor, $anio, $genero, $sistema);

            if ($result) {
                //echo json_encode(['success' => true, 'message' => 'User updated successfully']);
                echo json_encode(['success' => true, 'message' => $result]);
            } else {
                echo json_encode(['success' => false, 'message' => $result]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        }
    }
    public function eliminarUsuario()
    {
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

    public function eliminarJuego()
    {
        if (isset($_POST['id'])) {
            $id = intval($_POST['id']); // Asegúrate de convertir el ID a entero

            // // Verificar que el juego exista antes de eliminarlo
            // $gameExists = $this->modelo->buscarJuegoPorId($id);
            // if (!$gameExists) {
            //     echo json_encode(['success' => false, 'message' => 'Game not found']);
            //     return;
            // }

            // Llamar al modelo para eliminar el juego
            $result = $this->modelo->deleteJuego($id);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Game deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error deleting game']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        }
    }

    function procesarUsuario()
    {
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
                1 => 'Administrador',
                2 => 'Usuario',
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
    private function usuarioEsAdmin()
    {
        // Verifica si el usuario tiene rol de administrador
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 2;
    }
}
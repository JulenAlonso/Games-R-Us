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
            $nick = $_POST['nick'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($password)) {
                echo "La contraseña no puede estar vacía.";
                return;
            }

            // Busca al usuario en el modelo
            $user = $this->modelo->buscaUsuarioPorNick($nick);
            $role = $this->modelo->buscarRolePorNick($nick);

            if ($user) {
                // Verifica la contraseña
                if (password_verify($password, $user['password'])) {    //Passwd que nosotros creamos y la cifrada.
                    // Autenticación exitosa, guarda los datos en la sesión
                    $_SESSION['user_nick'] = $user['nick']; //Guardamos el id del usuario
                    $_SESSION['user_role'] = $role['id_rol'];   //Guardamos el rol del usuario
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['nombre'] = $user['nombre'];
                    $_SESSION['ape1'] = $user['ape1'];
                    $_SESSION['ape2'] = $user['ape2'];
                    $_SESSION['user_image'] = $user['imagen'];
                    $_SESSION['user_direccion_tipo'] = $user['direccion_tipo'];
                    $_SESSION['user_direccion_via'] = $user['direccion_via'];
                    $_SESSION['user_direccion_numero'] = $user['direccion_numero'];
                    $_SESSION['user_direccion_ciudad'] = $user['direccion_ciudad'];
                    $_SESSION['user_direccion_provincia'] = $user['direccion_provincia'];
                    $_SESSION['user_direccion_cp'] = $user['direccion_cp'];
                    $_SESSION['user_direccion_pais'] = $user['direccion_pais'];
                    Vista::MuestraBiblioteca(); //Cuando iniciamos sesion, nos manda directamente a la biblioteca
                    exit;
                } else {
                    //Si la parte de arriba no cumple, error de passwd
                    echo "Contraseña incorrecta.";
                }
            } else {
                //Si la parte de arriba no cumple y error de passwd: usuario no encontrado 
                echo "Usuario no encontrado.";
                echo $nick;
            }
        } elseif (isset($_POST['RegisterButtonBut'])) {
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
                    'direccion' => htmlspecialchars(trim(preg_replace('/\s+/', ' ', sprintf(
                        '%s %s %s %s %s',
                        $user['direccion'] ?? 'Desconocida',
                        $user['direccion_tipo'],
                        $user['direccion_via'],
                        ($user['direccion_numero'] === '0' ? null : $user['direccion_numero']),
                        $user['direccion_otros']
                    )))),
                    'rol' => htmlspecialchars($RolesPorId[$user['id_rol']]),
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

    private function procesaPerfilUsuario()
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
            $this->MuestraPerfilUsuario();
        } elseif (isset($_POST['nav_AdminButton'])) {
            if ($this->usuarioEsAdmin()) {
                Vista::MuestraAdmin();
            } else {
                // Vista::MuestraErrorPermisos(); // Mostrar un error si no es admin
            }
        }
    }
    private function MuestraPerfilUsuario()
    {
        // Asegurarse de que las variables de sesión necesarias estén configuradas
        $_SESSION['user_email'] = $_SESSION['user_email'] ?? 'admin@example.com';
        $_SESSION['user_nick'] = $_SESSION['user_nick'] ?? 'Admin';
        $_SESSION['user_role'] = $_SESSION['user_role'] ?? 2; // 2 = Administrador
        // Muestra el perfil del usuario
        Vista::MuestraPerfilUsuario();
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
                $result = $this->modelo->insertarUsuario($nick, $nombre, $pass, $ape1, $ape2, $tlf, $email, $rol);

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


    // private function usuarioAutenticado()
    // {
    //     // Verifica si hay una sesión activa del usuario
    //     return isset($_SESSION['user_email']);
    // }

    private function usuarioEsAdmin()
    {
        // Verifica si el usuario tiene rol de administrador
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 2;
    }
}
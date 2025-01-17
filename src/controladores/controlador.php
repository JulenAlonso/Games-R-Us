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

    // public function agregarJuego()
    // {
    //     try {
    //         if (isset($_POST['titulo'], $_POST['desarrollador'], $_POST['distribuidor'], $_POST['anio'], $_POST['genero'], $_POST['sistema'])) {
    //             // Filtrar y validar los datos
    //             $titulo = htmlspecialchars($_POST['titulo']);
    //             $desarrollador = htmlspecialchars($_POST['desarrollador']);
    //             $distribuidor = htmlspecialchars($_POST['distribuidor']);
    //             $anio = htmlspecialchars($_POST['anio']);
    //             $genero = explode(',', $_POST['genero']); // Convertir a array
    //             $sistema = explode(',', $_POST['sistema']); // Convertir a array

    //             // Verificar y mover los archivos subidos
    //             if (isset($_FILES['coverImage']) && $_FILES['coverImage']['error'] === UPLOAD_ERR_OK) {
    //                 $coverImage = $_FILES['coverImage'];
    //                 $coverImageName = $coverImage['name'];
    //                 $coverImageTmpName = $coverImage['tmp_name'];
    //                 $coverImageType = $coverImage['type'];

    //                 // Asegúrate de que el archivo sea una imagen
    //                 if (in_array($coverImageType, ['image/jpeg', 'image/png', 'image/gif'])) {
    //                     $coverImagePath = BASE_PATH . '/src/uploads/image/' . $coverImageName;
    //                     move_uploaded_file($coverImageTmpName, $coverImagePath); // Mover la imagen al servidor
    //                 } else {
    //                     echo json_encode(['success' => false, 'message' => 'Invalid cover image format']);
    //                     return;
    //                 }
    //             } else {
    //                 echo json_encode(['success' => false, 'message' => 'Cover image is required']);
    //                 return;
    //             }

    //             if (isset($_FILES['gameZip']) && $_FILES['gameZip']['error'] === UPLOAD_ERR_OK) {
    //                 $gameZip = $_FILES['gameZip'];
    //                 $gameZipName = $gameZip['name'];
    //                 $gameZipTmpName = $gameZip['tmp_name'];

    //                 // Asegúrate de que el archivo sea un zip
    //                 $gameZipPath = BASE_PATH . '/src/uploads/files/' . $gameZipName;
    //                 move_uploaded_file($gameZipTmpName, $gameZipPath); // Mover el archivo zip al servidor

    //             } else {
    //                 echo json_encode(['success' => false, 'message' => 'Game ZIP is required']);
    //                 return;
    //             }

    //             // Llamar al modelo para agregar el juego
    //             $result = $this->modelo->insertarJuego($titulo, $desarrollador, $distribuidor, $anio, $genero, $sistema, $coverImagePath, $gameZipPath);

    //             if ($result) {
    //                 echo json_encode(['success' => true, 'message' => $result]);
    //             } else {
    //                 echo json_encode(['success' => false, 'message' => 'Error adding game']);
    //             }
    //         } else {
    //             echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    //         }
    //     } catch (Exception $e) {
    //         echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    //     }
    // }

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

    public function editarJuego() {
        error_log("Datos recibidos: " . print_r($_POST, true)); // Depuración
    
        // Validar que se reciban los campos necesarios y que la acción sea "editarJuego"
        if (isset($_POST['accion'], $_POST['id'], $_POST['titulo'], $_POST['desarrollador'], 
                  $_POST['distribuidor'], $_POST['anio'], $_POST['generos'], $_POST['sistemas']) 
            && $_POST['accion'] === "editarJuego") {
            
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

    public function listadoGeneros() {
        try {
            $result= $this->modelo->obtenerGeneros();
    
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
    
    public function editarGenero() {
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

    public function eliminarGenero() {
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

    public function editarSistema() {
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

    public function crearSistema() {
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
    
    public function listadoRoles() {
        try {
            $roles = $this->modelo->obtenerRoles();
            echo json_encode(['success' => true, 'data' => $roles]);
        } catch (Exception $e) {
            error_log("Error al listar roles: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al listar roles']);
        }
    }
    
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
                    $nick, $email, $nombre, $ape1, $ape2, $tlf, $direccion_tipo, $direccion_via, $direccion_numero, $direccion_otros, $rol
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

    function cambiarAvatar()
    {
        
            // if (isset($_POST['accion']) && $_POST['accion'] === 'submitProfileForm') {
        echo "LUIS";
        print_r($_FILES);
        /*
            //Recogemos el archivo enviado por el formulario
            $archivo = $_FILES['archivo']['name'];
            //Si el archivo contiene algo y es diferente de vacio
            if (isset($archivo) && $archivo != "") {
                //Obtenemos algunos datos necesarios sobre el archivo
                $tipo = $_FILES['archivo']['type'];
                $tamano = $_FILES['archivo']['size'];
                $temp = $_FILES['archivo']['tmp_name'];
                

    

                print_r($_FILES);
                //Se comprueba si el archivo a cargar es correcto observando su extensión y tamaño
                if (!((strpos($tipo, "gif") || strpos($tipo, "jpeg") || strpos($tipo, "jpg") || strpos($tipo, "png")) && ($tamano < 2000000))) {
                    echo '<div><b>Error. La extensión o el tamaño de los archivos no es correcta.<br/>
        - Se permiten archivos .gif, .jpg, .png. y de 200 kb como máximo.</b></div>';
                } else {
                    //Si la imagen es correcta en tamaño y tipo
                    //Se intenta subir al servidor
                    if (move_uploaded_file($temp, 'avatar/' . $archivo)) {
                        //Cambiamos los permisos del archivo a 777 para poder modificarlo posteriormente
                        chmod('images/' . $archivo, 0777);
                        //Mostramos el mensaje de que se ha subido co éxito
                        echo '<div><b>Se ha subido correctamente la imagen.</b></div>';
                        //Mostramos la imagen subida
                        echo '<p><img src="images/' . $archivo . '"></p>';
                    } else {
                        //Si no se ha podido subir la imagen, mostramos un mensaje de error
                        echo '<div><b>Ocurrió algún error al subir el fichero. No pudo guardarse.</b></div>';
                    }
                }
            }


print_r($_FILES);
*/
            
        }











    

    private function usuarioEsAdmin()
    {
        // Verifica si el usuario tiene rol de administrador
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 2;
    }
}
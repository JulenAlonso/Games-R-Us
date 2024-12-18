<?php

use Dotenv\Parser\Value;

if (!defined('BASE_PATH')) {
    define('BASE_PATH', realpath(__DIR__ . '/../..')); // Define BASE_PATH si no está definida
}

require_once BASE_PATH . '/src/vistas/vista.php';
require_once BASE_PATH . '/src/modelos/modelo.php';

class Controlador {
    private $modelo;

    public function __construct() {
        $this->modelo = new Modelo();
        session_start(); // Asegura que las sesiones estén habilitadas en cada solicitud
    }

    //FUNCIONALIDAD DE TIENDA
    public function Inicia() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { //Primer filtrado que hacemos
            if (isset($_POST['accion']) && $_POST['accion'] === 'listadoJuegos') {
                $this->listadoJuegos();
            } else {
                $this->procesaNav();
                $this->procesaLogin();
                $this->procesaRegister();
                $this->procesaAgregarJuego();
                $this->procesarTienda();
            }
        } else {
            Vista::MuestraLanding(); // Carga la vista por defecto
        }
    }
    

    private function procesaNav() {
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
        }
    }

    // REV //
    // Esto ejecuta TODO el login.php
    private function procesaLogin() {
        if (isset($_POST['loginButtonBut'])) {
            $nick = $_POST['nick'] ?? '';
            $password = $_POST['password'] ?? '';

            // // Valida los datos
            // if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            //     echo "Formato de email inválido.";
            //     return;
            // }

            if (empty($password)) {
                echo "La contraseña no puede estar vacía.";
                return;
            }

           
            // Busca al usuario en el modelo
            $user = $this->modelo->buscaUsuarioPorNick($nick);

            if ($user) {
                // Verifica la contraseña
                if (password_verify($password, $user['password'])) {    //Passwd que nosotros creamos y la cifrada.
                    // Autenticación exitosa, guarda los datos en la sesión
                    $_SESSION['user_nick'] = $user['nick']; //Guardamos el id del usuario
                    $_SESSION['user_email'] = $user['email'];   //Guardamos el email del usuario
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

    // REV //
    //Gestiona la pagina de  registro
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
            echo "Registro exitoso. Ahora puedes iniciar sesión.";
        }
    }

    private function procesaAgregarJuego() {
        if (isset($_POST['agregarJuegoButton'])) {
            $titulo = $_POST['titulo'] ?? '';
            $titulo2 = $_POST['titulo2'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $id_categoria = $_POST['categoria'] ?? '';
            $precio = $_POST['precio'] ?? '';
            $imagen = $_FILES['imagen'] ?? null;
            $rutaZip = $_FILES['ruta'] ?? null;
    
            // Validación básica
            if (empty($titulo) || empty($descripcion) || empty($id_categoria) || empty($precio) || !$imagen || !$rutaZip) {
                echo "Todos los campos son obligatorios.";
                return;
            }
    
            // Subir imagen
            $imagenNombre = $this->subeArchivo($imagen, 'image/');
            if (!$imagenNombre) {
                echo "Error al subir la imagen.";
                return;
            }
    
            // Subir ZIP
            $zipNombre = $this->subeArchivo($rutaZip, 'files/');
            if (!$zipNombre) {
                echo "Error al subir el archivo ZIP.";
                return;
            }
    
            // Guardar juego en la base de datos
            $resultado = $this->modelo->agregarJuego($titulo, $titulo2, $descripcion, $id_categoria, $precio, $imagenNombre, $zipNombre);
    
            if ($resultado) {
                echo "Juego agregado exitosamente.";
            } else {
                echo "Error al agregar el juego.";
            }
        }
    }
    
    private function subeArchivo($archivo, $directorioDestino) {
        $nombreArchivo = basename($archivo['name']);
        $rutaDestino = BASE_PATH . "/src/uploads/$directorioDestino" . $nombreArchivo;
    
        if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            return false;
        }
    
        return $nombreArchivo;
    }

    // REV //
    private function usuarioAutenticado() {
        // Devuelve verdadero si hay una sesión activa
        return isset($_SESSION['user_nick']);
    }

    // REV //
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
            $juegosProcesados = array_map(function($juego) {
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

            $categorias = $this->modelo->obtenerCategorias();
            $categoriasPorId = array_column($categorias, 'nombre', 'id');

            // Procesar los datos de los juegos
            $juegosProcesados = array_map(function($juego) use ($categoriasPorId) {
                $baseUrl = 'https://localhost/Games-r-us/src/uploads/image/';
                return [
                    'id' => htmlspecialchars($juego['id']),
                    'title' => htmlspecialchars($juego['titulo']), // Sanitiza para HTML
                    'title2' => htmlspecialchars($juego['titulo2']),
                    'categoria' => htmlspecialchars($categoriasPorId[$juego['id_categoria']] ?? 'Categoria Desconocida'),
                    'description' => htmlspecialchars($juego['descripcion']),
                    'image' => $baseUrl . $juego['image'], // Asume que la imagen no necesita sanitización
                    'precio' => $juego['precio'],
                    'ruta' => $juego['ruta'],
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
    public function procesarTienda(){
        if (isset($_POST['tienda_comprar'])) { //POST
            //echo $_POST['compra_gameid'];
            Vista::MuestraFormularioCompra();//Aqui habria que poner la logica de "COMPRA"
        } else if (isset($_POST['tienda_regalar'])) { 
            Vista::MuestraFormularioRegalo();//Aqui habria que poner la logica de "REGALAR"
        }
    }
   
    
}

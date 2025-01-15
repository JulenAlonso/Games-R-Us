<?php
// modelo.php: Se encarga de la bbdd
require_once __DIR__ . '../../../vendor/autoload.php'; //Este enlace se encarga de la libreria de composer

use Dotenv\Dotenv;

class Modelo
{
    private $pdo;

    public function __construct(){
        $this->iniciaConexionBD();//inicia una conexion
    }

    private function iniciaConexionBD(){
        // Cargar las variables de entorno
        $dotenv = Dotenv::createImmutable(__DIR__ . '/');//Esto carga el archivo .env
        $dotenv->load();//lo hace funcionar

        // Obtener las credenciales de la base de datos
        // Cargamos variables del '.env' en variables de entorno de php:
        $host = $_ENV['DB_HOST'];
        $dbname = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASS'];

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass); //Estas variables se cogen del .env, 'charset=utf8': Para que cargue caracteres como ñ
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // setAttribute: Basicamente maneja las excepciones del PDO.
        } catch (PDOException $e) {
            die("Error al conectar con la base de datos: " . $e->getMessage());
        }
    }

    public function buscaUsuarioPorNick($nick){
        $stmt = $this->pdo->prepare("SELECT * FROM USUARIO WHERE nick = :nick");//Coge el pdo, le hace una query de sql donde el email sea igual al email que le paso por parametro.
        $stmt->bindParam(':nick', $nick);//El email introducido por parametro es la variable que le damos $email: puede ser julen@hotmail.com
        $stmt->execute();//ejecuta
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscaUsuarioPorMail($mail){
        $stmt = $this->pdo->prepare("SELECT * FROM USUARIO WHERE email = :email");//Coge el pdo, le hace una query de sql donde el email sea igual al email que le paso por parametro.
        $stmt->bindParam(':email', $mail);//El email introducido por parametro es la variable que le damos $email: puede ser julen@hotmail.com
        $stmt->execute();//ejecuta
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarRolePorNick($nick){
        $stmt = $this->pdo->prepare("SELECT id_rol FROM USUARIO WHERE nick = :nick");
        $stmt->bindParam(':nick', $nick);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function creaUsuario($nick, $email, $password){
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);//Cifra la passwd
        //Plantilla
        $stmt = $this->pdo->prepare("INSERT INTO USUARIO (nick, email, password, id_rol) VALUES (:nick, :email, :password, :id_rol)");//En los usuarios meter el email y la passwd en su columna correspondiente.
        $stmt->bindParam(':nick', $nick);
        $stmt->bindParam(':email', $email);//Reemplazamos el valor de email 
        $stmt->bindParam(':password', $passwordHash);//Reemplazamos el valor de passwd 
        $stmt->bindParam(':id_rol', 1);//Reemplazamos el valor de rol
        $stmt->execute();//ejecuta: reemplaza en la plantilla el email y la passwd que hemos introducido.
    }

    public function obtenerLanding() {
        $stmt = $this->pdo->prepare("SELECT titulo, ruta_imagen, desarrollador, distribuidor, anio FROM JUEGO LIMIT 6");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 

    public function obtenerJuegos() {
        $stmt = $this->pdo->prepare("SELECT id_juego, titulo, ruta, ruta_imagen, desarrollador, distribuidor, anio FROM JUEGO");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }   

    public function obtenerCategorias() {
        $stmt = $this->pdo->prepare("SELECT id, nombre_genero FROM GENERO");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }   

    public function obtenerCategoriasJuego($id_juego) {
        $categorias = $this->obtenerCategorias();
        $categoriasPorId = array_column($categorias, 'nombre_genero', 'id');

        $stmt = $this->pdo->prepare("SELECT id_juego, id_genero FROM JUEGO_GENERO WHERE id_juego = :id_juego");
        $stmt->bindParam(':id_juego', $id_juego);
        $stmt->execute();
        $categoriasJuego = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $categoriasJuegoNombre = array_map(function($categoria) use ($categoriasPorId) {
            return $categoriasPorId[$categoria['id_genero']];
        }, $categoriasJuego);

        return $categoriasJuegoNombre;
    }  

    public function obtenerSistemas() {
        $stmt = $this->pdo->prepare("SELECT id_sistema, nombre_sistema FROM SISTEMA");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }   

    public function obtenerSistemasJuego($id_juego) {
        $categorias = $this->obtenerSistemas();
        $categoriasPorId = array_column($categorias, 'nombre_sistema', 'id_sistema');

        $stmt = $this->pdo->prepare("SELECT id_juego, id_sistema FROM JUEGO_SISTEMA WHERE id_juego = :id_juego");
        $stmt->bindParam(':id_juego', $id_juego);
        $stmt->execute();
        $categoriasJuego = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $categoriasJuegoNombre = array_map(function($categoria) use ($categoriasPorId) {
            return $categoriasPorId[$categoria['id_sistema']];
        }, $categoriasJuego);

        return $categoriasJuegoNombre;
    } 

    public function obtenerUsuarios() {
        $stmt = $this->pdo->prepare("SELECT nick, email, nombre, ape1, ape2, tlf, direccion_tipo, direccion_via, direccion_numero, direccion_otros, id_rol, avatar FROM USUARIO");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 

    public function obtenerRoles() {
        $stmt = $this->pdo->prepare("SELECT nombre_rol, id_rol  FROM ROL");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 
    
    public function insertarUsuario($nick, $nombre, $pass, $ape1, $ape2, $tlf, $email, $rol) {
        try {
            // Crear la consulta SQL para insertar el usuario
            $sql = "INSERT INTO USUARIO (nick, nombre, password, ape1, ape2, tlf, email, id_rol)
                    VALUES (:nick, :nombre, :pass, :ape1, :ape2, :tlf, :email, :id_rol)";
            
            // Preparar la consulta
            $stmt = $this->pdo->prepare($sql);
        
            $haspass = password_hash($pass, PASSWORD_BCRYPT);
            // Enlazar los parámetros
            $stmt->bindParam(':nick', $nick);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':pass', $haspass);
            $stmt->bindParam(':ape1', $ape1);
            $stmt->bindParam(':ape2', $ape2);
            $stmt->bindParam(':tlf', $tlf);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':id_rol', $rol);
            // Ejecutar la consulta
            return $stmt->execute();
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function insertarJuego($titulo, $desarrollador, $distribuidor, $anio, $genero, $sistema, $coverImagePath, $gameZipPath){
        try {
            // Extraer el nombre del archivo de la ruta
            $coverImageName = basename($coverImagePath); // Extraer solo el nombre del archivo de la ruta completa
            $gameZipName = basename($gameZipPath); // Extraer solo el nombre del archivo de la ruta completa
    
            // Crear la consulta SQL para insertar el juego
            $sql = "INSERT INTO JUEGO (titulo, desarrollador, distribuidor, anio, ruta_imagen, ruta) 
                    VALUES (:titulo, :desarrollador, :distribuidor, :anio, :portada, :archivo_zip)";
            
            // Preparar la consulta
            $stmt = $this->pdo->prepare($sql);
    
            // Enlazar los parámetros
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':desarrollador', $desarrollador);
            $stmt->bindParam(':distribuidor', $distribuidor);
            $stmt->bindParam(':anio', $anio);
            $stmt->bindParam(':portada', $coverImageName); // Guardar solo el nombre de la imagen
            $stmt->bindParam(':archivo_zip', $gameZipName); // Guardar solo el nombre del archivo ZIP
    
            // Ejecutar la consulta
            $stmt->execute();
            $stmt = null;
    
            // Obtener el id del juego recién insertado
            $sql = "SELECT id_juego FROM JUEGO WHERE titulo = :titulo";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':titulo', $titulo);
            $stmt->execute();
            $id_juego = $stmt->fetch(PDO::FETCH_ASSOC)['id_juego']; // Obtener el id del juego recién insertado
            $stmt = null;
    
            // Insertar los géneros en la tabla JUEGO_GENEREO
            foreach ($genero as $gen) {
                // Verificar si el género ya existe
                $sql = "SELECT id FROM GENERO WHERE nombre_genero = :nombre";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':nombre', $gen);
                $stmt->execute();
                $id_genero = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
                $stmt = null;
    
                // Si el género no existe, insertarlo
                if (!$id_genero) {
                    $sql = "INSERT INTO GENERO (nombre_genero) VALUES (:nombre)";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->bindParam(':nombre', $gen);
                    $stmt->execute();
                    $id_genero = $this->pdo->lastInsertId(); // Obtener el ID del nuevo género
                    $stmt = null;
                }
    
                // Insertar la relación en la tabla intermedia JUEGO_GENEREO
                $sql = "INSERT INTO JUEGO_GENERO (id_juego, id_genero) VALUES (:id_juego, :id_genero)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':id_juego', $id_juego);
                $stmt->bindParam(':id_genero', $id_genero);
                $stmt->execute();
                $stmt = null;
            }
    
            // Insertar los sistemas en la tabla JUEGO_SISTEMA
            foreach ($sistema as $sys) {
                // Verificar si el sistema ya existe
                $sql = "SELECT id_sistema FROM SISTEMA WHERE nombre_sistema = :nombre";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':nombre', $sys);
                $stmt->execute();
                $id_sistema = $stmt->fetch(PDO::FETCH_ASSOC)['id_sistema'];
                $stmt = null;
    
                // Si el sistema no existe, insertarlo
                if (!$id_sistema) {
                    $sql = "INSERT INTO SISTEMA (nombre_sistema) VALUES (:nombre)";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->bindParam(':nombre', $sys);
                    $stmt->execute();
                    $id_sistema = $this->pdo->lastInsertId(); // Obtener el ID del nuevo sistema
                    $stmt = null;
                }
    
                // Insertar la relación en la tabla intermedia JUEGO_SISTEMA
                $sql = "INSERT INTO JUEGO_SISTEMA (id_juego, id_sistema) VALUES (:id_juego, :id_sistema)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':id_juego', $id_juego);
                $stmt->bindParam(':id_sistema', $id_sistema);
                $stmt->execute();
                $stmt = null;
            }
    
            // Si todo salió bien, devolver éxito
            return true;
        } catch (PDOException $e) {
            // Manejar errores (es importante para el registro de errores)
            error_log("Error inserting game: " . $e->getMessage());
            return $e->getMessage();
        }
    }
    
    public function actualizarUsuario($nick, $nombre, $ape1, $ape2, $tlf, $direccion_tipo, $direccion_via, $direccion_numero, $direccion_otros, $rol) {
        try {
            $sql = "UPDATE USUARIO 
                    SET nombre = :nombre, 
                        ape1 = :ape1, 
                        ape2 = :ape2,
                        tlf = :tlf, 
                        direccion_tipo = :direccion_tipo,
                        direccion_via = :direccion_via,
                        direccion_numero = :direccion_numero,
                        direccion_otros = :direccion_otros
                    WHERE nick = :nick";
    
            $stmt = $this->pdo->prepare($sql);
    
            $stmt->bindParam(':nick', $nick);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':ape1', $ape1);
            $stmt->bindParam(':ape2', $ape2);
            $stmt->bindParam(':tlf', $tlf);
            $stmt->bindParam(':direccion_tipo', $direccion_tipo);
            $stmt->bindParam(':direccion_via', $direccion_via);
            $stmt->bindParam(':direccion_numero', $direccion_numero);
            $stmt->bindParam(':direccion_otros', $direccion_otros);
    
            $stmt->execute();
    
            return ['success' => true, 'message' => 'User updated successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error updating user: ' . $e->getMessage()];
        }
    }
       
    public function obtenerGeneros() {
        $stmt = $this->pdo->prepare("SELECT id, nombre_genero FROM GENERO");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarJuego($id, $titulo, $desarrollador, $distribuidor, $anio, $genero, $sistema) {
        try {
            // Crear la consulta SQL para actualizar el juego
            $sql = "UPDATE JUEGO SET 
                        titulo = :titulo, 
                        desarrollador = :desarrollador, 
                        distribuidor = :distribuidor, 
                        anio = :anio
                    WHERE id_juego = :id";
    
            // Preparar la consulta
            $stmt = $this->pdo->prepare($sql);
    
            // Convertir los arrays a cadenas separadas por comas
            $generoStr = implode(',', $genero);
            $sistemaStr = implode(',', $sistema);
    
            // Enlazar los parámetros
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':desarrollador', $desarrollador);
            $stmt->bindParam(':distribuidor', $distribuidor);
            $stmt->bindParam(':anio', $anio);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
            // Ejecutar la consulta
            return $stmt->execute();
        } catch (PDOException $e) {
            // Manejar errores (es importante para el registro de errores)
            error_log("Error updating game: " . $e->getMessage());
            return $e->getMessage();
        }
    }

    public function deleteUsuario($nick) {
        try {
            // Crear la consulta SQL para eliminar el usuario
            $sql = "DELETE FROM USUARIO WHERE nick = :nick";
            
            // Preparar la consulta
            $stmt = $this->pdo->prepare($sql);
        
            // Enlazar los parámetros
            $stmt->bindParam(':nick', $nick);
        
            // Ejecutar la consulta
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting user: " . $e->getMessage());
            return false;
        }
    }

    public function deleteJuego($id) {
        try {
            // Eliminar las relaciones en la tabla JUEGO_GENERO
            $sql1 = "DELETE FROM JUEGO_GENERO WHERE id_juego = :id_juego";
            $stmt1 = $this->pdo->prepare($sql1);
            $stmt1->bindParam(':id_juego', $id);
            $stmt1->execute();
    
            // Eliminar las relaciones en la tabla JUEGO_SISTEMA
            $sql2 = "DELETE FROM JUEGO_SISTEMA WHERE id_juego = :id_juego";
            $stmt2 = $this->pdo->prepare($sql2);
            $stmt2->bindParam(':id_juego', $id);
            $stmt2->execute();
            
            // Eliminar el juego en la tabla principal JUEGO
            $sql = "DELETE FROM JUEGO WHERE id_juego = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
            return $stmt->execute(); // Ejecuta la eliminación y devuelve el resultado
        } catch (PDOException $e) {
            error_log("Error deleting game: " . $e->getMessage());
            return false;
        }
    }
    
}
?>
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

    //Usa la conexion a bbdd para buscar el usuario por email
    public function buscaUsuarioPorEmail($email){
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = :email");//Coge el pdo, le hace una query de sql donde el email sea igual al email que le paso por parametro.
        $stmt->bindParam(':email', $email);//El email introducido por parametro es la variable que le damos $email: puede ser julen@hotmail.com
        $stmt->execute();//ejecuta
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //Usa la conexion a bbdd para crear el usuario 
    public function creaUsuario($email, $password){
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);//Cifra la passwd
        //Plantilla
        $stmt = $this->pdo->prepare("INSERT INTO usuarios (email, password) VALUES (:email, :password)");//En los usuarios meter el email y la passwd en su columna correspondiente.
        $stmt->bindParam(':email', $email);//Reemplazamos el valor de email 
        $stmt->bindParam(':password', $passwordHash);//Reemplazamos el valor de passwd 
        $stmt->execute();//ejecuta: reemplaza en la plantilla el email y la passwd que hemos introducido.
    }

    public function agregarJuego($titulo, $titulo2, $descripcion, $id_categoria, $precio, $imagen, $ruta){
        $stmt = $this->pdo->prepare("INSERT INTO juegos (id_categoria, titulo, titulo2, descripcion, img, ruta, precio) VALUES (:id_categoria, :titulo, :titulo2, :descripcion, :img, :ruta, :precio)");
        if (!$stmt) {
            return false;
        }
    
        // Bind de parámetros utilizando bindValue
        $stmt->bindValue(':id_categoria', $id_categoria, PDO::PARAM_INT);
        $stmt->bindValue(':titulo', $titulo, PDO::PARAM_STR);
        $stmt->bindValue(':titulo2', $titulo2, PDO::PARAM_STR);
        $stmt->bindValue(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindValue(':img', $imagen, PDO::PARAM_STR);
        $stmt->bindValue(':ruta', $ruta, PDO::PARAM_STR);
        $stmt->bindValue(':precio', $precio, PDO::PARAM_STR);
    
        // Ejecutar la consulta
        if ($stmt->execute()) {
            return true;
        }
    
        return false;
    }

    public function obtenerJuegos() {
        $stmt = $this->pdo->prepare("SELECT titulo, titulo2, id_categoria, descripcion, img AS image, precio, ruta FROM juegos LIMIT 6");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }   
}
?>
<?php
require_once __DIR__ . '../../../vendor/autoload.php'; // Cambia la ruta si es necesario

use Dotenv\Dotenv;

class Modelo {
    private $pdo;

    public function __construct() {
        $this->iniciaConexionBD();
    }

    private function iniciaConexionBD() {
        // Cargar las variables de entorno
        $dotenv = Dotenv::createImmutable(__DIR__ . './');
        $dotenv->load();

        // Obtener las credenciales de la base de datos
        $host = $_ENV['DB_HOST'];
        $dbname = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASS'];

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error al conectar con la base de datos: " . $e->getMessage());
        }
    }

    public function buscaUsuarioPorEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function creaUsuario($email, $password) {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare("INSERT INTO usuarios (email, password) VALUES (:email, :password)");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $passwordHash);
        $stmt->execute();
    }
}
?>

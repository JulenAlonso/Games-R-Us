<?php
    class Modelo {
        private $pdo;

        public function __construct() {
            $this->iniciaConexionBD();
        }

        private function iniciaConexionBD() {
            $host = 'nas.snakernet.net';
            $dbname = 'Games_R_US';
            $user = 'gamesRusAdmin';
            $pass = 'M5e&<E1e/X?:m2jQ-jk}&I26@/!ziw@VVP>t]qWqd+P<r%l8_0vj@7E/.D{|i42#';

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
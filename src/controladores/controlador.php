<?php
require_once BASE_PATH . '/src/vistas/vista.php';
require_once BASE_PATH . '/src/modelos/modelo.php';

class Controlador {
    private $modelo;

    public function __construct() {
        $this->modelo = new Modelo();
    }

    public function Inicia() {
        // Verifica si hay una solicitud POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesaNav();
            $this->procesaLogin();
            $this->procesaRegister();
        } else {
            Vista::MuestraLanding(); // Carga la vista por defecto
        }
    }

    private function procesaNav() {
        // Verifica qué botón fue presionado
        if (isset($_POST['loginButton'])) {
            Vista::MuestraLogin();
        } elseif (isset($_POST['homeButton'])) {
            echo "Se presionó el botón Home";
        } elseif (isset($_POST['offersButton'])) {
            echo "Se presionó el botón Offers";
        };
    }

    private function procesaLogin() {
        if (isset($_POST['loginButtonBut'])) {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // Valida los datos
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "Formato de email inválido.";
                return;
            }

            if (empty($password)) {
                echo "La contraseña no puede estar vacía.";
                return;
            }

            // Busca al usuario en el modelo
            $user = $this->modelo->buscaUsuarioPorEmail($email);

            if ($user) {
                // Verifica la contraseña
                if (password_verify($password, $user['password'])) {
                    // Autenticación exitosa, guarda los datos en la sesión
                    session_start();
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    Vista::MuestraBiblioteca();;
                    exit;
                } else {
                    echo "Contraseña incorrecta.";
                }
            } else {
                echo "Usuario no encontrado.";
            }
        } else if(isset($_POST['RegisterButtonBut'])) {
            Vista::MuestraRegistro();
        }
    }

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
            $modelo = new Modelo();
            if ($modelo->buscaUsuarioPorEmail($email)) {
                echo "El email ya está registrado.";
                return;
            }

            // Crea el usuario
            $modelo->creaUsuario($email, $password);
            echo "Registro exitoso. Ahora puedes iniciar sesión.";
        }
    }
}
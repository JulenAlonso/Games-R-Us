<?php
// modelo.php: Se encarga de la bbdd
require_once __DIR__ . '../../../vendor/autoload.php'; //Este enlace se encarga de la libreria de composer

use Dotenv\Dotenv;

class Modelo
{
    private $pdo;

    public function __construct()
    {
        $this->iniciaConexionBD();//inicia una conexion
    }

    private function iniciaConexionBD()
    {
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

    public function buscaUsuarioPorNick($nick)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM USUARIO WHERE nick = :nick");//Coge el pdo, le hace una query de sql donde el email sea igual al email que le paso por parametro.
        $stmt->bindParam(':nick', $nick);//El email introducido por parametro es la variable que le damos $email: puede ser julen@hotmail.com
        $stmt->execute();//ejecuta
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscaUsuarioPorMail($mail)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM USUARIO WHERE email = :email");//Coge el pdo, le hace una query de sql donde el email sea igual al email que le paso por parametro.
        $stmt->bindParam(':email', $mail);//El email introducido por parametro es la variable que le damos $email: puede ser julen@hotmail.com
        $stmt->execute();//ejecuta
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarRolePorNick($nick)
    {
        $stmt = $this->pdo->prepare("SELECT id_rol FROM USUARIO WHERE nick = :nick");
        $stmt->bindParam(':nick', $nick);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function creaUsuario($nick, $email, $password)
    {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);//Cifra la passwd
        //Plantilla
        $stmt = $this->pdo->prepare("INSERT INTO USUARIO (nick, email, password, id_rol) VALUES (:nick, :email, :password, :id_rol)");//En los usuarios meter el email y la passwd en su columna correspondiente.
        $stmt->bindParam(':nick', $nick);
        $stmt->bindParam(':email', $email);//Reemplazamos el valor de email 
        $stmt->bindParam(':password', $passwordHash);//Reemplazamos el valor de passwd 
        $stmt->bindParam(':id_rol', 1);//Reemplazamos el valor de rol
        $stmt->execute();//ejecuta: reemplaza en la plantilla el email y la passwd que hemos introducido.
    }

    public function obtenerLanding()
    {
        $stmt = $this->pdo->prepare("SELECT titulo, ruta_imagen, desarrollador, distribuidor, anio FROM JUEGO LIMIT 6");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerJuegos()
    {
        $stmt = $this->pdo->prepare("SELECT id_juego, titulo, ruta, ruta_imagen, desarrollador, distribuidor, anio, precio FROM JUEGO");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerCategorias()
    {
        $stmt = $this->pdo->prepare("SELECT id, nombre_genero FROM GENERO");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerCategoriasJuego($id_juego)
    {
        $categorias = $this->obtenerCategorias();
        $categoriasPorId = array_column($categorias, 'nombre_genero', 'id');

        $stmt = $this->pdo->prepare("SELECT id_juego, id_genero FROM JUEGO_GENERO WHERE id_juego = :id_juego");
        $stmt->bindParam(':id_juego', $id_juego);
        $stmt->execute();
        $categoriasJuego = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $categoriasJuegoNombre = array_map(function ($categoria) use ($categoriasPorId) {
            return $categoriasPorId[$categoria['id_genero']];
        }, $categoriasJuego);

        return $categoriasJuegoNombre;
    }

    public function obtenerSistemas()
    {
        $stmt = $this->pdo->prepare("SELECT id_sistema, nombre_sistema FROM SISTEMA");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerSistemasJuego($id_juego)
    {
        $categorias = $this->obtenerSistemas();
        $categoriasPorId = array_column($categorias, 'nombre_sistema', 'id_sistema');

        $stmt = $this->pdo->prepare("SELECT id_juego, id_sistema FROM JUEGO_SISTEMA WHERE id_juego = :id_juego");
        $stmt->bindParam(':id_juego', $id_juego);
        $stmt->execute();
        $categoriasJuego = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $categoriasJuegoNombre = array_map(function ($categoria) use ($categoriasPorId) {
            return $categoriasPorId[$categoria['id_sistema']];
        }, $categoriasJuego);

        return $categoriasJuegoNombre;
    }

    public function obtenerUsuarios()
    {
        $stmt = $this->pdo->prepare("SELECT nick, email, nombre, ape1, ape2, tlf, direccion_tipo, direccion_via, direccion_numero, direccion_otros, id_rol, avatar FROM USUARIO");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerRoles()
    {
        $stmt = $this->pdo->prepare("SELECT nombre_rol, id_rol  FROM ROL");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertarUsuario($nick, $nombre, $pass, $ape1, $ape2, $tlf, $email, $rol)
    {
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

    public function actualizarUsuario($nick, $nombre, $ape1, $ape2, $tlf, $direccion_tipo, $direccion_via, $direccion_numero, $direccion_otros, $rol)
    {
        try {
            $sql = "UPDATE USUARIO 
                    SET nombre = :nombre, 
                        ape1 = :ape1, 
                        ape2 = :ape2,
                        tlf = :tlf, 
                        direccion_tipo = :direccion_tipo,
                        direccion_via = :direccion_via,
                        direccion_numero = :direccion_numero,
                        direccion_otros = :direccion_otros,
                        id_rol = :rol
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
            $stmt->bindParam(':rol', $rol);

            $stmt->execute();

            return ['success' => true, 'message' => 'User updated successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error updating user: ' . $e->getMessage()];
        }
    }

    public function obtenerGeneros()
    {
        $stmt = $this->pdo->prepare("SELECT id, nombre_genero FROM GENERO");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarJuego($id, $titulo, $desarrollador, $distribuidor, $anio, $generos, $sistemas)
    {
        try {
            $this->pdo->beginTransaction();

            // Actualizar los datos principales del juego
            $sqlJuego = "UPDATE JUEGO SET 
                            titulo = :titulo, 
                            desarrollador = :desarrollador, 
                            distribuidor = :distribuidor, 
                            anio = :anio
                         WHERE id_juego = :id";
            $stmtJuego = $this->pdo->prepare($sqlJuego);
            $stmtJuego->bindParam(':titulo', $titulo);
            $stmtJuego->bindParam(':desarrollador', $desarrollador);
            $stmtJuego->bindParam(':distribuidor', $distribuidor);
            $stmtJuego->bindParam(':anio', $anio);
            $stmtJuego->bindParam(':id', $id, PDO::PARAM_INT);

            if (!$stmtJuego->execute()) {
                $errorInfo = $stmtJuego->errorInfo();
                throw new PDOException("Error al actualizar el juego: " . $errorInfo[2]);
            }

            // Eliminar relaciones actuales en JUEGO_GENERO
            $sqlDeleteGeneros = "DELETE FROM JUEGO_GENERO WHERE id_juego = :id";
            $stmtDeleteGeneros = $this->pdo->prepare($sqlDeleteGeneros);
            $stmtDeleteGeneros->bindParam(':id', $id, PDO::PARAM_INT);

            if (!$stmtDeleteGeneros->execute()) {
                $errorInfo = $stmtDeleteGeneros->errorInfo();
                throw new PDOException("Error al eliminar géneros: " . $errorInfo[2]);
            }

            // Buscar IDs de géneros
            $sqlBuscarGeneros = "SELECT id FROM GENERO WHERE nombre_genero = :nombre_genero";
            $stmtBuscarGeneros = $this->pdo->prepare($sqlBuscarGeneros);
            $idsGeneros = [];
            foreach ($generos as $genero) {
                $stmtBuscarGeneros->bindParam(':nombre_genero', $genero);
                $stmtBuscarGeneros->execute();
                $result = $stmtBuscarGeneros->fetch(PDO::FETCH_ASSOC);
                if ($result) {
                    $idsGeneros[] = $result['id'];
                } else {
                    throw new PDOException("El género '{$genero}' no existe en la tabla GENERO.");
                }
            }

            // Insertar nuevas relaciones en JUEGO_GENERO
            $sqlInsertGeneros = "INSERT INTO JUEGO_GENERO (id_juego, id_genero) VALUES (:id_juego, :id_genero)";
            $stmtInsertGeneros = $this->pdo->prepare($sqlInsertGeneros);

            foreach ($idsGeneros as $idGenero) {
                $stmtInsertGeneros->bindParam(':id_juego', $id, PDO::PARAM_INT);
                $stmtInsertGeneros->bindParam(':id_genero', $idGenero, PDO::PARAM_INT);

                if (!$stmtInsertGeneros->execute()) {
                    $errorInfo = $stmtInsertGeneros->errorInfo();
                    throw new PDOException("Error al insertar género con ID {$idGenero}: " . $errorInfo[2]);
                }
            }

            // Eliminar relaciones actuales en JUEGO_SISTEMA
            $sqlDeleteSistemas = "DELETE FROM JUEGO_SISTEMA WHERE id_juego = :id";
            $stmtDeleteSistemas = $this->pdo->prepare($sqlDeleteSistemas);
            $stmtDeleteSistemas->bindParam(':id', $id, PDO::PARAM_INT);

            if (!$stmtDeleteSistemas->execute()) {
                $errorInfo = $stmtDeleteSistemas->errorInfo();
                throw new PDOException("Error al eliminar sistemas: " . $errorInfo[2]);
            }

            // Buscar IDs de sistemas
            $sqlBuscarSistemas = "SELECT id_sistema FROM SISTEMA WHERE nombre_sistema = :nombre_sistema";
            $stmtBuscarSistemas = $this->pdo->prepare($sqlBuscarSistemas);
            $idsSistemas = [];
            foreach ($sistemas as $sistema) {
                $stmtBuscarSistemas->bindParam(':nombre_sistema', $sistema);
                $stmtBuscarSistemas->execute();
                $result = $stmtBuscarSistemas->fetch(PDO::FETCH_ASSOC);
                if ($result) {
                    $idsSistemas[] = $result['id_sistema'];
                } else {
                    throw new PDOException("El sistema '{$sistema}' no existe en la tabla SISTEMA.");
                }
            }

            // Insertar nuevas relaciones en JUEGO_SISTEMA
            $sqlInsertSistemas = "INSERT INTO JUEGO_SISTEMA (id_juego, id_sistema) VALUES (:id_juego, :id_sistema)";
            $stmtInsertSistemas = $this->pdo->prepare($sqlInsertSistemas);

            foreach ($idsSistemas as $idSistema) {
                $stmtInsertSistemas->bindParam(':id_juego', $id, PDO::PARAM_INT);
                $stmtInsertSistemas->bindParam(':id_sistema', $idSistema, PDO::PARAM_INT);

                if (!$stmtInsertSistemas->execute()) {
                    $errorInfo = $stmtInsertSistemas->errorInfo();
                    throw new PDOException("Error al insertar sistema con ID {$idSistema}: " . $errorInfo[2]);
                }
            }

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error al actualizar el juego: " . $e->getMessage());
            return $e->getMessage(); // Devolver el mensaje de error
        }
    }

    public function deleteUsuario($nick)
    {
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

    public function eliminarJuego($id)
    {
        try {
            $this->pdo->beginTransaction();

            // Eliminar relaciones en JUEGO_GENERO
            $sqlDeleteGeneros = "DELETE FROM JUEGO_GENERO WHERE id_juego = :id";
            $stmtDeleteGeneros = $this->pdo->prepare($sqlDeleteGeneros);
            $stmtDeleteGeneros->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtDeleteGeneros->execute();

            // Eliminar relaciones en JUEGO_SISTEMA
            $sqlDeleteSistemas = "DELETE FROM JUEGO_SISTEMA WHERE id_juego = :id";
            $stmtDeleteSistemas = $this->pdo->prepare($sqlDeleteSistemas);
            $stmtDeleteSistemas->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtDeleteSistemas->execute();

            // Eliminar el juego de la tabla JUEGO
            $sqlDeleteJuego = "DELETE FROM JUEGO WHERE id_juego = :id";
            $stmtDeleteJuego = $this->pdo->prepare($sqlDeleteJuego);
            $stmtDeleteJuego->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtDeleteJuego->execute();

            // Confirmar la transacción
            $this->pdo->commit();
            return true; // Retorna éxito
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error al eliminar el juego: " . $e->getMessage());
            return false; // Retorna error
        }
    }

    public function actualizarGenero($id, $nombre_genero)
    {
        try {
            $sql = "UPDATE GENERO SET nombre_genero = :nombre_genero WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nombre_genero', $nombre_genero);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            } else {
                $errorInfo = $stmt->errorInfo();
                throw new PDOException("Error al actualizar género: " . $errorInfo[2]);
            }
        } catch (PDOException $e) {
            error_log("Error al actualizar género: " . $e->getMessage());
            return false;
        }
    }

    public function eliminarGenero($id)
    {
        try {
            $sql = "DELETE FROM GENERO WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            } else {
                $errorInfo = $stmt->errorInfo();
                throw new PDOException("Error al eliminar género: " . $errorInfo[2]);
            }
        } catch (PDOException $e) {
            error_log("Error al eliminar género: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarSistema($id, $nombre_sistema)
    {
        try {
            $sql = "UPDATE SISTEMA SET nombre_sistema = :nombre_sistema WHERE id_sistema = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nombre_sistema', $nombre_sistema);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            } else {
                $errorInfo = $stmt->errorInfo();
                throw new PDOException("Error al actualizar sistema: " . $errorInfo[2]);
            }
        } catch (PDOException $e) {
            error_log("Error al actualizar sistema: " . $e->getMessage());
            return false;
        }
    }

    public function eliminarSistema($id)
    {
        try {
            $sql = "DELETE FROM SISTEMA WHERE id_sistema = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            } else {
                $errorInfo = $stmt->errorInfo();
                throw new PDOException("Error al eliminar sistema: " . $errorInfo[2]);
            }
        } catch (PDOException $e) {
            error_log("Error al eliminar sistema: " . $e->getMessage());
            return false;
        }
    }

    public function guardarSistema($nombreSistema)
    {
        try {
            $sql = "INSERT INTO SISTEMA (nombre_sistema) VALUES (:nombre_sistema)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nombre_sistema', $nombreSistema, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al guardar sistema: " . $e->getMessage());
            return false;
        }
    }

    public function guardarGenero($nombreGenero)
    {
        try {
            $sql = "INSERT INTO GENERO (nombre_genero) VALUES (:nombre_genero)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nombre_genero', $nombreGenero, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al guardar género: " . $e->getMessage());
            return false;
        }
    }

    public function usuarioExiste($nick)
    {
        try {
            $sql = "SELECT COUNT(*) FROM USUARIO WHERE nick = :nick";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nick', $nick, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchColumn() > 0; // Devuelve true si el usuario existe
        } catch (PDOException $e) {
            error_log("Error al verificar si el usuario existe: " . $e->getMessage());
            return false;
        }
    }

    public function crearUsuario($nick, $email, $nombre, $ape1, $ape2, $tlf, $direccion_tipo, $direccion_via, $direccion_numero, $direccion_otros, $rol)
    {
        try {
            // Consulta SQL para insertar un usuario
            $sql = "INSERT INTO USUARIO (nick, email, nombre, ape1, ape2, tlf, direccion_tipo, direccion_via, direccion_numero, direccion_otros, id_rol, password) 
                    VALUES (:nick, :email, :nombre, :ape1, :ape2, :tlf, :direccion_tipo, :direccion_via, :direccion_numero, :direccion_otros, :rol, :password)";

            $stmt = $this->pdo->prepare($sql);

            // Preparar valores para los parámetros opcionales
            $nombre = $nombre ?: null;
            $ape1 = $ape1 ?: null;
            $ape2 = $ape2 ?: null;
            $tlf = $tlf ?: null;
            $direccion_tipo = $direccion_tipo ?: null;
            $direccion_via = $direccion_via ?: null;
            $direccion_numero = $direccion_numero ?: null;
            $direccion_otros = $direccion_otros ?: null;

            // Cifrar la contraseña basada en el nick
            $passwordHash = password_hash($nick, PASSWORD_BCRYPT);

            // Asociar parámetros
            $stmt->bindParam(':nick', $nick, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':ape1', $ape1, PDO::PARAM_STR);
            $stmt->bindParam(':ape2', $ape2, PDO::PARAM_STR);
            $stmt->bindParam(':tlf', $tlf, PDO::PARAM_STR);
            $stmt->bindParam(':direccion_tipo', $direccion_tipo, PDO::PARAM_STR);
            $stmt->bindParam(':direccion_via', $direccion_via, PDO::PARAM_STR);
            $stmt->bindParam(':direccion_numero', $direccion_numero, PDO::PARAM_STR);
            $stmt->bindParam(':direccion_otros', $direccion_otros, PDO::PARAM_STR);
            $stmt->bindParam(':rol', $rol, PDO::PARAM_INT);
            $stmt->bindParam(':password', $passwordHash, PDO::PARAM_STR);

            // Ejecutar la consulta y retornar el resultado
            if ($stmt->execute()) {
                return true;
            } else {
                // Registrar errores específicos de PDO
                error_log("Error al ejecutar la consulta: " . print_r($stmt->errorInfo(), true));
                return false;
            }
        } catch (PDOException $e) {
            // Registrar errores de la base de datos
            error_log("Error al crear usuario: " . $e->getMessage());
            return false;
        }
    }

    public function crearJuego($titulo, $desarrollador, $distribuidor, $anio, $rutaImagen, $rutaArchivo, $generos, $sistemas)
    {
        try {
            $this->pdo->beginTransaction();

            // Insertar el juego en la tabla `juego`
            $sqlJuego = "INSERT INTO JUEGO (titulo, ruta, ruta_imagen, desarrollador, distribuidor, anio) 
                         VALUES (:titulo, :ruta, :ruta_imagen, :desarrollador, :distribuidor, :anio)";
            $stmtJuego = $this->pdo->prepare($sqlJuego);

            // Vincular parámetros, asignando null si están vacíos
            $stmtJuego->bindValue(':titulo', $titulo ?: null, PDO::PARAM_STR);
            $stmtJuego->bindValue(':ruta', $rutaArchivo ?: null, PDO::PARAM_STR);
            $stmtJuego->bindValue(':ruta_imagen', $rutaImagen ?: null, PDO::PARAM_STR);
            $stmtJuego->bindValue(':desarrollador', $desarrollador ?: null, PDO::PARAM_STR);
            $stmtJuego->bindValue(':distribuidor', $distribuidor ?: null, PDO::PARAM_STR);
            $stmtJuego->bindValue(':anio', $anio ?: null, PDO::PARAM_INT);

            $stmtJuego->execute();

            // Obtener el ID del juego recién insertado
            $juegoId = $this->pdo->lastInsertId();

            // Insertar géneros en la tabla `juego_genero`
            if (!empty($generos)) {
                $sqlGenero = "INSERT INTO JUEGO_GENERO (id_juego, id_genero) 
                              SELECT :id_juego, id 
                              FROM GENERO 
                              WHERE id = :id_genero";
                $stmtGenero = $this->pdo->prepare($sqlGenero);

                foreach ($generos as $generoId) {
                    $stmtGenero->bindValue(':id_juego', $juegoId, PDO::PARAM_INT);
                    $stmtGenero->bindValue(':id_genero', $generoId, PDO::PARAM_INT);
                    $stmtGenero->execute();
                }
            }

            // Insertar sistemas en la tabla `juego_sistema`
            if (!empty($sistemas)) {
                $sqlSistema = "INSERT INTO JUEGO_SISTEMA (id_juego, id_sistema) 
                               SELECT :id_juego, id_sistema 
                               FROM SISTEMA 
                               WHERE id_sistema = :id_sistema";
                $stmtSistema = $this->pdo->prepare($sqlSistema);

                foreach ($sistemas as $sistemaId) {
                    $stmtSistema->bindValue(':id_juego', $juegoId, PDO::PARAM_INT);
                    $stmtSistema->bindValue(':id_sistema', $sistemaId, PDO::PARAM_INT);
                    $stmtSistema->execute();
                }
            }

            // Confirmar la transacción
            $this->pdo->commit();
            return true;

        } catch (PDOException $e) {
            // Revertir la transacción en caso de error
            $this->pdo->rollBack();
            error_log("Error al crear juego: " . $e->getMessage());
            return $e->getMessage();
        }
    }

    public function aniadirCarro($id_juego, $nick)
    {
        // echo "hola";
        try {
            // Preparar y ejecutar la consulta
            $query = "INSERT INTO CARRO (ID_juego, nick) 
                     VALUES (:id_juego, :nick_usuario)";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':id_juego', $id_juego, PDO::PARAM_INT);
            $stmt->bindValue(':nick_usuario', $nick, PDO::PARAM_STR);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log("Error al añadir al carro: " . $e->getMessage());
            return false;
        }
    }

    public function listarCesta($nick)
    {
        try {
            $query = "SELECT id_juego
                      FROM CARRO
                      WHERE nick = :nick";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':nick', $nick, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al listar la cesta: " . $e->getMessage());
            return false;
        }
    }

    public function cargarJuegosCestaUser($nick)
    {
        // Obtener los IDs de los juegos en la cesta del usuario
        $ids = $this->listarCesta($nick);

        // Si no hay IDs, retornar un array vacío
        if (empty($ids)) {
            return [];
        }

        // Extraer solo los valores de los IDs del array asociativo
        $idJuegos = array_column($ids, 'id_juego');

        try {
            // Crear una cadena de placeholders para la consulta IN
            $placeholders = implode(',', array_fill(0, count($idJuegos), '?'));

            // Consulta para obtener los juegos que coinciden con los IDs
            $query = "SELECT * 
                      FROM JUEGO
                      WHERE id_juego IN ($placeholders)";
            $stmt = $this->pdo->prepare($query);

            // Ejecutar la consulta con los IDs como parámetros
            $stmt->execute($idJuegos);

            // Retornar los resultados como un array asociativo
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Registrar el error en caso de excepción
            error_log("Error al cargar los juegos de la cesta: " . $e->getMessage());
            return $e->getMessage();
        }
    }

    public function eliminarJuegoCesta($id_juego, $nick)
    {
        try {
            $query = "DELETE FROM CARRO WHERE id_juego = :id_juego AND nick = :nick";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':id_juego', $id_juego, PDO::PARAM_INT);
            $stmt->bindValue(':nick', $nick, PDO::PARAM_STR);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log("Error al eliminar juego de la cesta: " . $e->getMessage());
            return false;
        }
    }

    public function vaciarCarrito($nick)
    {
        try {
            $query = "DELETE FROM CARRO WHERE nick = :nick";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':nick', $nick, PDO::PARAM_STR);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log("Error al vaciar el carrito: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarDatosUsuario($nick, $nombre, $ape1, $ape2, $tlf, $direccion_tipo, $direccion_via, $direccion_numero, $direccion_otros){
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

    public function actualizarAvatarUsuario($nick, $nombreImagen){
        try {
            $sql = "UPDATE USUARIO 
                    SET avatar = :avatar
                    WHERE nick = :nick";

            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':nick', $nick);
            $stmt->bindParam(':avatar', $nombreImagen);

            $stmt->execute();

            return ['success' => true, 'message' => 'User updated successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error updating user: ' . $e->getMessage()];
        }
    }

    public function obtenerJuegoPorId($id_juego){
        try {
            $query = "SELECT * FROM JUEGO WHERE id_juego = :id_juego";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':id_juego', $id_juego, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener el juego: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerJuegosUsuario($nick) {
        try {
            $sql = "SELECT JUEGO.* 
                    FROM JUEGO
                    INNER JOIN USUARIO_JUEGO ON JUEGO.id_juego = USUARIO_JUEGO.id_juego
                    WHERE USUARIO_JUEGO.nick = :nick";
    
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nick', $nick, PDO::PARAM_STR);
            $stmt->execute();
    
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devolver los juegos en formato de array asociativo
        } catch (PDOException $e) {
            error_log("Error al obtener los juegos del usuario: " . $e->getMessage());
            return []; // Devuelve un array vacío en caso de error
        }
    }

    public function guardarTarjetaUsuario($nick, $numero_tarjeta, $exp_mes, $exp_anio) {
        $query = "INSERT INTO TARJETA_BANCARIA (nick, numero_tarjeta, fecha_caducidad) 
                  VALUES (?, ?, ?) 
                  ON DUPLICATE KEY UPDATE numero_tarjeta = VALUES(numero_tarjeta), 
                  fecha_caducidad = VALUES(fecha_caducidad)";
    
        $stmt = $this->pdo->prepare($query);
    
        // Obtener el último día del mes
        $fecha_caducidad = date('Y-m-t', strtotime("$exp_anio-$exp_mes-01"));
    
        $stmt->execute([$nick, $numero_tarjeta, $fecha_caducidad]);
    }
    
    public function agregarJuegoBiblioteca($nick, $id_juego) {
        $query = "INSERT INTO USUARIO_JUEGO (nick, id_juego) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$nick, $id_juego]);
    }

    public function obtenerUltimasInserciones($limite = 5) {
        $sql = "SELECT * FROM JUEGO ORDER BY fecha_creacion DESC LIMIT :limite";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":limite", $limite, PDO::PARAM_INT); // Usar bindValue con PDO
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Usar fetchAll con PDO
    }
    
}
?>
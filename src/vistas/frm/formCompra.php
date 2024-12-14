<?php
// Configuración de la base de datos
$host = "localhost"; // Dirección del servidor de la base de datos (generalmente localhost).
$dbname = "tu_base_de_datos"; // Nombre de la base de datos donde se guardarán los datos.
$username = "tu_usuario"; // Nombre de usuario de la base de datos.
$password = "tu_contraseña"; // Contraseña para acceder a la base de datos.

// Crear conexión con la base de datos
$conn = new mysqli($host, $username, $password, $dbname); // Se crea un objeto mysqli para conectarse a la base de datos.

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error); // Si hay un error, se termina el script mostrando el error.
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Verifica si la solicitud al servidor es de tipo POST.
    // Recoge los datos del formulario y los sanitiza para evitar inyecciones de código o XSS.
    $name = htmlspecialchars($_POST['name']);
    $ap1 = htmlspecialchars($_POST['ap1']);
    $ap2 = htmlspecialchars($_POST['ap2']);
    $email = htmlspecialchars($_POST['email']);
    $tlf = htmlspecialchars($_POST['tlf']);
    $fechaNac = htmlspecialchars($_POST['fechaNac']);
    $calle = htmlspecialchars($_POST['calle']);
    $num = htmlspecialchars($_POST['num']);
    $piso = htmlspecialchars($_POST['piso']);
    $letra = htmlspecialchars($_POST['letra']);
    $cp = htmlspecialchars($_POST['cp']);
    $localidad = htmlspecialchars($_POST['localidad']);
    $pais = htmlspecialchars($_POST['pais']);
    $cardName = htmlspecialchars($_POST['input-name']);
    $cardNumber = htmlspecialchars($_POST['input-number']);
    $expMonth = htmlspecialchars($_POST['input-month']);
    $expYear = htmlspecialchars($_POST['input-year']);
    $cvc = htmlspecialchars($_POST['input-cvc']);

    // Nombre del juego comprado (puedes hacerlo dinámico si proviene de otro formulario o base de datos).
    $juegoComprado = "Nombre del juego"; // Cambia esto según el sistema.

    // Validación básica
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // Valida que el email tenga un formato correcto.
        die("El correo electrónico no es válido"); // Si el email es inválido, se detiene el script con un mensaje.
    }

    // Guardar los datos en la base de datos
    $stmt = $conn->prepare("INSERT INTO pedidos (nombre, apellido1, apellido2, email, telefono, fecha_nacimiento, calle, numero, piso, letra, cp, localidad, pais, titular_tarjeta, numero_tarjeta, exp_mes, exp_anio, cvc, juego) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    // Se prepara una consulta SQL para insertar los datos en la tabla `pedidos`.

    $stmt->bind_param(
        "sssssssssssssssssss", // Define los tipos de datos de los parámetros (todos cadenas de texto).
        $name,
        $ap1,
        $ap2,
        $email,
        $tlf,
        $fechaNac,
        $calle,
        $num,
        $piso,
        $letra,
        $cp,
        $localidad,
        $pais,
        $cardName,
        $cardNumber,
        $expMonth,
        $expYear,
        $cvc,
        $juegoComprado
    );

    if ($stmt->execute()) { // Ejecuta la consulta y verifica si se realiza correctamente.
        echo "Datos guardados en la base de datos correctamente.<br>";

        // Enviar correo de confirmación
        $to = $email; // Dirección de correo del destinatario.
        $subject = "Confirmación de compra - $juegoComprado"; // Asunto del correo.
        $message = " 
        Hola $name $ap1 $ap2,

        Gracias por tu compra. Aquí están los detalles de tu pedido:

        Juego comprado: $juegoComprado
        Datos personales:
        - Nombre: $name $ap1 $ap2
        - Email: $email
        - Teléfono: $tlf
        - Fecha de nacimiento: $fechaNac

        Dirección de envío:
        $calle, $num, Piso: $piso, Letra: $letra
        Código Postal: $cp
        Localidad: $localidad
        País: $pais

        Información de la tarjeta:
        - Titular: $cardName
        - Número de tarjeta: **** **** **** " . substr($cardNumber, -4) /*Oculta los primeros dígitos de la tarjeta.*/ . " 
        - Fecha de expiración: $expMonth/$expYear

        Si tienes alguna pregunta, no dudes en contactarnos.

        ¡Gracias por confiar en nosotros!
        ";

        $headers = "From: no-reply@pepe-dominio.com\r\n"; // Dirección del remitente del correo.
        // Esta línea define quién es el remitente del correo.
        // "no-reply@pepe-dominio.com" sería la dirección de correo desde donde se enviará el mensaje, por ejemplo, una dirección automática de un sistema.

        $headers .= "Reply-To: contacto@pepe-dominio.com\r\n"; // Dirección para responder el correo.
        // Esta línea define a qué dirección se deben enviar las respuestas si alguien contesta el correo.
        // En este caso, las respuestas irían a "contacto@pepe-dominio.com".

        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n"; // Tipo de contenido del correo.
        //Esta línea especifica el tipo de contenido del correo, en este caso, es texto plano (text/plain) y la codificación de caracteres es UTF-8, lo que asegura que los caracteres especiales (como tildes) se vean correctamente.

        if (mail($to, $subject, $message, $headers)) { // Envía el correo y verifica si se envió correctamente.
            echo "El correo de confirmación ha sido enviado correctamente a $email.";
        } else {
            echo "Hubo un problema al enviar el correo."; // Mensaje de error si no se puede enviar el correo.
        }
    } else {
        echo "Error al guardar los datos: " . $stmt->error; // Mensaje de error si falla la inserción en la base de datos.
    }

    $stmt->close(); // Cierra la declaración preparada.
} else {
    // Si alguien intenta acceder directamente a este archivo sin enviar datos, se redirige al formulario.
    header('Location: formulario.php'); // Redirige al formulario (cambia por la URL de tu formulario).
    exit;
}

$conn->close(); // Cierra la conexión con la base de datos.
?>
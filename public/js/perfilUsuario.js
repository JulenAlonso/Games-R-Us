// Previsualización de la imagen seleccionada.
/* 
Cómo funciona:
    El usuario selecciona una imagen a través del input de tipo file.
    Usamos el objeto FileReader para leer el archivo localmente.
    Una vez leído, el archivo se convierte en una URL de datos (base64) que se asigna como el nuevo src de la imagen de perfil en la página. 
    Esto hace que la imagen seleccionada se muestre automáticamente en el navegador.
*/
// document.addEventListener('DOMContentLoaded', () => {
// const fileInput = document.getElementById('profileImageInput'); // Input de archivo.
// const profilePicture = document.querySelector('.profile-picture'); // Imagen actual.
//
// fileInput.addEventListener('change', function (event) {
// const file = event.target.files[0]; // Obtener el archivo seleccionado.
// if (file) {
// const reader = new FileReader(); // Crear un lector de archivos.
// reader.onload = function (e) {
// e = event
// /* En el contexto de FileReader, el objeto e tiene una propiedad llamada target,
// que hace referencia al objeto que disparó el evento (en este caso, el propio FileReader),
// y la propiedad result, que contiene el contenido del archivo leído.
// Este contenido es lo que necesitamos para mostrar la previsualización de la imagen.*/
// profilePicture.src = e.target.result; // Actualizar la imagen con la previsualización.
// /* e.target.result se refiere al contenido del archivo (en formato base64 para imágenes) que el FileReader ha leído.*/
// };
// reader.readAsDataURL(file); // Leer el archivo como una URL de datos.
// }
// });
// });
//
// Validar el archivo de imagen antes de enviarlo al servidor.
// document.addEventListener('DOMContentLoaded', () => {
// const form = document.querySelector('form'); // Seleccion del formulario.
// const fileInput = document.getElementById('profileImageInput'); // Input de archivo.
// const validExtensions = ['image/jpeg', 'image/png', 'image/gif']; // Tipos permitidos.
// const maxSize = 500000; // Tamaño máximo en bytes (500 KB).
//
// form.addEventListener('submit', function (event) {
// const file = fileInput.files[0]; // Obtener el archivo seleccionado.
// if (file) {
// Validamos el tipo de archivo.
// if (!validExtensions.includes(file.type)) {
// alert('Solo se permiten imágenes en formato JPG, PNG o GIF.'); // Mostrar alerta.
// event.preventDefault(); // Cancelar el envío.
// }
// Validamos el tamaño del archivo.
// else if (file.size > maxSize) {
// alert('El tamaño de la imagen no puede exceder los 500 KB.'); // Mostrar alerta.
// event.preventDefault(); // Cancelar el envío.
// }
// } else {
// alert('Por favor, selecciona una imagen.'); // Alerta si no se selecciona archivo.
// event.preventDefault(); // Cancelar el envío.
// }
// });
// });
//
// Alternar la visibilidad del menú de perfil.
// function toggleProfileMenu() {
// const menu = document.getElementById('profileMenu'); // Seleccionar el menú de perfil.
// menu.classList.toggle('hidden'); // Alternar la clase 'hidden' para mostrar/ocultar.
// }
//
function loadUserInfo(data) {
    // Los campos Nick y Email se muestran como texto no editable
    document.getElementById("user_nick").innerText = data.nick;
    document.getElementById("user_email").innerText = data.email;
    document.getElementById("user_role").innerText = data.rol;w1

    // Precargar valores en los campos del formulario
    document.getElementById("user_nombre").value = data.nombre;
    document.getElementById("user_ape1").value = data.ape1;
    document.getElementById("user_ape2").value = data.ape2;
    document.getElementById("user_tlf").value = data.tlf;
    document.getElementById("user_direccion_tipo").value = data.direccion_tipo;
    document.getElementById("user_direccion_via").value = data.direccion_via;
    document.getElementById("user_direccion_numero").value = data.direccion_numero;
    document.getElementById("user_direccion_otros").value = data.direccion_otros;
    // document.getElementById("user_avatar").value = data.avatar;

    //Añadir imagen de perfil
}

async function enviarUsuario(user) {
    try {
        const response = await fetch("/Games-r-us/public/index.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: new URLSearchParams({
                accion: "cargarUsuario",
                usuario: JSON.stringify(user),
            }),
        });

        if (!response.ok) {
            throw new Error("Error al obtener los datos");
        }

        const result = await response.json();
        if (result.success) {
            data = result.data;
            loadUserInfo(data);
        } else {
            throw new Error("Error en la respuesta del servidor");
        }
    } catch (error) {
        console.error("Error al realizar la solicitud:", error);
    }
}

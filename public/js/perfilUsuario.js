function toggleProfileMenu() {
    const menu = document.getElementById('profileMenu'); // Seleccionar el menú de perfil.
    menu.classList.toggle('hidden'); // Alternar la clase 'hidden' para mostrar/ocultar.
}
function loadUserInfo(data) {
    // Los campos Nick y Email se muestran como texto no editable
    document.getElementById("user_nick").innerText = data.nick;
    document.getElementById("user_email").innerText = data.email;
    document.getElementById("user_role").innerText = data.rol;

    // Precargar valores en los campos del formulario
    document.getElementById("user_nombre").value = data.nombre;
    document.getElementById("user_ape1").value = data.ape1;
    document.getElementById("user_ape2").value = data.ape2;
    document.getElementById("user_tlf").value = data.tlf;
    document.getElementById("user_direccion_tipo").value = data.direccion_tipo;
    document.getElementById("user_direccion_via").value = data.direccion_via;
    document.getElementById("user_direccion_numero").value = data.direccion_numero;
    document.getElementById("user_direccion_otros").value = data.direccion_otros;
    document.getElementById("user_avatar").src = "../avatar/" + data.avatar;
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


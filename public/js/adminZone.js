let users = [];
let games = [];
let genres = [];
let systems = [];

// Mostrar la sección seleccionada
function showSection(sectionId) {
    console.log('Mostrando sección:', sectionId);

    // Ocultar todas las secciones y limpiar su contenido
    document.querySelectorAll('.section').forEach(section => {
        console.log('Ocultando y limpiando sección:', section.id);
        section.classList.add('hidden');
        if (section.id !== sectionId) {
            section.innerHTML = ''; // Limpia el contenido de las secciones no activas
        }
    });

    // Mostrar la sección activa
    const section = document.getElementById(sectionId);
    if (section) {
        console.log('Sección encontrada:', sectionId);
        section.classList.remove('hidden');

        // Cargar los datos necesarios para cada sección
        if (sectionId === 'users') loadUsers();
        if (sectionId === 'games') loadGames();
        if (sectionId === 'genres') loadGenres();
        if (sectionId === 'systems') loadSystems();
    } else {
        console.error(`No se encontró la sección con ID: ${sectionId}`);
    }
}

// Cargar lista de usuarios
async function loadUsers() {
    console.log("Cargando usuarios...");

    try {
        const response = await fetch("/Games-r-us/public/index.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: new URLSearchParams({ accion: "listadoUsers" }),
        });

        if (!response.ok) throw new Error("Error al obtener los datos");

        const result = await response.json();
        if (result.success) {
            users = result.data;
            const userList = document.getElementById("userList");
            userList.innerHTML = users.map(user => `
                <div class="card" onclick="showUserDetails('${user.nick}')">
                    <img src="${ '../avatar/' +user.avatar }" alt="Profile">
                    <h3>${user.nick}</h3>
                    <p>${user.email}</p>
                </div>
            `).join('');
        } else {
            console.error("Error en el servidor:", result.message);
        }
    } catch (error) {
        console.error("Error al cargar usuarios:", error);
    }
}

// Mostrar detalles de un usuario
function showUserDetails(userNick) {
    console.log('Mostrando detalles del usuario con nick:', userNick);

    // Buscar el usuario correspondiente por nick
    const user = users.find(u => u.nick === userNick);
    if (!user) return console.error("Usuario no encontrado:", userNick);

    const formHTML = `
        <h2>Editar Usuario</h2>
        <form onsubmit="saveUser(event, '${user.nick}')">
            <div>
                <p>Rol:</p>
                <input type="text" id="editRole" placeholder="Role" value="${user.rol}">
            </div>
            <div>
                <p>Nick:</p>
                <input type="text" id="editNick" placeholder="Nick" value="${user.nick}" readonly>
            </div>
            <div>
                <p>Email:</p>
                <input type="email" id="editEmail" placeholder="Email" value="${user.email}" required>
            </div>
            <div>
                <p>Nombre:</p>
                <input type="text" id="editNombre" placeholder="Nombre" value="${user.nombre}">
            </div>
            <div>
                <p>Apellido 1:</p>
                <input type="text" id="editApe1" placeholder="Ape1" value="${user.ape1}">
            </div>
            <div>
                <p>Apellido 2:</p>
                <input type="text" id="editApe2" placeholder="Ape2" value="${user.ape2}">
            </div>
            <div>
                <p>Teléfono:</p>
                <input type="text" id="editTlf" placeholder="Tlf" value="${user.tlf}">
            </div>
            <div>
                <p>Dirección:</p>
                <div class="direccion">
                    <input type="text" id="editDir_tipo" placeholder="Tipo de dirección" value="${user.direccion_tipo}">
                    <p></p>
                    <input type="text" id="editDir_via" placeholder="Vía" value="${user.direccion_via}">
                    <p></p>
                    <input type="text" id="editDir_numero" placeholder="Número" value="${user.direccion_numero}">
                    <p></p>
                    <input type="text" id="editDir_otros" placeholder="Otros detalles" value="${user.direccion_otros}">
                </div>
            </div>
            <br>
            <button type="submit">Guardar</button>
            <button type="button" onclick="deleteUser('${user.nick}')" class="delete-btn">Eliminar</button>
        </form>

    `;
    openPopout(formHTML);
}

// Guardar usuario
async function saveUser(event, userNick) {
    event.preventDefault();
    const user = {
        nick: userNick,
        email: document.getElementById('editEmail').value,
        nombre: document.getElementById('editNombre').value,
        ape1: document.getElementById('editApe1').value,
        ape2: document.getElementById('editApe2').value,
        tlf: document.getElementById('editTlf').value,
        direccion_tipo: document.getElementById('editDir_tipo').value,
        direccion_via: document.getElementById('editDir_via').value,
        direccion_numero: document.getElementById('editDir_numero').value,
        direccion_otros: document.getElementById('editDir_otros').value,
        rol: document.getElementById('editRole').value,
    };

    console.log("Guardando usuario:", user);
    try {
        const response = await fetch("/Games-r-us/public/index.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ accion: "editarUsuario", ...user }),
        });

        const result = await response.json();
        if (result.success) {
            alert("Usuario guardado correctamente");
            closePopout();
            loadUsers();
        } else {
            console.error("Error al guardar usuario:", result.message);
        }
    } catch (error) {
        console.error("Error al guardar usuario:", error);
    }
}

// Eliminar usuario
async function deleteUser(userNick) {
    console.log("Eliminando usuario:", userNick);
    try {
        const response = await fetch("/Games-r-us/public/index.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ accion: "eliminarUsuario", nick: userNick }),
        });

        const result = await response.json();
        if (result.success) {
            alert("Usuario eliminado correctamente");
            closePopout();
            loadUsers();
        } else {
            console.error("Error al eliminar usuario:", result.message);
        }
    } catch (error) {
        console.error("Error al eliminar usuario:", error);
    }
}

// Abrir popout
function openPopout(content) {
    console.log("Abriendo popout");
    const popout = document.getElementById("popout");
    document.getElementById("popoutContent").innerHTML = content;
    popout.classList.remove("hidden");
}

// Cerrar popout
function closePopout() {
    console.log("Cerrando popout");
    const popout = document.getElementById("popout");
    document.getElementById("popoutContent").innerHTML = "";
    popout.classList.add("hidden");
}

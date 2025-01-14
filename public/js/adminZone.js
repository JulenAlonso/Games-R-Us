let users = [];
let games = [];
let genres = [];
let systems = [];

// Mostrar la sección seleccionada
function showSection(sectionId) {
    console.log('Mostrando sección:', sectionId);
    // Ocultar todas las secciones
    document.querySelectorAll('.section').forEach(section => {
        console.log('Ocultando sección:', section.id);
        section.classList.add('hidden');
    });

    // Mostrar la sección activa
    const section = document.getElementById(sectionId);
    if (section) {
        console.log('Sección encontrada:', sectionId);
        section.classList.remove('hidden');
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
                <div class="card" onclick="showUserDetails(${user.id})">
                    <img src="${user.profileImage || 'https://img.freepik.com/vector-premium/icono-circulo-usuario-anonimo-ilustracion-vector-estilo-plano-sombra_520826-1931.jpg'}" alt="Profile">
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
function showUserDetails(userId) {
    const user = users.find(u => u.id === userId);
    if (!user) return console.error("Usuario no encontrado:", userId);

    const formHTML = `
        <h2>Editar Usuario</h2>
        <form onsubmit="saveUser(event, ${user.id})">
            <input type="text" id="editNick" placeholder="Nick" value="${user.nick}" required>
            <input type="email" id="editEmail" placeholder="Email" value="${user.email}" required>
            <input type="text" id="editNombre" placeholder="Nombre" value="${user.nombre}">
            <button type="submit">Guardar</button>
            <button type="button" onclick="deleteUser(${user.id})" class="delete-btn">Eliminar</button>
        </form>
    `;
    openPopout(formHTML);
}

// Guardar usuario
async function saveUser(event, userId) {
    event.preventDefault();
    const user = {
        id: userId,
        nick: document.getElementById('editNick').value,
        email: document.getElementById('editEmail').value,
        nombre: document.getElementById('editNombre').value,
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
async function deleteUser(userId) {
    console.log("Eliminando usuario:", userId);
    try {
        const response = await fetch("/Games-r-us/public/index.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ accion: "eliminarUsuario", id: userId }),
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

// Cargar lista de juegos
async function loadGames() {
    console.log("Cargando juegos...");
    try {
        const response = await fetch("/Games-r-us/public/index.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ accion: "listadoJuegos" }),
        });

        if (!response.ok) throw new Error("Error al obtener los datos");

        const result = await response.json();
        if (result.success) {
            games = result.data;
            const gameList = document.getElementById("gameList");
            gameList.innerHTML = games.map(game => `
                <div class="card" onclick="showGameDetails(${game.id})">
                    <img src="${game.coverImage || 'https://cdn-icons-png.flaticon.com/512/5260/5260498.png'}" alt="Game Cover">
                    <h3>${game.titulo}</h3>
                </div>
            `).join('');
        } else {
            console.error("Error en el servidor:", result.message);
        }
    } catch (error) {
        console.error("Error al cargar juegos:", error);
    }
}

// Mostrar detalles de un juego
function showGameDetails(gameId) {
    const game = games.find(g => g.id === gameId);
    if (!game) return console.error("Juego no encontrado:", gameId);

    const formHTML = `
        <h2>Editar Juego</h2>
        <form onsubmit="saveGame(event, ${game.id})">
            <input type="text" id="editTitle" placeholder="Título" value="${game.titulo}" required>
            <input type="text" id="editDeveloper" placeholder="Desarrollador" value="${game.desarrollador}">
            <button type="submit">Guardar</button>
            <button type="button" onclick="deleteGame(${game.id})" class="delete-btn">Eliminar</button>
        </form>
    `;
    openPopout(formHTML);
}

// Guardar juego
async function saveGame(event, gameId) {
    event.preventDefault();
    const game = {
        id: gameId,
        titulo: document.getElementById('editTitle').value,
        desarrollador: document.getElementById('editDeveloper').value,
    };

    console.log("Guardando juego:", game);
    try {
        const response = await fetch("/Games-r-us/public/index.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ accion: "editarJuego", ...game }),
        });

        const result = await response.json();
        if (result.success) {
            alert("Juego guardado correctamente");
            closePopout();
            loadGames();
        } else {
            console.error("Error al guardar juego:", result.message);
        }
    } catch (error) {
        console.error("Error al guardar juego:", error);
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

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
                <input type="email" id="editEmail" placeholder="Email" value="${user.email}" readonly>
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
                    <img src="${game.ruta_imagen || 'https://cdn-icons-png.flaticon.com/512/5260/5260498.png'}" alt="Game Cover">
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

let allGenres = [
    "Acción", "Aventura", "RPG", "Estrategia", "Deportes", "Simulación", 
    "Shooter", "Carreras", "Terror", "Música", "Puzzle", "Plataformas"
  ];
  let selectedGenres = [];
  
function showGameDetails(gameId) {
    console.log("Games array:", games); // Verifica los datos del array
    const game = games.find(g => g.id == gameId);
    if (!game) return console.error("Juego no encontrado:", gameId);

    selectedGenres = [...game.genero]; // Inicializar los géneros seleccionados del juego

    const modalHTML = `
        <h2>Editar Juego</h2>
        <img src="${game.ruta_imagen}" alt="Game Cover">

        <!-- Formulario de edición -->
        <form onsubmit="saveGame(event, ${game.id})">
            <p>Título:</p>
            <input type="text" id="editTitle" placeholder="Título" value="${game.titulo}" required>
            <p>Desarrollador:</p>
            <input type="text" id="editDeveloper" placeholder="Desarrollador" value="${game.desarrollador}">
            <p>Distribuidor:</p>
            <input type="text" id="editDistribuidor" placeholder="Distribuidor" value="${game.distribuidor}">
            <p>Año:</p>
            <input type="text" id="editAnio" placeholder="Año" value="${game.anio}">
            
            <!-- Campo oculto para los géneros seleccionados -->
            <input type="hidden" id="selectedGenresField" name="generos" value="">
        </form>

        <!-- Buscador de género fuera del formulario -->
        <div style="margin-bottom: 20px;">
            <p style="text-align: left;">Género:</p>
            <div style="position: relative;">
                <input type="text" id="generoSearch" 
                        placeholder="Buscar género" 
                        oninput="filterGenres()" 
                        onclick="toggleSuggestions(true)" 
                        onfocus="toggleSuggestions(true)" 
                        onblur="hideSuggestions()">
                <div id="generoSuggestions" class="suggestions hidden"></div>
            </div>
            <div id="generoContainer" class="tag-container">
                <!-- Géneros seleccionados -->
            </div>
        </div>

        <!-- Botones al final -->
        <div class="button-container">
            <button type="button" onclick="submitGameForm(${game.id})">Guardar</button>
            <button type="button" onclick="deleteGame(${game.id})" class="delete-btn">Eliminar</button>
        </div>
    `;
    openPopout(modalHTML);

    // Actualiza los géneros seleccionados y las sugerencias al abrir el modal
    updateSelectedGenres();
    filterGenres();
}

function toggleSuggestions(show) {
    const suggestions = document.getElementById("generoSuggestions");
    if (show) {
        suggestions.classList.remove("hidden");
    } else {
        suggestions.classList.add("hidden");
    }
}

function hideSuggestions() {
    setTimeout(() => toggleSuggestions(false), 200); // Espera para permitir clics en sugerencias
}

function submitGameForm(gameId) {
    // Sincroniza los géneros seleccionados con el campo oculto
    const selectedGenresField = document.getElementById("selectedGenresField");
    selectedGenresField.value = JSON.stringify(selectedGenres); // Convierte los géneros a formato JSON

    // Encuentra el formulario y envíalo manualmente
    const form = document.querySelector("form");
    const formData = new FormData(form);

    // Agrega el campo de géneros al FormData
    formData.append("generos", JSON.stringify(selectedGenres));

    // Simula el envío del formulario
    saveGame({ preventDefault: () => {} }, gameId, formData);
}

  
function filterGenres() {
    const search = document.getElementById("generoSearch").value.toLowerCase();
    const filteredGenres = allGenres.filter(genre => 
        genre.toLowerCase().includes(search) && !selectedGenres.includes(genre)
    );
    const suggestionsContainer = document.getElementById("generoSuggestions");

    suggestionsContainer.innerHTML = ""; // Limpiar sugerencias

    filteredGenres.forEach(genre => {
        const suggestion = document.createElement("div");
        suggestion.className = "suggestion-item";
        suggestion.textContent = genre;
        suggestion.onclick = () => toggleGenre(genre);
        suggestionsContainer.appendChild(suggestion);
    });
}

function toggleGenre(genre) {
    if (selectedGenres.includes(genre)) {
        selectedGenres = selectedGenres.filter(g => g !== genre);
    } else {
        selectedGenres.push(genre);
    }
    updateSelectedGenres();
    filterGenres(); // Actualizar sugerencias
}

function updateSelectedGenres() {
    const container = document.getElementById("generoContainer");
    container.innerHTML = ""; // Limpiar el contenedor

    selectedGenres.forEach(genre => {
        const tag = document.createElement("div");
        tag.className = "tag";
        tag.textContent = genre;

        const removeButton = document.createElement("span");
        removeButton.className = "tag-remove";
        removeButton.textContent = "X";
        removeButton.onclick = () => toggleGenre(genre);

        tag.appendChild(removeButton);
        container.appendChild(tag);
    });
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

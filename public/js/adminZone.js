let users = [];
let games = [];
let genres = [];
let systems = [];

// ------------------- Funciones de UI -------------------
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

// ------------------- Funciones de USUARIO -------------------
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

// ------------------- Funciones de JUEGOS -------------------
let allGenres = [];
let allSystems = [];
let selectedGenres = [];
let selectedSystems = []; 

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

// Mostrar detalles de un Juego
function showGameDetails(gameId) {
    console.log("Games array:", games); // Verifica los datos del array
    const game = games.find(g => g.id == gameId);
    if (!game) return console.error("Juego no encontrado:", gameId);

    selectedGenres = [...game.genero]; // Inicializar los géneros seleccionados del juego
    selectedSystems = [...game.sistema]; // Inicializar los sistemas seleccionados del juego

    const modalHTML = `
        <h2>Editar Juego</h2>
        <img src="${game.ruta_imagen}" alt="Game Cover" class="game-cover">
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
            <!-- Campo oculto para los sistemas seleccionados -->
            <input type="hidden" id="selectedSystemsField" name="sistemas" value="">
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

        <!-- Buscador de sistemas fuera del formulario -->
        <div style="margin-bottom: 20px;">
            <p style="text-align: left;">Sistema:</p>
            <div style="position: relative;">
                <input type="text" id="sistemaSearch" 
                        placeholder="Buscar sistema" 
                        oninput="filterSystems()" 
                        onclick="toggleSystemSuggestions(true)" 
                        onfocus="toggleSystemSuggestions(true)" 
                        onblur="hideSystemSuggestions()">
                <div id="sistemaSuggestions" class="suggestions hidden"></div>
            </div>
            <div id="sistemaContainer" class="tag-container">
                <!-- Sistemas seleccionados -->
            </div>
        </div>

        <!-- Botones al final -->
        <div class="button-container">
            <button type="button" onclick="submitGameForm(${game.id})">Guardar</button>
            <button type="button" onclick="deleteGame(${game.id})" class="delete-btn">Eliminar</button>
        </div>
    `;
    openPopout(modalHTML);

    // Actualizar los géneros y sistemas seleccionados al abrir el modal
    updateSelectedGenres();
    updateSelectedSystems();
    filterGenres();
    filterSystems();
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

// ------------------- Funciones de JUEGOS-Generos -------------------
// Función para cargar géneros desde la base de datos
async function loadGenres() {
    try {
        // Hacer la solicitud al back-end
        const response = await fetch("/Games-r-us/public/index.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ accion: "listadoGeneros" }) // Asegúrate de que "listadoGeneros" coincida con tu back-end
        });

        // Verificar si la respuesta es válida
        if (!response.ok) {
            throw new Error(`Error en la solicitud: ${response.statusText}`);
        }

        // Parsear el JSON devuelto por el servidor
        const result = await response.json();

        // Manejar la respuesta
        if (result.success) {
            // Transformar los datos de géneros en un array de nombres, si es necesario
            allGenres = result.data.map(genre => genre.nombre_genero); 
            console.log("Géneros cargados:", allGenres);
        } else {
            console.error("Error al cargar géneros:", result.message);
        }
    } catch (error) {
        // Manejar errores en la solicitud o el procesamiento
        console.error("Error en la solicitud de géneros:", error);
    }
}

// Mostrar lista de géneros
function toggleSuggestions(show) {
    const suggestions = document.getElementById("generoSuggestions");
    if (show) {
        suggestions.classList.remove("hidden");
    } else {
        suggestions.classList.add("hidden");
    }
}

// Ocultar sugerencias
function hideSuggestions() {
    setTimeout(() => toggleSuggestions(false), 200); // Espera para permitir clics en sugerencias
}

// Filtrar generos basados en el texto del buscador
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
// ------------------- Funciones de JUEGOS-Sistemas -------------------
// Función para cargar sistemas desde la base de datos
async function loadSystems() {
    try {
        const response = await fetch("/Games-r-us/public/index.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ accion: "listadoSistemas" }) // Asegúrate de que "listadoSistemas" coincide con tu backend
        });

        if (!response.ok) {
            throw new Error(`Error en la solicitud: ${response.statusText}`);
        }

        const result = await response.json();

        if (result.success) {
            allSystems = result.data.map(system => system.nombre_sistema);
            console.log("Sistemas cargados:", allSystems);
        } else {
            console.error("Error al cargar sistemas:", result.message);
        }
    } catch (error) {
        console.error("Error en la solicitud de sistemas:", error);
    }
}

// Actualizar la lista de sistemas seleccionados
function updateSelectedSystems() {
    const container = document.getElementById("sistemaContainer");
    container.innerHTML = "";

    selectedSystems.forEach((system, index) => {
        const tag = document.createElement("div");
        tag.className = "tag";
        tag.innerHTML = `
            ${system}
            <span class="tag-remove" onclick="removeSystem(${index})">X</span>
        `;
        container.appendChild(tag);
    });

    // Sincronizar los sistemas seleccionados con el campo oculto
    const selectedSystemsField = document.getElementById("selectedSystemsField");
    selectedSystemsField.value = JSON.stringify(selectedSystems);
}

// Filtrar sistemas basados en el texto del buscador
function filterSystems() {
    const search = document.getElementById("sistemaSearch").value.toLowerCase();
    const filteredSystems = allSystems.filter(system => 
        system.toLowerCase().includes(search) && !selectedSystems.includes(system)
    );

    const suggestionsContainer = document.getElementById("sistemaSuggestions");
    suggestionsContainer.innerHTML = ""; // Limpiar sugerencias

    filteredSystems.forEach(system => {
        const suggestion = document.createElement("div");
        suggestion.className = "suggestion-item";
        suggestion.textContent = system;
        suggestion.onclick = () => addSystem(system);
        suggestionsContainer.appendChild(suggestion);
    });
}

function toggleSystemSuggestions(show) {
    const suggestionsContainer = document.getElementById("sistemaSuggestions");
    if (show) {
        suggestionsContainer.classList.remove("hidden");
    } else {
        setTimeout(() => suggestionsContainer.classList.add("hidden"), 200); // Retraso para permitir clics
    }
}

function hideSystemSuggestions() {
    setTimeout(() => {
        const suggestionsContainer = document.getElementById("sistemaSuggestions");
        suggestionsContainer.classList.add("hidden");
    }, 200); // Permitir tiempo para clics en sugerencias
}

// Añadir un sistema a la lista seleccionada
function addSystem(system) {
    if (!selectedSystems.includes(system)) {
        selectedSystems.push(system);
        updateSelectedSystems();
        filterSystems();
    }
}

// Eliminar un sistema de la lista seleccionada
function removeSystem(index) {
    selectedSystems.splice(index, 1);
    updateSelectedSystems();
    filterSystems();
}

// ------------------- Funciones de UI -------------------
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

document.addEventListener("DOMContentLoaded", async () => {
    await loadGenres(); // Cargar géneros desde la base de datos
    await loadSystems(); // Cargar sistemas desde la base de datos
});

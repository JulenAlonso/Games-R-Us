let users = [];
let games = [];
let genres = [];
let systems = [];

// ------------------- Funciones de UI -------------------
// Mostrar la sección seleccionada
function showSection(sectionId) {
  console.log("Mostrando sección:", sectionId);

  // Ocultar todas las secciones y limpiar su contenido
  document.querySelectorAll(".section").forEach((section) => {
    console.log("Ocultando y limpiando sección:", section.id);
    section.classList.add("hidden");
    if (section.id !== sectionId) {
      section.innerHTML = ""; // Limpia el contenido de las secciones no activas
    }
  });

  // Mostrar la sección activa
  const section = document.getElementById(sectionId);
  if (section) {
    console.log("Sección encontrada:", sectionId);
    section.classList.remove("hidden");

    // Cargar los datos necesarios para cada sección
    if (sectionId === "users") loadUsers();
    if (sectionId === "games") loadGames();
    if (sectionId === "genres") loadGenresSection();
    if (sectionId === "systems") loadSystemsSection();
  } else {
    console.error(`No se encontró la sección con ID: ${sectionId}`);
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
      userList.innerHTML = users
        .map(
          (user) => `
                <div class="card" onclick="showUserDetails('${user.nick}')">
                    <img src="${"../src/uploads/image/avatar/" + user.avatar}" alt="Profile">
                    <h3>${user.nick}</h3>
                    <p>${user.email}</p>
                </div>
            `
        )
        .join("");

      userList.innerHTML += `<button id="floatingAddButton" class="floating-add" onclick="NewUserForm()"> + </button>`;
    } else {
      console.error("Error en el servidor:", result.message);
    }
  } catch (error) {
    console.error("Error al cargar usuarios:", error);
  }
}

// Mostrar detalles de un usuario
async function showUserDetails(userNick) {
    console.log("Mostrando detalles del usuario con nick:", userNick);

    // Buscar el usuario correspondiente por nick
    const user = users.find((u) => u.nick === userNick);
    if (!user) return console.error("Usuario no encontrado:", userNick);

    try {
        // Solicitar los roles desde la base de datos
        const response = await fetch("/Games-r-us/public/index.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ accion: "listadoRoles" }), // Acción para obtener los roles
        });

        if (!response.ok) throw new Error("Error al obtener los roles");

        const result = await response.json();
        if (!result.success) {
            console.error("Error al cargar roles:", result.message);
            return;
        }

        const roles = result.data; // Array de roles desde la base de datos

        // Generar las opciones del selector y preseleccionar el rol actual
        const roleOptions = roles
            .map((role) => {
                const isSelected = role.nombre_rol === user.rol ? "selected" : "";
                return `<option value="${role.id_rol}" ${isSelected}>${role.nombre_rol}</option>`;
            })
            .join("");

        // Crear el HTML del formulario
        const formHTML = `
            <h2>Editar Usuario</h2>
            <img src="${"../src/uploads/image/avatar/" + user.avatar}" alt="Profile" class="profile-avatar">
            <form onsubmit="saveUser(event, '${user.nick}')">
                <div>
                    <p>Rol:</p>
                    <select id="editRole">${roleOptions}</select>
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
                    <input type="text" id="editApe1" placeholder="Apellido 1" value="${user.ape1}">
                </div>
                <div>
                    <p>Apellido 2:</p>
                    <input type="text" id="editApe2" placeholder="Apellido 2" value="${user.ape2}">
                </div>
                <div>
                    <p>Teléfono:</p>
                    <input type="text" id="editTlf" placeholder="Teléfono" value="${user.tlf}">
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
    } catch (error) {
        console.error("Error al cargar roles:", error);
        alert("Error al cargar los roles. Inténtalo de nuevo.");
    }
}

// Guardar usuario
async function saveUser(event, userNick) {
  event.preventDefault();
  const user = {
    nick: userNick,
    email: document.getElementById("editEmail").value,
    nombre: document.getElementById("editNombre").value,
    ape1: document.getElementById("editApe1").value,
    ape2: document.getElementById("editApe2").value,
    tlf: document.getElementById("editTlf").value,
    direccion_tipo: document.getElementById("editDir_tipo").value,
    direccion_via: document.getElementById("editDir_via").value,
    direccion_numero: document.getElementById("editDir_numero").value,
    direccion_otros: document.getElementById("editDir_otros").value,
    rol: document.getElementById("editRole").value,
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

async function NewUserForm() {
    try {
        // Solicitar los roles desde la base de datos
        const response = await fetch("/Games-r-us/public/index.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ accion: "listadoRoles" }), // Acción para obtener los roles
        });

        if (!response.ok) throw new Error("Error al obtener los roles");

        const result = await response.json();
        if (!result.success) {
            console.error("Error al cargar roles:", result.message);
            return;
        }

        const roles = result.data; // Array de roles desde la base de datos

        // Generar las opciones del selector
        const roleOptions = roles
            .map((role) => `<option value="${role.id_rol}">${role.nombre_rol}</option>`)
            .join("");

        // Crear el HTML del formulario
        const formHTML = `
            <h2>Añadir Nuevo Usuario</h2>
            <form onsubmit="saveNewUser(event)">
                <div>
                    <p>Rol:</p>
                    <select id="newUserRole">${roleOptions}</select>
                </div>
                <div>
                    <p>Nick:</p>
                    <input type="text" id="newUserNick" placeholder="Nick" required>
                </div>
                <div>
                    <p>Email:</p>
                    <input type="email" id="newUserEmail" placeholder="Email" required>
                </div>
                <div>
                    <p>Nombre:</p>
                    <input type="text" id="newUserNombre" placeholder="Nombre">
                </div>
                <div>
                    <p>Apellido 1:</p>
                    <input type="text" id="newUserApe1" placeholder="Apellido 1">
                </div>
                <div>
                    <p>Apellido 2:</p>
                    <input type="text" id="newUserApe2" placeholder="Apellido 2">
                </div>
                <div>
                    <p>Teléfono:</p>
                    <input type="text" id="newUserTlf" placeholder="Teléfono">
                </div>
                <div>
                    <p>Dirección:</p>
                    <div class="direccion">
                        <input type="text" id="newUserDireccion_tipo" placeholder="Tipo de dirección">
                        <p></p>
                        <input type="text" id="newUserDireccion_via" placeholder="Vía">
                        <p></p>
                        <input type="text" id="newUserDireccion_numero" placeholder="Número">
                        <p></p>
                        <input type="text" id="newUserDireccion_otros" placeholder="Otros detalles">
                    </div>
                </div>
                <br>
                <button type="submit">Guardar</button>
                <button type="button" onclick="closePopout()" class="delete-btn">Cancelar</button>
            </form>
        `;

        openPopout(formHTML);
    } catch (error) {
        console.error("Error al cargar roles:", error);
        alert("Error al cargar los roles. Inténtalo de nuevo.");
    }
}

async function saveNewUser(event) { 
    event.preventDefault();

    // Crear el objeto con los datos del usuario
    const newUser = {
        rol: document.getElementById("newUserRole").value,
        nick: document.getElementById("newUserNick").value.trim(),
        email: document.getElementById("newUserEmail").value.trim(),
    };

    // Validar los campos obligatorios
    if (!newUser.rol || !newUser.nick || !newUser.email) {
        alert("Los campos 'Nick', 'Rol' y 'Email' son obligatorios.");
        return;
    }

    // Agregar campos opcionales si tienen valor
    const optionalFields = [
        "newUserNombre",
        "newUserApe1",
        "newUserApe2",
        "newUserTlf",
        "newUserDireccion_tipo",
        "newUserDireccion_via",
        "newUserDireccion_numero",
        "newUserDireccion_otros",
    ];

    optionalFields.forEach((field) => {
        const value = document.getElementById(field).value.trim();
        if (value) {
            newUser[field.replace("newUser", "").toLowerCase()] = value;
        }
    });

    console.log("Guardando nuevo usuario:", newUser);

    try {
        const response = await fetch("/Games-r-us/public/index.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ accion: "agregarUsuario", ...newUser }),
        });
    
        // Capturar y mostrar la respuesta como texto
        const textResponse = await response.text();
        console.log("Respuesta completa del servidor:", textResponse);
    
        // Procesar como JSON
        const result = JSON.parse(textResponse);
        if (!result.success) {
            if (result.message === "El nick ya está en uso") {
                alert("El nick ingresado ya está en uso. Por favor, elige otro.");
            } else {
                alert("Error al crear el usuario: " + result.message);
            }
            return;
        }
    
        alert("Usuario creado correctamente.");
        closePopout();
        loadUsers(); // Recargar la lista de usuarios
    } catch (error) {
        console.error("Error procesando la respuesta:", error);
        alert("Error al guardar usuario. Inténtalo de nuevo.");
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
      gameList.innerHTML = games
        .map(
          (game) => `
                <div class="card" onclick="showGameDetails(${game.id})">
                    <img src="${
                      game.ruta_imagen ||
                      "https://cdn-icons-png.flaticon.com/512/5260/5260498.png"
                    }" alt="Game Cover">
                    <h3>${game.titulo}</h3>
                </div>
            `
        )
        .join("");

      gameList.innerHTML += `<button id="floatingAddButton" class="floating-add" onclick="newGameForm()"> + </button>`;
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
  const game = games.find((g) => g.id == gameId);
  if (!game) return console.error("Juego no encontrado:", gameId);

  selectedGenres = [...game.genero]; // Inicializar los géneros seleccionados del juego
  selectedSystems = [...game.sistema]; // Inicializar los sistemas seleccionados del juego

  const modalHTML = `
        <h2>Editar Juego</h2>
        <img src="${game.ruta_imagen}" alt="Game Cover" class="game-cover">
        <!-- Formulario de edición -->
        <form onsubmit="saveGame(event, ${game.id})">
            <p>Título:</p>
            <input type="text" id="editTitle" name="titulo" placeholder="Título" value="${game.titulo}" required>
            <p>Desarrollador:</p>
            <input type="text" id="editDeveloper" name="desarrollador" placeholder="Desarrollador" value="${game.desarrollador}">
            <p>Distribuidor:</p>
            <input type="text" id="editDistribuidor" name="distribuidor" placeholder="Distribuidor" value="${game.distribuidor}">
            <p>Año:</p>
            <input type="text" id="editAnio" name="anio" placeholder="Año" value="${game.anio}">
            
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
  // Crear un objeto de juego basado en los valores del formulario
  const game = {
    accion: "editarJuego", // Especificar la acción
    id: gameId,
    titulo: document.getElementById("editTitle").value.trim(),
    desarrollador: document.getElementById("editDeveloper").value.trim(),
    distribuidor: document.getElementById("editDistribuidor").value.trim(),
    anio: document.getElementById("editAnio").value.trim(),
    generos: JSON.stringify(selectedGenres), // Convertir a JSON los géneros seleccionados
    sistemas: JSON.stringify(selectedSystems), // Convertir a JSON los sistemas seleccionados
  };

  // Validar que los campos obligatorios no estén vacíos
  if (!game.titulo || !game.anio) {
    alert("Por favor, completa todos los campos obligatorios.");
    return;
  }

  console.log("Guardando juego:", game); // Mostrar en consola el objeto del juego

  // Llamar a saveGame
  saveGame(game);
}

// Guardar juego
async function saveGame(game) {
  try {
    // Construir los parámetros para enviar al backend
    const response = await fetch("/Games-r-us/public/index.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams(game), // Convertir el objeto a URL-encoded
    });

    const result = await response.json();

    if (result.success) {
      alert("Juego guardado correctamente");
      closePopout();
      loadGames(); // Recargar la lista de juegos
    } else {
      console.error("Error al guardar juego:", result.message);
      if (result.error) {
        console.error("Detalles del error:", result.error);
        alert("Error SQL: " + result.error); // Mostrar error específico del backend
      }
    }
  } catch (error) {
    console.error("Error al guardar juego:", error);
  }
}

//Eliminar Juego
function deleteGame(gameId) {
  // Confirmación antes de eliminar
  if (!confirm("¿Estás seguro de que deseas eliminar este juego?")) {
    return; // Cancelar si el usuario no confirma
  }

  // Enviar la solicitud al backend
  fetch("/Games-r-us/public/index.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: new URLSearchParams({
      accion: "eliminarJuego", // Acción que el backend procesará
      id: gameId, // ID del juego a eliminar
    }),
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        alert("Juego eliminado correctamente.");
        closePopout(); // Cerrar el modal
        loadGames(); // Recargar la lista de juegos
      } else {
        console.error("Error al eliminar el juego:", result.message);
        alert("Error al eliminar el juego: " + result.message);
      }
    })
    .catch((error) => {
      console.error("Error al enviar la solicitud:", error);
      alert("Error al enviar la solicitud.");
    });
}

function newGameForm() {
  const modalHTML = `
    <h2>Añadir Nuevo Juego</h2>

    <!-- Buscador para encontrar juegos en MovieGames -->
    <div style="margin-bottom: 20px;">
        <p style="text-align: left;">Buscar Juego:</p>
        <div style="position: relative;">
            <input type="text" id="gameSearch" 
                  placeholder="Introduce el título del juego a buscar" 
                  oninput="searchGame()" 
                  onclick="toggleSearchResults(true)" 
                  onfocus="toggleSearchResults(true)">
            <div id="searchResults" class="suggestions hidden"></div>
        </div>
    </div>

    <!-- Botón para importar JSON -->
    <div style="margin-bottom: 20px;">
        <p>Importar JSON:</p>
        <input type="file" id="jsonImport" accept=".json" onchange="importJSON(event)">
    </div>

    <form onsubmit="saveNewGame(event)" enctype="multipart/form-data">
        <p>Título:</p>
        <input type="text" id="newGameTitle" name="titulo" placeholder="Introduce el título del juego" required>

        <p>Desarrollador:</p>
        <input type="text" id="newGameDeveloper" name="desarrollador" placeholder="Introduce el desarrollador del juego">

        <p>Distribuidor:</p>
        <input type="text" id="newGameDistributor" name="distribuidor" placeholder="Introduce el distribuidor del juego">

        <p>Año:</p>
        <input type="text" id="newGameYear" name="anio" placeholder="Introduce el año de lanzamiento">

        <p>Portada:</p>
        <input type="file" id="newGameCover" name="portada" accept="image/*" required>

        <p>Archivo ZIP del Juego:</p>
        <input type="file" id="newGameZip" name="archivo" accept=".zip" required>

        <!-- Campos ocultos para sincronizar géneros y sistemas seleccionados -->
        <input type="hidden" id="selectedGenresField" name="generos" value="">
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
        <button type="button" onclick="saveNewGame()">Guardar</button>
        <button type="button" onclick="closePopout()" class="delete-btn">Cancelar</button>
    </div>
  `;

  openPopout(modalHTML);
}

async function saveNewGame() {
  // Crear el objeto con los datos del juego
  const newGame = {
      titulo: document.getElementById("newGameTitle").value.trim(),
      desarrollador: document.getElementById("newGameDeveloper").value.trim(),
      distribuidor: document.getElementById("newGameDistributor").value.trim(),
      anio: document.getElementById("newGameYear").value.trim(),
      generos: JSON.stringify(selectedGenres), // Géneros seleccionados
      sistemas: JSON.stringify(selectedSystems), // Sistemas seleccionados
      accion: "agregarJuego", // Acción para identificar en el servidor
  };

  // Validar los campos obligatorios
  if (!newGame.titulo || !document.getElementById("newGameCover").files[0] || !document.getElementById("newGameZip").files[0]) {
      alert("Los campos 'Título', 'Portada' y 'Archivo ZIP' son obligatorios.");
      return;
  }

  // Crear un objeto FormData para manejar los datos y los archivos
  const formData = new FormData();

  // Añadir los datos al FormData
  for (const [key, value] of Object.entries(newGame)) {
      formData.append(key, value);
  }

  // Añadir los archivos al FormData
  formData.append("portada", document.getElementById("newGameCover").files[0]); // Archivo de portada
  formData.append("archivo", document.getElementById("newGameZip").files[0]);   // Archivo ZIP del juego

  console.log("Guardando nuevo juego con FormData:", formData);

  try {
      const response = await fetch("/Games-r-us/public/index.php", {
          method: "POST",
          body: formData, // Incluye FormData con datos y archivos
      });

      // Capturar y mostrar la respuesta como texto
      const textResponse = await response.text();
      console.log("Respuesta completa del servidor:", textResponse);

      // Procesar como JSON
      const result = JSON.parse(textResponse);
      if (!result.success) {
          alert("Error al crear el juego: " + result.message);
          return;
      }

      alert("Juego creado correctamente.");
      closePopout();
      loadGames(); // Recargar la lista de juegos
  } catch (error) {
      console.error("Error procesando la respuesta:", error);
      alert("Error al guardar el juego. Inténtalo de nuevo.");
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
      body: new URLSearchParams({ accion: "listadoGeneros" }), // Asegúrate de que "listadoGeneros" coincida con tu back-end
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
      allGenres = result.data.map((genre) => genre.nombre_genero);
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
  const filteredGenres = allGenres.filter(
    (genre) =>
      genre.toLowerCase().includes(search) && !selectedGenres.includes(genre)
  );
  const suggestionsContainer = document.getElementById("generoSuggestions");

  suggestionsContainer.innerHTML = ""; // Limpiar sugerencias

  filteredGenres.forEach((genre) => {
    const suggestion = document.createElement("div");
    suggestion.className = "suggestion-item";
    suggestion.textContent = genre;
    suggestion.onclick = () => toggleGenre(genre);
    suggestionsContainer.appendChild(suggestion);
  });
}

function toggleGenre(genre) {
  if (selectedGenres.includes(genre)) {
    selectedGenres = selectedGenres.filter((g) => g !== genre);
  } else {
    selectedGenres.push(genre);
  }
  updateSelectedGenres();
  filterGenres(); // Actualizar sugerencias
}

function updateSelectedGenres() {
  const container = document.getElementById("generoContainer");
  container.innerHTML = ""; // Limpiar el contenedor

  selectedGenres.forEach((genre) => {
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
      body: new URLSearchParams({ accion: "listadoSistemas" }), // Asegúrate de que "listadoSistemas" coincide con tu backend
    });

    if (!response.ok) {
      throw new Error(`Error en la solicitud: ${response.statusText}`);
    }

    const result = await response.json();

    if (result.success) {
      allSystems = result.data.map((system) => system.nombre_sistema);
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
  const filteredSystems = allSystems.filter(
    (system) =>
      system.toLowerCase().includes(search) && !selectedSystems.includes(system)
  );

  const suggestionsContainer = document.getElementById("sistemaSuggestions");
  suggestionsContainer.innerHTML = ""; // Limpiar sugerencias

  filteredSystems.forEach((system) => {
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

document.addEventListener("DOMContentLoaded", async () => {
  await loadGenres(); // Cargar géneros desde la base de datos
  await loadSystems(); // Cargar sistemas desde la base de datos
});

// ------------------- Funciones de GENEROS --------------------
async function loadGenresSection() {
  console.log("Cargando géneros...");
  try {
    const response = await fetch("/Games-r-us/public/index.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({ accion: "listadoGeneros" }), // Acción para obtener los géneros
    });

    if (!response.ok) throw new Error("Error al obtener los datos");

    const result = await response.json();
    if (result.success) {
      genres = result.data; // Guardar los géneros en la variable global
      const genreList = document.getElementById("genreList"); // Contenedor donde se mostrarán los géneros

      console.log(genres);
      // Mapear y generar las tarjetas de los géneros
      genreList.innerHTML = genres
        .map(
          (genre) => `
                <div class="card" onclick="showGenreDetails(${genre.id})">
                    <h3>${genre.nombre_genero}</h3>
                </div>
            `
        )
        .join("");

      genreList.innerHTML += `<button id="floatingAddButton" class="floating-add" onclick="newGenero()"> + </button>`;
    } else {
      console.error("Error en el servidor:", result.message);
    }
  } catch (error) {
    console.error("Error al cargar géneros:", error);
  }
}

function showGenreDetails(genreId) {
  console.log("Cargando detalles del género con ID:", genreId);

  // Buscar el género correspondiente por su ID
  console.log(genres);
  const genre = genres.find((g) => g.id == genreId);
  if (!genre) return console.error("Género no encontrado:", genreId);

  // Crear el contenido del modal
  const modalHTML = `
        <h2>Editar Género</h2>
        <form onsubmit="saveGenre(event, ${genre.id})">
            <p>Nombre:</p>
            <input type="text" id="editGenreName" name="nombre_genero" placeholder="Nombre del género" value="${genre.nombre_genero}" required>
            <br><br>
            <button type="submit">Guardar</button>
            <button type="button" onclick="deleteGenre(${genre.id})" class="delete-btn">Eliminar</button>
        </form>
    `;

  openPopout(modalHTML); // Abrir el popout con el contenido generado
}

// Guardar género editado
async function saveGenre(event, genreId) {
  event.preventDefault();
  const nombre_genero = document.getElementById("editGenreName").value.trim();

  if (!nombre_genero) {
    alert("El nombre del género no puede estar vacío.");
    return;
  }

  console.log("Guardando género:", { id: genreId, nombre_genero });

  try {
    const response = await fetch("/Games-r-us/public/index.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({
        accion: "editarGenero",
        id: genreId,
        nombre_genero,
      }),
    });

    const result = await response.json();
    if (result.success) {
      alert("Género actualizado correctamente.");
      closePopout();
      loadGenresSection(); // Recargar la lista de géneros
    } else {
      console.error("Error al actualizar el género:", result.message);
    }
  } catch (error) {
    console.error("Error al guardar el género:", error);
  }
}

// Eliminar género
async function deleteGenre(genreId) {
  if (!confirm("¿Estás seguro de que deseas eliminar este género?")) return;

  try {
    const response = await fetch("/Games-r-us/public/index.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({
        accion: "eliminarGenero", // Acción específica para eliminar géneros
        id: genreId,
      }),
    });

    const result = await response.json();
    if (result.success) {
      alert("Género eliminado correctamente.");
      closePopout();
      loadGenresSection(); // Recargar la lista de géneros
    } else {
      console.error("Error al eliminar el género:", result.message);
    }
  } catch (error) {
    console.error("Error al eliminar el género:", error);
  }
}

function newGenero() {
  const modalHTML = `
      <h2>Añadir Nuevo Género</h2>
      <form onsubmit="saveNewGenero(event)">
        <div>
          <p>Nombre:</p>
          <input type="text" id="newGeneroName" placeholder="Introduce el nombre del género" required>
        </div>
        <br>
        <button type="submit">Guardar</button>
      </form>
    `;

  openPopout(modalHTML);
}

async function saveNewGenero(event) {
  event.preventDefault();

  // Obtener el valor del campo de entrada
  const generoName = document.getElementById("newGeneroName").value.trim();

  // Validar que el campo no esté vacío
  if (!generoName) {
    alert("El nombre del género no puede estar vacío.");
    return;
  }

  try {
    // Enviar el género al backend
    const response = await fetch("/Games-r-us/public/index.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({
        accion: "crearGenero",
        nombre_genero: generoName,
      }),
    });

    const result = await response.json();

    if (result.success) {
      alert("Género añadido correctamente");
      closePopout();
      loadGenresSection(); // Recargar la lista de géneros
    } else {
      console.error("Error al guardar el género:", result.message);
      alert("Error: " + result.message);
    }
  } catch (error) {
    console.error("Error al enviar la solicitud:", error);
    alert("Error al añadir el género.");
  }
}

// ------------------- Funciones de SISTEMAS -------------------
async function loadSystemsSection() {
  console.log("Cargando sistemas...");
  try {
    const response = await fetch("/Games-r-us/public/index.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({ accion: "listadoSistemas" }), // Acción para obtener los sistemas
    });

    if (!response.ok) throw new Error("Error al obtener los datos");

    const result = await response.json();
    if (result.success) {
      systems = result.data; // Guardar los sistemas en la variable global
      const systemList = document.getElementById("systemList"); // Contenedor donde se mostrarán los sistemas

      // Mapear y generar las tarjetas de los sistemas
      systemList.innerHTML = systems
        .map(
          (system) => `
                <div class="card" onclick="showSystemDetails(${system.id_sistema})">
                    <h3>${system.nombre_sistema}</h3>
                </div>
            `
        )
        .join("");

      systemList.innerHTML += `<button id="floatingAddButton" class="floating-add" onclick="newSistema()"> + </button>`;
    } else {
      console.error("Error en el servidor:", result.message);
    }
  } catch (error) {
    console.error("Error al cargar sistemas:", error);
  }
}

function showSystemDetails(systemId) {
  console.log("Cargando detalles del sistema con ID:", systemId);

  // Buscar el sistema correspondiente por su ID
  const system = systems.find((s) => s.id_sistema == systemId);
  if (!system) return console.error("Sistema no encontrado:", systemId);

  // Crear el contenido del modal
  const modalHTML = `
        <h2>Editar Sistema</h2>
        <form onsubmit="saveSystem(event, ${system.id_sistema})">
            <p>Nombre:</p>
            <input type="text" id="editSystemName" name="nombre_sistema" placeholder="Nombre del sistema" value="${system.nombre_sistema}" required>
            <br><br>
            <button type="submit">Guardar</button>
            <button type="button" onclick="deleteSystem(${system.id_sistema})" class="delete-btn">Eliminar</button>
        </form>
    `;

  openPopout(modalHTML); // Abrir el popout con el contenido generado
}

// Guardar sistema editado
async function saveSystem(event, systemId) {
  event.preventDefault();
  const nombre_sistema = document.getElementById("editSystemName").value.trim();

  if (!nombre_sistema) {
    alert("El nombre del sistema no puede estar vacío.");
    return;
  }

  console.log("Guardando sistema:", { id: systemId, nombre_sistema });

  try {
    const response = await fetch("/Games-r-us/public/index.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({
        accion: "editarSistema", // Acción específica para editar sistemas
        id: systemId,
        nombre_sistema,
      }),
    });

    const result = await response.json();
    if (result.success) {
      alert("Sistema actualizado correctamente.");
      closePopout();
      loadSystemsSection(); // Recargar la lista de sistemas
    } else {
      console.error("Error al actualizar el sistema:", result.message);
    }
  } catch (error) {
    console.error("Error al guardar el sistema:", error);
  }
}

// Eliminar sistema
async function deleteSystem(systemId) {
  if (!confirm("¿Estás seguro de que deseas eliminar este sistema?")) return;

  try {
    const response = await fetch("/Games-r-us/public/index.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({
        accion: "eliminarSistema", // Acción específica para eliminar sistemas
        id: systemId,
      }),
    });

    const result = await response.json();
    if (result.success) {
      alert("Sistema eliminado correctamente.");
      closePopout();
      loadSystemsSection(); // Recargar la lista de sistemas
    } else {
      console.error("Error al eliminar el sistema:", result.message);
    }
  } catch (error) {
    console.error("Error al eliminar el sistema:", error);
  }
}

function newSistema() {
  const modalHTML = `
      <h2>Añadir Nuevo Sistema</h2>
      <form onsubmit="saveNewSistema(event)">
        <div>
          <p>Nombre:</p>
          <input type="text" id="newSistemaName" placeholder="Introduce el nombre del sistema" required>
        </div>
        <br>
        <button type="submit">Guardar</button>
      </form>
    `;

  openPopout(modalHTML);
}

async function saveNewSistema(event) {
  event.preventDefault();

  // Obtener el valor del campo de entrada
  const sistemaName = document.getElementById("newSistemaName").value.trim();

  // Validar que el campo no esté vacío
  if (!sistemaName) {
    alert("El nombre del sistema no puede estar vacío.");
    return;
  }

  try {
    // Enviar el sistema al backend
    const response = await fetch("/Games-r-us/public/index.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({
        accion: "crearSistema",
        nombre_sistema: sistemaName,
      }),
    });

    const result = await response.json();

    if (result.success) {
      alert("Sistema añadido correctamente");
      closePopout();
      loadSystemsSection(); // Recargar la lista de sistemas
    } else {
      console.error("Error al guardar el sistema:", result.message);
      alert("Error: " + result.message);
    }
  } catch (error) {
    console.error("Error al enviar la solicitud:", error);
    alert("Error al añadir el sistema.");
  }
}

// Función para buscar juegos en el API de MovieGames mediante POST
async function searchGame() {
  const query = document.getElementById("gameSearch").value.trim();
  if (query.length < 3) return;

  try {
      // Crear los datos del cuerpo de la solicitud
      const formData = new FormData();
      formData.append("accion", "buscarJuego");
      formData.append("title", query);

      // Enviar solicitud POST al backend
      const response = await fetch("/Games-r-us/public/index.php", {
          method: "POST",
          body: formData
      });

      const data = await response.json();
      console.log("Resultados de la búsqueda:", data);

      const resultsContainer = document.getElementById("searchResults");
      resultsContainer.innerHTML = "";

      if (!data.success || data.data.length === 0) {
          resultsContainer.innerHTML = "<p>No se encontraron juegos.</p>";
          return;
      }

      // Mostrar los juegos encontrados
      data.data.forEach((game) => {
          const gameItem = document.createElement("div");
          gameItem.classList.add("suggestion-item");
          gameItem.innerHTML = `
              <img src="${game.portada}" alt="${game.titulo}" width="50">
              <span>${game.titulo} (${game.plataformas.join(", ")})</span>
          `;
          gameItem.onclick = () => fillGameForm(game);
          resultsContainer.appendChild(gameItem);
      });

      resultsContainer.classList.remove("hidden");
  } catch (error) {
      console.error("Error al buscar juegos:", error);
  }
}

// Función para rellenar el formulario con los datos del juego seleccionado
function fillGameForm(game) {
  document.getElementById("newGameTitle").value = game.titulo;
  document.getElementById("newGameDeveloper").value = game.desarrollador || "";
  document.getElementById("newGameDistributor").value = game.distribuidor || "";
  document.getElementById("newGameYear").value = game.anio || "";

  // Ocultar los resultados de búsqueda
  document.getElementById("searchResults").classList.add("hidden");
}

// Función para mostrar u ocultar la lista de sugerencias
function toggleSearchResults(show) {
  const resultsContainer = document.getElementById("searchResults");
  if (show) {
      resultsContainer.classList.remove("hidden");
  } else {
      setTimeout(() => resultsContainer.classList.add("hidden"), 200);
  }
}

function importJSON(event) {
  const file = event.target.files[0]; // Obtener el archivo seleccionado

  if (!file) return;

  const reader = new FileReader();
  reader.onload = function (e) {
      try {
          const jsonData = e.target.result; // Obtener el contenido del JSON como string
          console.log("Datos importados:", jsonData);

          // Crear un objeto FormData para enviar al backend
          const formData = new FormData();
          formData.append("accion", "importarJuegoJSON"); // Acción para el backend
          formData.append("jsonData", jsonData); // Enviar el JSON como string

          // Enviar al backend con Fetch API
          fetch("/Games-r-us/public/index.php", {
              method: "POST",
              body: formData,
          })
          .then(response => response.json())
          .then(result => {
              if (result.success) {
                  closePopout();
                  loadGames(); // Recargar la lista de juegos
              } else {
                  alert("Error al importar el juego: " + result.message);
              }
          })
          .catch(error => {
              console.error("Error en la importación:", error);
          });

      } catch (error) {
          console.error("Error al leer el JSON:", error);
          alert("Error al leer el archivo JSON.");
      }
  };

  reader.readAsText(file); // Leer el archivo JSON como texto
}
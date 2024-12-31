let data = [];
let data2 = [];

async function UserData() {
  try {
    const response = await fetch("/Games-r-us/public/index.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        accion: "listadoUsers",
      }),
    });

    if (!response.ok) {
      throw new Error("Error al obtener los datos");
    }

    const result = await response.json();
    if (result.success) {
      data = result.data;
      loadUsers();
    } else {
      throw new Error("Error en la respuesta del servidor");
    }
  } catch (error) {
    console.error("Error al realizar la solicitud:", error);
  }
}

async function GameData() {
  try {
    const response = await fetch('/Games-r-us/public/index.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({ accion: 'listadoJuegos' }),
    });

    if (!response.ok) {
        throw new Error("Error al obtener los datos");
    }

    const result = await response.json();
    if (result.success) {
        data2 = result.data;
        loadGames();
    } else {
        console.error("Error en el servidor:", result.message);
    }
  } catch (error) {
    console.error("Error al realizar la solicitud:", error);
  }
}

function loadUsers() {
  console.log("Loading users...");
  const users = data;

  document.getElementById("gameList").classList.add("hidden");
  document.getElementById('userForm').classList.add('hidden');
  document.getElementById('gameForm').classList.add('hidden')
  document.getElementById("userList").classList.remove("hidden");
;

  const tableBody = document.getElementById("userTableBody");
  tableBody.innerHTML = "";
  users.forEach((user) => {
    const row = `
      <tr data-id="${user.id}">
          <td><input type="text" value="${user.nick}" id="nick-${user.id}"></td>
          <td><input type="email" value="${user.email}" id="email-${user.id}"></td>
          <td><input type="text" value="${user.nombre}" id="nombre-${user.id}"></td>
          <td><input type="text" value="${user.ape1}" id="ape1-${user.id}"></td>
          <td><input type="text" value="${user.ape2}" id="ape2-${user.id}"></td>
          <td><input type="text" value="${user.tlf}" id="tlf-${user.id}"></td>
          <td><input type="text" value="${user.direccion}" id="direccion-${user.id}"></td>
          <td><input type="number" value="${user.rol}" id="rol-${user.id}"></td>
          <td><button onclick="saveUser(${user.id})">Save</button></td>
          <td><button onclick="deleteUser(${user.id})">Delete</button></td>
      </tr>`;
    tableBody.innerHTML += row;
  });
}

function loadGames() {
  console.log("Loading games...");
  const games = data2;
  document.getElementById("userList").classList.add("hidden");
  document.getElementById('userForm').classList.add('hidden');
  document.getElementById('gameForm').classList.add('hidden');
  document.getElementById("gameList").classList.remove("hidden");

  const tableBody = document.getElementById("gameTableBody");
  tableBody.innerHTML = "";
  games.forEach((game) => {
    const row = `
      <tr data-id="${game.id}">
          <td><input type="text" value="${game.titulo}" id="titulo-${game.id}"></td>
          <td><input type="text" value="${game.desarrollador}" id="desarrollador-${game.id}"></td>
          <td><input type="text" value="${game.distribuidor}" id="distribuidor-${game.id}"></td>
          <td><input type="text" value="${game.anio}" id="anio-${game.id}"></td>
          <td><input type="text" value="${game.genero.join(', ')}" id="genero-${game.id}"></td>
          <td><input type="text" value="${game.sistema.join(', ')}" id="sistema-${game.id}"></td>
          <td><button onclick="saveGame(${game.id})">Save</button></td>
          <td><button onclick="deleteGame(${game.id})">Delete</button></td>
      </tr>`;
    tableBody.innerHTML += row;
  });
}

async function saveUser(userId) {
  // Obtener los datos del formulario
  const user = {
    nick: document.getElementById(`nick-${userId}`).value,
    email: document.getElementById(`email-${userId}`).value,
    nombre: document.getElementById(`nombre-${userId}`).value,
    ape1: document.getElementById(`ape1-${userId}`).value,
    ape2: document.getElementById(`ape2-${userId}`).value,
    tlf: document.getElementById(`tlf-${userId}`).value,
    direccion: document.getElementById(`direccion-${userId}`).value,
    rol: document.getElementById(`rol-${userId}`).value,
  };

  // Convertir los valores 'Desconocido' a NULL
  if (user.nombre === 'Desconocido') {
    user.nombre = null;
  }
  if (user.ape1 === 'Desconocido') {
    user.ape1 = null;
  }
  if (user.ape2 === 'Desconocido') {
    user.ape2 = null;
  }
  if (user.tlf === 'Desconocido') {
    user.tlf = null;
  }
  // Si es necesario también puedes manejar la dirección o el rol
  if (user.direccion === 'Desconocido') {
    user.direccion = null;
  }
  if (user.rol === 'Desconocido') {
    user.rol = null;
  }

  try {
    console.log('Saving user:', user);  // Verifica que los datos se están enviando correctamente

    // Enviar los datos al backend
    const response = await fetch('/Games-r-us/public/index.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({
        accion: 'editarUsuario',
        ...user,
      }),
    });

    // Verifica si la respuesta fue exitosa
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }

    // Capturar el contenido de la respuesta como texto para diagnosticar el problema
    const text = await response.text(); // Obtén la respuesta como texto
    console.log("Response text:", text); // Imprime el contenido de la respuesta

    // Intentar convertirlo a JSON
    const result = JSON.parse(text); // Usamos JSON.parse para ver el contenido antes de asumir que es JSON

    if (result.success) {
      alert('User updated successfully!');
    } else {
      console.error("Error updating user:", result.message);
      alert('Error updating user: ' + result.message);
    }
  } catch (error) {
    console.error("Error during the request:", error);
    alert('There was an error while updating the user: ' + error.message);  // Mensaje de error al usuario
  }
}

async function saveGame(gameId) {
  const game = {
    id: gameId,
    titulo: document.getElementById(`titulo-${gameId}`).value,
    desarrollador: document.getElementById(`desarrollador-${gameId}`).value,
    distribuidor: document.getElementById(`distribuidor-${gameId}`).value,
    anio: document.getElementById(`anio-${gameId}`).value,
    genero: document.getElementById(`genero-${gameId}`).value.split(',').map(s => s.trim()),
    sistema: document.getElementById(`sistema-${gameId}`).value.split(',').map(s => s.trim()),
  };

  console.log('Saving game:', game);
  
  try {
    const response = await fetch('/Games-r-us/public/index.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({
        accion: 'editarJuego',
        ...game,
      }),
    });

    const result = await response.json();
    if (result.success) {
      alert('Game updated successfully!');
    } else {
      console.error("Error updating game:", result.message);
    }
  } catch (error) {
    console.error("Error during the request:", error);
  }
}

function showUserForm() {
  document.getElementById('gameForm').classList.add('hidden');
  document.getElementById("userList").classList.add("hidden");
  document.getElementById("gameList").classList.add("hidden");
  document.getElementById('userForm').classList.remove('hidden');
}

function showGameForm() {
  document.getElementById('userForm').classList.add('hidden');
  document.getElementById("userList").classList.add("hidden");
  document.getElementById("gameList").classList.add("hidden");
  document.getElementById('gameForm').classList.remove('hidden');
}

async function addUser(event) {
  event.preventDefault(); // Prevenir el comportamiento por defecto del formulario

  const user = {
    nick: document.getElementById('nick').value,
    email: document.getElementById('email').value,
    nombre: document.getElementById('nombre').value,
    pass: document.getElementById('pass').value,
    ape1: document.getElementById('ape1').value,
    ape2: document.getElementById('ape2').value,
    tlf: document.getElementById('tlf').value,
    direccion: document.getElementById('direccion').value,
    rol: document.getElementById('rol').value,
  };

  try {
    const response = await fetch('/Games-r-us/public/index.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({
        accion: 'agregarUsuario',
        ...user,
      }),
    });

    const text = await response.text(); // Obtén la respuesta como texto
    console.log("Response text:", text); // Imprime el contenido de la respuesta

    // Si el texto contiene JSON, intenta analizarlo
    try {
      const result = JSON.parse(text);
      if (result.success) {
        alert('User added successfully!');
        document.getElementById('addUserForm').reset();
        showUserForm();
        UserData(); // Reload the users after adding
      } else {
        alert('Error adding user: ' + result.message);
      }
    } catch (jsonError) {
      console.error("Error parsing JSON:", jsonError);
      console.error("Response was not valid JSON.");
    }

  } catch (error) {
    console.error("Error during the request:", error);
  }
}


// Agregar un juego
async function addGame(event) {
  event.preventDefault(); // Prevenir el comportamiento por defecto del formulario

  const game = new FormData();
  game.append('accion', 'agregarJuego');
  game.append('titulo', document.getElementById('titulo').value);
  game.append('desarrollador', document.getElementById('desarrollador').value);
  game.append('distribuidor', document.getElementById('distribuidor').value);
  game.append('anio', document.getElementById('anio').value);
  game.append('genero', document.getElementById('genero').value);
  game.append('sistema', document.getElementById('sistema').value);

  // Añadir archivos al FormData
  game.append('coverImage', document.getElementById('coverImage').files[0]);
  game.append('gameZip', document.getElementById('gameZip').files[0]);

  try {
    const response = await fetch('/Games-r-us/public/index.php', {
      method: 'POST',
      body: game, // Usamos FormData directamente
    });

    const text = await response.text(); // Captura la respuesta como texto
    console.log("Response text:", text); // Imprime el texto de la respuesta

    // Si el texto es vacío o no se pudo convertir en JSON, lanzamos un error
    if (!text) {
      throw new Error('Empty response from server');
    }

    const result = JSON.parse(text); // Intentamos convertir la respuesta en JSON

    if (result.success) {
      alert('Game added successfully!');
      document.getElementById('addGameForm').reset();
      showGameForm();
      GameData(); // Reload the games after adding
    } else {
      alert('Error adding game: ' + result.message);
    }
  } catch (error) {
    console.error("Error during the request:", error);
    alert('There was an error while adding the game: ' + error.message);  // Mensaje de error al usuario
  }
}

async function deleteUser(userId) {
  const user = {
    nick: document.getElementById(`nick-${userId}`).value,
  };

  try {
    // Enviar los datos al backend
    const response = await fetch('/Games-r-us/public/index.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({
        accion: 'eliminarUsuario',
        ...user,
      }),
    });

    // Capturar el contenido de la respuesta como texto para diagnosticar el problema
    const text = await response.text(); // Obtén la respuesta como texto
    console.log("Response text:", text); // Imprime el contenido de la respuesta

    // Intentar convertirlo a JSON
    const result = JSON.parse(text); // Usamos JSON.parse para ver el contenido antes de asumir que es JSON

    if (result.success) {
      alert('User deleted successfully!');
      UserData();
    } else {
      console.error("Error deleting user:", result.message);
      alert('Error deleting user: ' + result.message);
    }
  } catch (error) {
    console.error("Error during the request:", error);
    alert('There was an error while updating the user: ' + error.message);  // Mensaje de error al usuario
  }
}

async function deleteGame(gameId) {

  const game = {
    id: gameId, 
  };

  try {
    const response = await fetch('/Games-r-us/public/index.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({
        accion: 'eliminarJuego',
        ...game,
      }),
    });

    const text = await response.text();
    console.log("Response text:", text);

    const result = JSON.parse(text);

    if (result.success) {
      alert('Game deleted successfully!');
      GameData(); // Recargar los juegos después de eliminar
    } else {
      console.error("Error deleting game:", result.message);
      alert('Error deleting game: ' + result.message);
    }
  } catch (error) {
    console.error("Error during the request:", error);
    alert('There was an error while deleting the game: ' + error.message);
  }
}

let data = [];
let data2 = [];
async function UserData() {
  try {
    const response = await fetch("/Games-r-us/public/index.php", {
      method: "POST", // Cambiamos el método a POST
      headers: {
        "Content-Type": "application/x-www-form-urlencoded", // Establecemos el tipo de contenido
      },
      body: new URLSearchParams({
        accion: "listadoUsers",
      }), // Enviamos el parámetro 'accion'
    });

    if (!response.ok) {
      throw new Error("Error al obtener los datos");
    }

    const result = await response.json();
    if (result.success) {
      data = result.data; // Asignamos los datos obtenidos al array data
      console.log(data);
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
          console.log(data2);
          loadGames()
      } else {
          console.error("Error en el servidor:", result.message);
      }
  } catch (error) {
      console.error("Error al realizar la solicitud:", error);
  }
}

function loadUsers() {
  const users = data;
  const gameList = document.getElementById("gameList");
  gameList.classList.add("hidden");
  const userList = document.getElementById("userList");
  userList.classList.remove("hidden");

  const tableBody = document.getElementById("userTableBody");
  tableBody.innerHTML = "";
  users.forEach((user) => {
    const row = `
      <tr>
          <td>${user.nick}</td>
          <td>${user.email}</td>
          <td>${user.nombre}</td>
          <td>${user.ape1}</td>
          <td>${user.ape2}</td>
          <td>${user.tlf}</td>
          <td>${user.direccion}</td>
          <td>${user.rol}</td>
      </tr>`;
    tableBody.innerHTML += row;
  });
}

function loadGames() {
  const games = data2;
  const userList = document.getElementById("userList");
  userList.classList.add("hidden");
  const gameList = document.getElementById("gameList");
  gameList.classList.remove("hidden");

  const tableBody = document.getElementById("gameTableBody");
  tableBody.innerHTML = "";
  games.forEach((game) => {
    const row = `
      <tr>
          <td>${game.titulo}</td>
          <td>${game.desarrollador}</td>
          <td>${game.distribuidor}</td>
          <td>${game.anio}</td>
          <td>${game.genero.join(', ')}</td>
          <td>${game.sistema.join(', ')}</td>
      </tr>`;
    tableBody.innerHTML += row;
  });
}
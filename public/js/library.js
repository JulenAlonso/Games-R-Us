async function fetchLibraryGames(nick) {
    try {
        const response = await fetch('/Games-r-us/public/index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({ accion: 'listadoBiblioteca', nick: nick }),
        });

        if (!response.ok) {
            throw new Error("Error al obtener los juegos de la biblioteca");
        }

        const result = await response.json();

        if (result.success) {
            renderLibrary(result.data);
        } else {
            console.error("Error en el servidor:", result.message);
            document.querySelector('.game-list').innerHTML = `<p class="error-message">${result.message}</p>`;
            document.getElementById('gameGallery').innerHTML = `<p class="error-message">${result.message}</p>`;
        }
    } catch (error) {
        console.error("Error al obtener la biblioteca:", error);
    }
}

function renderLibrary(games) {
    const gameList = document.querySelector('.game-list');
    const gameGallery = document.querySelector('#gameGallery .gallery');
    gameList.innerHTML = "";
    gameGallery.innerHTML = "";

    if (games.length === 0) {
        gameList.innerHTML = "<p class='error-message'>No tienes juegos en tu biblioteca.</p>";
        gameGallery.innerHTML = "<p class='error-message'>No tienes juegos en tu biblioteca.</p>";
        return;
    }

    games.forEach(game => {
        // Crear elemento en la barra lateral
        const listItem = document.createElement("li");
        listItem.classList.add("game-item");
        listItem.innerHTML = `<img src="${game.ruta_imagen}" alt="${game.titulo}" class="thumbnail"> ${game.titulo}`;
        listItem.onclick = () => showGameDetails(game);
        gameList.appendChild(listItem);

        // Crear elemento en la galería central
        const galleryItem = document.createElement("div");
        galleryItem.classList.add("gallery-item");
        galleryItem.innerHTML = `<img src="${game.ruta_imagen}" alt="${game.titulo}"><p>${game.titulo}</p>`;
        galleryItem.onclick = () => showGameDetails(game);
        gameGallery.appendChild(galleryItem);
    });
}

function showGameDetails(game) {
    const gameGallery = document.getElementById("gameGallery");
    const gameDetails = document.getElementById("gameDetails");

    // Ocultar la galería y mostrar los detalles del juego
    gameGallery.classList.add("hidden");
    gameDetails.classList.remove("hidden");

    // Insertar la información del juego
    gameDetails.innerHTML = `
      <h2>${game.titulo}</h2>
      <img src="${game.ruta_imagen}" alt="${game.titulo}" class="detail-image">
      <p><strong>Desarrollador:</strong> ${game.desarrollador || "Desconocido"}</p>
      <p><strong>Distribuidor:</strong> ${game.distribuidor || "Desconocido"}</p>
      <p><strong>Año:</strong> ${game.anio || "Desconocido"}</p>
      <p><strong>Género:</strong> ${game.genero ? game.genero.join(", ") : "No disponible"}</p>
      <p><strong>Plataformas:</strong> ${game.sistema ? game.sistema.join(", ") : "No disponible"}</p>

      <div class="game-actions">
          <button class="play-btn" onclick="playGame('${game.id}')">Jugar</button>
          <button class="gift-btn" onclick="openGiftPopup('${game.id}')">Prestar</button>
      </div>
  `;
}


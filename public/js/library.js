function showGameDetails(game) {
    document.getElementById("gameGallery").classList.add("hidden");
    document.getElementById("gameDetails").classList.remove("hidden");
    document.getElementById("gameTitle").innerText = game;
    document.getElementById("gameDescription").innerText = `Detalles del juego: ${game}`;
  }
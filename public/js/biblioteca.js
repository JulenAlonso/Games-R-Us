function showDetails(gameTitle) {
    // Update details section
    const gallery = document.getElementById("gallery");
    const details = document.getElementById("details");
  
    gallery.classList.add("hidden");
    details.classList.remove("hidden");
  
    document.getElementById("game-title").textContent = gameTitle;
    document.getElementById("game-description").textContent =
      `Details about ${gameTitle}. This game is amazing!`;
}
  
  function navigateTo(section) {
    alert(`Navigating to ${section}`); // Placeholder
  }

  
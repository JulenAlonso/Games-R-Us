// Importa GSAP si es necesario
const gsapAnimation = () => {
    // Animación simple para la tarjeta de login
    gsap.from(".login-card", {
      y: -50,
      opacity: 0,
      duration: 1,
      ease: "sine.inOut",
    });
  };
  
  // Llama la función de animación
  gsapAnimation();
  
  document.addEventListener("DOMContentLoaded", () => {
    // Ejemplo: Animación de entrada para el navbar
    gsap.from("nav", {
      y: -50,
      opacity: 0,
      duration: 1,
      ease: "power3.out",
    });
  });
  
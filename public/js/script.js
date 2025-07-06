
  document.addEventListener("DOMContentLoaded", function () {
    const dropdown = document.querySelector("#accountDropdown");
    const dropdownToggle = dropdown.querySelector(".dropdown-toggle");

    if (window.innerWidth >= 992) {
      // Grand écran : hover active le menu
      dropdown.addEventListener("mouseenter", () => {
        dropdown.classList.add("show");
        dropdown.querySelector(".dropdown-menu").classList.add("show");
      });

      dropdown.addEventListener("mouseleave", () => {
        dropdown.classList.remove("show");
        dropdown.querySelector(".dropdown-menu").classList.remove("show");
      });
    } else {
      // Mobile : clic natif Bootstrap, pas besoin d'ajouter quoi que ce soit
    }
  });


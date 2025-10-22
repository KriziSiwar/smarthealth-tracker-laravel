// main-public.js
document.addEventListener("DOMContentLoaded", () => {
    // Menu toggle si bouton existe
    const menuToggle = document.querySelector(".mobile-nav-toggle");
    const navMenu = document.querySelector(".navbar");

    if (menuToggle && navMenu) {
        menuToggle.addEventListener("click", () => {
            navMenu.classList.toggle("navbar-mobile");
            menuToggle.classList.toggle("bi-list");
            menuToggle.classList.toggle("bi-x");
        });
    }

    // Dropdown mobile
    document.querySelectorAll('.navbar .dropdown > a').forEach(link => {
        link.addEventListener('click', e => {
            if (navMenu && navMenu.classList.contains('navbar-mobile')) {
                e.preventDefault();
                link.nextElementSibling.classList.toggle('dropdown-active');
            }
        });
    });
});

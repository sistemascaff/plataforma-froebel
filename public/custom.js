$(document).ready(function () {
    // Obtener elementos
    const toggleButton = document.getElementById('toggle_theme');
    const htmlElement = document.documentElement;
    const icon = toggleButton.querySelector('i');

    // Función para aplicar el tema
    function setTheme(theme) {
        htmlElement.setAttribute('data-bs-theme', theme);

        // Cambiar el ícono según el tema
        if (theme === 'dark') {
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
            toggleButton.classList.remove('btn-dark');
            toggleButton.classList.add('btn-light');
        } else {
            icon.classList.remove('fa-sun');
            icon.classList.add('fa-moon');
            toggleButton.classList.remove('btn-light');
            toggleButton.classList.add('btn-dark');
        }

        // Guardar preferencia en localStorage
        localStorage.setItem('theme', theme);
    }

    // Cargar tema guardado al cargar la página
    function loadTheme() {
        const savedTheme = localStorage.getItem('theme');

        // Si hay un tema guardado, usarlo; si no, usar 'dark' por defecto
        const theme = savedTheme || 'dark';
        setTheme(theme);
    }

    // Event listener para el botón
    toggleButton.addEventListener('click', function () {
        const currentTheme = htmlElement.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        setTheme(newTheme);
    });

    // Cargar el tema al iniciar
    loadTheme();
});
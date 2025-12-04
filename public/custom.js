// Crear objeto global de Helpers ANTES del document.ready
window.Helpers = window.Helpers || {};

// Agregar el helper al objeto global
window.Helpers.abreviarCurso = function(cadena) {
    if (!cadena) {
        return '';
    }

    cadena = cadena.toUpperCase().trim();
    const partes = cadena.split(' ');

    // Casos especiales
    const especiales = {
        'TALLER INICIAL ROT': 'TIR',
        'TALLER INICIAL WEISS': 'TIW',
        'PRE KINDER ROT': 'PKR',
        'PRE KINDER WEISS': 'PKW',
        'KINDER ROT': 'KR',
        'KINDER WEISS': 'KW',
    };

    if (especiales[cadena]) {
        return especiales[cadena];
    }

    // Tablas para los casos regulares
    const cursos = {
        'PRIMERO': '1',
        'SEGUNDO': '2',
        'TERCERO': '3',
        'CUARTO': '4',
        'QUINTO': '5',
        'SEXTO': '6',
    };

    const niveles = {
        'PRIMARIA': 'P',
        'SECUNDARIA': 'S',
    };

    const paralelos = {
        'ROT': 'R',
        'WEISS': 'W',
    };

    // Búsqueda y armado del resultado
    const curso = cursos[partes[0]] || '';
    const nivel = niveles[partes[2]] || '';
    const paralelo = paralelos[partes[3] || partes[2]] || '';

    return curso + nivel + paralelo;
};

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
            icon.classList.remove('text-info');
            icon.classList.add('fa-sun');
            icon.classList.add('text-warning');
            toggleButton.classList.remove('btn-dark');
            toggleButton.classList.add('btn-light');
        } else {
            icon.classList.remove('fa-sun');
            icon.classList.remove('text-warning');
            icon.classList.add('fa-moon');
            icon.classList.add('text-info');
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
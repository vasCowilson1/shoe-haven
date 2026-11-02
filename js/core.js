(function () {
    function applyTheme(theme) {
        document.documentElement.setAttribute('data-bs-theme', theme);
        var icon = document.getElementById('themeToggleIcon');
        if (icon) {
            icon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-stars';
        }
        localStorage.setItem('shoehaven-theme', theme);
    }

    window.toggleTheme = function () {
        var current = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
        applyTheme(current);
    };

    window.showToast = function (message) {
        var toast = document.getElementById('sh-global-toast');
        var text = document.getElementById('sh-toast-text');
        if (!toast || !text) return;

        text.textContent = message;
        toast.classList.add('show');
        window.clearTimeout(window.shoeHavenToastTimeout);
        window.shoeHavenToastTimeout = window.setTimeout(function () {
            toast.classList.remove('show');
        }, 2800);
    };

    document.addEventListener('DOMContentLoaded', function () {
        var savedTheme = localStorage.getItem('shoehaven-theme');
        var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        applyTheme(savedTheme || (prefersDark ? 'dark' : 'light'));

        var themeToggleButton = document.getElementById('themeToggleButton');
        if (themeToggleButton) {
            themeToggleButton.addEventListener('click', window.toggleTheme);
        }
    });
})();

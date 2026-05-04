(function () {
    const html = document.documentElement;
    const body = document.body;

    function setTheme(theme) {
        html.setAttribute('data-bs-theme', theme);
        const form = document.getElementById('loginForm');

        if (theme === 'dark') {
            body.classList.add('bg-dark', 'text-white');
            body.classList.remove('bg-light', 'text-dark');
            if (form) {
                form.classList.add('bg-dark', 'text-white');
                form.classList.remove('bg-light', 'text-dark');
            }
        } else {
            body.classList.add('bg-light', 'text-dark');
            body.classList.remove('bg-dark', 'text-white');
            if (form) {
                form.classList.add('bg-light', 'text-dark');
                form.classList.remove('bg-dark', 'text-white');
            }
        }
        localStorage.setItem('theme', theme);
    }

    function toggleTheme() {
        const currentTheme = html.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        setTheme(newTheme);
    }

    const savedTheme = localStorage.getItem('theme') || 'light';
    setTheme(savedTheme);

    document.addEventListener('click', function (e) {
        if (e.target && (e.target.id === 'toggleTheme' || e.target.id === 'toggleThemeNav' || e.target.closest('#toggleThemeNav'))) {
            toggleTheme();
        }
    });

    window.AppTheme = {
        set: setTheme,
        toggle: toggleTheme
    };
})();

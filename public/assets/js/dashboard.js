function confirmLogout() {
    if (confirm('¿Está seguro que desea cerrar sesión?')) {
        window.location.href = '/timeControl/public/logout';
    }
}

// Theme Toggle
document.getElementById('toggleTheme').addEventListener('click', function() {
    document.documentElement.classList.toggle('dark');
    const isDark = document.documentElement.classList.contains('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    this.querySelector('i').classList.toggle('fa-sun');
    this.querySelector('i').classList.toggle('fa-moon');
});

// Initialize theme
if (localStorage.theme === 'dark' || 
    (!('theme' in localStorage) && 
     window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark');
    document.getElementById('toggleTheme').querySelector('i').classList.replace('fa-moon', 'fa-sun');
}
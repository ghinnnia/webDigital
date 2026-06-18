// Theme Manager
const ThemeManager = {
    init() {
        // Ambil tema dari localStorage
        const savedTheme = localStorage.getItem('theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        // Tentukan tema awal
        let theme = savedTheme;
        if (!theme) {
            theme = systemPrefersDark ? 'dark' : 'light';
        }
        
        // Terapkan tema
        this.setTheme(theme);
        
        // Dengarkan perubahan tema dari header
        this.listenForThemeChanges();
    },
    
    setTheme(theme) {
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        localStorage.setItem('theme', theme);
        
        // Update icon jika ada
        this.updateIcons(theme);
    },
    
    toggleTheme() {
        const isDark = document.documentElement.classList.contains('dark');
        this.setTheme(isDark ? 'light' : 'dark');
    },
    
    updateIcons(theme) {
        // Update icon di header jika ada
        const sunIcons = document.querySelectorAll('#sun-icon, #mobile-sun-icon');
        const moonIcons = document.querySelectorAll('#moon-icon, #mobile-moon-icon');
        
        if (theme === 'dark') {
            sunIcons.forEach(icon => icon.style.display = 'block');
            moonIcons.forEach(icon => icon.style.display = 'none');
        } else {
            sunIcons.forEach(icon => icon.style.display = 'none');
            moonIcons.forEach(icon => icon.style.display = 'block');
        }
    },
    
    listenForThemeChanges() {
        // Event listener untuk perubahan tema dari header
        window.addEventListener('storage', (e) => {
            if (e.key === 'theme') {
                this.setTheme(e.newValue);
            }
        });
        
        // Custom event untuk toggle dari header
        window.addEventListener('theme-toggled', (e) => {
            this.setTheme(e.detail.theme);
        });
    }
};

// Inisialisasi saat DOM siap
document.addEventListener('DOMContentLoaded', () => {
    ThemeManager.init();
});

// Export untuk digunakan di header
window.ThemeManager = ThemeManager;
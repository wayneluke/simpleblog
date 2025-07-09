    (() => {
        const root = document.documentElement;
        const button = document.getElementById('theme-toggle');
        const icon = button.querySelector('i');
        const label = button.querySelector('span');
        const themes = ['system', 'light', 'dark'];
    
        const icons = {
            system: 'fa-desktop',
            light: 'fa-sun',
            dark: 'fa-moon'
        };
    
        function applyTheme(theme) {
            if (theme === 'system') {
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                root.setAttribute('data-theme', prefersDark ? 'dark' : 'light');
            } else {
                root.setAttribute('data-theme', theme);
            }
    
            // Update icon and label
            for (const key in icons) {
                icon.classList.remove('fa-' + icons[key].split('-')[1]);
            }
            icon.classList.add(icons[theme]);
            //label.textContent = capitalize(theme);
        }
    
        function capitalize(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }
    
        function getNextTheme(current) {
            const index = themes.indexOf(current);
            return themes[(index + 1) % themes.length];
        }
    
        function initTheme() {
            const saved = localStorage.getItem('theme') || 'system';
            button.dataset.theme = saved;
            applyTheme(saved);
        }
    
        button.addEventListener('click', () => {
            const current = button.dataset.theme || 'system';
            const next = getNextTheme(current);
            button.dataset.theme = next;
            localStorage.setItem('theme', next);
            applyTheme(next);
        });
    
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if (localStorage.getItem('theme') === 'system') {
                applyTheme('system');
            }
        });
    
        initTheme();
    })();
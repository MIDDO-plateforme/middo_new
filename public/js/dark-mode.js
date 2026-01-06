document.addEventListener('DOMContentLoaded', function() {
    const THEME_KEY = 'middo-theme';
    
    function setTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem(THEME_KEY, theme);
    }
    
    function getTheme() {
        return localStorage.getItem(THEME_KEY) || 'light';
    }
    
    function createToggleButton() {
        let toggle = document.getElementById('dark-mode-toggle');
        if (toggle) return;
        
        toggle = document.createElement('button');
        toggle.id = 'dark-mode-toggle';
        toggle.className = 'dark-mode-toggle';
        toggle.innerHTML = '<span class="sun-icon"></span><span class="moon-icon"></span>';
        toggle.setAttribute('aria-label', 'Changer de thème');
        toggle.style.cssText = 'position:fixed;top:20px;right:80px;width:50px;height:50px;border-radius:50%;border:2px solid #f4a261;background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:24px;z-index:99999;box-shadow:0 4px 12px rgba(0,0,0,0.15);transition:all 0.3s ease;';
        
        toggle.addEventListener('click', function() {
            const currentTheme = getTheme();
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            setTheme(newTheme);
        });
        
        document.body.appendChild(toggle);
    }
    
    setTheme(getTheme());
    createToggleButton();
});
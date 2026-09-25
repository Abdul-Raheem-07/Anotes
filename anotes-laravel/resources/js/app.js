import './bootstrap';

const sidebar = document.querySelector('[data-sidebar-toggle]');
const backdrop = document.querySelector('[data-sidebar-close]');
const themeToggles = document.querySelectorAll('[data-theme-toggle]');

const setTheme = (isDark) => {
	document.body.classList.toggle('theme-dark', isDark);
	themeToggles.forEach((toggle) => {
		toggle.setAttribute('aria-pressed', String(isDark));
		toggle.setAttribute('aria-label', isDark ? 'Disable dark theme' : 'Enable dark theme');
		toggle.setAttribute('title', isDark ? 'Disable dark theme' : 'Enable dark theme');
		const icon = toggle.querySelector('i');
		icon?.classList.toggle('bi-sun', isDark);
		icon?.classList.toggle('bi-moon-stars', !isDark);
	});
};

setTheme(window.localStorage.getItem('anotes-theme') === 'dark');

themeToggles.forEach((toggle) => {
	toggle.addEventListener('click', () => {
		const isDark = !document.body.classList.contains('theme-dark');
		setTheme(isDark);
		window.localStorage.setItem('anotes-theme', isDark ? 'dark' : 'light');
	});
});

const setSidebarState = (isOpen) => {
	document.body.classList.toggle('sidebar-open', isOpen);
	sidebar?.setAttribute('aria-expanded', String(isOpen));
};

sidebar?.addEventListener('click', () => setSidebarState(true));
backdrop?.addEventListener('click', () => setSidebarState(false));

document.querySelectorAll('.app-sidebar a').forEach((link) => {
	link.addEventListener('click', () => setSidebarState(false));
});

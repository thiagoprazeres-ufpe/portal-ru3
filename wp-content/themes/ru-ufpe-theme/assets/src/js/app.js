import '../css/app.css';
import { createIcons, icons } from 'lucide';

document.addEventListener('DOMContentLoaded', () => {
	// ── Lucide icons ─────────────────────────────────
	createIcons({
		icons,
		attrs: { width: 18, height: 18, 'stroke-width': 1.8 },
	});

	// ── Nav drawer toggle ─────────────────────────────
	const menuBtn    = document.getElementById('menu-btn');
	const menuClose  = document.getElementById('menu-close');
	const navDrawer  = document.getElementById('nav-drawer');
	const navOverlay = document.getElementById('nav-overlay');

	function openNav() {
		navDrawer?.classList.add('is-open');
		navOverlay?.classList.add('is-open');
		document.body.style.overflow = 'hidden';
	}

	function closeNav() {
		navDrawer?.classList.remove('is-open');
		navOverlay?.classList.remove('is-open');
		document.body.style.overflow = '';
	}

	menuBtn?.addEventListener('click', openNav);
	menuClose?.addEventListener('click', closeNav);
	navOverlay?.addEventListener('click', closeNav);

	document.addEventListener('keydown', (e) => {
		if (e.key === 'Escape') closeNav();
	});

	// ── Search panel toggle ───────────────────────────
	const searchBtn   = document.getElementById('search-btn');
	const searchPanel = document.getElementById('search-panel');

	searchBtn?.addEventListener('click', (e) => {
		e.stopPropagation();
		const isOpen = !searchPanel?.classList.contains('hidden');
		searchPanel?.classList.toggle('hidden', isOpen);
		if (!isOpen) searchPanel?.querySelector('input')?.focus();
	});

	document.addEventListener('click', (e) => {
		if (!searchPanel?.contains(e.target) && !searchBtn?.contains(e.target)) {
			searchPanel?.classList.add('hidden');
		}
	});
});

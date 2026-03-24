/**
 * Header navigation: mega menu, sticky header, mobile overlay.
 * Hook classes use .js- prefix (markup must match).
 */

const STICKY_THRESHOLD = 80;
const MOBILE_MQ = '(max-width: 1023px)';

/**
 * @param {HTMLElement} root
 * @param {HTMLElement} trigger
 * @param {HTMLElement} panel
 */
function bindMegaMenuDesktopHover(root, trigger, panel) {
	const open = () => {
		panel.hidden = false;
		trigger.setAttribute('aria-expanded', 'true');
	};
	const close = () => {
		panel.hidden = true;
		trigger.setAttribute('aria-expanded', 'false');
	};

	root.addEventListener('mouseenter', open);
	root.addEventListener('mouseleave', close);
	root.addEventListener('focusin', open);
	root.addEventListener('focusout', (e) => {
		if (!root.contains(e.relatedTarget)) {
			close();
		}
	});
}

/**
 * @param {HTMLElement} root
 * @param {HTMLElement} trigger
 * @param {HTMLElement} panel
 */
function bindMegaMenuMobileToggle(root, trigger, panel) {
	const toggle = () => {
		const isOpen = trigger.getAttribute('aria-expanded') === 'true';
		trigger.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
		panel.hidden = isOpen;
	};

	trigger.addEventListener('click', (e) => {
		if (!window.matchMedia(MOBILE_MQ).matches) {
			return;
		}
		e.preventDefault();
		toggle();
	});

	document.addEventListener('keydown', (e) => {
		if (e.key === 'Escape' && trigger.getAttribute('aria-expanded') === 'true') {
			trigger.setAttribute('aria-expanded', 'false');
			panel.hidden = true;
			trigger.focus();
		}
	});
}

export function initMegaMenu() {
	const roots = document.querySelectorAll('.js-mega-menu');

	roots.forEach((root) => {
		const trigger = root.querySelector('.js-mega-menu-trigger');
		const panel = root.querySelector('.js-mega-menu-panel');
		if (!trigger || !panel) {
			return;
		}

		const panelId = panel.id || `mega-panel-${Math.random().toString(36).slice(2, 9)}`;
		panel.id = panelId;
		panel.setAttribute('role', 'region');
		panel.setAttribute('aria-label', panel.getAttribute('data-aria-label') || 'Submenu');
		trigger.setAttribute('aria-haspopup', 'true');
		trigger.setAttribute('aria-controls', panelId);
		trigger.setAttribute('aria-expanded', 'false');
		panel.hidden = true;

		const mobile = window.matchMedia(MOBILE_MQ);

		const applyMode = () => {
			if (mobile.matches) {
				panel.hidden = trigger.getAttribute('aria-expanded') !== 'true';
			} else {
				panel.hidden = true;
				trigger.setAttribute('aria-expanded', 'false');
			}
		};

		bindMegaMenuDesktopHover(root, trigger, panel);
		bindMegaMenuMobileToggle(root, trigger, panel);
		mobile.addEventListener('change', applyMode);
		applyMode();
	});
}

export function initStickyHeader() {
	const headers = document.querySelectorAll('.js-sticky-header');
	if (!headers.length) {
		return;
	}

	const onScroll = () => {
		const y = window.scrollY || document.documentElement.scrollTop;
		const stuck = y > STICKY_THRESHOLD;
		headers.forEach((el) => el.classList.toggle('is-stuck', stuck));
	};

	onScroll();
	window.addEventListener('scroll', onScroll, { passive: true });
}

export function initMobileNav() {
	const toggleBtn = document.querySelector('.js-mobile-nav-toggle');
	const overlay = document.querySelector('.js-mobile-nav-overlay');
	const closeBtn = document.querySelector('.js-mobile-nav-close');

	if (!toggleBtn || !overlay) {
		return;
	}

	if (!overlay.id) {
		overlay.id = 'arcopan-mobile-nav-overlay';
	}

	const open = () => {
		overlay.hidden = false;
		overlay.classList.add('is-open');
		toggleBtn.setAttribute('aria-expanded', 'true');
		document.body.classList.add('js-mobile-nav-open');
		overlay.setAttribute('aria-hidden', 'false');
	};

	const close = () => {
		overlay.classList.remove('is-open');
		toggleBtn.setAttribute('aria-expanded', 'false');
		document.body.classList.remove('js-mobile-nav-open');
		overlay.setAttribute('aria-hidden', 'true');
		overlay.hidden = true;
	};

	toggleBtn.setAttribute('aria-expanded', 'false');
	toggleBtn.setAttribute('aria-controls', overlay.id);
	overlay.hidden = true;
	overlay.setAttribute('aria-hidden', 'true');
	overlay.setAttribute('role', 'dialog');
	overlay.setAttribute('aria-modal', 'true');
	if (!overlay.getAttribute('aria-label')) {
		overlay.setAttribute('aria-label', 'Mobile navigation');
	}

	toggleBtn.addEventListener('click', () => {
		if (toggleBtn.getAttribute('aria-expanded') === 'true') {
			close();
		} else {
			open();
		}
	});

	closeBtn?.addEventListener('click', close);

	overlay.addEventListener('click', (e) => {
		if (e.target === overlay) {
			close();
		}
	});

	document.addEventListener('keydown', (e) => {
		if (e.key === 'Escape' && overlay.classList.contains('is-open')) {
			close();
			toggleBtn.focus();
		}
	});
}

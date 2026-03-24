/**
 * ARCOPAN front-end bootstrap (ES modules).
 */

import { initMegaMenu, initStickyHeader, initMobileNav } from './nav.js';
import { initMultiStep } from './forms.js';
import { initFilters } from './filters.js';
import { initLangSwitcher } from './language.js';

/**
 * IntersectionObserver: add .is-visible to .js-lazy-animate when in view.
 */
function initLazyAnimations() {
	const els = document.querySelectorAll('.js-lazy-animate');

	if (!els.length) {
		return;
	}

	if (!('IntersectionObserver' in window)) {
		els.forEach((el) => el.classList.add('is-visible'));
		return;
	}

	const io = new IntersectionObserver(
		(entries) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					entry.target.classList.add('is-visible');
					io.unobserve(entry.target);
				}
			});
		},
		{ root: null, rootMargin: '0px 0px -8% 0px', threshold: 0.12 },
	);

	els.forEach((el) => io.observe(el));
}

function init() {
	initMegaMenu();
	initStickyHeader();
	initMobileNav();
	initMultiStep();
	initFilters();
	initLangSwitcher();
	initLazyAnimations();
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', init);
} else {
	init();
}

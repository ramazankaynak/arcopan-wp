/**
 * WPML language switcher + localStorage preference (UX hint only; navigation is WPML).
 */

const STORAGE_KEY = 'arcopan_lang_preference';

/**
 * @param {string} code BCP 47 or WPML language code
 */
export function setLangPreference(code) {
	if (!code || typeof code !== 'string') {
		return;
	}
	try {
		localStorage.setItem(STORAGE_KEY, code);
	} catch {
		// Private mode or blocked storage
	}
}

/**
 * @returns {string|null}
 */
export function getLangPreference() {
	try {
		return localStorage.getItem(STORAGE_KEY);
	} catch {
		return null;
	}
}

/**
 * Detect language code from WPML link or data-lang.
 *
 * @param {HTMLAnchorElement} a
 * @returns {string}
 */
function codeFromLink(a) {
	const fromData = a.getAttribute('data-lang') || a.getAttribute('data-wpml-lang');
	if (fromData) {
		return fromData;
	}
	const href = a.getAttribute('href') || '';
	try {
		const u = new URL(href, window.location.origin);
		const seg = u.pathname.split('/').filter(Boolean)[0];
		if (seg && /^[a-z]{2}(-[a-z]{2})?$/i.test(seg)) {
			return seg.toLowerCase();
		}
	} catch {
		// ignore
	}
	return '';
}

export function initLangSwitcher() {
	const switcher =
		document.querySelector('.js-lang-switcher') ||
		document.querySelector('.wpml-ls');

	if (!switcher) {
		return;
	}

	const saved = getLangPreference();
	if (saved) {
		const links = switcher.querySelectorAll('a[href]');
		links.forEach((a) => {
			if (!(a instanceof HTMLAnchorElement)) {
				return;
			}
			const code = codeFromLink(a);
			a.classList.toggle('is-preferred-lang', code === saved);
		});
	}

	switcher.addEventListener('click', (e) => {
		const t = e.target;
		const a = t instanceof Element ? t.closest('a[href]') : null;
		if (!(a instanceof HTMLAnchorElement)) {
			return;
		}
		const code = codeFromLink(a);
		if (code) {
			setLangPreference(code);
		}
	});
}

/**
 * Product / project archive filters via WP REST API.
 */

/**
 * @returns {string}
 */
function getRestBase() {
	const cfg = window.arcopanTheme;
	if (cfg && typeof cfg.restUrl === 'string' && cfg.restUrl) {
		return cfg.restUrl.replace(/\/?$/, '/');
	}
	return '/wp-json/wp/v2/';
}

/**
 * @returns {Record<string, string>}
 */
function getDefaultHeaders() {
	const cfg = window.arcopanTheme;
	const headers = { Accept: 'application/json' };
	if (cfg && typeof cfg.restNonce === 'string' && cfg.restNonce) {
		headers['X-WP-Nonce'] = cfg.restNonce;
	}
	return headers;
}

/**
 * @param {string} restBase e.g. https://example.com/wp-json/wp/v2/
 * @param {string} postType e.g. arcopan_product
 * @param {Record<string, string|number>} params query params
 * @returns {Promise<object[]>}
 */
export async function fetchFilteredPosts(restBase, postType, params = {}) {
	const url = new URL(`${restBase}${encodeURIComponent(postType)}`);
	Object.entries(params).forEach(([k, v]) => {
		if (v !== '' && v !== null && v !== undefined) {
			url.searchParams.set(k, String(v));
		}
	});

	const res = await fetch(url.toString(), {
		credentials: 'same-origin',
		headers: getDefaultHeaders(),
	});

	if (!res.ok) {
		throw new Error(`REST ${res.status}`);
	}

	return res.json();
}

/**
 * @param {HTMLElement} container
 * @param {object[]} posts REST post objects
 */
export function renderCards(container, posts) {
	container.replaceChildren();

	if (!posts.length) {
		const empty = document.createElement('p');
		empty.className = 'js-filter-empty';
		empty.textContent = 'No results.';
		container.appendChild(empty);
		return;
	}

	const frag = document.createDocumentFragment();

	posts.forEach((post) => {
		const title = post?.title?.rendered ?? '';
		const link = post?.link ?? '#';
		const excerpt = post?.excerpt?.rendered
			? new DOMParser().parseFromString(post.excerpt.rendered, 'text/html').body.textContent ?? ''
			: '';

		const article = document.createElement('article');
		article.className = 'js-filter-card';

		const a = document.createElement('a');
		a.href = link;
		a.className = 'js-filter-card-link';

		const h = document.createElement('h3');
		h.className = 'js-filter-card-title';
		h.innerHTML = title;

		const p = document.createElement('p');
		p.className = 'js-filter-card-excerpt';
		p.textContent = excerpt.trim();

		a.append(h, p);
		article.appendChild(a);
		frag.appendChild(article);
	});

	container.appendChild(frag);
}

/**
 * Read filter values from .js-archive-filters (inputs with name or data-rest-param).
 *
 * @param {HTMLFormElement|HTMLElement} root
 * @returns {Record<string, string>}
 */
function collectParams(root) {
	/** @type {Record<string, string>} */
	const params = { per_page: '12', _embed: '0' };

	const fields = root.querySelectorAll('[data-rest-param], [name]');
	fields.forEach((el) => {
		if (!(el instanceof HTMLInputElement || el instanceof HTMLSelectElement)) {
			return;
		}
		const key = el.dataset.restParam || el.name;
		if (!key || key === 'per_page') {
			return;
		}
		if (el instanceof HTMLInputElement && (el.type === 'checkbox' || el.type === 'radio') && !el.checked) {
			return;
		}
		const val = el.value.trim();
		if (val !== '') {
			params[key] = val;
		}
	});

	return params;
}

export function initFilters() {
	const roots = document.querySelectorAll('.js-filter-root');

	roots.forEach((root) => {
		const postType = root.getAttribute('data-post-type') || 'arcopan_product';
		const form = root.querySelector('.js-archive-filters');
		const results = root.querySelector('.js-archive-results');

		if (!(form instanceof HTMLFormElement) || !results) {
			return;
		}

		const restBase = root.getAttribute('data-rest-base') || getRestBase();

		const run = async () => {
			results.classList.add('is-loading');
			try {
				const params = collectParams(form);
				const posts = await fetchFilteredPosts(restBase, postType, params);
				renderCards(results, Array.isArray(posts) ? posts : []);
			} catch (e) {
				results.replaceChildren();
				const err = document.createElement('p');
				err.className = 'js-filter-error';
				err.textContent = 'Could not load results.';
				results.appendChild(err);
				console.error(e);
			} finally {
				results.classList.remove('is-loading');
			}
		};

		form.addEventListener('submit', (e) => {
			e.preventDefault();
			run();
		});

		form.querySelectorAll('select, input[data-instant-filter]').forEach((el) => {
			el.addEventListener('change', () => {
				run();
			});
		});

		if (root.hasAttribute('data-autoload')) {
			run();
		}
	});
}

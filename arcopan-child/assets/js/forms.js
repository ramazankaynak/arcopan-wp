/**
 * Multi-step RFQ form (5 steps), validation, progress, conditional fields.
 */

const STEP_TOTAL = 5;

/**
 * @param {HTMLElement} stepEl
 * @returns {{ ok: boolean, firstInvalid: HTMLElement|null }}
 */
export function validateStep(stepEl) {
	const candidates = stepEl.querySelectorAll('input, select, textarea');
	let firstInvalid = null;

	candidates.forEach((el) => el.classList.remove('is-invalid'));

	const seenRadio = new Set();

	for (const field of candidates) {
		if (!(field instanceof HTMLInputElement || field instanceof HTMLSelectElement || field instanceof HTMLTextAreaElement)) {
			continue;
		}
		if (field.disabled) {
			continue;
		}
		if (!field.required) {
			continue;
		}

		let valid = true;

		if (field instanceof HTMLInputElement && field.type === 'radio') {
			if (seenRadio.has(field.name)) {
				continue;
			}
			seenRadio.add(field.name);
			const group = stepEl.querySelectorAll(`input[type="radio"][name="${CSS.escape(field.name)}"]`);
			valid = Array.from(group).some((r) => r.checked);
		} else if (field instanceof HTMLInputElement && field.type === 'checkbox') {
			valid = field.checked;
		} else {
			valid = field.value.trim() !== '' && field.checkValidity();
		}

		if (!valid) {
			field.classList.add('is-invalid');
			firstInvalid = firstInvalid || field;
		}
	}

	return { ok: firstInvalid === null, firstInvalid };
}

/**
 * @param {HTMLFormElement} form
 * @param {number} stepIndex 1-based
 */
export function updateProgress(form, stepIndex) {
	const bar = form.querySelector('.js-rfq-progress-bar');
	const label = form.querySelector('.js-rfq-progress-label');
	const pct = Math.min(100, Math.round((stepIndex / STEP_TOTAL) * 100));

	if (bar) {
		bar.style.width = `${pct}%`;
		bar.setAttribute('aria-valuenow', String(pct));
	}
	if (label) {
		label.textContent = `${stepIndex} / ${STEP_TOTAL}`;
	}
}

/**
 * Blast-freeze & ATEX conditional blocks (RFQ).
 *
 * @param {HTMLFormElement} form
 */
export function conditionalFields(form) {
	const blastControl = form.querySelector('[data-rfq-control="blast_freeze"]');
	const blastBlocks = form.querySelectorAll('.js-conditional-blast-freeze');
	const atexControl = form.querySelector('[data-rfq-control="atex"]');
	const atexBlocks = form.querySelectorAll('.js-conditional-atex');

	const sync = () => {
		const blastOn =
			blastControl instanceof HTMLSelectElement &&
			['yes', 'blast_freeze'].includes(blastControl.value);
		blastBlocks.forEach((el) => {
			el.hidden = !blastOn;
			el.querySelectorAll('input, select, textarea').forEach((inp) => {
				if (inp instanceof HTMLInputElement || inp instanceof HTMLSelectElement || inp instanceof HTMLTextAreaElement) {
					inp.disabled = !blastOn;
				}
			});
		});

		const atexOn =
			atexControl instanceof HTMLSelectElement &&
			['yes', 'atex', 'required'].includes(atexControl.value);
		atexBlocks.forEach((el) => {
			el.hidden = !atexOn;
			el.querySelectorAll('input, select, textarea').forEach((inp) => {
				if (inp instanceof HTMLInputElement || inp instanceof HTMLSelectElement || inp instanceof HTMLTextAreaElement) {
					inp.disabled = !atexOn;
				}
			});
		});
	};

	blastControl?.addEventListener('change', sync);
	atexControl?.addEventListener('change', sync);
	sync();
}

/**
 * @param {HTMLFormElement} form
 * @param {number} stepIndex 1-based
 */
function showStep(form, stepIndex) {
	const steps = form.querySelectorAll('.js-rfq-step');
	steps.forEach((step) => {
		const n = Number(step.getAttribute('data-step'));
		const active = n === stepIndex;
		step.hidden = !active;
		step.classList.toggle('is-active', active);
		step.setAttribute('aria-hidden', active ? 'false' : 'true');
	});

	const prevBtn = form.querySelector('.js-rfq-prev');
	const nextBtn = form.querySelector('.js-rfq-next');
	const submitBtn = form.querySelector('.js-rfq-submit');

	if (prevBtn instanceof HTMLButtonElement) {
		prevBtn.hidden = stepIndex <= 1;
		prevBtn.disabled = stepIndex <= 1;
	}
	if (nextBtn instanceof HTMLButtonElement) {
		const last = stepIndex >= STEP_TOTAL;
		nextBtn.hidden = last;
		nextBtn.disabled = last;
	}
	if (submitBtn instanceof HTMLButtonElement) {
		const showSubmit = stepIndex >= STEP_TOTAL;
		submitBtn.hidden = !showSubmit;
		submitBtn.disabled = !showSubmit;
	}

	form.dataset.rfqCurrentStep = String(stepIndex);
	updateProgress(form, stepIndex);
}

export function initMultiStep() {
	const forms = document.querySelectorAll('form.js-rfq-form');

	forms.forEach((form) => {
		if (!(form instanceof HTMLFormElement)) {
			return;
		}

		form.setAttribute('novalidate', '');

		let step = Math.min(STEP_TOTAL, Math.max(1, Number(form.dataset.rfqInitialStep) || 1));
		conditionalFields(form);

		const progress = form.querySelector('.js-rfq-progress');
		if (progress) {
			progress.setAttribute('role', 'progressbar');
			progress.setAttribute('aria-valuemin', '0');
			progress.setAttribute('aria-valuemax', '100');
		}
		const bar = form.querySelector('.js-rfq-progress-bar');
		if (bar) {
			bar.setAttribute('aria-valuenow', '0');
		}

		showStep(form, step);

		form.querySelector('.js-rfq-next')?.addEventListener('click', (e) => {
			e.preventDefault();
			const current = form.querySelector(`.js-rfq-step[data-step="${step}"]`);
			if (current) {
				const { ok, firstInvalid } = validateStep(current);
				if (!ok && firstInvalid) {
					firstInvalid.focus();
					return;
				}
			}
			step = Math.min(STEP_TOTAL, step + 1);
			showStep(form, step);
		});

		form.querySelector('.js-rfq-prev')?.addEventListener('click', (e) => {
			e.preventDefault();
			step = Math.max(1, step - 1);
			showStep(form, step);
		});
	});
}

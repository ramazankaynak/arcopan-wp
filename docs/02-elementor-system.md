# ELEMENTOR SYSTEM ARCHITECTURE

**Project:** ARCOPAN Cold Storage WordPress  
**Document:** Elementor Theme Builder system, section library, component mapping  
**Updated:** March 25, 2026

---

## ELEMENTOR GLOBAL SETTINGS

### Global Colors
All colors defined in Elementor Global Settings (dashboard **Colors** tab).

| Color Name | Hex | Usage |
|---|---|---|
| Primary | #0D6E6E | Buttons, headers, section BG |
| Primary Dark | #094F4F | Hover states, dark sections |
| Primary Light | #E6F4F4 | Card BG, callout boxes |
| Accent | #B36200| Warning/highlight badges |
| Text Dark | #1A1A1A | Body text, headings |
| Text Muted | #888888 | Captions, meta labels |
| Border | #CCCCCC | Dividers, borders |

### Global Typography
All fonts defined in Elementor Global Settings (**Fonts** tab).

| Role | Font | Weight | Usage |
|---|---|---|---|
| Heading | Inter | 700 | All H1–H6 |
| Body | Inter | 400/500 | All body text, paragraphs |
| Monospace | JetBrains Mono | 400 | Code blocks, stats, eyebrow labels |

### Global Spacing Scale
Defined in **Elementor → Global Settings → Spacing**.

| Token | Value | Usage |
|---|---|---|
| XS | 4px | Micro spacing |
| SM | 8px | Small gaps |
| MD | 16px | Standard spacing |
| LG | 32px | Large sections |
| XL | 64px | Hero spacing |

---

## THEME BUILDER TEMPLATES

Elementor Theme Builder handles site-wide layouts for header, footer, and single templates.

### Header Template
**Template ID:** `header.elementor`  
**Location:** Top of all pages  
**Sections:**

1. **Top Bar** (optional) — Contact info, language switcher
2. **Navigation Bar** — Logo, menu (8 items with mega menu), RFQ button
3. **Sticky Behavior** — Remains fixed on scroll (optional)

**Key Components:**
- Logo widget (custom link to homepage)
- Nav Menu widget (set to 'Primary Menu')
- RFQ button (calls contact form)
- Language switcher (WPML)

### Footer Template
**Template ID:** `footer.elementor`  
**Location:** Bottom of all pages  
**Sections:**

1. **Footer Top** (4-column) — Brand info, Products links, Solutions links, Contact CTA
2. **Footer Bottom** — Copyright, legal links, social icons

**Key Components:**
- Custom HTML for logo + brand info
- Menu widgets (repeated for each column)
- Contact form mini-widget
- Language selector

### Single Page Template
**Template ID:** `page.elementor`  
**Location:** All standard pages (About, Team, etc.)  
**Display:** Replaces `page.php`  
**Sections:**
- Dynamic content area (page title auto-inserted)
- Sidebar (optional for resources)

### Single Product Template
**Template ID:** `single-product.elementor`  
**Location:** All product detail pages  
**Display:** Replaces `single-arcopan_product.php`  
**Sections:**
- Product hero (image + specs)
- Spec table
- Related products
- CTA sidebar

### Archive Template
**Template ID:** `archive-product.elementor`  
**Location:** `/products`, `/projects` archive pages  
**Display:** Replaces `archive-arcopan_product.php`  
**Sections:**
- Filter bar (category, specs, price range)
- Product grid (4-column desktop, 2-column tablet, 1-column mobile)
- Pagination

### Page-Specific Templates
- `page-homepage.elementor` — Custom homepage (S01–S11 sections)
- `page-contact.elementor` — Contact page with RFQ form + map
- `page-industry.elementor` — Reusable industry page template
- `page-solution.elementor` — Reusable solution page template

---

## REUSABLE SECTION LIBRARY

All sections are created once and saved to Elementor's section library for reuse.

### SEC-HERO-01: Full-Width Hero
**Used On:** Homepage, Solution pages, Industry pages  
**Content:** Video/image BG + H1 + subtitle + 2 CTAs + trust strip  
**Key Elements:**
- Background image (1920×1080 min)
- H1 text (max 5 words, uppercase)
- Subtitle (max 30 words)
- CTA buttons (primary + secondary)
- Cert logo strip (below fold)

### SEC-HERO-02: Page Hero
**Used On:** Product detail, Resource pages  
**Content:** Shorter hero + breadcrumb + H1 + single CTA  
**Styling:** Navy BG, no background image

### SEC-PROD-01: Product Card Grid
**Used On:** Product hub, homepage  
**Content:** 4-column grid, category cards (icon + title + count)  
**Breakpoints:** 4-col (desktop) → 2-col (tablet) → 1-col (mobile)

### SEC-PROD-02: Product Spec Card
**Used On:** Individual product pages  
**Content:** Left column (specs/table) + Right column (image/CTA)  
**Layout:** 50/50 split on desktop, stacked on mobile

### SEC-SOL-01: Solution Strip
**Used On:** Homepage, Industry pages  
**Content:** 3-column card grid (solution cards with icon + text)  
**Styling:** Teal BG, icons 48×48px

### SEC-CTA-01: Full-Width CTA Banner
**Used On:** Bottom of all inner pages  
**Content:** Large headline + subtext + 1–2 buttons  
**Styling:** Dark gradient BG, centered

### SEC-CTA-02: Split CTA Block
**Used On:** Solution pages, Industry pages  
**Content:** Left (text + CTA) | Right (mini lead form 3 fields)  
**Layout:** 50/50 split desktop, stacked mobile

### SEC-FAQ-01: Accordion FAQ
**Used On:** FAQ page, product pages  
**Content:** Searchable accordion with category grouping  
**Key Feature:** Auto-expand/collapse with smooth animation

### SEC-TRUST-01: Certification Strip
**Used On:** Homepage, all product pages  
**Content:** Logo row (ISO, EN, DIN, industry certs) — full width  
**Styling:** Muted grayscale, hover color on-click

### SEC-TRUST-03: Numbers Bar
**Used On:** Homepage, about page  
**Content:** 4 stat boxes (30+ years | 500+ projects | 25+ countries | 5 languages)  
**Styling:** Large condensed numerals, dark BG

### SEC-BREADCRUMB
**Used On:** All inner pages  
**Content:** Navigation breadcrumb with JSON-LD schema  
**Format:** Homepage > Category > Current Page

---

## COMPONENT STYLING GUIDE

All components follow BEM naming convention in CSS.

### Product Card Component
```
.arcopan-product-card {
  border-radius: 8px;
  box-shadow: var(--shadow-sm);
  transition: all 0.2s ease;
}

.arcopan-product-card:hover {
  box-shadow: var(--shadow-md);
  transform: translateY(-2px);
}

.arcopan-product-card__image { }
.arcopan-product-card__title { }
.arcopan-product-card__description { }
.arcopan-product-card__cta { }
```

### Button Variants
All buttons use utility classes:

```
.btn.btn--primary    /* Teal background, white text */
.btn.btn--secondary  /* Outline style */
.btn.btn--ghost      /* Text only */
.btn.btn--sm         /* Small size */
.btn.btn--md         /* Medium (default) */
.btn.btn--lg         /* Large size */
```

---

## RESPONSIVE BREAKPOINTS

All sections must support:

| Breakpoint | Width | Columns |
|---|---|---|
| Desktop (default) | >1200px | 4 columns |
| Tablet | 768px–1199px | 2 columns |
| Mobile | <768px | 1 column |
| Small Mobile | <375px | 1 column (careful scaling) |

All column grids collapse to stacked on mobile. No horizontal scrolling.

---

## COLOR APPLICATION RULES

**Apply Colors Consistently:**

1. **Primary (#0D6E6E)** — Main CTAs, section headlines, focus states
2. **Primary Dark (#094F4F)** — Button hover, dark theme variants
3. **Accent (#B36200)** — Badges, warning callouts, highlights
4. **Text Dark (#1A1A1A)** — All body copy
5. **Text Muted (#888888)** — Captions, meta information, secondary text

Never hardcode hex values. Always use Elementor global colors or CSS custom properties.

---

## CTA PLACEMENT

**Above the Fold:** Every page must have a primary CTA visible before scrolling.

**Sticky Header:** Optional sticky "Request RFQ" button in navigation (secondary CTA).

**Section CTAs:** Sections should include CTA at the end (buttons or form).

**Sidebar CTA:** Product pages include sticky sidebar RFQ mini-form on desktop.

---

## DESIGN QA CHECKLIST

Before publishing any section:

- [ ] Colors match global color palette
- [ ] Typography uses only 3 font families (heading, body, mono)
- [ ] Spacing uses scale tokens (XS/SM/MD/LG/XL)
- [ ] Mobile responsive at 375px (test on device)
- [ ] All links functional
- [ ] Images optimized (WebP format preferred)
- [ ] Button text clear and actionable
- [ ] Contrast ratios meet WCAG AA standard (4.5:1 for text)
- [ ] No placeholder text (lorem ipsum)
- [ ] Section doesn't exceed 80 characters for body text line length

---

**Next:** Use these sections to build all 36 pages rapidly. Each page combines 4–6 reusable sections.

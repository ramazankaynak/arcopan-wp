# PERFORMANCE & OPTIMIZATION

**Project:** ARCOPAN Cold Storage WordPress  
**Document:** Core Web Vitals, image optimization, caching strategy  
**Updated:** March 25, 2026

---

## CORE WEB VITALS TARGETS

All pages must meet **"Good"** threshold on Google's Core Web Vitals within 30 days of launch.

### Largest Contentful Paint (LCP)
**What it measures:** How quickly the main content loads  
**Good threshold:** <2.5 seconds  
**Target:** <2.0 seconds  

**Optimization levers:**
- Pre-load critical assets (hero image, fonts)
- Lazy-load below-fold images
- Minimize render-blocking CSS
- Optimize server response time

### First Input Delay (FID)
**What it measures:** Responsiveness to user interaction  
**Good threshold:** <100 milliseconds  
**Target:** <50 milliseconds  

**Optimization levers:**
- Break up long JavaScript tasks (>50ms)
- Reduce main-thread work
- Use Web Workers for heavy computations
- Defer non-critical JavaScript

### Cumulative Layout Shift (CLS)
**What it measures:** Visual stability during page load  
**Good threshold:** <0.1  
**Target:** <0.05  

**Optimization levers:**
- Reserve space for images (set width/height)
- Avoid inserting content above existing content
- Use CSS transforms instead of property changes
- Avoid dynamically-sized fonts

---

## ASSET OPTIMIZATION

### Image Optimization

**Format Strategy:**
- **Modern format:** WebP (primary)
- **Fallback format:** JPG (for unsupported browsers)
- **Serving method:** Cloudinary CDN with auto-format detection

**Image Sizing Guidelines:**

| Location | Max Width | Compression | Format |
|---|---|---|---|
| Hero images | 1920px | 75% quality | WebP + JPG |
| Product cards | 600px | 80% quality | WebP + JPG |
| Thumbnail icons | 200px | 85% quality | WebP + SVG |
| Certifications | 80px | 90% quality | SVG preferred |
| Blog images | 800px | 75% quality | WebP + JPG |

**Responsive Images:**
All images use `srcset` for device-specific sizing:
```html
<img 
  src="image-600w.jpg" 
  srcset="image-300w.jpg 300w, 
          image-600w.jpg 600w, 
          image-1200w.jpg 1200w"
  sizes="(max-width: 600px) 100vw, 600px"
  alt="Descriptive alt text"
  width="600" 
  height="400"
/>
```

**Alt Text Standards:**
- Product images: Product name + key feature (e.g., "Blast Freezing Cabinet – 500 Units/Hour Capacity")
- Hero images: Scene description + benefit (e.g., "Industrial cold storage facility maintaining 18°C for dairy products")
- Icons: Function description (e.g., "ISO 22000 certified")
- Decorative images: `alt=""` (empty)

### Font Optimization

**Font Loading Strategy:**
```css
@font-face {
  font-family: 'Inter';
  src: url('/fonts/inter-var.woff2') format('woff2-variations');
  font-display: swap;  /* Show system font during load */
  font-weight: 100 900;  /* Variable font, all weights */
}

@font-face {
  font-family: 'JetBrains Mono';
  src: url('/fonts/jetbrains-mono.woff2') format('woff2');
  font-display: swap;
  font-weight: 400 600;
}
```

**Font Loading Optimization:**
- Use `font-display: swap` (show system font while custom loads)
- Host fonts locally (avoid external Google Fonts request)
- Subset fonts (only characters used on site)
- Use variable fonts (single file for all weights)

### CSS Optimization

**Critical Path CSS:**
Only non-render-blocking CSS in `<head>`:
```html
<style>
  /* Critical styles for hero section only */
  body { font-family: Inter, sans-serif; }
  .hero { background: #0D6E6E; color: white; }
  h1 { font-size: 48px; margin: 0; }
</style>
<link rel="stylesheet" href="/css/critical.css">
<link rel="preload" href="/css/global.css" as="style">
<link rel="stylesheet" href="/css/global.css">
```

**CSS Splitting Strategy:**
- `critical.css` (2KB) — Hero only, inline in head
- `global.css` (inline after) — Components, utilities, responsive
- `rtl.css` (deferred) — Load only if RTL language detected
- `print.css` (media="print") — Print styles only

**CSS Minification:**
All CSS files minified before deployment:
- `global.css` (128KB → 48KB minified + gzipped)
- `components.css` (530KB → 180KB minified + gzipped)
- Total: <250KB gzipped (target: <200KB)

### JavaScript Optimization

**Script Loading Strategy:**
All scripts in `<head>` with `defer` attribute:
```html
<script src="/js/main.js" defer></script>
<script src="/js/nav.js" defer></script>
<script src="/js/forms.js" defer></script>
```

**JavaScript Bundle Optimization:**
- Main bundle: ~40KB gzipped
- No third-party analytics during initial page load
- Defer analytics to `window.addEventListener('load')`
- Lazy-load form validation (only on form pages)

**Code Splitting:**
- `main.js` (core functionality) — always loaded
- `nav.js` (menu interactions) — loaded with main
- `forms.js` (form handling) — deferred load (only on form pages)
- `filters.js` (product filters) — deferred load (only on /products/)

---

## CACHING STRATEGY

### Browser Caching (Client-Side)

Set via WordPress header (in `functions.php`):

```php
// Cache static assets for 1 year
header( 'Cache-Control: public, max-age=' . YEAR_IN_SECONDS );

// Cache HTML for 1 hour (allows updates to propagate)
header( 'Cache-Control: public, max-age=3600' );
```

**Cache duration by asset type:**

| Asset Type | Duration | Reason |
|---|---|---|
| Images (.jpg, .png, .webp) | 1 year | Immutable (versioned) |
| Fonts (.woff2) | 1 year | Immutable |
| CSS/JS (minified, versioned) | 1 year | Immutable (version in filename) |
| HTML pages | 1 hour | Allow content updates |

### Server-Side Caching (WP Rocket)

**WP Rocket Configuration:**

| Setting | Value | Purpose |
|---|---|---|
| Page Caching | Enabled | Cache full HTML pages |
| Browser Caching | Enabled | Set cache headers |
| Gzip Compression | Enabled | Reduce file size 70% |
| Minify CSS | Enabled | Remove unused CSS |
| Minify JS | Enabled | Remove whitespace/comments |
| Lazy Load Images | Enabled | Load images on scroll |
| CDN Integration | Cloudinary | Serve images from edge locations |

**Cache Purge Rules:**
- Purge all cache when post published
- Purge homepage when product updated
- Purge /products/ when product published
- Purge industry pages when industry taxonomy updated

### Database Optimization

**Revision Limiting:**
```php
define( 'WP_POST_REVISIONS', 3 );  // Keep only last 3 revisions
```

**Auto-draft Cleanup:**
```php
// Delete auto-drafts older than 1 week
DELETE FROM wp_posts 
WHERE post_status = 'auto-draft' 
AND post_date < DATE_SUB(NOW(), INTERVAL 7 DAY);
```

**Index Optimization:**
Ensure these columns are indexed (in wp-admin → database):
- `wp_posts.post_status`
- `wp_postmeta.meta_key`
- `wp_postmeta.post_id`
- `wp_posts.post_type`

---

## LIGHTHOUSE AUDIT TARGETS

After launch, all pages must achieve:

| Category | Target Score | What it measures |
|---|---|---|
| Performance | >90 | Speed (LCP, FID, CLS) |
| Accessibility | >95 | WCAG compliance |
| Best Practices | >90 | Security, modern standards |
| SEO | >95 | On-page SEO signals |

**Key Lighthouse Metrics:**

| Metric | Target | Good | Needs Work |
|---|---|---|---|
| First Contentful Paint | <1.8s | <1.8s | >3.0s |
| Largest Contentful Paint | <2.0s | <2.5s | >4.0s |
| Cumulative Layout Shift | <0.05 | <0.1 | >0.25 |
| Speed Index | <3.0s | <3.4s | >5.8s |
| Total Blocking Time | <100ms | <200ms | >600ms |

**Monthly Lighthouse Audit:**
1. Run Lighthouse on 10 representative pages
2. Document baseline scores
3. Identify top 3 bottlenecks
4. Optimize, then re-audit
5. Target: 10-point improvement monthly

---

## PERFORMANCE MONITORING

### Real User Monitoring (RUM)

Track actual user experience via Google Analytics 4 + Web Vitals library:

```javascript
// In main.js
import { getCLS, getFID, getFCP, getLCP, getTTFB } from 'web-vitals';

getCLS(metric => {
  gtag('event', 'page_view', {
    'cls': metric.value,
    'event_category': 'web_vitals'
  });
});
```

**Monitor Monthly:**
- Average LCP across all pages
- Average FID across all pages
- Average CLS across all pages
- Page load time by device type (mobile vs. desktop)
- Slowest pages (identify and prioritize)

### Synthetic Monitoring (Uptime Monitoring)

Use Pingdom or UptimeRobot to monitor:
- Site availability (target: 99.9% uptime)
- Response time (target: <500ms)
- SSL certificate validity
- Monthly alert if downtime detected

---

## CONTENT DELIVERY NETWORK (CDN)

All images, CSS, and fonts served via Cloudinary CDN.

### CDN Configuration

**Image Delivery:**
```
https://res.cloudinary.com/arcopan/image/upload/w_600,q_80,f_auto/products/chiller-cabinet.jpg
```

**Transformation rules:**
- `w_600` — Resize to 600px width
- `q_80` — 80% quality (balance quality vs. file size)
- `f_auto` — Auto-select format (WebP for modern browsers, JPG fallback)

**Fonts via CDN:**
```
https://res.cloudinary.com/arcopan/font/upload/inter-var.woff2
```

**CSS via CDN (optional):**
```
https://res.cloudinary.com/arcopan/raw/upload/global.css
```

---

## PERFORMANCE REGRESSION PREVENTION

**Before each deploy:**

1. **Run Lighthouse** — All 10 key pages
   - Performance target: >90
   - If below, investigate and fix before deploy
   
2. **Check Core Web Vitals** — Google Search Console
   - All metrics must be "Good"
   
3. **Check Page Speed** — PageSpeed Insights
   - Mobile score >85
   - Desktop score >90

4. **Load Test** — Simulate 1000 concurrent users (LoadImpact or similar)
   - Page response time <2s at 50th percentile
   - 95th percentile <4s

**If any check fails:** Do not deploy. Investigate and fix.

---

## PERFORMANCE OPTIMIZATION ROADMAP

**Week 1–2 (Pre-launch):**
- [ ] Optimize hero image (reduce to <200KB)
- [ ] Enable WP Rocket caching
- [ ] Set up Cloudinary CDN
- [ ] Minify all CSS/JS

**Week 3–4 (Post-launch):**
- [ ] Run Lighthouse audit on all 36 pages
- [ ] Identify 5 slowest pages
- [ ] Optimize top 5 bottlenecks
- [ ] Target: 90+ Lighthouse score on 95% of pages

**Month 2:**
- [ ] Implement lazy-loading for below-fold images
- [ ] Split JavaScript bundles
- [ ] Optimize database (revisions, auto-drafts)
- [ ] Target: 95+ Lighthouse score on 100% of pages

**Month 3+:**
- [ ] Monthly Lighthouse audits
- [ ] Continuous optimization (Core Web Vitals monitoring)
- [ ] React to performance regressions

---

**Next:** Set up monitoring dashboards, establish performance alerts, document monthly reporting process.

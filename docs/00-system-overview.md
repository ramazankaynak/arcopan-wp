# ARCOPAN WordPress System — Architecture Overview

**Project:** ARCOPAN Cold Storage & Industrial Refrigeration  
**Status:** Phase 2 Implementation (Content + SEO)  
**Last Updated:** March 25, 2026  
**Version:** 1.0.0

---

## 1. System Architecture

### 1.1 Three-Layer Stack

```
┌─────────────────────────────────────────────────┐
│       WORDPRESS INSTANCE (dt-the7 parent)      │
├─────────────────────────────────────────────────┤
│     ARCOPAN CHILD THEME (customizations)       │
│  ├─ inc/                (PHP hooks & CPTs)     │
│  ├─ template-parts/     (reusable sections)    │
│  ├─ assets/css/         (design tokens + BEM) │
│  ├─ assets/js/          (ES modules)            │
│  ├─ acf-json/           (field group exports)  │
│  └─ seo/                (schema + metadata)    │
├─────────────────────────────────────────────────┤
│              PLUGINS (required)                  │
│  • Advanced Custom Fields Pro (ACF)             │
│  • Gravity Forms + Webhooks                     │
│  • The7 Elementor Addon                         │
│  • WPML Multilingual                            │
│  • Yoast SEO Premium                            │
│  • WP Rocket (caching)                          │
│  • WP Mail SMTP                                 │
└─────────────────────────────────────────────────┘
```

### 1.2 Key Technology Choices

| Layer | Technology | Rationale |
|---|---|---|
| **CMS** | WordPress 6.4+ | Mature, ACF-native, WPML-ready |
| **Parent Theme** | The7 | Elementor Pro native, no code bloat |
| **Page Builder** | Elementor Pro | Non-developer friendly, Team Builder library |
| **Content Model** | ACF Pro | CPTs + field groups, API-ready |
| **Language** | WPML | Native 5-language support (EN, TR, FR, DE, RU) |
| **Forms** | Gravity Forms | n8n integration, multi-page, conditional logic |
| **SEO** | Yoast Premium | Native schema, readability, hreflang |
| **Email** | WP Mail SMTP + n8n | Reliable delivery, CRM webhook integration |
| **Caching** | WP Rocket | LCP <2.5s, recommended for production |

---

## 2. Root vs Child Theme Responsibility

### Root Folder Structure

```
arcopan-wp/
├── docs/                     ← Operational & deployment docs
├── seo/                       ← Metadata, keywords, schema templates
├── forms/                     ← RFQ spec, email templates, PDF config
├── content/                   ← Master content copies (mirrors child theme)
├── ARCOPAN_Master.md         ← Authoritative system design
├── AUDIT_REPORT.md           ← Phase 1 audit findings
├── REMEDIATION_REPORT.md     ← Phase 2 remediation summary
└── [parent theme files]      ← dt-the7 (not modified)

arcopan-child/                ← Child theme (active on WordPress)
├── functions.php             ← Bootstrap, hooks, enqueue
├── style.css                 ← Theme header + CSS imports
├── inc/                       ← PHP implementation
│   ├── cpt.php              ← CPT registration
│   ├── taxonomies.php       ← Taxonomy registration
│   ├── acf-fields.php       ← ACF field group definitions
│   ├── forms.php            ← Gravity Forms hooks
│   ├── seo.php              ← Schema.org + JSON-LD
│   └── performance.php       ← Optimization hooks
├── template-parts/          ← Reusable section templates
├── assets/
│   ├── css/                 ← Design system CSS
│   ├── js/                  ← ES module scripts
│   └── images/              ← Static images
├── acf-json/                ← ACF field group JSON exports
├── seo/
│   ├── schema-templates/    ← JSON-LD template files
│   └── metadata.csv         ← Page SEO metadata
├── content/                 ← Markdown content files (reference)
├── forms/                   ← Form spec & email templates
└── languages/               ← i18n language files
```

**Key Distinction:**
- **Root files** = documentation, design specs, asset templates
- **Child theme files** = WordPress implementation, executable code
- **Synergy:** Documentation in root informs implementation in child

---

## 3. Current Repository Status

### Phase Completion

| Phase | Task | Status | Completion |
|---|---|---|---|
| **Phase 1** | File structure + CSS/JS | ✅ Complete | 100% |
| **Phase 2** | Content pages + SEO | 🟡 Partial | 52% (missing solutions/industries/resources) |
| **Phase 3** | Form integration + n8n | 🟡 Design only | 30% (spec exists, not integrated) |
| **Phase 4** | Deployment docs | 🔴 Incomplete | 10% |
| **Phase 5** | WPML setup + multi-language | ⏳ Pending | 0% |

### Critical Gaps (from Phase 1 Audit)

1. **18 missing markdown content pages** (solutions, industries, resources, etc.)
2. **Incomplete JS module verification** (nav.js, forms.js existence unknown)
3. **Zero deployment documentation** (docs/ is empty)
4. **Forms not integrated** (spec exists, but not wired into GF/n8n)
5. **Plugin dependencies undocumented** (ACF Pro, GF not listed in requirements)

---

## 4. Deployment Checklist

### Pre-Production (Staging Server)

- [ ] WordPress 6.4+ fresh install
- [ ] Install required plugins (see section 5)
- [ ] Activate `arcopan-child` theme
- [ ] Import ACF field groups (acf-json/*.json via ACF UI)
- [ ] Create WordPress menus (see 01-page-matrix.md)
- [ ] Flush permalinks (Settings → Permalinks → Save)
- [ ] Test all 36+ URLs return 200 + H1 tag
- [ ] Verify CSS loads (check global.css in <head>)
- [ ] Test JS console for errors (F12 → Console)
- [ ] Create sample Gravity Form (see 04-rfq-configurator.md)
- [ ] Test form submission flow
- [ ] Setup SMTP (WP Mail SMTP plugin + credentials)
- [ ] Run Yoast audit on 5 key pages
- [ ] Test WPML language switcher (if activated)
- [ ] Core Web Vitals check (Google PageSpeed Insights)
- [ ] Security scan (Wordfence / Sucuri)

### Production (Live Server)

- [ ] All staging tests pass
- [ ] SSL certificate installed
- [ ] WP Rocket configured (cache rules for mega menu, etc.)
- [ ] Google Search Console + Analytics connected
- [ ] Sitemap submitted (Yoast auto-generates)
- [ ] robots.txt configured
- [ ] CDN configured (if needed for images)
- [ ] Backup strategy in place
- [ ] Monitoring alerts configured (uptime, error logs)

---

## 5. Plugin Stack & Installation Order

### Plugins (Required)

1. **ACF Pro** (Advanced Custom Fields Pro)
   - License key required
   - Manages CPT fields, options pages
   - Install first (acf-json/ depends on it)

2. **Gravity Forms**
   - License key required
   - Manages RFQ form, notifications, webhooks
   - Install after ACF

3. **The7 Elementor Integration**
   - Included with The7 theme
   - Enables Team Builder library, mega menu
   - Activate after theme

4. **WPML Multilingual WordPress**
   - License key required (if using 5 languages)
   - Manages content translation, language switcher
   - Install early (affects content setup)

5. **Yoast SEO Premium**
   - License key required
   - Meta, readability, hreflang, sitemap
   - Install after content is substantial

6. **WP Rocket**
   - License key required
   - Caching, optimization, critical CSS
   - Install before production

7. **WP Mail SMTP**
   - Free plugin
   - Manages email delivery
   - Configure with SendGrid / AWS SES / Gmail

### Installation Order

```
1. ACF Pro
2. Gravity Forms
3. WPML (if multi-language needed)
4. Yoast SEO Premium
5. The7 Elementor (theme plugin)
6. WP Rocket
7. WP Mail SMTP
8. (optional) Wordfence Security
```

---

## 6. File Structure Inventory

### Essential Files Status

| File | Location | Status | Importance |
|---|---|---|---|
| `functions.php` | `arcopan-child/` | ✅ Created | CRITICAL |
| `style.css` | `arcopan-child/` | ✅ Updated | CRITICAL |
| `inc/cpt.php` | `arcopan-child/` | ✅ Created | CRITICAL |
| `inc/taxonomies.php` | `arcopan-child/` | ✅ Created | CRITICAL |
| `inc/acf-fields.php` | `arcopan-child/` | ✅ Created | CRITICAL |
| `inc/seo.php` | `arcopan-child/` | ✅ Created | HIGH |
| `inc/forms.php` | `arcopan-child/` | ✅ Stub only | HIGH |
| `inc/performance.php` | `arcopan-child/` | ✅ Created | MEDIUM |
| `acf-json/*.json` | `arcopan-child/` | ✅ Created (3) | CRITICAL |
| `assets/css/*.css` | `arcopan-child/` | ✅ Created (5 files) | CRITICAL |
| `assets/js/*.js` | `arcopan-child/` | ⚠️ Main only verified | HIGH |
| `template-parts/*.php` | `arcopan-child/` | ✅ Created (4) | MEDIUM |
| `content/*.md` | `arcopan-child/` | 🟡 Partial (23/36) | HIGH |
| `seo/metadata.csv` | `arcopan-child/` | ✅ Created | HIGH |
| `forms/rfq-form-spec.md` | `arcopan-child/` | ✅ Created | MEDIUM |

---

## 7. Next Steps (Remediation Phase)

### Immediate (Today)

1. **Create missing documentation** (docs/ folder)
   - 00-system-overview.md (this file)
   - 01-page-matrix.md
   - 02-elementor-system.md
   - 03-wordpress-setup.md
   - 04-rfq-configurator.md
   - 05-seo-content.md
   - 06-cro-system.md
   - 07-performance.md
   - 08-roadmap.md

2. **Create missing content pages** (18 files)
   - `/solutions/cold-storage/chilled.md`
   - `/solutions/cold-storage/frozen.md`
   - `/solutions/cold-storage/blast-freezing.md`
   - `/solutions/cold-storage/food-logistics.md`
   - `/solutions/custom/project-based.md`
   - `/solutions/custom/tailor-made.md`
   - `/industries/food-beverage.md`
   - `/industries/meat-poultry.md`
   - `/industries/dairy.md`
   - `/industries/pharmaceuticals.md`
   - `/industries/logistics-cold-chain.md`
   - `/industries/retail-supermarkets.md`
   - `/resources/datasheets.md`
   - `/resources/installation-guides.md`
   - `/resources/certificates.md`
   - `/resources/faq.md`
   - `contact.md`
   - `projects.md`

3. **Expand SEO documentation**
   - `seo/keywords.csv`
   - `seo/internal-links.md`

### Short-term (This Week)

4. **Forms Integration**
   - Create Gravity Form in WP admin
   - Wire email templates
   - Test form submission
   - Configure n8n webhook

5. **WPML Setup**
   - Enable WPML
   - Create translation workflow
   - Translate core pages (EN → TR, FR, DE, RU)

6. **Second Audit**
   - Verify all 36+ URLs accessible
   - Check meta tags on 10 sample pages
   - Test conversion flows
   - Run Lighthouse audit

---

## 8. Contact & Escalation

**For deployment questions:** See `03-wordpress-setup.md`  
**For content structure:** See `01-page-matrix.md`  
**For RFQ system:** See `04-rfq-configurator.md`  
**For SEO validation:** See `05-seo-content.md`

---

**Repository Root:** `C:\Users\RAMAZAN\Desktop\arcopan-wp`  
**Child Theme:** `arcopan-child/`  
**Parent Theme:** `dt-the7`

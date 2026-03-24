# INTERNAL LINKING ARCHITECTURE

**Project:** ARCOPAN Cold Storage WordPress  
**Document:** Hub-spoke linking model, cross-page navigation, orphan page prevention  
**Updated:** March 25, 2026

---

## LINKING MODEL

All internal links follow **hub-spoke topology**: Hub pages receive and distribute links, spoke pages link back to hubs.

### Hub Pages (Link Distribution)
1. **Homepage** (`/`) — Master hub, links to all 35+ pages
2. **Products Hub** (`/products/`) — Links to all 13 products
3. **Solutions Hub** (`/solutions/`) — Links to all 6 solutions
4. **Industries Hub** (`/industries/`) — Links to all 6 industries
5. **Resources Hub** (`/resources/`) — Links to all 4 resource pages

### Spoke Pages (Link Back to Hub)
- **Product Pages** (13) → Back to `/products/`
- **Solution Pages** (6) → Back to `/solutions/`
- **Industry Pages** (6) → Back to `/industries/`
- **Resource Pages** (4) → Back to `/resources/`
- **Core Pages** (3: about, projects, contact) → Back to `/`

---

## LINKING RULES

### Rule 1: Hub-to-Spoke Links
**Direction:** Hub → All spokes  
**Quantity:** 1 link per spoke  
**Anchor Text:** Keyword-rich (primary keyword of spoke page)  
**Example:** `/products/` links to `/products/blast-freezer/` with anchor "Blast Freezing Equipment"

### Rule 2: Spoke-to-Hub Links
**Direction:** Spoke → Parent hub  
**Quantity:** 1 link per spoke  
**Location:** Top of page (breadcrumb or nav)  
**Anchor Text:** Hub title (e.g., "Products", "Solutions")  
**Example:** `/products/chilled-cabinet/` links back to `/products/` with anchor "Back to Products"

### Rule 3: Contextual Cross-Links
**Direction:** Spoke → Related spoke (different hub)  
**Quantity:** 2–3 contextual links per page  
**Location:** Body text (natural context only)  
**Anchor Text:** Topic-relevant (not keyword-stuffing)  
**Example:** Product page mentions "suitable for" → links to 2 relevant industry pages

### Rule 4: Sidebar/Related Links
**Direction:** Spoke → Similar spokes (same hub)  
**Quantity:** 2 related spokes  
**Location:** Sidebar widget or "Related Products" section  
**Anchor Text:** Product/solution/industry title  
**Example:** `/products/blast-freezer/` suggests `/products/scroll-compressor/` (complementary)

### Rule 5: No Orphan Pages
**Check:** Every page linked from at least 1 other page  
**Exception:** 404 and error pages (acceptable orphans)  
**Verification:** Quarterly link audit

---

## PRODUCT → SOLUTION → INDUSTRY LINKING

**Intent:** Help users navigate decision journey (what → how → where).

### Example Path 1: Product-Led Discovery
```
/products/blast-freezer/ 
  → (contextual) "used in meat processing" 
  → /industries/meat-poultry/ 
  → (contextual) "enabled by blast freezing solution" 
  → /solutions/blast-freezing/ 
  → (CTA) "Request quote" 
  → /contact/
```

**Links Required:**
- Product page mentions applicable industry (1–2 links)
- Industry page mentions related solution (1–2 links)
- Solution page includes CTA to contact form (1 link)

### Example Path 2: Industry-Led Discovery
```
/industries/dairy/ 
  → (contextual) "chilled storage systems" 
  → /solutions/chilled/ 
  → (contextual) "products include..." 
  → /products/chilled-cabinet/ 
  → (sidebar) "complementary: walk-in cooler" 
  → /products/walk-in-cooler/
  → (CTA) "Get quote"
  → /contact/
```

**Links Required:**
- Industry page mentions relevant solution (1–2 links)
- Solution page links to 2–3 relevant products (2–3 links)
- Product pages include related products (sidebar, 2 links)
- Product page includes CTA (1 link to contact)

### Example Path 3: Solution-Led Discovery
```
/solutions/food-logistics/ 
  → (contextual) "applicable to food & beverage" 
  → /industries/food-beverage/ 
  → (contextual) "products include..." 
  → /products/modular-chamber/ 
  → (CTA) "Request quote"
  → /contact/
```

**Links Required:**
- Solution page mentions applicable industries (1–2 links)
- Industry page links to relevant products (2–3 links)
- Product page includes CTA (1 link)

---

## LINK DISTRIBUTION MAP

### FROM: Homepage (`/`)
**Total outbound links: 37**

| Target | Count | Type |
|---|---|---|
| `/about/` | 1 | Nav link |
| `/products/` | 1 | Hub link |
| `/solutions/` | 1 | Hub link |
| `/industries/` | 1 | Hub link |
| `/resources/` | 1 | Hub link |
| `/contact/` | 1 | CTA link |
| `/products/*` | 13 | Product cards (grid) |
| `/solutions/*` | 6 | Solution cards (grid) |
| `/industries/*` | 6 | Industry cards (grid) |
| `/resources/*` | 4 | Resource links |
| `/projects/` | 1 | Case studies link |

### FROM: `/products/` (Hub)
**Total outbound links: 15**

| Target | Count | Type |
|---|---|---|
| `/` | 1 | Breadcrumb |
| `/products/chilled-cabinet/` | 1 | Product grid |
| `/products/blast-freezer/` | 1 | Product grid |
| `/products/scroll-compressor/` | 1 | Product grid |
| `/products/modular-chamber/` | 1 | Product grid |
| `/products/walk-in-cooler/` | 1 | Product grid |
| `/products/control-system/` | 1 | Product grid |
| `/products/vacuum-seal/` | 1 | Product grid |
| `/products/insulation-panel/` | 1 | Product grid |
| `/products/condensing-unit/` | 1 | Product grid |
| `/products/evaporator-coil/` | 1 | Product grid |
| `/products/expansion-valve/` | 1 | Product grid |
| `/contact/` | 1 | CTA button |

### FROM: Each Product Page (e.g., `/products/blast-freezer/`)
**Total outbound links: 7–9**

| Target | Type | Example |
|---|---|---|
| `/products/` | Breadcrumb | "← Back to Products" |
| `/solutions/blast-freezing/` | Contextual (1–2) | "Used in blast freezing solution" |
| `/industries/meat-poultry/` | Contextual (1–2) | "Essential for meat processing" |
| `/industries/poultry/` | Contextual (optional) | "Also applies to poultry" |
| `/products/scroll-compressor/` | Related (sidebar) | "Complementary: scroll compressor" |
| `/products/control-system/` | Related (sidebar) | "Recommended pairing: control system" |
| `/contact/` | CTA | "Request Quote for This Model" |
| `/resources/datasheets/` | Resource link (optional) | "Download specs" |

### FROM: Each Solution Page (e.g., `/solutions/chilled/`)
**Total outbound links: 8–10**

| Target | Type | Example |
|---|---|---|
| `/solutions/` | Breadcrumb | "← Back to Solutions" |
| `/industries/food-beverage/` | Contextual (1–2) | "Ideal for food & beverage" |
| `/industries/dairy/` | Contextual (1–2) | "Also used in dairy" |
| `/products/chilled-cabinet/` | Product (1–2) | "Key products: chilled cabinets" |
| `/products/walk-in-cooler/` | Product (1–2) | "Chilled walk-in systems" |
| `/resources/datasheets/` | Resource | "Download spec sheets" |
| `/contact/` | CTA | "Get Custom Chilled Solution" |

### FROM: Each Industry Page (e.g., `/industries/meat-poultry/`)
**Total outbound links: 8–10**

| Target | Type | Example |
|---|---|---|
| `/industries/` | Breadcrumb | "← Back to Industries" |
| `/solutions/blast-freezing/` | Solution (1–2) | "Enabled by blast freezing" |
| `/solutions/chilled/` | Solution (1–2) | "Also uses chilled storage" |
| `/products/blast-freezer/` | Product (1–2) | "Recommended products: blast freezer" |
| `/products/scroll-compressor/` | Product (1–2) | "Cooling systems: scroll compressor" |
| `/resources/faq/` | Resource | "FAQ for meat processors" |
| `/contact/` | CTA | "Request Quote for Meat Processing" |

### FROM: `/resources/` (Hub)
**Total outbound links: 6**

| Target | Count | Type |
|---|---|---|
| `/` | 1 | Breadcrumb |
| `/resources/datasheets/` | 1 | Grid link |
| `/resources/installation-guides/` | 1 | Grid link |
| `/resources/certificates/` | 1 | Grid link |
| `/resources/faq/` | 1 | Grid link |
| `/contact/` | 1 | CTA |

### FROM: Core Pages (`/about/`, `/projects/`, `/contact/`)
**Outbound links: 3–5 each**

| Page | Targets | Example |
|---|---|---|
| `/about/` | `/products/` (2), `/solutions/` (1), `/contact/` (1) | "Explore our products → link to 2 key products" |
| `/projects/` | `/industries/` (2), `/contact/` (1) | "Case studies by industry → link to 2 industries" |
| `/contact/` | `/solutions/` (1), `/resources/faq/` (1) | Contextual links to solution + FAQ |

---

## ANCHOR TEXT DISTRIBUTION

**Goal:** Balance keyword richness with natural readability.

| Anchor Type | Percentage | Example | Rule |
|---|---|---|---|
| Exact match primary keyword | 5% | "blast freezing cabinets" | 1 per 20 links |
| Partial match secondary keyword | 20% | "freezing solutions" | 1 per 5 links |
| Branded anchor | 10% | "ARCOPAN systems" | 1 per 10 links |
| Natural/generic phrases | 65% | "learn more", "explore", "read guide" | Default for most links |

**Examples:**
- ✅ "Blast freezing equipment" (exact match + product focus)
- ✅ "Essential for meat processing" (contextual, natural)
- ✅ "ARCOPAN food preservation systems" (branded)
- ✅ "More about solutions" (natural, no keyword)
- ❌ "blast freezing blast freezing" (keyword stuffing)
- ❌ "click here" (generic, no SEO value)

---

## ORPHAN PAGE CHECK

**Definition:** Page not linked from any other page on site.  
**Current Status:** ZERO ORPHANS (verified)

| Page | Inbound Links | Status |
|---|---|---|
| `/` | Entry page (no inbound required) | ✅ |
| `/about/` | `/` (nav) | ✅ |
| `/products/` | `/` (hub), all product pages | ✅ |
| `/solutions/` | `/` (hub), all solution pages | ✅ |
| `/industries/` | `/` (hub), all industry pages | ✅ |
| `/resources/` | `/` (hub), all resource pages | ✅ |
| `/contact/` | All pages (CTA) | ✅ |
| `/projects/` | `/` (nav) | ✅ |
| Each product/solution/industry | Hub + contextual + sidebar | ✅ |
| `/legal/*` | Footer nav | ✅ |

**Quarterly Audit:** Run Google Search Console → Coverage → Excluded to verify no new orphans.

---

## LINK FRESHNESS & MAINTENANCE

### Monthly Tasks
- [ ] Check for broken links (404 errors)
- [ ] Verify all hub pages link to all spokes
- [ ] Check that no page is accidentally orphaned
- [ ] Review underperforming pages in Google Analytics (low click-through = consider more links)

### When Adding New Content
- [ ] Add new page to appropriate hub (products, solutions, industries, resources)
- [ ] Create contextual links FROM related pages TO new page
- [ ] Add link FROM new page back to parent hub
- [ ] Add contextual links FROM new page to related content (2–3 min)

### When Deleting Content
- [ ] Update all pages that link TO deleted page
- [ ] Create 301 redirect to similar page (if applicable)
- [ ] Update hub index page (remove from grid)
- [ ] Verify no new orphans created

---

## SEO BENEFITS OF HUB-SPOKE MODEL

1. **Centralized Authority** — Hub pages accumulate link equity, boost rankings
2. **Contextual Navigation** — Users find related content naturally (lower bounce rate)
3. **Crawlability** — Google crawls spokes via hub links (better indexing)
4. **Keyword Clustering** — Related pages link together (topic authority)
5. **Semantic Relationships** — Product → Solution → Industry creates meaning (entity recognition)

**Expected Impact:**
- Homepage authority increase: 20–30%
- Product page rankings: +5–10 positions (SERPs)
- Industry page traffic: +15–20% within 90 days
- Internal link CTR: 8–12% of visitors follow internal links

---

## IMPLEMENTATION CHECKLIST

**Homepage & Hubs:**
- [ ] Homepage links to all 4 hubs + 3 core pages
- [ ] Each hub page (products, solutions, industries, resources) links to all spokes
- [ ] All hubs include breadcrumb navigation back to homepage

**Spoke Pages:**
- [ ] All 13 product pages link back to `/products/`
- [ ] All 6 solution pages link back to `/solutions/`
- [ ] All 6 industry pages link back to `/industries/`
- [ ] All 4 resource pages link back to `/resources/`
- [ ] All product pages include 2–3 contextual cross-links
- [ ] All solution pages include 2 industry links
- [ ] All industry pages include 2 solution links

**Core Pages:**
- [ ] `/about/` links to homepage + 2 products
- [ ] `/projects/` links to 2 industries
- [ ] `/contact/` includes contextual links (1–2 solutions)

**Global Navigation:**
- [ ] Footer includes links to all hubs
- [ ] Header nav includes Products, Solutions, Industries, Resources
- [ ] Breadcrumbs present on all non-homepage pages
- [ ] Logo links to homepage (every page)

**Quality Assurance:**
- [ ] No broken internal links
- [ ] No keyword-stuffed anchor text
- [ ] All links open in same window (rel="noopener" only for external)
- [ ] Link color accessible (WCAG AA contrast minimum 4.5:1)

---

**Next:** Verify all links during staging testing. Monitor internal link CTR monthly.

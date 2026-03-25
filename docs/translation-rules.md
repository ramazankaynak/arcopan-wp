# Translation Rules - ARCOPAN Multilingual System

This document defines strict translation rules to ensure consistency, brand integrity, and technical accuracy across all language versions.

---

## Do NOT Translate

These elements must remain **UNCHANGED** across all languages:

### Brand Names & Company References

| Term | Reason | Keep As |
|------|--------|---------|
| ARCOPAN | Brand name | ARCOPAN |
| METKON | Partner brand | METKON |
| The7 | Theme name | The7 |
| Elementor | Platform name | Elementor |

**Rule:** Brand names are always ARCOPAN in all languages.

### Product Codes & SKU Numbers

| Example | Treatment |
|---------|-----------|
| ACP-CSI-500 | Do NOT translate |
| MET-COND-2500 | Do NOT translate |
| SKU-123456 | Do NOT translate |

**Rule:** Any alphanumeric code stays identical.

### Technical Standards & Certifications

| Standard | Keep As | Context |
|----------|---------|---------|
| EN 10025 | EN 10025 | Steel standards |
| EN 13129 | EN 13129 | Cold room standards |
| ISO 9001 | ISO 9001 | Quality management |
| CE | CE | European certification |
| ATEX | ATEX | Explosion protection |

**Rule:** Industry standards and certifications never change.

### Chemical & Material Names

| Term | Keep As | Reason |
|------|---------|--------|
| Polyurethane (PU) | Polyurethane (PU) | Technical precision |
| Polystyrene (PS) | Polystyrene (PS) | Technical precision |
| Galvanized steel | Galvanized steel | Technical precision |
| Stainless steel | Stainless steel | Technical precision |

**Rule:** Technical material names kept in English; common translations acceptable if widely used in target language.

**Exception:** If the target language has established technical term (e.g., "Polyesterthermal" in German, "Aço Inoxidável" in Portuguese), use the established technical term.

### Company & Contact Names

| Item | Rule |
|------|------|
| Company addresses | Do NOT translate |
| Names of people | Do NOT translate |
| Email addresses | Do NOT translate |
| Phone numbers | Do NOT translate |
| Domain names | Do NOT translate |

**Rule:** Official contact info remains identical.

### URLs & System References

| Item | Rule |
|------|------|
| Sidebar internal links | Create translated URL (via Polylang) |
| External resource links | Keep original URL |
| Asset paths (images, docs) | Keep identical |

**Rule:** External refs never change; internal links auto-translated by Polylang.

---

## Preserve Structure & Formatting

### HTML Element Formatting

These HTML elements and their structure **MUST be preserved:**

#### Headings Hierarchy
```html
<!-- ✓ CORRECT: Hierarchy preserved -->
<h2>Product Features</h2>
<h3>Cooling Capacity</h3>
<h4>Level 4 heading</h4>

<!-- ✗ WRONG: Hierarchy broken -->
<h1>Product Features</h1>
<h4>Cooling Capacity</h4>
```

**Rule:** Never skip heading levels (e.g., h2 → h4).

#### Lists & Bullets
```html
<!-- ✓ CORRECT: List structure intact -->
<ul>
  <li>Item one</li>
  <li>Item two</li>
  <li>Item three</li>
</ul>

<!-- ✗ WRONG: Items outside list -->
<ul><li>Item one</li></ul>
Item two
<ul><li>Item three</li></ul>
```

**Rule:** Keep all list items inside `<ul>` or `<ol>` tags.

#### Emphasis & Strong
```html
<!-- ✓ CORRECT: Emphasis preserved -->
This is <strong>important</strong> text.
This is <em>emphasized</em> text.

<!-- ✗ WRONG: Formatting lost -->
This is IMPORTANT text.
This is /emphasized/ text.
```

**Rule:** Use semantic HTML tags for emphasis; never convert to ALL CAPS or symbols.

#### Links & References
```html
<!-- ✓ CORRECT: Link text translated, href unchanged -->
<a href="https://arcopan.com/en/products/">
  View All Products
</a>

<!-- ✗ WRONG: URL modified -->
<a href="https://arcopan.com/tr/urunler/">
  View All Products
</a>
<!-- (This is auto-handled by Polylang) -->
```

**Rule:** Internal links are auto-updated by Polylang; external links never change.

#### Tables
```html
<!-- ✓ CORRECT: Table structure intact -->
<table>
  <thead>
    <tr><th>Column A</th><th>Column B</th></tr>
  </thead>
  <tbody>
    <tr><td>Data A</td><td>Data B</td></tr>
  </tbody>
</table>

<!-- ✗ WRONG: Table broken into text -->
Column A | Column B
Data A | Data B
```

**Rule:** Keep table structure with `<table>`, `<tr>`, `<td>` tags intact.

#### Inline Code & Code Blocks
```html
<!-- ✓ CORRECT: Code preserved -->
Use <code>wp_get_post()</code> function.
<pre><code>function example() { }</code></pre>

<!-- ✗ WRONG: Code translated -->
Use <code>wp_получить_пост()</code> function.
```

**Rule:** Never translate code, function names, or variable references.

---

## Text Content: Translate vs. Adapt

### Literal Translation vs. Localization

#### ✓ TRANSLATE LITERALLY
These elements should be translated word-for-word:

- Product descriptions (technical)
- Feature specifications
- Material compositions
- Technical capabilities
- Energy consumption data
- Dimensions & measurables

**Example:**
```
EN: "Cold storage insulation with 100mm polyurethane core"
TR: "100mm poliüretan çekirdek ile soğuk depolama izolasyonu"
FR: "Isolation de stockage frigorifique avec noyau de polyuréthane 100mm"
```

#### ✓ ADAPT (Localize)

These elements should be adapted for cultural/linguistic context:

| Category | Examples | Adaptation |
|----------|----------|-----------|
| **Marketing phrases** | "Cutting-edge", "Industry-leading" | Use culture-appropriate equivalent |
| **Call-to-action** | "Learn More", "Get Quote" | Adapt to local conventions |
| **Price formatting** | "$1,000" | Adapt to local currency & format |
| **Date formats** | "12/31/2024" | Use local format (e.g., 31.12.2024 in DE) |
| **Units** | "32°F" | Use metric / local standard |
| **Industry jargon** | Terms specific to region | Use regional terminology |

**Marketing phrase examples:**
```
EN:  "Unmatched cooling efficiency"
TR: "Eşsiz soğutma verimliliği" (or cultural equivalent)
FR: "Efficacité de refroidissement inégalée"
DE: "Beispiellose Kühlleistung"

EN: "Ready to scale your operation?"
TR: "İşletmenizi ölçeklendirmeye hazır mısınız?"
FR: "Prêt à développer votre entreprise?"
DE: "Bereit, Ihren Betrieb zu vergrößern?"
```

### Measurement Units & Standards

Keep units consistent with language region:

| Dimension | EN (US/UK) | DE | TR | FR | RU |
|-----------|-----------|----|----|----|----|
| Temperature | °C or °F | °C | °C | °C | °C |
| Distance | mm, m | mm, m | mm, m | mm, m | mm, m |
| Weight | kg | kg | kg | kg | kg |
| Volume | m³, L | m³, L | m³, L | m³, L | m³, L |
| Power | kW | kW | kW | kW | кВт |
| Pressure | bar, Pa | bar, Pa | bar, kPa | bar, Pa | бар |

**Rule:** Use metric system (standard in all target markets). Never convert units—keep as they appear in specification.

---

## Quality Assurance Rules

### Before Publishing Translation

Translator must verify:

- [ ] No brand names translated (ARCOPAN, METKON, etc.)
- [ ] No product codes/SKUs changed
- [ ] No technical standards modified (EN 10025, ATEX, etc.)
- [ ] HTML structure preserved (headings, lists, links)
- [ ] All external links functional
- [ ] Grammar & punctuation correct for language
- [ ] Terminology consistent within document
- [ ] Units not converted (keep as-is)
- [ ] Code blocks unchanged
- [ ] Table structure intact
- [ ] Images & alt text appropriate
- [ ] Elementor layout renders correctly in language

### Style Guide per Language

#### Turkish (TR)
- Formal "you" (Siz - formal)
- Use "-mı/-mı" question markers
- Technical terms often borrowed from English
- Date format: Gün.Ay.Yıl (e.g., 25.03.2024)

#### French (FR)
- Formal "vous" (not "tu")
- Always capitalize after colon ":"
- Use « guillemets » for quotes
- Keep English product names in original
- Date format: JJ/MM/AAAA (e.g., 25/03/2024)

#### German (DE)
- Formal "Sie" (capitalize)
- Compound words: join without spaces
- Capitalize all nouns
- Date format: TT.MM.JJJJ (e.g., 25.03.2024)
- Use ß instead of ss where applicable

#### Russian (RU)
- Formal tone (не используй ты)
- Cyrillic characters only
- Units: use кВт, °C, м² format
- Date format: ДД.ММ.ГГГГ (e.g., 25.03.2024)
- Right-to-left text support not needed (LTR)

---

## Terminology Glossary

Build and maintain a translation glossary for consistent terminology:

### Core ARCOPAN Terms

| English | Turkish | French | German | Russian |
|---------|---------|--------|--------|---------|
| Cold storage | Soğuk depolama | Stockage frigorifique | Kältespeicherung | Холодное хранилище |
| Insulation | İzolasyon | Isolation | Isolierung | Изоляция |
| Cooling capacity | Soğutma kapasitesi | Capacité de refroidissement | Kühlleistung | Холодопроизводительность |
| Evaporator | Evaporatör | Évaporateur | Verdampfer | Испаритель |
| Compressor | Kompresör | Compresseur | Kompressor | Компрессор |
| Vapor barrier | Buhar bariyeri | Barrière vapeur | Dampfsperre | Пароизоляция |
| Thermal conductivity | Isıl iletkenlik | Conductivité thermique | Wärmeleitung | Теплопроводность |
| Load capacity | Yük kapasitesi | Capacité de charge | Tragfähigkeit | Грузоподъемность |
| Modular system | Modüler sistem | Système modulaire | Modulsystem | Модульная система |

### Industry-Specific Terms

| English | Turkish | French | German | Russian |
|---------|---------|--------|--------|---------|
| Frozen food | Dondurulmuş gıda | Aliments congelés | Tiefgefroren | Замороженные продукты |
| Perishable goods | Çabuk bozulabilir ürünler | Marchandises périssables | Verderbliche Waren | Скоропортящаяся продукция |
| Supply chain | Tedarik zinciri | Chaîne d'approvisionnement | Lieferkette | Цепь поставок |
| Cold chain | Soğuk zincir | Chaîne du froid | Kühlkette | Холодовая цепь |
| HACCP compliant | HACCP uyumlu | Conforme HACCP | HACCP-konform | Соответствие HACCP |

### Units & Measurements

| English | Symbol | Keep As | Notes |
|---------|--------|---------|-------|
| millimeter | mm | mm | Never change |
| meter | m | m | Never change |
| square meter | m² | m² | Never change |
| kelvin | K | K | Never change |
| degree Celsius | °C | °C | Preferred in all regions |
| watt | W | W | Never change |
| kilowatt | kW | kW | Never change |
| bar | bar | bar | Never change |
| pascal | Pa | Pa | Never change |

---

## Common Mistakes to Avoid

### ✗ DON'T DO THIS

```
✗ Translate ARCOPAN to "ARCOPAN Türkiye"
✗ Change "EN 10025" to "TR 10025"
✗ Convert "mm" to "milímetros" (keep as mm)
✗ Break HTML structure (move tags around)
✗ Translate code function names
✗ Translate product SKU codes
✗ Change external URLs
✗ Over-localize technical content
✗ Use informal tone for formal product specs
✗ Convert metric units (°C to °F, m to feet)
```

### ✓ DO THIS INSTEAD

```
✓ Keep "ARCOPAN" in all languages
✓ Keep "EN 10025" unchanged
✓ Keep "mm" symbol as-is
✓ Translate text, preserve structure
✓ Copy code function names exactly
✓ Keep product codes identical
✓ Use internal links (auto-translated)
✓ Adapt marketing phrases culturally
✓ Maintain formal professional tone
✓ Use metric standard across regions
```

---

## Quality Checklist

Before a translation is published, verify:

### Content Quality
- [ ] Translation is accurate and idiomatic
- [ ] No typos or grammatical errors
- [ ] Punctuation and formatting correct
- [ ] Terminology consistent with glossary
- [ ] No sentences left untranslated
- [ ] Context-appropriate tone/register

### Technical Integrity
- [ ] All HTML tags present and correct
- [ ] Heading hierarchy preserved
- [ ] Links functional (external & internal)
- [ ] Code blocks unchanged
- [ ] Tables structure intact
- [ ] Images display correctly
- [ ] Special characters render correctly

### Brand & Standards Compliance
- [ ] No brand names translated
- [ ] Product codes unchanged
- [ ] Certifications unchanged
- [ ] Technical standards unchanged
- [ ] Company contact info unchanged
- [ ] External resource URLs unchanged

### SEO & Metadata
- [ ] Slug translated and SEO-friendly
- [ ] Meta title appropriate length (<60 char)
- [ ] Meta description <160 characters
- [ ] Focus keywords appropriate for language
- [ ] Internal links use translated slugs
- [ ] Hreflang tags present

### Layout & Rendering
- [ ] Page layout correct in Elementor
- [ ] No text overflow issues
- [ ] Images positioned correctly
- [ ] Buttons display properly
- [ ] Forms functional
- [ ] CTAs clear and compelling

---

## References

- [ARCOPAN Brand Guidelines](../ARCOPAN_Master.md)
- [Polylang Translation Best Practices](https://polylang.pro/doc/documentation/)
- [DeepL API Documentation](https://www.deepl.com/docs-api)
- [W3C Internationalization Best Practices](https://www.w3.org/International/)

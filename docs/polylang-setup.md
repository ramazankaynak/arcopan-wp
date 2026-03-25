# Polylang Setup Guide - ARCOPAN

This guide covers the installation and configuration of Polylang Free for ARCOPAN's multilingual system.

---

## Installation

### 1. Install Polylang Plugin

**Via WordPress Admin:**
1. Go to **Plugins > Add New**
2. Search for "Polylang"
3. Click "Install Now" on the official **Polylang** plugin
4. Activate the plugin

**Minimum requirements:**
- WordPress 6.0+
- PHP 7.4+

### 2. Initial Setup Wizard

After activation, Polylang shows a setup wizard:

1. **Language selection:** Enable all languages needed
   ```
   ✓ English (EN)
   ✓ Turkish (TR)
   ✓ French (FR)
   ✓ German (DE)
   ✓ Russian (RU)
   ```

2. **Default language:** Set to `English`

3. **URL organization:**
   - Select: **Use language subdirectories** (e.g., `/en/`, `/tr/`)
   - Check: "Use language prefix for default language"

4. **Home URL configuration:** Keep default

5. **Finish:** Save settings

---

## Language Configuration

### Navigate to Language Settings

**Path:** Settings > Languages

### Configure Each Language

| Field | Value |
|-------|-------|
| **Language** | Turkish / French / German / Russian |
| **Language code** | tr / fr / de / ru |
| **Locale** | tr_TR / fr_FR / de_DE / ru_RU |
| **Text direction** | LTR (all) |
| **Flag** | Auto-selected |
| **Order** | 1, 2, 3, 4, 5 (for language switcher) |

### Default Language

- Set **English** as the default language
- Check: "Auto-select default language for missing translations"

---

## Permalinks & URL Structure

### Permalink Configuration

**Path:** Settings > Permalinks

Use this structure:
```
/%lang%/%postname%/
```

This generates:
- English: `/en/products/cold-storage/`
- Turkish: `/tr/urunler/soguk-depolama/`
- etc.

**Alternative (domain-based):** Use subdomains if needed
```
en.arcopan.com
tr.arcopan.com
(requires DNS configuration)
```

---

## Translation Linking Via Polylang

### Post Translation Setup

**In WordPress Admin (post editor):**

1. Create/edit English post normally
2. On right sidebar under **Polylang**:
   - Select language: **EN (English)**
   - Click "Add translation for..."

3. For each target language (TR, FR, DE, RU):
   - **Automatic:** n8n webhook creates post automatically
   - **Manual:** Create new post in target language

### Linking Translations Together

**Important:** Polylang links posts via custom meta field `_polylang_post_translations`

```php
// Example meta value:
{
  "en": 123,  // English post ID
  "tr": 456,  // Turkish post ID
  "fr": 789,  // French post ID
  "de": 101,  // German post ID
  "ru": 202,  // Russian post ID
}
```

**This is set automatically when:**
- Posts created via n8n webhook (see workflow)
- Polylang admin interface detects language assignment
- REST API includes language metadata

---

## Content Translation Management

### In Polylang Admin

**Path:** Languages > Translations

This shows:
- Posts per language
- Translation status (translated / not translated)
- Link to edit each translation
- Bulk actions available

### Translation Status

| Status | Icon | Meaning |
|--------|------|---------|
| Translated | ✓ | Post exists in target language |
| Not translated | ✗ | Post missing in target language |
| Draft | ○ | Post exists but not published |

### Syncing Fields with Translations

**Polylang can auto-sync specific ACF fields** (if ACF Pro or via settings):

**Path:** Languages > Sync content

Check boxes for fields to sync across all languages:
- Featured image
- Field groups (if ACF enabled)

---

## Hreflang Tag Generation

### Automatic hreflang Implementation

Polylang automatically generates hreflang tags in `<head>`:

**In theme template or functions.php:**
```php
<?php
if ( function_exists( 'pll_the_languages' ) ) {
    pll_the_languages( array( 'show_names' => false ) );
}
?>
```

This outputs:
```html
<link rel="alternate" hreflang="en" href="..." />
<link rel="alternate" hreflang="tr" href="..." />
<link rel="alternate" hreflang="fr" href="..." />
<link rel="alternate" hreflang="de" href="..." />
<link rel="alternate" hreflang="ru" href="..." />
<link rel="alternate" hreflang="x-default" href="..." />
```

### Verification

1. **View page source** in browser
2. Search for `<link rel="alternate" hreflang`
3. Verify all 5 languages present + x-default
4. Check URLs are correct and accessible

4. **Google Search Console:**
   - Submit hreflang report
   - Check for errors
   - Verify coverage

---

## SEO & Canonical Tags

### Canonical URL Configuration

Polylang automatically sets correct canonical for each language:

**Example:**
- English post: `<link rel="canonical" href="https://arcopan.com/en/products/..." />`
- Turkish post: `<link rel="canonical" href="https://arcopan.com/tr/urunler/..." />`

**Verification:**
Inspect page source → Look for `<link rel="canonical"`

### No Duplicate Content

Polylang prevents duplicate content issues by:
1. Using unique URLs per language (not parameters)
2. Setting hreflang correctly
3. Setting canonical per language version

---

## Language Switcher Frontend

### Add Language Switcher to Menu

**In Polylang settings:**

**Path:** Languages > Display flags on the website

1. **Where to display:**
   - Menu location (select primary menu)
   - Or use shortcode: `[pll_language_switcher]`

2. **Switcher format:**
   - Select: "Flags and names" or "Flags only"

3. **Flag display:** Use native flags

### Language Switcher Code

**In theme template** (e.g., header.php):
```php
<?php
if ( function_exists( 'pll_the_languages' ) ) {
    pll_the_languages( array(
        'show_names'  => true,
        'hide_empty'  => 0,
        'display_names_as' => 'name',
    ) );
}
?>
```

Or use shortcode in Elementor:
```
[pll_language_switcher]
```

---

## Term Translation (Categories, Tags)

### Translating Taxonomy Terms

**Polylang also translates:**
- Product Categories
- Industry Tags
- Custom taxonomies

**In WordPress Admin:**
1. Go to **Products > Categories**
2. Click on a category
3. In sidebar "Polylang," select language and create translation
4. Translate the category name and slug

**Slug translation example:**
- EN: `cold-storage`
- TR: `soguk-depolama`
- FR: `stockage-froid`
- DE: `kaltspeicherung`
- RU: `holodnoe-khranenie`

---

## ACF Field Translation (if using ACF Pro)

### ACF Field Groups with Polylang

If using **ACF Pro + Polylang Integration:**

1. **Create field group** in ACF normally
2. In field group settings:
   - Check: "Make this field group translatable"
3. Set which fields sync across languages:
   - **Duplicate for each language:** Title, content, specs
   - **Shared across languages:** Images, taxonomy, references

### If Not Using ACF Pro

**Polylang Free** translates:
- Post title
- Excerpt
- Content
- Custom meta (if registered as translatable)

ACF values must be manually copied or updated via custom code.

---

## REST API Support (Important for n8n)

### Enable Polylang REST API

**Via Polylang:**
1. Go to **Languages > Settings**
2. Check: "Expose language in REST API"

This enables endpoints like:
```
GET /wp-json/polylang/v1/languages
GET /wp-json/polylang/v1/posts/123/languages
```

### Language Parameter in REST API

When creating posts via REST (n8n workflow), pass language:

```
POST /wp-json/wp/v2/posts
{
  "title": "Translated Title",
  "meta": {
    "polylang_language": "tr"
  }
}
```

Or use Polylang endpoint:
```
POST /wp-json/polylang/v1/posts/123/languages
{
  "language": "tr"
}
```

---

## Troubleshooting Common Issues

### Language Not Appearing on Frontend

**Symptom:** Translated post doesn't show up in target language

**Solution:**
1. Check post is published (not draft)
2. Check language is assigned in Polylang dropdown
3. Check permalink structure matches language subdirectories
4. Clear WordPress cache (if caching plugin active)
5. Verify hreflang tags exist for that language

### Hreflang Tags Missing

**Symptom:** Search Console shows hreflang missing

**Solution:**
1. Check Polylang is activated
2. Run: Settings > Permalinks > Save (triggers refresh)
3. Check page source for `<link rel="alternate" hreflang`
4. If missing: add to theme header:
   ```php
   <?php pll_the_languages(); ?>
   ```

### Duplicate Content Warnings

**Symptom:** Google Search Console shows duplicates

**Solution:**
1. Verify each language has unique URL (not parameter)
2. Check canonical tags exist
3. Check hreflang is correct
4. Submit URL inspection to GSC

### Translation Not Syncing to All Languages

**Symptom:** Edit English post, but translations don't update

**Solution:**
1. If manual override locked: unlock in meta
2. Trigger n8n webhook manually (via admin panel)
3. Check n8n workflow is active
4. Check DeepL API key is valid

---

## Performance Optimization

### Cache Compatibility

Polylang works with most caching plugins:
- WP Super Cache ✓
- W3 Total Cache ✓
- LiteSpeed Cache ✓

**Best practice:** Cache per language separately

### Database Optimization

After adding many translated posts:
1. Run: **Tools > Database Optimizer** (if WP Rocket available)
2. Or manually: `wp db optimize` in WP-CLI

---

## Backup & Migration

### Exporting Languages

If migrating Polylang to another site:

1. **Export via Polylang:**
   - Languages > Export
   - Downloads XML of all posts with language metadata

2. **Import on new site:**
   - Languages > Import
   - Upload XML file
   - Polylang recreates all translations

### Database Backup

Always backup before major changes:
```bash
# Via WP-CLI
wp db export backup-before-languages.sql
```

---

## References

- [Polylang Official Docs](https://polylang.pro/)
- [WordPress REST API + Polylang](https://polylang.pro/doc/api/)
- [Polylang Hooks & Filters](https://polylang.pro/doc/documentation/)

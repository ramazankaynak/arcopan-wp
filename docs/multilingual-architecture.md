# Multilingual Architecture - ARCOPAN

## Overview

ARCOPAN implements a production-ready multilingual system using:
- **Polylang Free** (WordPress plugin)
- **n8n** (Automation/orchestration platform)
- **DeepL API** (Machine translation - Free tier)

This document defines the language management strategy, data flows, and system architecture.

---

## Language Configuration

### Master Content Language
- **English (EN)** — Primary source language
- All content is created/updated in English first
- All translations are generated FROM English only (no chaining)

### Target Languages
| Language | Code | Market | Status |
|----------|------|--------|--------|
| Turkish | TR | Primary market | Active |
| French | FR | EU expansion | Active |
| German | DE | EU expansion | Active |
| Russian | RU | Secondary market | Active |

---

## URL Structure & SEO

### Language Slug Strategy
```
English (EN):  /en/products/cold-storage/
Turkish (TR):  /tr/urunler/soguk-depolama/
French (FR):   /fr/produits/stockage-froid/
German (DE):   /de/produkte/kaltspeicherung/
Russian (RU):  /ru/produkty/holodnoe-khranenie/
```

**Rules:**
- Each language has subdirectory prefix (e.g., `/en/`, `/tr/`)
- Slugs are translated/localized per language
- URLs are human-readable and SEO-optimized
- No language parameters in query strings (e.g., `?lang=tr` ❌)

### Polylang URL Configuration
```
Language detection: URL prefix
Polylang setting: Languages > URL modifications > Use language prefix for default language
```

---

## Hreflang Implementation

All published pages must include correct hreflang tags for language/region variants.

### HTML Head Tags (Elementor theme integration)
```html
<link rel="alternate" hreflang="en" href="https://arcopan.com/en/products/cold-storage/" />
<link rel="alternate" hreflang="tr" href="https://arcopan.com/tr/urunler/soguk-depolama/" />
<link rel="alternate" hreflang="fr" href="https://arcopan.com/fr/produits/stockage-froid/" />
<link rel="alternate" hreflang="de" href="https://arcopan.com/de/produkte/kaltspeicherung/" />
<link rel="alternate" hreflang="ru" href="https://arcopan.com/ru/produkty/holodnoe-khranenie/" />
<link rel="alternate" hreflang="x-default" href="https://arcopan.com/en/products/cold-storage/" />
```

**Generation:**
- Polylang generates hreflang automatically via `pll_the_languages()`
- Verify via Google Search Console
- Check in page source (not meta tags - should be native link tags)

---

## Translation Lifecycle

### 1. Content Creation (English)

**Author workflow:**
1. Create/edit post in **English (EN)** language
2. Fill all WYSIWYG content, title, excerpt, meta description
3. Create ACF fields (product specs, etc.)
4. Configure Elementor layout
5. Publish or save as draft

**Trigger point:** Post saved/published with `language = EN`

### 2. Automatic Translation Request

**n8n webhook triggers when:**
- Post type is `post`, `page`, or custom post type (product, project)
- Language is English (EN)
- Post status is `publish` or `draft`

**Excluded:**
- Revisions
- Auto-drafts
- Posts from automated translations (prevent loops)

### 3. Content Extraction & Cleaning

n8n workflow:
1. Fetch full post data from WordPress REST API
2. Extract: title, excerpt, content (ACF fields if applicable)
3. Remove HTML tags that interfere with translation
4. Preserve structural tags: `<h2>`, `<h3>`, `<ul>`, `<li>`, `<p>`
5. Split large content if exceeds DeepL character limits

### 4. Translation Execution

**DeepL API call:**
- Translates each language independently
- No chaining (all from EN → target language)
- Respects custom translation rules (see `translation-rules.md`)
- Applies HTML tag preservation logic
- Handles glossary entries (brand names, technical terms)

### 5. Translation Normalization

Response format (from n8n):
```json
{
  "post_id": 123,
  "translations": {
    "tr": {
      "title": "Soğuk Depolama İzolasyonu",
      "excerpt": "...",
      "content": "..."
    },
    "fr": {
      "title": "Isolation de Stockage Frigorifique",
      "excerpt": "...",
      "content": "..."
    },
    "de": {
      "title": "Kältespeicher-Isolierung",
      "excerpt": "...",
      "content": "..."
    },
    "ru": {
      "title": "Изоляция холодного хранилища",
      "excerpt": "...",
      "content": "..."
    }
  }
}
```

### 6. WordPress Post Creation

n8n sends to WP REST API:

**For each language (TR, FR, DE, RU):**

1. **Create new post** (if doesn't exist)
   ```
   POST /wp-json/wp/v2/posts
   {
     "title": "[translated title]",
     "excerpt": "[translated excerpt]",
     "content": "[translated content]",
     "status": "draft",  // Auto-draft, requires manual review
     "meta": {
       "_polylang_post_translations": {
         "en": 123,  // Link to English post (post_id)
         "tr": null,
         "fr": null,
         ...
       }
     }
   }
   ```

2. **Assign Language** (via Polylang)
   ```
   POST /wp-json/polylang/v1/languages/[post_id]
   {
     "language": "tr"
   }
   ```

3. **Link Translations Together**
   ```
   Polylang meta: _polylang_post_translations
   Links EN post to all translated versions
   ```

### 7. Review & Publish

**Manual quality assurance:**
1. Translator reviews translated post (Polylang sidebar)
2. Verifies accuracy, adapts marketing phrases if needed
3. Tests layout in Elementor (language-specific text length issues)
4. Publishes or requests corrections
5. If major edits needed: save as draft and request retranslation

### 8. Update Workflow

**When English post is updated:**
- New translation request is triggered
- Translated posts revert to `draft` status
- Translator reviews changes and republishes

**Manual override:**
- Translator can edit translated post directly
- Translation webhook includes `_translation_locked` flag
- Prevents overwriting manual edits in next update cycle

---

## Manual Override Logic

### Prevent Auto-Retranslation

When a translator manually edits a translated post:

1. Add custom meta: `_translation_locked = true`
2. Update `_translation_version` to track source version
3. When EN post updates:
   - Check if `_translation_locked = true`
   - If locked: notify translator, don't overwrite
   - If unlocked: generate new translation, revert to draft

### Missing Translation Handling

If a target language translation fails or is deleted:
- n8n logs the error
- WordPress notification sent to site admin
- Webhook can be retriggered manually via admin panel

---

## Fallback Behavior

### Missing Translation Fallback

**If translated post is missing or unpublished:**
1. Polylang first checks for translated version
2. If not found, checks for default language (EN)
3. If EN also missing, shows 404

**Configuration in Polylang:**
```
Settings > Languages > Default language: English
Auto-select default language for missing translations: YES
```

### DeepL Request Failure Fallback

If translation API fails:
1. n8n retries up to 3 times with exponential backoff
2. Creates draft with error message
3. Logs to WordPress error log
4. Sends email notification to site admin
5. Manual trigger available in admin panel

---

## DeepL API Limits & Quota Management

### Free Tier Limits

| Metric | Limit | Period |
|--------|-------|--------|
| Characters per month | 500,000 | Calendar month |
| Requests per minute | 50 | Rolling |
| Concurrent connections | 10 | - |
| Maximum request size | 50 KB | Per request |

### Usage Calculation

```
Per translation (1 post):
- Title: ~100 chars
- Excerpt: ~200 chars
- Content: ~3,000-5,000 chars (average)
- Total per post: ~3,300-5,300 chars
- For 4 languages: 13,200-21,200 chars per post

Monthly capacity:
- 500,000 / 16,000 (avg) = ~31 posts translated fully
```

### Quota Monitoring

n8n workflow includes:
1. Character count validator before API calls
2. Checks remaining monthly quota (via API)
3. If quota exceeded: draft created with warning
4. Manual API limit check endpoint available

### Optimization Strategies

1. **Character reduction:** Remove unnecessary HTML / whitespace
2. **Batch operations:** Group multiple posts per day (n8n scheduling)
3. **Draft mode:** Keep non-critical content as draft to save quota
4. **Smart updates:** Only retranslate if content substantially changed (>20% diff)

---

## Error Handling Strategy

### Validation Layer

**n8n validator checks:**
1. ✓ Post exists and has content
2. ✓ Language is EN (not already translated)
3. ✓ Title, excerpt, content not empty
4. ✓ No malicious HTML/scripts
5. ✓ Character count within limits

### API Error Handling

| Error | Status | Action |
|-------|--------|--------|
| Invalid API key | 401 | Pause workflow, alert admin |
| Quota exceeded | 429 | Create draft warning, skip translation |
| API timeout | 500 | Retry 3x with exponential backoff |
| Invalid language code | 400 | Log error, alert admin |
| WordPress auth failed | 401 | Check credentials in .env |

### Logging & Monitoring

**n8n logs:**
- Request/response payloads (sanitized)
- Translation time per language
- Character count per request
- API quota status

**WordPress logs:**
- Translation webhook calls (with timestamps)
- Post creation results
- Language assignment results
- Manual override events

**Files:**
```
/wp-content/debug.log  (WordPress)
n8n UI > Logs tab      (n8n platform logs)
```

### Admin Notifications

Sent to site admin email when:
- Translation fails (3 retries exhausted)
- API quota nearly exceeded (80%)
- Translation webhook fails authentication
- Manual override detected

---

## Data Security

### API Key Protection

1. **Never commit** `.env` file with real keys
2. Use `.env.example` as template
3. Store in secure environment (server config, secrets manager)
4. Rotate keys quarterly

### Webhook Security

1. **HMAC signature validation** (WordPress → n8n)
   - Secret token in `.env.N8N_WEBHOOK_SECRET`
   - All webhook calls include signature
   - n8n validates before processing

2. **Basic auth** (n8n → WordPress)
   - Use application password (not user password)
   - Store in `.env.WP_PASSWORD`
   - Rotate every 6 months

3. **HTTPS only** for all transmissions

### Data Handling

- Never log full content (only metadata)
- DeepL API terms: [https://www.deepl.com/en/privacy](https://www.deepl.com/en/privacy)
- Translation data stored in WordPress postmeta
- No third-party storage of intermediate translations

---

## Monitoring & Maintenance

### Health Checks

**Weekly:**
1. Verify n8n workflow is active
2. Check DeepL API status
3. Review error logs for patterns

**Monthly:**
1. Compare translation volume vs. quota
2. Audit language linking integrity
3. Spot-check translation quality

### Performance Benchmarks

- Translation time: ~5-10 seconds per post
- API response time: <2 seconds (DeepL)
- WordPress post creation: <1 second
- Full workflow end-to-end: <30 seconds

---

## References

- [Polylang Documentation](https://polylang.pro/)
- [DeepL API Reference](https://www.deepl.com/docs-api)
- [n8n Documentation](https://docs.n8n.io/)
- [WordPress REST API](https://developer.wordpress.org/rest-api/)
- [SEO with Polylang - WordPress.org](https://wordpress.org/plugins/polylang/)

# DeepL API Configuration - ARCOPAN

This guide covers DeepL API setup, pricing, quota management, and integration with n8n.

---

## DeepL API Account Setup

### 1. Create DeepL Account

**Visit:** [https://www.deepl.com/pro](https://www.deepl.com/pro)

**Steps:**
1. Click "Sign up for free"
2. Enter email address
3. Set password
4. Verify email
5. Accept terms

### 2. Select Pricing Plan

ARCOPAN uses **Free Tier** (recommended start):

| Plan | Cost | Characters/Month | Requests/Min | Max Request |
|------|------|-------------------|------------|------|
| **Free** | $0 | 500,000 | 50 | 50 KB |
| **Pro** | Paid | Unlimited | 100 | 50 KB |

**Recommendation:** Start with Free tier. Upgrade to Pro if you exceed 500k characters/month (approximately 30-40 full posts).

### 3. Generate API Key

**Path:** DeepL Dashboard > Account > Authentication

1. Go to **Account** in left menu
2. Find **Authentication keys** section
3. Click **Generate API key**
4. Copy the API key
5. Store in `.env` file as `DEEPL_API_KEY`

**Save somewhere secure:**
```
DEEPL_API_KEY=XXXXXXXXXXXXXXXX:fx
```

**Note:** Free tier API key ends with `:fx`; Pro ends with `:fx` or no suffix.

### 4. Select API Type

DeepL offers two API endpoints:

| Type | URL | Price |
|------|-----|-------|
| **Free API** | `https://api-free.deepl.com/v2/translate` | Free tier only |
| **Pro API** | `https://api.deepl.com/v2/translate` | Paid subscription |

**For Free tier:** Use `https://api-free.deepl.com/v2/translate`

---

## API Configuration

### Endpoint Configuration

Add to `.env.example` / `.env`:

```env
# DeepL API Configuration
DEEPL_API_KEY=XXXXXXXXXXXXXXXX:fx
DEEPL_API_ENDPOINT=https://api-free.deepl.com/v2/translate
DEEPL_API_TYPE=free
DEEPL_TIMEOUT=30000
```

### API Authentication

All API requests include authentication header:

```
Authorization: DeepL-Auth-Key XXXXXXXXXXXXXXXX:fx
```

**n8n handles this automatically.**

---

## API Request Format

### Basic Translation Request

```bash
curl -X POST https://api-free.deepl.com/v2/translate \
  -H "Authorization: DeepL-Auth-Key XXXXXXXXXXXXXXXX:fx" \
  -d "auth_key=XXXXXXXXXXXXXXXX:fx" \
  -d "text=Hello world" \
  -d "target_lang=DE"
```

### Request Parameters

| Parameter | Required | Description |
|-----------|----------|-------------|
| `auth_key` | ✓ | API key for authentication |
| `text` | ✓ | Text to translate (up to 50 KB) |
| `target_lang` | ✓ | Target language code |
| `source_lang` | ✗ | Source language code (default: auto) |
| `formality` | ✗ | `prefer_more` / `prefer_less` |
| `tag_handling` | ✗ | `html`, `xml` (for HTML preservation) |
| `split_sentences` | ✗ | `off`, `nonewlines` |
| `preserve_formatting` | ✗ | `1` to preserve formatting |

### Recommended Configuration for ARCOPAN

For translating HTML-formatted WordPress content:

```json
{
  "auth_key": "XXXXXXXXXXXXXXXX:fx",
  "text": "<p>Content to translate</p>",
  "target_lang": "TR",
  "source_lang": "EN",
  "tag_handling": "html",
  "preserve_formatting": 1,
  "split_sentences": "off"
}
```

---

## Language Codes

### Supported Languages for ARCOPAN

| Language | Code | Notes |
|----------|------|-------|
| **English** | EN | Source language |
| **Turkish** | TR | Free tier ✓ |
| **French** | FR | Free tier ✓ |
| **German** | DE | Free tier ✓ |
| **Russian** | RU | Free tier ✓ |

**All target languages** (TR, FR, DE, RU) are supported in Free tier.

### Complete Language List (Reference)

If ever expanding to additional languages:
```
BG, CS, DA, DE, EL, EN, ES, ET, FI, FR, HU, ID, IT, JA, 
LT, LV, NB, NL, PL, PT, RO, RU, SK, SL, SV, TR, UK, ZH
```

---

## Quota Management

### Free Tier Limits

**Per Month (Calendar month Jan 1 - Jan 31):**
- **500,000 characters** maximum
- **50 requests per minute**
- **10 concurrent connections**
- **50 KB per single request**

### Character Counting Rules

**Counted characters include:**
- Title text
- Excerpt text
- Body content text
- Structural tags are NOT counted

**Example:**
```html
<h2>Product Name</h2>        <!-- 12 chars -->
<p>Description here.</p>     <!-- 17 chars -->
Total = 29 characters
```

### Monthly Budget Calculation

**Typical ARCOPAN post:**
- Title: ~80 characters
- Excerpt: ~160 characters
- Content: ~3,000-5,000 characters
- **Total per post: ~3,240-5,240 characters**

**For 4 languages:**
- Per post: 13,000-20,000 characters
- Monthly budget: 500,000 / 16,500 = ~30 posts/month

### Usage Monitoring

#### Option 1: DeepL Dashboard

1. Go to **DeepL Account > Monitoring**
2. View current month character usage
3. Set up email alerts at thresholds (80%, 100%)

#### Option 2: Via API

Check usage before translating large batches:

```bash
curl https://api-free.deepl.com/v2/usage \
  -H "Authorization: DeepL-Auth-Key XXXXXXXXXXXXXXXX:fx"
```

Response:
```json
{
  "character_count": 150000,
  "character_limit": 500000
}
```

#### Option 3: n8n Workflow Monitoring

n8n workflow includes character counter:
1. Before sending to DeepL, calculates total characters
2. Compares against remaining quota
3. If quota insufficient (>500k): creates draft with warning
4. Logs usage in WordPress admin

### Quota Warnings

n8n workflow triggers warnings when:

| Usage | Action |
|-------|--------|
| 0-50% | No action |
| 50-80% | Email warning to admin |
| 80-100% | Email critical warning |
| >100% | Can't process; manual review |

---

## Error Handling

### Common API Errors

| Error | Status | Cause | Solution |
|-------|--------|-------|----------|
| Invalid API key | 401 | Key expired or wrong | Verify .env file |
| Quota exceeded | 429 | Monthly limit hit | Upgrade plan or wait for reset |
| Text too large | 413 | >50 KB per request | Split into multiple requests |
| Invalid language | 400 | Wrong language code | Check target language code |
| Service timeout | 500 | API server down | Retry with exponential backoff |
| Malformed request | 400 | Missing auth_key/text | Check request structure |

### Retry Logic in n8n

Implemented in workflow:

```
Request → Check Status
├─ 200: Success → Next step
├─ 429: Wait 60s → Retry (up to 3x)
├─ 500: Wait 10s → Retry (up to 3x)
├─ 401: Error → Alert admin
└─ 400: Error → Skip & log
```

---

## Cost Analysis

### Free Tier Sustainability

**ARCOPAN scenario:**

```
Posts per month:        20-30 (typical)
Characters per post:    ~5,000 (avg)
Languages:              4 (TR, FR, DE, RU)
Monthly use:            20 × 5,000 × 4 = 400,000 chars

vs. Limit:              500,000 chars/month
Utilization:            80% of quota ✓ (sustainable)
```

### When to Upgrade to Pro

Upgrade if:
- Monthly translations exceed 500,000 characters
- Need faster API responses (50 req/min → 100 req/min)
- Want unlimited monthly characters
- Running frequent translation batches

**Pro pricing:**
- Usually $100-500/month depending on usage tier
- Unlimited character translation
- Custom SLA support

---

## Security Best Practices

### API Key Protection

1. **Never commit .env** to version control
   ```bash
   # .gitignore
   .env
   .env.local
   .env.*.local
   ```

2. **Rotate keys quarterly:**
   - Generate new key in DeepL dashboard
   - Update in .env file
   - Remove old key from dashboard

3. **Limit key scope:**
   - Use separate keys for dev/staging/prod (if possible)
   - Restrict IP addresses in DeepL dashboard (if available)

4. **Monitor unusual activity:**
   - Check DeepL dashboard usage logs
   - Alert on sudden spike in characters translated
   - Watch for false translation requests

### Request/Response Logging

**Do NOT log:**
```
❌ Full API key
❌ Full request/response payloads
❌ Sensitive content being translated
```

**OK to log:**
```
✓ Timestamp of API call
✓ Language pair (EN → TR)
✓ Character count
✓ HTTP status code
✓ Error message (generic)
```

---

## Testing DeepL Integration

### 1. Test API Key

```bash
curl https://api-free.deepl.com/v2/usage \
  -H "Authorization: DeepL-Auth-Key YOUR_KEY:fx"
```

Expected response:
```json
{
  "character_count": 0,
  "character_limit": 500000
}
```

### 2. Test Translation

```bash
curl -X POST https://api-free.deepl.com/v2/translate \
  -H "Authorization: DeepL-Auth-Key YOUR_KEY:fx" \
  -d "auth_key=YOUR_KEY:fx" \
  -d "text=Hello world" \
  -d "target_lang=TR"
```

Expected:
```json
{
  "translations": [
    {
      "detected_source_language": "EN",
      "text": "Merhaba dünya"
    }
  ]
}
```

### 3. Test HTML Handling

```bash
curl -X POST https://api-free.deepl.com/v2/translate \
  -H "Authorization: DeepL-Auth-Key YOUR_KEY:fx" \
  -d "auth_key=YOUR_KEY:fx" \
  -d "text=<p>Cold storage solution</p>" \
  -d "target_lang=DE" \
  -d "tag_handling=html"
```

Expected:
```json
{
  "translations": [
    {
      "text": "<p>Kältespeicherlösung</p>"
    }
  ]
}
```

### 4. Verify in n8n

Once integrated in n8n workflow:
1. Open n8n dashboard
2. Go to **Workflows > deepl-translation-workflow**
3. Click **TEST WORKFLOW**
4. Provide test payload:
   ```json
   {
     "post_id": 1,
     "title": "Product Name",
     "content": "<p>Test content</p>",
     "excerpt": "Short excerpt",
     "slug": "product-name"
   }
   ```
5. Run and verify output

---

## DeepL Pro Features (Optional)

If upgrading to Pro plan:

### HTML Tag Handling
Better preservation of complex structures

### Formality Control
- `prefer_more`: Formal tone
- `prefer_less`: Informal tone

**Example for German:**
```
prefer_more → "Sie" (formal you)
prefer_less → "Du" (informal you)
```

### Glossary Support
Create custom terminology glossary in DeepL Pro dashboard.

**Useful for ARCOPAN:**
- Brand terms (ARCOPAN → always ARCOPAN)
- Product codes (ACP-CSI → unchanged)
- Industry terms (Cold Storage → standardized term)

---

## Monitoring & Maintenance

### Weekly
- Check for unusual API errors
- Review character usage progress
- Verify no failed translations

### Monthly
- Review total character usage
- Compare against budget
- Plan for next month (upgrade needed?)
- Archive API logs

### Quarterly
- Rotate API key
- Review pricing plan fit
- Check DeepL feature updates

---

## References

- [DeepL API Documentation](https://www.deepl.com/docs-api)
- [DeepL Supported Languages](https://www.deepl.com/docs-api/general/get-languages/)
- [DeepL Glossary Documentation](https://www.deepl.com/docs-api/glossaries/)
- [DeepL Pro Features](https://www.deepl.com/pro)

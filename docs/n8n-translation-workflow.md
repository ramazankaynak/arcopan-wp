# n8n Translation Workflow - ARCOPAN

Complete guide to the DeepL + Polylang translation automation workflow in n8n.

---

## Workflow Overview

### Architecture

```
WordPress Post (EN)
       ↓
  Post Published/Updated (Hook)
       ↓
  n8n Webhook Triggered
       ↓
  Validate Payload
       ↓
  Clean HTML Content
       ↓
  Check DeepL Quota
       ↓
  Send to DeepL API (4 languages)
       ↓
  Normalize Response
       ↓
  Create/Update Translated Posts (WP REST)
       ↓
  Assign Languages (Polylang)
       ↓
  Link Translations Together
       ↓
  Log Results & Notify Admin

```

### Workflow Components

| Component | Type | Purpose |
|-----------|------|---------|
| **Webhook** | Trigger | Receives post data from WordPress |
| **Validator** | Control | Checks payload integrity |
| **HTML Cleaner** | Function | Removes problematic tags |
| **Quota Checker** | HTTP | Checks DeepL remaining quota |
| **DeepL Translator** | HTTP (4x) | Translates to TR, FR, DE, RU |
| **Response Normalizer** | Function | Transforms API responses |
| **WP API Creator** | HTTP (4x) | Creates posts in target languages |
| **Polylang Linker** | HTTP | Links translations together |
| **Error Handler** | Control | Catches & logs errors |
| **Admin Notifier** | Email | Sends status to admin |

---

## Webhook Trigger

### Setup WordPress to WordPress Webhook

The webhook is triggered **from WordPress** when a post is published/updated.

**In WordPress (functions.php):**
```php
// See "WordPress Integration" section below
// Hook fires on post_publish / post_update
// Sends POST to n8n webhook endpoint
```

**n8n receives:**
```json
{
  "post_id": 123,
  "title": "Product Name",
  "excerpt": "Short description",
  "content": "<h2>Features</h2><p>Content here...</p>",
  "slug": "product-name",
  "post_type": "post",
  "timestamp": "2024-03-25T10:30:00Z"
}
```

### Webhook Configuration in n8n

**In n8n Workflow:**

1. **Create Workflow**
   - Name: `deepl-translation-workflow`
   - Description: "Translate WordPress posts via DeepL"

2. **Add Webhook Node**
   - Click **+** → Search "Webhook"
   - Select **Webhook** node
   - Configure:
     - **Authentication**: Basic Auth (optional, but recommended)
     - **HTTP Method**: POST
     - **URL**: `https://n8n.example.com/webhook/arcopan-translate`
       (Replace with your n8n domain)

3. **Save & Get Webhook URL**
   - Copy the generated URL
   - Store in `.env` as `N8N_WEBHOOK_URL`

**Security:**
- Use HTTPS (required for webhook)
- Add Basic Auth username/password
- Validate request signature (HMAC)

---

## Node 1: Validate Payload

### Validation Logic

**Node type:** IF/Condition

**Check these conditions:**
1. `post_id` exists and is number
2. `title` not empty
3. `content` not empty
4. `slug` not empty
5. `post_type` is translatable (post, page, product, project)

**IF all valid:** Continue to next node
**IF invalid:** Send error notification, stop

### Implementation

```javascript
// In n8n Expression field

function validatePayload() {
  const payload = $input.first().json;
  
  const errors = [];
  
  if (!payload.post_id || typeof payload.post_id !== 'number') {
    errors.push('Missing or invalid post_id');
  }
  
  if (!payload.title || payload.title.trim() === '') {
    errors.push('Title is empty');
  }
  
  if (!payload.content || payload.content.trim() === '') {
    errors.push('Content is empty');
  }
  
  if (!payload.slug || payload.slug.trim() === '') {
    errors.push('Slug is empty');
  }
  
  const validTypes = ['post', 'page', 'product', 'project'];
  if (!validTypes.includes(payload.post_type)) {
    errors.push(`Post type not translatable: ${payload.post_type}`);
  }
  
  return {
    valid: errors.length === 0,
    errors: errors,
    payload: payload
  };
}

return validatePayload();
```

---

## Node 2: Clean HTML Content

### Purpose

Remove problematic tags/attributes that interfere with DeepL translation while preserving structural tags.

### Clean Rules

**Keep these tags:**
- `<p>`, `<h2>`, `<h3>`, `<h4>`, `<ul>`, `<ol>`, `<li>`
- `<strong>`, `<em>`, `<b>`, `<i>`
- `<br>`, `<a>` (with href preserved)
- `<blockquote>`, `<code>`, `<pre>`

**Remove these tags/attributes:**
- `<div>` (replace with `<p>`)
- `<span>` (keep text, remove tags)
- `class`, `id`, `style`, `onclick` attributes
- `<script>`, `<style>` tags (and content)
- HTML comments `<!-- -->`
- Empty tags
- Excessive whitespace

### Implementation

```javascript
function cleanHTMLContent(html) {
  if (!html) return '';
  
  // Remove script and style tags
  html = html.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
  html = html.replace(/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/gi, '');
  
  // Remove HTML comments
  html = html.replace(/<!--.*?-->/gs, '');
  
  // Remove event handlers and dangerous attributes
 html = html.replace(/\s*(onclick|onload|onerror|onmouseover)\s*=\s*"[^"]*"/gi, '');
  html = html.replace(/\s*class\s*=\s*"[^"]*"/gi, '');
  html = html.replace(/\s*id\s*=\s*"[^"]*"/gi, '');
  html = html.replace(/\s*style\s*=\s*"[^"]*"/gi, '');
  
  // Convert div to p
  html = html.replace(/<div\b[^>]*>/gi, '<p>');
  html = html.replace(/<\/div>/gi, '</p>');
  
  // Remove span tags but keep content
  html = html.replace(/<span\b[^>]*>/gi, '');
  html = html.replace(/<\/span>/gi, '');
  
  // Remove empty tags
  html = html.replace(/<p>\s*<\/p>/gi, '');
  html = html.replace(/<h[2-4]>\s*<\/h[2-4]>/gi, '');
  
  // Collapse multiple whitespace
  html = html.replace(/\s{2,}/g, ' ');
  
  return html.trim();
}

return {
  title: $input.first().json.title,
  excerpt: $input.first().json.excerpt,
  content: cleanHTMLContent($input.first().json.content),
  slug: $input.first().json.slug,
  post_id: $input.first().json.post_id,
  post_type: $input.first().json.post_type
};
```

---

## Node 3: Check Quota

### Purpose

Verify DeepL has sufficient quota before translating.

### Request

**Node type:** HTTP Request

**Configuration:**
```
Method: GET
URL: https://api-free.deepl.com/v2/usage
Headers:
  Authorization: DeepL-Auth-Key {{ $env.DEEPL_API_KEY }}
Authentication: None (using header instead)
```

### Response Processing

```javascript
// Check response
const usage = $input.first().json;
const remaining = usage.character_limit - usage.character_count;

// Calculate needed characters for 4 languages + safety margin
const titleChars = $input.first().json.title.length;
const excerptChars = $input.first().json.excerpt.length;
const contentChars = $input.first().json.content.length;
const totalNeeded = (titleChars + excerptChars + contentChars) * 4 * 1.1; // 10% buffer

return {
  character_count: usage.character_count,
  character_limit: usage.character_limit,
  remaining: remaining,
  needed: totalNeeded,
  sufficient: remaining > totalNeeded,
  utilization: Math.round((usage.character_count / usage.character_limit) * 100)
};
```

### Quota Warning Thresholds

**IF remaining < needed:**
1. Create draft post with warning message
2. Send email to admin with warning
3. Stop workflow (don't proceed to translation)

---

## Node 4A-4D: DeepL Translation (4 parallel HTTP requests)

### Language-Specific Requests

Create 4 identical nodes, one for each language:

| Node | Language | Code | Variable |
|------|----------|------|----------|
| 4A | Turkish | tr | `translator_tr` |
| 4B | French | fr | `translator_fr` |
| 4C | German | de | `translator_de` |
| 4D | Russian | ru | `translator_ru` |

### Request Configuration

**Method:** POST

**URL (Free tier):**
```
https://api-free.deepl.com/v2/translate
```

**Headers:**
```
Content-Type: application/x-www-form-urlencoded
Authorization: DeepL-Auth-Key {{ $env.DEEPL_API_KEY }}
```

**Body (form-data):**

```
auth_key={{ $env.DEEPL_API_KEY }}
text={{ $input.all()[0].json.content }}
target_lang=TR
source_lang=EN
tag_handling=html
preserve_formatting=1
split_sentences=off
formality=prefer_more
```

**Example for Turkish (adjust code per node):**

```
auth_key=YOUR_KEY:fx
text=<p>Content to translate</p>
target_lang=TR
source_lang=EN
tag_handling=html
preserve_formatting=1
```

### Response Format

```json
{
  "translations": [
    {
      "detected_source_language": "EN",
      "text": "Tercüme edilen metin"
    }
  ]
}
```

### Error Handling per Language

```javascript
// In each translator node
try {
  const response = $input.first().json;
  
  if (response.translations && response.translations[0]) {
    return {
      success: true,
      language: 'TR',
      title: response.title_translated,
      excerpt: response.excerpt_translated,
      content: response.translations[0].text,
      raw_response: response
    };
  } else {
    throw new Error('No translation in response');
  }
} catch (error) {
  return {
    success: false,
    language: 'TR',
    error: error.message,
    status_code: $input.first().getMetaData().statusCode
  };
}
```

---

## Node 5: Normalize Responses

### Purpose

Combine 4 language responses into structured format.

### Node Type: Function

```javascript
// Collect all 4 translation responses
const responses = $input.all();

// Extract results by language
const translations = {};

responses.forEach(resp => {
  const data = resp.json;
  const lang = data.language;
  
  translations[lang] = {
    success: data.success,
    title: data.title_translated || '',
    excerpt: data.excerpt_translated || '',
    content: data.content || '',
    error: data.error || null
  };
});

// Check if all succeeded
const allSuccess = Object.values(translations).every(t => t.success);

return {
  post_id: responses[0].json.post_id,  // From first response
  slug: responses[0].json.slug,
  post_type: responses[0].json.post_type,
  all_success: allSuccess,
  translations: translations,
  translated_at: new Date().toISOString()
};
```

---

## Node 6A-6D: Create Posts via WP REST API (4 parallel)

### WordPress REST API Authentication

**Add to n8n HTTP nodes:**

```
Authentication Type: Generic Credential
Username: {{ $env.WP_API_USERNAME }}
Password: {{ $env.WP_API_PASSWORD }}
```

**Note:** Password should be WordPress Application Passwords (not user password).

### POST Request Configuration

**For Turkish (Node 6A):**

```
Method: POST
URL: {{ $env.WP_API_URL }}/wp-json/wp/v2/posts

Headers:
  Content-Type: application/json

Body (raw JSON):
{
  "title": "{{ $input.first().json.translations.tr.title }}",
  "excerpt": "{{ $input.first().json.translations.tr.excerpt }}",
  "content": "{{ $input.first().json.translations.tr.content }}",
  "status": "draft",
  "type": "{{ $input.first().json.post_type }}",
  "meta": {
    "_arcopan_source_post_id": {{ $input.first().json.post_id }},
    "_arcopan_translation_language": "tr",
    "_translation_locked": false
  }
}
```

**Response format (WordPress returns):**
```json
{
  "id": 456,
  "title": {"rendered": "..."},
  "content": {"rendered": "..."},
  "link": "https://arcopan.com/tr/.../"
}
```

### Extract Post ID

```javascript
return {
  language: 'tr',
  new_post_id: $input.first().json.id,
  post_link: $input.first().json.link,
  status: $input.first().json.status
};
```

**Repeat for FR, DE, RU** with appropriate language codes.

---

## Node 7: Assign Languages via Polylang

### Polylang REST Endpoint

After posts created, assign language via Polylang API.

**For each created post:**

```
Method: POST
URL: {{ $env.WP_API_URL }}/wp-json/polylang/v1/posts/{{ post_id }}/languages

Headers:
  Content-Type: application/json

Body:
{
  "language": "tr"
}
```

### Link Translations Together

**Critical:** Polylang uses `_polylang_post_translations` meta to link all language versions.

After all 4 posts created, update English post with links:

```
Method: POST
URL: {{ $env.WP_API_URL }}/wp-json/wp/v2/posts/{{ english_post_id }}

Body:
{
  "meta": {
    "_polylang_post_translations": {
      "en": {{ english_post_id }},
      "tr": {{ turkish_post_id }},
      "fr": {{ french_post_id }},
      "de": {{ german_post_id }},
      "ru": {{ russian_post_id }}
    }
  }
}
```

---

## Node 8: Error Handler

### Purpose

Catch any errors from previous nodes (API failures, timeouts, etc.).

### Node Type: Try/Catch

**Try block:**
- Execute all translation & post creation nodes

**Catch block:**
- Log full error to WordPress error log
- Send error email to admin
- Create notification post in WordPress admin
- Set post status to `draft` with error message

### Error Response

```javascript
const error = $input.first().json;

return {
  success: false,
  post_id: error.post_id,
  error_message: error.message,
  error_code: error.statusCode,
  translated_before_error: error.partially_translated || false,
  timestamp: new Date().toISOString(),
  action: 'Manual review required'
};
```

---

## Node 9: Admin Notification

### Email Notification

**Node type:** Send Email

**Configuration:**
```
To: {{ $env.ADMIN_EMAIL }}
Subject: ARCOPAN Translation Status: {{ post_id }}

Body:
Post ID: {{ post_id }}
Status: {{ success ? 'Completed' : 'Failed' }}
Languages processed: TR, FR, DE, RU
Draft created: {{ created_posts }}
Errors: {{ errors || 'None' }}
Link: {{ $env.WP_API_URL }}/wp-admin/post.php?post={{ post_id }}&action=edit
Timestamp: {{ timestamp }}
```

### Webhook Response to WordPress

Also send callback to WordPress:

```
Method: POST
URL: {{ $env.WP_API_URL }}/wp-json/arcopan/v1/translation-status

Body:
{
  "post_id": {{ post_id }},
  "success": {{ success }},
  "translated_posts": {{ translated_posts }},
  "errors": null,
  "timestamp": "{{ timestamp }}"
}
```

---

## Workflow JSON Export

See `automation/n8n/deepl-translation-workflow.json` for complete workflow definition.

### Import in n8n

1. In n8n, go to **Workflows**
2. Click **Import from File**
3. Select `deepl-translation-workflow.json`
4. Set environment variables
5. Activate workflow

---

## Testing Workflow

### Manual Test

1. **Open n8n Dashboard**
2. **Select workflow:** `deepl-translation-workflow`
3. **Click "Get Triggered By"** → Copy webhook URL
4. **Store in WordPress** as `N8N_WEBHOOK_URL`
5. **Click "Test Workflow"** in n8n
6. **Paste test payload:**
   ```json
   {
     "post_id": 1,
     "title": "Test Product",
     "excerpt": "Short description",
     "content": "<p>This is test content for translation.</p>",
     "slug": "test-product",
     "post_type": "post"
   }
   ```
7. **Run** and verify output
8. **Check WordPress** for created draft posts

### Production Trigger

Once deployed:

1. Create new post in English in WordPress
2. Fill content
3. Click Publish
4. n8n receives webhook automatically
5. Workflow runs
6. Draft posts created in target languages
7. Admin receives email notification

---

## Retry Logic & Timeouts

### Retry Configuration (per HTTP node)

```
Retry on Fail: Enabled
Number of Retries: 3
Wait Between Retries: 5 seconds
Backoff:
  - Attempt 1: Immediate
  - Attempt 2: 5 seconds
  - Attempt 3: 15 seconds (exponential)
```

### Timeout Settings

```
DeepL API timeout: 30 seconds
WordPress API timeout: 10 seconds
Polylang API timeout: 10 seconds
```

### Partial Completion Handling

If some languages fail:
1. Log which languages succeeded/failed
2. Still create posts for successful languages
3. Send email with details
4. Admin can retry failed languages

---

## Monitoring & Logging

### n8n Execution Logs

**View in n8n:**
1. Go to **Workflows** > Select workflow
2. Click **Executions** tab
3. Each run shows:
   - Execution time
   - Status (success/error)
   - Node-by-node results
   - Full request/response data

### WordPress Logs

**File:** `/wp-content/debug.log`

Logged entries:
```
[Date] Translation webhook received: post_id=123
[Date] DeepL API call: EN→TR (5000 chars)
[Date] WordPress post created: Turkish (id=456, status=draft)
[Date] Polylang language assigned: tr
[Date] Translation workflow completed: 4 posts created
```

### Admin Notifications

Check WordPress admin:
1. **Tools > Logs** (if available)
2. **Email alerts** (sent by n8n on error)
3. **Custom dashboard** (future enhancement)

---

## Scaling & Performance

### Workflow Performance Targets

- Title translation: <2 seconds
- Excerpt translation: <2 seconds
- Content translation: 3-5 seconds
- WordPress post creation: <2 seconds per post
- Full workflow: <30 seconds total

### Batch Processing (Optional)

For bulk translating existing posts:
1. Create manual trigger in n8n
2. Query WordPress for unpublished/EN posts
3. Loop through each post
4. Trigger translation workflow
5. Limit to 5-10 posts per batch (quota management)

---

## References

- [n8n Documentation](https://docs.n8n.io/)
- [WordPress REST API](https://developer.wordpress.org/rest-api/)
- [Polylang REST API](https://polylang.pro/doc/api/)
- [DeepL API](https://www.deepl.com/docs-api)

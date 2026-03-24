# FORMS SYSTEM — IMPLEMENTATION STATUS

**Project:** ARCOPAN Cold Storage WordPress  
**Document:** Forms architecture, current implementation, integration roadmap  
**Updated:** March 25, 2026

---

## FORMS SYSTEM OVERVIEW

### Current State

**✅ COMPLETE:**
- [x] `arcopan-child/inc/forms.php` — Webhook handler + admin email (123 lines)
- [x] Form specification: 5-step RFQ structure (docs/04-rfq-configurator.md)
- [x] Admin email template (forms/email-templates/admin-notification.html)
- [x] Customer confirmation template (forms/email-templates/customer-confirmation.html)
- [x] n8n webhook payload structure documented
- [x] HubSpot CRM field mapping documented

**🟡 IN PROGRESS (Phase 4 - Setup):**
- [ ] Gravity Forms plugin installation & license
- [ ] Form field configuration (5 steps, conditional logic)
- [ ] reCAPTCHA v3 integration
- [ ] SMTP email configuration (SendGrid)
- [ ] n8n workflow deployment
- [ ] HubSpot API credentials setup

**❌ NOT YET IMPLEMENTED:**
- [ ] Elementor form widget integration
- [ ] Form styling (CSS in assets/css/)
- [ ] File upload fields (optional Phase 2)

---

## ARCOPAN-CHILD/INC/FORMS.PHP — CURRENT IMPLEMENTATION

### File Overview (123 lines)
**Location:** `arcopan-child/inc/forms.php`  
**Purpose:** Gravity Forms submission handler, webhook relay, admin email sender  
**Dependencies:** Gravity Forms plugin (checked via `class_exists( 'GFForms' )`)

### Main Functions

#### 1. `arcopan_form_handler()` — Primary Submission Handler
**Hook:** `gform_after_submission` (fires after GF submission)  
**Parameters:**
- `$entry` (array) — Form field values
- `$form` (array) — Form definition (fields, settings)

**Actions:**
1. Validates inputs (checks arrays not empty)
2. Conditionally sends admin email (via `arcopan_form_send_admin_email()`)
3. Relays payload to n8n webhook (if URL provided)
4. Non-blocking webhook call (doesn't slow down form submission)

**Webhook Payload Structure:**
```php
$payload = array(
    'source'     => 'arcopan-wordpress',
    'site_url'   => home_url( '/' ),
    'site_name'  => get_bloginfo( 'name' ),
    'form_id'    => $form_id,
    'form_title' => $form['title'],
    'entry_id'   => $entry_id,
    'entry'      => $entry,          // All field values
    'submitted'  => time(),          // Unix timestamp
);
```

**Webhook Call:**
```php
wp_remote_post(
    $webhook_url,
    array(
        'timeout'  => 8,
        'headers'  => array( 'Content-Type' => 'application/json; charset=utf-8' ),
        'body'     => wp_json_encode( $payload ),
        'blocking' => false,  // Non-blocking (doesn't wait for response)
    )
);
```

**Extensibility:**
- `apply_filters( 'arcopan_crm_webhook_payload', ... )` — Modify payload before sending
- `apply_filters( 'arcopan_crm_webhook_url', ... )` — Set webhook URL dynamically

#### 2. `arcopan_form_send_admin_email()` — Admin Notification Email
**Parameters:**
- `$entry` (array) — Submitted field values
- `$form` (array) — Form definition

**Email Details:**
- **To:** Filtered via `apply_filters( 'arcopan_gf_admin_notify_email', ... )`
  - Default: WordPress admin email (from settings)
  - Can be overridden per form
- **Subject:** `[Site Name] Form received: [Form Title]`
- **Body:** Plain text (field name: value, one per line)
- **Content-Type:** `text/plain; charset=UTF-8`

**Example Output:**
```
Company Name: Acme Food Corp
Email: contact@acme.com
Project Type: Solution
Cooling Capacity: 500 units/hour
Temperature Range: -18°C
Budget Range: €100–500k
Timeline: 0–3 months
```

**Extensibility:**
- `apply_filters( 'arcopan_gf_notify_subject', ... )` — Customize subject
- `apply_filters( 'arcopan_gf_notify_body', ... )` — Customize email body
- `apply_filters( 'arcopan_gf_send_admin_notification', true, ... )` — Disable per form

#### 3. `arcopan_main_script_as_module()` — Helper (Not Forms-Related)
**Purpose:** Mark main.js as ES module type  
**Note:** In functions.php, not forms.php (included for completeness)

### Integration Points

**With Gravity Forms:**
```php
if ( class_exists( 'GFForms' ) ) {
    add_action( 'gform_after_submission', 'arcopan_form_handler', 10, 2 );
}
```
- Only hooks if GF plugin is active
- Safe fallback (no errors if GF not installed)

**With n8n (Webhook):**
- Webhook URL set via `apply_filters( 'arcopan_crm_webhook_url', ... )`
- Typically configured in wp-config.php or theme options:
  ```php
  define( 'ARCOPAN_N8N_WEBHOOK_URL', 'https://n8n.example.com/webhook/rfq-hubspot' );
  
  add_filter( 'arcopan_crm_webhook_url', function() {
      return defined( 'ARCOPAN_N8N_WEBHOOK_URL' ) ? ARCOPAN_N8N_WEBHOOK_URL : '';
  });
  ```

**With SendGrid/SMTP:**
- Uses WordPress `wp_mail()` function
- SMTP configured via WP Mail SMTP plugin (separate)

---

## GRAVITY FORMS CONFIGURATION — REQUIRED SETUP

### Phase 4: Installation & Configuration

#### Step 1: Plugin Installation
```bash
# Via WordPress admin:
Plugins → Add New → Search "Gravity Forms"
```

**Required Add-ons:**
1. Gravity Forms (core) — $199/year or $399/lifetime
2. Conditional Logic (included in core)
3. Signature (optional, included in core)

#### Step 2: Form Creation (5-Step RFQ)
**Location:** Forms → New Form  
**Configuration:** See `docs/04-rfq-configurator.md` for full field list

**Quick Overview:**

| Step | Fields | Conditional Logic |
|---|---|---|
| 1 | Company, Industry, Country, Email, Phone | None (all required) |
| 2 | Project Type, Cooling Capacity, Temp, Footprint | None (all required) |
| 3A | Industry-specific fields | Only if Step 2 "Project Type" = "Industry" |
| 3B | Product selection, quantity | Only if Step 2 "Project Type" = "Product" |
| 4 | Budget, Timeline, Constraints | None |
| 5 | Summary, Terms checkbox, Subscribe, Submit | None |

**Form Settings:**
- **Confirmation:** Redirect to thank-you page (`/contact/#thank-you`)
- **Notifications:** Enable (admin email configured in forms.php)
- **CAPTCHA:** reCAPTCHA v3 (free, invisible)
- **AJAX:** Enabled (smooth submission without page reload)

#### Step 3: Conditional Logic Setup
**Example:** Step 3A fields only show if Project Type = "Industry"

```
Field: Industry-Specific Fields
Display If:
  - Step 2 "Project Type" Equals "Industry"
```

**All conditional rules documented in `docs/04-rfq-configurator.md`**

#### Step 4: Admin Notification Email
**Recipient:** WordPress admin email (default)  
**Trigger:** After form submission  
**Template:** Auto-generated by `arcopan_form_send_admin_email()`

**Current Setup:**
- Function builds email from all submitted fields
- Plain text format (no HTML)
- Can be customized via filters in functions.php

#### Step 5: CAPTCHA Configuration
**Setup:**
1. WordPress admin → Gravity Forms → Settings → reCAPTCHA
2. Add site key + secret key (get from Google reCAPTCHA admin console)
3. Select reCAPTCHA v3 (invisible, no user interaction)
4. Set threshold (default 0.5 = moderate filtering)

---

## EMAIL TEMPLATE IMPLEMENTATION

### Admin Notification Email
**File:** `arcopan-child/forms/email-templates/admin-notification.html`  
**Usage:** Reference template (actual email generated by `wp_mail()`)

**Template Content:**
```html
<!-- Header -->
<h2>New RFQ: {Company Name}</h2>

<!-- Summary -->
<p>New form submission from <strong>{Company Name}</strong>:</p>

<!-- Form Data Table -->
<table>
  <tr><td>Email:</td><td>{Email}</td></tr>
  <tr><td>Phone:</td><td>{Phone}</td></tr>
  <tr><td>Industry:</td><td>{Industry}</td></tr>
  <tr><td>Project Type:</td><td>{Project Type}</td></tr>
  <!-- ... all fields ... -->
</table>

<!-- CTA -->
<p><a href="{Admin Link}">View in HubSpot</a></p>
```

**Current Implementation:**
- Template is reference only
- Actual email generated by `arcopan_form_send_admin_email()` (plain text)
- To use HTML template: Customize `wp_mail()` in forms.php

### Customer Confirmation Email
**File:** `arcopan-child/forms/email-templates/customer-confirmation.html`  
**Usage:** Send to customer after RFQ submission

**Template Content:**
```html
<h2>Thank You for Your Quote Request</h2>

<p>Dear {First Name},</p>

<p>We received your request for quote and will respond within 24 hours.</p>

<!-- Reference Number -->
<p><strong>Reference #:</strong> {Entry ID}</p>

<!-- Next Steps -->
<h3>What Happens Next:</h3>
<ol>
  <li>Our team reviews your requirements</li>
  <li>We prepare a customized quote</li>
  <li>We contact you within 24 hours</li>
</ol>

<!-- Footer -->
<p>Questions? <a href="mailto:contact@arcopan.com">Email us</a></p>
```

**Implementation Steps (Phase 4):**
1. Install WP Mail SMTP plugin
2. Configure SendGrid API key
3. Create WordPress email hook to send confirmation email:
   ```php
   add_action( 'gform_after_submission', function( $entry, $form ) {
       $to = rgar( $entry, '4' ); // Field ID 4 = email field
       $subject = 'Your RFQ has been received – ARCOPAN';
       
       ob_start();
       include ARCOPAN_CHILD_PATH . 'forms/email-templates/customer-confirmation.html';
       $body = ob_get_clean();
       
       wp_mail( $to, $subject, $body, array( 'Content-Type: text/html; charset=UTF-8' ) );
   }, 10, 2 );
   ```

---

## N8N WEBHOOK INTEGRATION

### Webhook Configuration

**Webhook URL:**
```
https://n8n.example.com/webhook/rfq-hubspot-sync
```

**Method:** POST  
**Content-Type:** application/json

### Payload Structure
(Sent from WordPress to n8n)

```json
{
  "source": "arcopan-wordpress",
  "site_url": "https://arcopan.com/",
  "site_name": "ARCOPAN",
  "form_id": 1,
  "form_title": "Request for Quote",
  "entry_id": "12345",
  "entry": {
    "1": "Acme Food Corp",       // Company Name
    "2": "1",                     // Industry (ID: Food & Beverage)
    "3": "5",                     // Country (ID: Germany)
    "4": "contact@acme.de",       // Email
    "5": "+49 30 12345678",       // Phone
    "6": "Solution",              // Project Type
    "7": "500 units/hour",        // Cooling Capacity
    "8": "3",                     // Temperature Range (ID: -18°C)
    "9": "5000 m²",               // Building Footprint
    "10": "2",                    // Budget Range (ID: €100–500k)
    "11": "1",                    // Timeline (ID: 0–3 months)
    "12": "Power limited to 50kW" // Constraints
  },
  "submitted": 1711353600
}
```

### N8N Workflow Steps

**Step 1: Receive Webhook**
- Webhook node listens for POST requests
- Validates JSON payload
- Pass to next step

**Step 2: Transform Data**
- Map GF field IDs to HubSpot field names
- Extract email, phone, company
- Build contact data object

**Step 3: HubSpot Contact Lookup/Create**
- Query HubSpot: Does contact with email exist?
- If yes: Update existing contact
- If no: Create new contact with email, phone, company

**Step 4: Create Deal in HubSpot**
- Create deal linked to contact
- Deal stage: "New Lead"
- Deal value: Midpoint of budget range
- Deal name: "{Company} – RFQ"

**Step 5: Send Confirmation Email**
- Trigger SendGrid email to customer
- Subject: "Request for Quote Received – ARCOPAN"
- Body: Template with reference number

**Step 6: Log Execution**
- Store in CRM notes (optional)
- Log any errors for debugging

### HubSpot Field Mapping

| WordPress Field | HubSpot Field | Type | Required |
|---|---|---|---|
| Email (Field 4) | Email | Email | Yes |
| Phone (Field 5) | Phone | Phone | Yes |
| Company (Field 1) | Company | Text | Yes |
| Industry (Field 2) | Industry | Dropdown | Yes |
| Country (Field 3) | Country | Dropdown | Yes |
| Budget Range (Field 10) | Deal Amount | Currency | No |
| Timeline (Field 11) | Close Date | Date | No |
| Project Type (Field 6) | Description | Text | No |

---

## IMPLEMENTATION ROADMAP — PHASE 4

### Week 1: Gravity Forms Setup
**Timeline:** April 7–9, 2026

**Tasks:**
- [ ] Purchase Gravity Forms license ($199/year)
- [ ] Install & activate plugin
- [ ] Create RFQ form (5 steps)
- [ ] Configure conditional logic (3A/3B fields)
- [ ] Enable reCAPTCHA v3
- [ ] Test form submission (desktop + mobile)
- [ ] Create admin email template

**Deliverable:** Functional RFQ form in WordPress admin

### Week 2: Email & Webhook Setup
**Timeline:** April 1, 20260–12, 2026

**Tasks:**
- [ ] Install WP Mail SMTP plugin
- [ ] Configure SendGrid account + API key
- [ ] Create customer confirmation email hook
- [ ] Test email delivery (test submission)
- [ ] Deploy n8n workflow (RFQ → HubSpot)
- [ ] Configure webhook URL in forms.php (constant)
- [ ] Test end-to-end: Form → Email → HubSpot

**Deliverable:** Email + webhook working, data flowing to CRM

### Week 3: Testing & Optimization
**Timeline:** April 1, 20263–19, 2026

**Tasks:**
- [ ] Cross-browser testing (form validation)
- [ ] Mobile responsiveness testing
- [ ] Load testing (100 concurrent submissions)
- [ ] Error handling (test invalid inputs)
- [ ] Spam testing (reCAPTCHA effectiveness)
- [ ] Accessibility audit (WCAG AA)

**Deliverable:** Production-ready form

---

## CURRENT BLOCKERS & DEPENDENCIES

### Blockers (Nothing Blocking)
✅ All code ready for Phase 4 deployment

### Dependencies

| Task | Depends On | Status |
|---|---|---|
| GF form creation | GF plugin install | Phase 4 (pending) |
| Email sending | SendGrid account | Phase 4 (pending) |
| Webhook → HubSpot | n8n instance setup | Phase 4 (pending) |
| Form styling | Elementor design | Phase 3 (pending) |
| SMTP configuration | WP Mail SMTP plugin | Phase 4 (pending) |

---

## QUALITY ASSURANCE CHECKLIST

**Before Production:**
- [ ] Form submits without JavaScript errors
- [ ] All 5 steps display correctly
- [ ] Conditional logic works (Step 3A/3B toggles)
- [ ] Admin email arrives within 1 minute
- [ ] Customer confirmation email arrives within 1 minute
- [ ] Data appears in HubSpot CRM within 5 minutes
- [ ] reCAPTCHA blocks spam bots
- [ ] Mobile form usable at 375px width
- [ ] Form works without JavaScript (graceful degradation)
- [ ] Error messages clear and helpful

---

## MAINTENANCE & MONITORING

### Weekly
- [ ] Check admin email inbox (submissions arriving?)
- [ ] Verify HubSpot deals created (check pipeline)
- [ ] Monitor error logs (Search Console, server logs)

### Monthly
- [ ] Review form completion rate (Analytics)
- [ ] Check average submission time (should be <5 min)
- [ ] Audit 5 random submissions (verify accuracy)
- [ ] Review reCAPTCHA score distribution (0.0–1.0)

### Quarterly
- [ ] Update form fields if business requirements change
- [ ] Review spam submissions (adjust reCAPTCHA threshold if needed)
- [ ] Performance audit (form load time, conversion rate)

---

**Status:** ✅ Code ready for Phase 4. Gravity Forms setup starts April 7, 2026.

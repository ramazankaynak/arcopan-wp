# RFQ FORM ARCHITECTURE

**Project:** ARCOPAN Cold Storage WordPress  
**Document:** RFQ Form implementation guide, conditional logic, integrations  
**Updated:** March 25, 2026

---

## FORM OVERVIEW

The RFQ (Request For Quote) form is the primary lead generation tool. It collects customer project requirements and integrates with HubSpot CRM via n8n webhook.

**Form Type:** Gravity Forms + Elementor form widget  
**Pages:** 5-step wizard (progressively disclose fields)  
**Submission Flow:** GF submission → n8n webhook → HubSpot CRM  
**Styling:** Elementor form styling via `assets/css/components.css`

---

## FORM STRUCTURE: 5 STEPS

### Step 1: Business Information
**Display Condition:** Always shown  
**Fields:**

| Field | Type | Validation | Placeholder |
|---|---|---|---|
| Company Name* | Text | Required, 2–50 chars | "Acme Food Processing" |
| Industry* | Select | Required | "Select industry..." |
| Country* | Select | Required, WPML aware | "Select country..." |
| Email* | Email | Required, valid email | "you@company.com" |
| Phone* | Phone | Required, E.164 format | "+1 (555) 000-0000" |

**Conditional Logic:** None. All required.

### Step 2: Project Scope
**Display Condition:** Shown after Step 1 submission  
**Fields:**

| Field | Type | Validation | Placeholder |
|---|---|---|---|
| Project Type* | Radio | Required | Option group: (Solution, Industry, Product) |
| Cooling Capacity* | Text | Required, number | "500 units/hour" |
| Temperature Range* | Select | Required | "Choose: 0–-5°C, -5–-18°C, <-18°C" |
| Building Footprint* | Text | Required, number | "5000 m²" |

**Conditional Logic:**
- If "Project Type" = "Solution" → skip to Step 3
- If "Project Type" = "Industry" → show industry-specific fields (next)
- If "Project Type" = "Product" → show product-specific fields (next)

### Step 3A: Industry-Specific Fields (if Step 2 = "Industry")
**Display Condition:** Shown only if `Project Type = "Industry"`  
**Fields:** Vary by selected industry (see **Conditional Field Sets** below)

### Step 3B: Product-Specific Fields (if Step 2 = "Product")
**Display Condition:** Shown only if `Project Type = "Product"`  
**Fields:**

| Field | Type | Validation | Notes |
|---|---|---|---|
| Product Selection* | Checkbox | At least 1 required | Multi-select from 13 products |
| Quantity (per product) | Number | Optional, default 1 | Qty per product line |

### Step 4: Budget & Timeline
**Display Condition:** Shown after Steps 1–3  
**Fields:**

| Field | Type | Validation | Placeholder |
|---|---|---|---|
| Budget Range* | Radio | Required | Options: <€50k, €50–100k, €100–500k, >€500k |
| Project Timeline* | Select | Required | Options: ASAP, 0–3 months, 3–6 months, 6+ months |
| Key Constraints | Textarea | Optional, max 500 chars | "Space limits, noise concerns, power availability..." |

**Conditional Logic:** None.

### Step 5: Review & Submit
**Display Condition:** Shown after Step 4  
**Fields:**

| Field | Type | Action | Notes |
|---|---|---|---|
| Summary Display | HTML | Read-only recap | Auto-generated summary of submitted fields |
| Agree to Terms* | Checkbox | Required | Link to `/legal/terms` |
| Subscribe to Newsletter | Checkbox | Optional, default unchecked | Opt-in for email updates |
| Submit Button | Button | Submits form | Text: "Send Quote Request" |

**Post-Submit Behavior:**
1. Show success message: "Thank you! We'll contact you within 24 hours."
2. Trigger email notification to admin
3. Trigger customer confirmation email
4. Post to n8n webhook → HubSpot sync

---

## CONDITIONAL FIELD SETS

Gravity Forms conditional logic determines which fields appear based on previous selections.

### If Industry = "Food & Beverage"
Show additional fields:
- [ ] Product Type (e.g., "Chilled Storage", "Frozen Storage")
- [ ] Volume of Perishables per Day
- [ ] Compliance Requirements (HACCP, BRC, IFS)

### If Industry = "Meat & Poultry"
Show additional fields:
- [ ] Processing Capacity (kg/hour)
- [ ] Chill vs. Freeze Balance
- [ ] Cold Chain Requirements (tracking needed?)

### If Industry = "Dairy"
Show additional fields:
- [ ] Milk Temperature Standard (4°C, 6°C)
- [ ] Storage Duration
- [ ] Hygiene Level (ISO/EN standard)

### If Industry = "Pharmaceuticals"
Show additional fields:
- [ ] Temperature Stability (±0.5°C tolerance?)
- [ ] Humidity Control Required?
- [ ] Regulatory Standard (GMP, GDP)

### If Industry = "Logistics"
Show additional fields:
- [ ] Distribution Model (hub, regional, micro)
- [ ] Vehicle Fleet Size
- [ ] Average Daily Throughput (units)

### If Industry = "Retail"
Show additional fields:
- [ ] Store Format (hypermarket, specialty, convenience)
- [ ] Number of Units Needed
- [ ] Display vs. Storage Split

---

## EMAIL TEMPLATES

### Admin Notification Email
**Recipient:** `admin@arcopan.com`  
**Template File:** `arcopan-child/forms/email-templates/admin-notification.html`  
**Subject:** `New RFQ: {Company Name} ({Industry})`  
**Content:**
- Company info
- Project scope (capacity, temps)
- Budget range
- Timeline
- Key constraints
- Link to edit in HubSpot

### Customer Confirmation Email
**Recipient:** Form submitter (email field)  
**Template File:** `arcopan-child/forms/email-templates/customer-confirmation.html`  
**Subject:** `Request for Quote Received – ARCOPAN`  
**Content:**
- Thank you message
- Reference number (auto-generated)
- Expected response timeframe (24 hours)
- Next steps
- ARCOPAN contact info
- Unsubscribe link (GDPR)

---

## GRAVITYFORMS CONFIGURATION

### Form Setup
```
Form ID: 1
Form Name: arcopan_rfq_form
Title: "Request for Quote"
Notification: Enabled (see Email Templates)
Confirmation: Redirect to thank-you page
CAPTCHA: Enabled (reCAPTCHA v3)
```

### Field Labels & Descriptions

All field labels and help text must support WPML translations (stored in `/languages/` PO files).

```php
[gf-form id="1" title="false" description="false" ajax="true"]
```

### Form Styling (CSS)

All form styling handled via Elementor form widget styling or custom CSS in `assets/css/components.css`:

```css
.arcopan-form .gfield {
  margin-bottom: var(--space-md);
}

.arcopan-form .gfield input,
.arcopan-form .gfield textarea,
.arcopan-form .gfield select {
  border: 1px solid var(--color-border);
  border-radius: 4px;
  padding: var(--space-sm);
  font-family: inherit;
  font-size: 16px; /* iOS zoom prevention */
}

.arcopan-form .gfield input:focus,
.arcopan-form .gfield textarea:focus,
.arcopan-form .gfield select:focus {
  border-color: var(--color-primary);
  outline: none;
  box-shadow: 0 0 0 3px rgba(13, 110, 110, 0.1);
}

.arcopan-form button.gform_button {
  background: var(--color-primary);
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
  font-weight: 600;
}

.arcopan-form button.gform_button:hover {
  background: var(--color-primary-dark);
}
```

---

## N8N WEBHOOK INTEGRATION

### Webhook URL
```
https://n8n.arcopan.io/webhook/rfq-hubspot-sync
```

### Payload Structure (sent from GF to n8n)

```json
{
  "form_id": 1,
  "form_title": "Request for Quote",
  "submission_id": "12345",
  "timestamp": "2026-03-25T14:30:00Z",
  "fields": {
    "company_name": "Acme Food",
    "industry": "Food & Beverage",
    "country": "Germany",
    "email": "contact@acme.de",
    "phone": "+49 30 12345678",
    "project_type": "Solution",
    "cooling_capacity": "500 units/hour",
    "temperature_range": "-18°C",
    "building_footprint": "5000 m²",
    "budget_range": "€100–500k",
    "timeline": "0–3 months",
    "constraints": "Power limitations..."
  }
}
```

### n8n Workflow Steps
1. **Receive webhook** → Validate payload
2. **Transform data** → Map GF fields to HubSpot company/deal format
3. **HubSpot API call** → Create Contact OR Update Company
4. **Create Deal** → Stage: "New Lead", value: midpoint of budget range
5. **Send confirmation** → Trigger customer confirmation email (via SMTP)
6. **Log entry** → Store in CRM notes

### n8n Configuration Details

**Workflow:** `rfq-hubspot-sync`  
**Trigger:** Webhook (POST)  
**API Key:** Stored in n8n env var `HUBSPOT_API_TOKEN`

**Contact Creation Logic:**
```
If email exists in HubSpot:
  → Update existing contact
Else:
  → Create new contact with email, phone, company name
```

**Deal Creation Logic:**
```
Company Name: {company_name}
Deal Stage: "New Lead"
Deal Value: Budget range midpoint
Deal Name: "{company_name} – RFQ"
Timeline: {timeline}
```

---

## HUBSPOT CRM MAPPING

### Contact Fields
| GF Field | HubSpot Field | Type | Required |
|---|---|---|---|
| Email | Email | Email | Yes |
| Phone | Phone | Phone | Yes |
| Company Name | Company Name | Text | Yes |

### Company Fields (auto-created if not exists)
| GF Field | HubSpot Field | Type | Notes |
|---|---|---|---|
| Company Name | Company Name | Text | Lookup field |
| Industry | Industry | Dropdown | "Food & Beverage", "Meat", etc. |
| Country | Country | Dropdown | ISO 2-letter code |

### Deal Fields
| GF Field | HubSpot Field | Type | Default |
|---|---|---|---|
| Budget Range | Deal Amount | Number | Midpoint of range |
| Timeline | Close Date | Date | Today + 90 days |
| Project Type | Deal Description | Text | Submitted value |
| Constraints | Deal Notes | LongText | Submitted constraints |

---

## FORM VALIDATION RULES

**Client-Side:** Gravity Forms validation (instant feedback)  
**Server-Side:** PHP validation in `arcopan-child/inc/forms.php`

```php
// Example validation
add_filter( 'gform_field_validation_1_2', function( $result, $value, $form, $field ) {
    if ( $field['inputType'] === 'email' && ! is_email( $value ) ) {
        $result['is_valid'] = false;
        $result['message'] = __( 'Please enter a valid email address.', 'arcopan' );
    }
    return $result;
}, 10, 4 );
```

---

## GDPR & CONSENT

**Privacy Notice:** Must appear above form
```
"We process your information to provide you a quote and keep you informed. We never share your data with third parties. View our Privacy Policy."
```

**Double Opt-In:** Newsletter subscription requires email confirmation (via Gravity Forms signup add-on).

**Data Retention:** Contact data retained 24 months in HubSpot (per GDPR Article 5).

---

## TESTING CHECKLIST

Before launch:

- [ ] All 5 form steps validate correctly
- [ ] Conditional logic works (Step 3 shows correct fields per industry)
- [ ] Form submits to n8n webhook successfully
- [ ] Admin notification email arrives
- [ ] Customer confirmation email arrives with reference number
- [ ] Data appears in HubSpot within 1 minute of submission
- [ ] Mobile responsiveness (test on iPhone 12, Samsung S21)
- [ ] CAPTCHA blocks spam submissions
- [ ] Form error messages clear and helpful

---

**Next:** Deploy form to contact page, set up Gravity Forms license, configure n8n webhook credentials.

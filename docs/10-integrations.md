# SYSTEM INTEGRATIONS & ARCHITECTURE

**Project:** ARCOPAN Cold Storage WordPress  
**Document:** Full system architecture, data flows, failure scenarios  
**Updated:** March 25, 2026

---

## SYSTEM ARCHITECTURE OVERVIEW

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                         ARCOPAN SYSTEM ARCHITECTURE                         │
└─────────────────────────────────────────────────────────────────────────────┘

┌──────────────────┐
│   VISITOR/USER   │
└────────┬─────────┘
         │
         ▼
   ┌─────────────────────────────────────────────────────────────┐
   │         WORDPRESS SITE (arcopan.com)                        │
   │  ┌────────────────────────────────────────────────────────┐ │
   │  │  Parent Theme: dt-the7                                │ │
   │  │  Child Theme: arcopan-child (implementation layer)   │ │
   │  │  ─────────────────────────────────────────────────── │ │
   │  │                                                       │ │
   │  │  Frontend (User-Facing)                             │ │
   │  │  ├─ Homepage (hero + products + solutions)          │ │
   │  │  ├─ Product pages (13 products)                     │ │
   │  │  ├─ Solution pages (6 solutions)                    │ │
   │  │  ├─ Industry pages (6 industries)                   │ │
   │  │  ├─ Resource pages (datasheets, guides, FAQ)        │ │
   │  │  └─ Contact page (RFQ form)                         │ │
   │  │                                                       │ │
   │  │  Backend Systems                                     │ │
   │  │  ├─ ACF Pro (custom fields)                         │ │
   │  │  ├─ Gravity Forms (form builder)                    │ │
   │  │  ├─ Yoast SEO (search engine optimization)          │ │
   │  │  ├─ WPML (5 languages)                              │ │
   │  │  ├─ WP Rocket (caching & performance)               │ │
   │  │  └─ Wordfence (security)                            │ │
   │  │                                                       │ │
   │  │  Data Storage                                        │ │
   │  │  ├─ MySQL Database (posts, pages, metadata)         │ │
   │  │  ├─ Uploads folder (images, PDFs)                   │ │
   │  │  └─ ACF JSON exports (field groups)                 │ │
   │  └────────────────────────────────────────────────────┘ │
   └────────────┬────────────────────────────────────────────┘
                │
        ┌───────┴────────┐
        ▼                ▼
   ┌──────────────┐  ┌──────────────┐
   │   FORMS      │  │  CLOUDINARY  │
   │ (GF Plugin)  │  │  (CDN/Image  │
   │              │  │   Delivery)  │
   │ • Validate   │  │              │
   │ • Store data │  │ • WebP conv  │
   │ • Trigger    │  │ • Resize     │
   │   webhook    │  │ • Cache      │
   └──────┬───────┘  └──────────────┘
          │
          ▼
   ┌─────────────────────────────────────────────────────────┐
   │  N8N AUTOMATION ENGINE (Webhook Relay)                 │
   │  ─────────────────────────────────────────────────────  │
   │  URL: https://n8n.arcopan.io/webhook/rfq-hubspot      │
   │                                                         │
   │  Tasks:                                                 │
   │  1. Receive webhook from WordPress                     │
   │  2. Validate payload structure                         │
   │  3. Transform data (GF fields → HubSpot fields)       │
   │  4. Check: Contact exists in HubSpot?                 │
   │     ├─ Yes → Update existing contact                  │
   │     └─ No → Create new contact                        │
   │  5. Create Deal in HubSpot pipeline                   │
   │  6. Send customer confirmation email (SendGrid)       │
   │  7. Log transaction (success/failure)                 │
   └──────┬──────────────────────────────────────────────────┘
          │
        ┌─┴──────────────────┬──────────────────┐
        ▼                    ▼                  ▼
   ┌──────────────┐   ┌──────────────┐   ┌──────────────┐
   │   HUBSPOT    │   │  SENDGRID    │   │   LOGGING    │
   │   CRM        │   │  (SMTP)      │   │  (Loggly/    │
   │              │   │              │   │  Sentry)     │
   │ • Contacts   │   │ • Customer   │   │              │
   │ • Deals      │   │   confirm    │   │ • Errors     │
   │ • Pipeline   │   │ • Admin      │   │ • Webhooks   │
   │ • Notes      │   │   alert      │   │ • Failures   │
   └──────────────┘   └──────────────┘   └──────────────┘
```

---

## DATA FLOW DIAGRAM

### Flow 1: RFQ Form Submission

```
STEP 1: User fills RFQ form (5 steps)
┌──────────────────────────────────────┐
│ Customer enters:                     │
│ • Company Name                       │
│ • Email                              │
│ • Phone                              │
│ • Project Type (Solution/Industry)  │
│ • Budget Range                       │
│ • Timeline                           │
│ • Additional fields (per type)       │
└──────────────────┬───────────────────┘
                   │
                   ▼
STEP 2: Form validation (client-side)
┌──────────────────────────────────────┐
│ Browser checks:                      │
│ • Required fields filled?             │
│ • Email format valid?                 │
│ • Phone format valid?                 │
│                                       │
│ ✅ Pass → Submit (AJAX)              │
│ ❌ Fail → Show error message         │
└──────────────────┬───────────────────┘
                   │
                   ▼
STEP 3: Server-side validation & submission
┌──────────────────────────────────────┐
│ WordPress (forms.php):               │
│ • Validate all fields                 │
│ • Sanitize input                      │
│ • Check reCAPTCHA v3 score           │
│ • Store in Gravity Forms DB          │
│                                       │
│ ✅ Pass → Trigger gform_after_       │
│           submission hook             │
│ ❌ Fail → Return error (log in error │
│           logs, don't proceed)        │
└──────────────────┬───────────────────┘
                   │
                   ▼
STEP 4: Admin email notification
┌──────────────────────────────────────┐
│ arcopan_form_send_admin_email():    │
│ • Format field values as text         │
│ • Send to admin@arcopan.com          │
│ • Subject: [ARCOPAN] Form received   │
│                                       │
│ ✅ Success → Email queued (WP Mail)  │
│ ❌ Fail → Log error (not blocking)   │
└──────────────────┬───────────────────┘
                   │
                   ▼
STEP 5: Webhook relay to n8n
┌──────────────────────────────────────────────────┐
│ arcopan_form_handler() builds payload:          │
│                                                  │
│ {                                                │
│   "source": "arcopan-wordpress",                │
│   "site_url": "https://arcopan.com",            │
│   "form_id": 1,                                  │
│   "entry_id": "12345",                          │
│   "entry": { all field values },               │
│   "submitted": 1711353600                       │
│ }                                                │
│                                                  │
│ wp_remote_post() sends to:                      │
│ https://n8n.arcopan.io/webhook/rfq-hubspot     │
│                                                  │
│ ✅ Success → Log "webhook sent"                 │
│ ❌ Timeout → Log error (doesn't block user)    │
│ ❌ 4xx/5xx → Log error (n8n down, retry later) │
│                                                  │
│ Non-blocking: Don't wait for response           │
└──────────────────┬───────────────────────────────┘
                   │
                   ▼
STEP 6: n8n processes webhook
┌────────────────────────────────────────────┐
│ n8n Workflow: rfq-hubspot-sync            │
│                                            │
│ 1. Webhook trigger node receives data     │
│ 2. Validate payload (JSON schema)         │
│ 3. If validation fails → Log error & stop │
│ 4. Transform data (map fields)            │
│ 5. HubSpot API lookup contact by email    │
│    ├─ Found → Update contact              │
│    └─ Not found → Create new contact      │
│ 6. Create Deal in HubSpot:                │
│    • Company: {company_name}              │
│    • Stage: "New Lead"                    │
│    • Amount: Budget midpoint              │
│    • Timeline: User input                 │
│ 7. Send customer email (SendGrid)         │
│    Subject: "Request received – ARCOPAN"  │
│    Body: Thank you, reference #, timeline │
│ 8. Log success: "Deal created: [ID]"      │
│                                            │
│ Errors at any step:                       │
│ • Logged to Loggly/Sentry                │
│ • Alert sent to Slack #errors             │
│ • Manual review required                  │
└────────────────────┬──────────────────────┘
                     │
                     ▼
STEP 7: Data arrives in HubSpot
┌────────────────────────────────────────┐
│ HubSpot CRM (immediate):               │
│ • Contact created/updated              │
│ • Deal created in "New Lead" stage      │
│ • Contact linked to Deal               │
│ • Timeline: <5 seconds                 │
│                                         │
│ Sales team can now:                    │
│ • View deal in CRM                     │
│ • Call customer (phone in record)       │
│ • Send proposal                        │
│ • Track in sales pipeline              │
└────────────────────────────────────────┘
                     │
                     ▼
STEP 8: Customer receives confirmation email
┌────────────────────────────────────────┐
│ SendGrid sends (via n8n):              │
│ From: noreply@arcopan.com             │
│ To: [customer email]                   │
│ Subject: "Request received – ARCOPAN" │
│                                         │
│ Body includes:                         │
│ • Thank you message                    │
│ • Reference number (entry ID)          │
│ • Expected response timeline (24h)     │
│ • Contact info                         │
│ • Unsubscribe link (GDPR)             │
│                                         │
│ Delivery:                              │
│ ✅ Success → Logged in SendGrid        │
│ ❌ Bounce → Retry x3, then give up    │
│ ❌ Spam block → Manual review needed   │
└────────────────────────────────────────┘

TOTAL TIMELINE: Submission to HubSpot = <5 seconds
```

---

## FAILURE SCENARIOS & FALLBACKS

### Scenario 1: Form Validation Fails

**Trigger:** User submits invalid data (empty email, bad phone)

**Error Handling:**
```
User submits form
  ↓
WordPress form validation (forms.php)
  ↓
❌ FAIL: Email invalid
  ↓
Return error message to form (show in UI)
  ↓
User sees: "Please enter a valid email address"
  ↓
User corrects & resubmits
  ↓
✅ PASS: Validation succeeds
  ↓
Continue to Step 4 (admin email)
```

**Fallback:** No data stored. No webhook sent. User fixes and tries again.

---

### Scenario 2: reCAPTCHA Blocks Submission (Spam)

**Trigger:** reCAPTCHA v3 score <0.5 (bot-like behavior)

**Error Handling:**
```
Form submitted with reCAPTCHA score = 0.3
  ↓
WordPress checks: score < 0.5?
  ↓
❌ YES: Reject submission
  ↓
Log in error logs: "reCAPTCHA blocked: score=0.3"
  ↓
Show message to user: "Unable to process (security check failed)"
  ↓
User sees form is blocked, can't resubmit immediately
  ↓
Real users retry (score improves)
Bots give up
```

**Fallback:** Legitimate user retries later. Spam blocked.

---

### Scenario 3: Admin Email Fails to Send

**Trigger:** SMTP server unavailable, email provider down

**Error Handling:**
```
arcopan_form_send_admin_email() called
  ↓
wp_mail() tries to send via SMTP (SendGrid)
  ↓
❌ FAIL: SendGrid timeout (>5 second retry)
  ↓
wp_mail() returns false
  ↓
Log error: "Admin email failed for entry ID 12345"
  ↓
Don't block form submission (non-critical)
  ↓
Webhook still sends to n8n
  ↓
Developer sees error in logs next day
  ↓
Manually review missed admin emails
```

**Fallback:** Form submission succeeds anyway. Admin email can be sent manually if needed.

---

### Scenario 4: Webhook Times Out (n8n Down)

**Trigger:** n8n server unreachable, network timeout

**Error Handling:**
```
Form submission stored in WordPress
  ↓
arcopan_form_handler() calls wp_remote_post()
  ↓
❌ TIMEOUT: n8n URL unreachable after 8 seconds
  ↓
wp_remote_post() returns WP_Error
  ↓
apply_filters() catches error
  ↓
Log: "Webhook failed for entry ID 12345, URL unreachable"
  ↓
Don't block user (non-blocking call)
  ↓
Return control to WordPress (form submission complete)
  ↓
User sees success message: "Quote request received"
```

**Fallback:**
- Form data safe in WordPress Gravity Forms
- Developer manually triggers webhook later (WP-CLI command):
  ```bash
  wp arcopan resend-webhook --entry-id=12345
  ```
- Or: Manual import of data to HubSpot (CSV upload)

---

### Scenario 5: n8n Webhook Receives Bad Payload

**Trigger:** WordPress sends malformed JSON or missing required fields

**Error Handling:**
```
n8n receives webhook POST
  ↓
Webhook node validates JSON structure
  ↓
❌ FAIL: Missing required field "email"
  ↓
Validation step rejects
  ↓
Log error: "Invalid payload: missing email field"
  ↓
Alert to Slack: #n8n-errors channel
  ↓
Workflow stops (doesn't proceed to HubSpot)
  ↓
Developer reviews error
  ↓
Identify root cause (WordPress bug or user didn't fill field)
  ↓
Fix & reprocess
```

**Prevention:** WordPress validation ensures all required fields present before webhook.

---

### Scenario 6: HubSpot API Fails

**Trigger:** HubSpot API rate limit exceeded, authentication error

**Error Handling:**
```
n8n calls HubSpot API: "Create contact"
  ↓
❌ FAIL: 429 Too Many Requests (rate limit)
  ↓
n8n error handler catches
  ↓
Retry logic: Wait 60 seconds, retry (3 times max)
  ↓
First retry: ✅ SUCCESS
  ↓
Contact created in HubSpot
  ↓
Proceed to Deal creation
  ↓
Timeline slightly delayed (but successful)

OR (if all 3 retries fail):
  ↓
❌ FINAL FAIL: Cannot reach HubSpot
  ↓
Log error with context:
  - Entry ID
  - Company name
  - Error message
  - Timestamp
  ↓
Send alert to Slack: #hubspot-errors
  ↓
Manual intervention:
  - Check HubSpot status page
  - Verify API credentials
  - Reprocess manually once fixed
```

**Fallback:** Webhook retries built into n8n. If HubSpot recovers, data syncs.

---

### Scenario 7: SendGrid Email Blocked as Spam

**Trigger:** Email provider rejects mail as spam (bad DKIM/SPF)

**Error Handling:**
```
n8n sends customer email via SendGrid
  ↓
Customer mail server receives
  ↓
❌ FAIL: DKIM/SPF signature invalid
  ↓
Email bounces: Spam Folder OR Rejection
  ↓
SendGrid logs bounce
  ↓
n8n error handler logs: "Email bounce: invalid DKIM"
  ↓
Alert to Slack: #email-errors
  ↓
Admin action:
  - Check SPF/DKIM records (DNS)
  - Verify SendGrid credentials
  - Test with internal email first
  - Manually send email to customer
```

**Prevention:**
- SPF record: `v=spf1 include:sendgrid.net ~all`
- DKIM: Enable in SendGrid dashboard
- DMARC: `v=DMARC1; p=none; rua=mailto:admin@arcopan.com`
- Test sending before launch

---

### Scenario 8: Multiple Failures (Cascading Failure)

**Trigger:** n8n down AND SMTP down simultaneously

**Error Handling:**
```
Customer submits form
  ↓
✅ Form validation passes
  ✅ reCAPTCHA passes
  ✅ Data stored in WordPress
  │
  ❌ Admin email fails (SMTP down)
  │  → Logged, doesn't block
  │
  ❌ Webhook fails (n8n down)
  │  → Logged, doesn't block
  │
  ✅ User sees success: "Request received"

What actually happened:
  • Form data SAFE in WordPress
  • Contact NOT in HubSpot
  • Admin NOT notified by email
  • Customer NOT notified

Recovery:
  1. Fix SMTP (SendGrid credentials)
  2. Fix n8n (restart, check logs)
  3. Manually resend admin email (template)
  4. Manually create contact in HubSpot (CSV import or form)
  5. Manually send customer email (SendGrid interface)

Timeline:
  • Discovery: Within 1 hour (dev checks error logs)
  • Fix: 30–60 minutes
  • Full recovery: 2–3 hours (all systems operational)
```

**Prevention Strategy:**
- Monitor all systems (error logs, uptime monitoring)
- Alerts on Slack for critical failures
- Daily backup of form submissions (Gravity Forms export)
- Failover SMTP provider (if SendGrid down, use AWS SES)

---

## SYSTEM DEPENDENCIES & REQUIREMENTS

### Critical Services (Blocking)

If **DOWN** → Form submissions cannot complete properly:

| Service | Role | Fallback |
|---|---|---|
| WordPress | Form storage | None (critical) |
| MySQL | Data persistence | Backup restore |
| Gravity Forms | Form validation | None (critical) |

### Important Services (Non-Blocking)

If **DOWN** → Submissions complete, but follow-up delayed:

| Service | Role | Fallback |
|---|---|---|
| SendGrid SMTP | Email notifications | Retry queue |
| n8n | HubSpot sync | Manual import |
| HubSpot API | CRM storage | Local queue, retry |

### Enhancement Services (Optional)

If **DOWN** → Feature degradation only:

| Service | Role | Fallback |
|---|---|---|
| Cloudinary CDN | Image delivery | Direct WP uploads |
| Yoast SEO | SEO tooling | Manual metadata entry |
| Wordfence | Security | Native WP protection |

---

## MONITORING & ALERTS

### Error Monitoring

**Tool:** Sentry (or similar)  
**Logs to Watch:**

```php
// Critical errors (alert immediately)
→ Fatal PHP errors in forms.php
→ Database connection failures
→ Gravity Forms plugin errors

// Important errors (alert within 1 hour)
→ SMTP send failures
→ Webhook timeouts
→ Webhook validation failures

// Minor errors (log only, review daily)
→ Missing images
→ Slow queries
→ Deprecated code
```

### Uptime Monitoring

**Tool:** Pingdom or UptimeRobot  
**Checks:**

```
Every 5 minutes:
  ✓ Is arcopan.com HTTP 200?
  ✓ Is contact form reachable?
  ✓ Is n8n webhook endpoint reachable?

On failure:
  → Send Slack alert (immediate)
  → Page on-call engineer (after 5 min)
  → Create incident ticket
```

### Performance Monitoring

**Tool:** Google Analytics + Core Web Vitals  
**Thresholds:**

```
Daily check:
  • Page load time <2.5 seconds
  • Core Web Vitals all "Good"
  • Form submission success rate >99%
  • Zero 5xx server errors

Weekly check:
  • API response times <500ms
  • Email delivery rate >95%
  • Webhook success rate >99%
```

---

## DISASTER RECOVERY PLAN

### Backup Strategy

**Database Backups:**
- Frequency: Daily (automated)
- Retention: 30 days
- Test restore: Weekly

**Code Backups:**
- Location: Git repository (GitHub/GitLab)
- Frequency: Every commit
- Retention: Unlimited

**Form Data Backups:**
- Export Gravity Forms data: Weekly
- Location: Encrypted S3 bucket
- Retention: 90 days

### Recovery Procedures

**Scenario: Database Corrupted**

```
1. Detect: Database query errors in error logs
2. Verify: WordPress unable to load pages
3. Alert: Page on-call DBA
4. Restore: Use most recent daily backup
5. Verify: Check all forms, pages load correctly
6. Notify: Tell team (Slack: #incidents)
7. Post-mortem: What caused corruption?
8. Prevent: Implement safeguards
```

**Scenario: Ransomware Attack**

```
1. Detect: Files encrypted, inaccessible
2. Isolate: Take site offline immediately
3. Assess: Was backup infected too?
4. Restore: Use clean backup (must be older than attack)
5. Patch: Update all plugins, WordPress core
6. Scan: Run antivirus on restored server
7. Monitor: Watch for re-infection
8. Post-mortem: How did attack happen?
```

**Scenario: Complete Server Failure**

```
1. Detect: All services down, 500 errors
2. Provision: Spin up new server (AWS/VPS)
3. Restore: Deploy code + database from backup
4. Verify: All pages load, forms work
5. DNS: Point domain to new server IP
6. Notify: Tell customers "Brief maintenance"
7. Monitor: Watch for 24 hours (stability)
```

**RTO (Recovery Time Objective):** <4 hours  
**RPO (Recovery Point Objective):** <24 hours (latest backup)

---

## SECURITY ARCHITECTURE

### Input Validation

**All form inputs:**
- Sanitize via WordPress `sanitize_*()` functions
- Validate via `wp_kses_*()` (HTML allowed: none)
- Escape output via `esc_html()`, `esc_url()`

### Authentication & Authorization

**Users:**
- 2FA required (Two-Factor Authentication via plugin)
- Passwords: Min 12 chars, complexity required
- Roles: Principle of least privilege

**API Keys:**
- Never committed to Git (use environment variables)
- Rotated quarterly
- Stored in secure manager (AWS Secrets Manager)

### SSL/TLS

**Certificate:**
- Let's Encrypt (auto-renew every 90 days)
- Covers: arcopan.com + *.arcopan.com + international domains

**Headers:**
- HSTS: `Strict-Transport-Security: max-age=31536000`
- CSP: Content Security Policy (whitelist trusted domains)

### Database Security

**Backups:**
- Encrypted with AES-256
- Stored in S3 with restricted access
- Tested quarterly for integrity

**Queries:**
- All via WordPress functions (SQL injection protected)
- Parameterized queries (use placeholders)
- No direct SQL from forms

---

**Next:** System fully documented. Team can operate and troubleshoot with confidence.

# OPERATIONS & CONTENT MANAGEMENT

**Project:** ARCOPAN Cold Storage WordPress  
**Document:** Daily operations, content workflow, user roles, lead handling  
**Updated:** March 25, 2026

---

## TEAM ROLES & PERMISSIONS

### Role 1: Administrator
**Responsibilities:** System maintenance, security, backups  
**WordPress Role:** Administrator  
**Capabilities:**
- Install/deactivate plugins
- Manage user accounts
- Configure backups
- Access error logs
- Modify theme settings

**Team Members:**
- DevOps Lead: [Name]
- IT Manager: [Name]

---

### Role 2: Content Manager
**Responsibilities:** Create/edit pages, blog, resources  
**WordPress Role:** Editor  
**Capabilities:**
- Create/edit posts and pages
- Publish/schedule content
- Manage categories and tags
- Upload media
- Review SEO metadata (Yoast)

**Access:** All content areas except plugins/theme  
**Team Members:**
- Content Lead: [Name]
- Technical Writer: [Name]

---

### Role 3: Product Manager
**Responsibilities:** Product pages, specifications, pricing  
**WordPress Role:** Editor (with ACF access)  
**Capabilities:**
- Create/edit products (CPT: arcopan_product)
- Manage product metadata (ACF fields)
- Upload product datasheets
- Manage product taxonomy (categories)

**Cannot:** Publish to production (Editor review required)  
**Team Members:**
- Product Marketing: [Name]
- Technical Sales: [Name]

---

### Role 4: Marketing Manager
**Responsibilities:** Campaign pages, email, analytics  
**WordPress Role:** Contributor (limited)  
**Capabilities:**
- Create/edit blog posts (draft only)
- Suggest edits to pages
- View analytics dashboard

**Cannot:** Publish pages, edit products  
**Team Members:**
- Marketing Lead: [Name]
- Digital Marketing: [Name]

---

### Role 5: RFQ Support Specialist
**Responsibilities:** Monitor and respond to form submissions  
**Access:** HubSpot CRM + Email  
**Capabilities:**
- View RFQ submissions in HubSpot
- Send email responses
- Update deal status
- Export lead reports

**Cannot:** Edit WordPress content  
**Team Members:**
- Sales Support: [Name]
- Account Manager: [Name]

---

## CONTENT MANAGEMENT WORKFLOW

### Stage 1: Planning & Approval

**Trigger:** Need for new page/content  
**Owner:** Content Lead or Product Manager

**Process:**
1. **Requirements Document**
   - Page URL slug (e.g., `/solutions/custom-solution/`)
   - Page title (H1)
   - Keyword focus
   - Target audience
   - Internal links required

2. **Content Brief**
   - Outline with H2 headings
   - Target word count (800–1,200)
   - CTA placement (primary + secondary)
   - Media requirements (images, videos)

3. **Approval Checklist**
   - [ ] Content aligned with ARCOPAN_Master.md
   - [ ] Keyword research completed
   - [ ] Internal linking mapped
   - [ ] Approved by Content Lead

**Timeline:** 2–3 business days

---

### Stage 2: Content Creation

**Owner:** Technical Writer or Content Manager  
**Tools:** VS Code (Markdown) or WordPress editor

**Process:**

1. **Create Markdown File** (recommended for version control)
   ```
   File location: arcopan-child/content/[category]/[slug].md
   Example: arcopan-child/content/solutions/custom-solution.md
   ```

2. **YAML Frontmatter** (required on all pages)
   ```yaml
   ---
   url: /solutions/custom-solution/
   meta_title: "Custom Cold Storage Solutions | ARCOPAN"
   meta_desc: "Tailor-made cooling systems for unique requirements. 20+ years expertise. Request quote."
   h1: "Custom-Built Cold Storage Solutions"
   primary_cta: "Request Custom Quote"
   primary_cta_url: /contact/?solution=custom
   secondary_cta: "View Our Solutions"
   secondary_cta_url: /solutions/
   ---
   ```

3. **Content Requirements**
   - Minimum 1,000 words (substantive)
   - H1 once (auto-inserted from frontmatter)
   - H2 subheadings (3–5 per page)
   - Bulleted lists (minimum 2 per page)
   - Bold key terms (2–3 per section)
   - Internal links (minimum 3, maximum 7)

4. **SEO Checklist**
   - [ ] Primary keyword in first 100 words
   - [ ] Secondary keywords (2–3) mentioned at least once
   - [ ] Meta title 50–60 characters
   - [ ] Meta description 145–158 characters
   - [ ] No keyword stuffing
   - [ ] Natural, scannable structure

5. **Image Requirements**
   - Minimum 1 hero image (1920×1080px)
   - WebP format (JPG fallback)
   - Descriptive alt text (include keyword)
   - File size <200KB (Cloudinary CDN handles compression)

**Timeline:** 3–5 business days per page

---

### Stage 3: Review & SEO Optimization

**Owner:** Content Lead + SEO Specialist  
**Tools:** Yoast SEO, Google Search Console

**Review Checklist:**
- [ ] Spelling & grammar (no errors)
- [ ] Brand voice consistent
- [ ] Links are functional
- [ ] Images have alt text
- [ ] Yoast SEO: Green light (all indicators)
- [ ] Readability score: >60 (Yoast)
- [ ] No duplicate content (Copyscape check)

**Yoast Configuration:**
```
Focus Keyword: [primary keyword]
Readability: Pass (green light)
Content Type: Product/Industry/Solution/Resource
Synonyms: [secondary keywords]
```

**SEO Feedback Loop:**
- If red flags: Return to writer for revision
- If yellow flags: Minor adjustments acceptable
- If green: Approved for publishing

**Timeline:** 2 business days

---

### Stage 4: Publishing

**Owner:** Content Manager + Editor (approval)  
**Tools:** WordPress admin, Git (if versioned)

**Pre-Publishing Checklist:**
- [ ] SEO review passed
- [ ] Legal/compliance review (if applicable)
- [ ] All internal links verified
- [ ] Images optimized
- [ ] Meta tags correct
- [ ] Canonical URL set (auto-generated by Yoast)

**Publishing Steps:**

**Option A: Via WordPress Admin**
1. Create new page/post
2. Add content (markdown or editor)
3. Set YAML frontmatter as meta fields (ACF)
4. Configure SEO metadata (Yoast SEO plugin)
5. Set publish date (now or schedule)
6. Click "Publish"

**Option B: Via Markdown + Git** (recommended for version control)
1. Create `.md` file in `arcopan-child/content/[category]/`
2. Commit to Git with message: `feat: Add [page title]`
3. Use WordPress import tool (or PHP script) to sync markdown → WordPress
4. Verify published in WordPress admin

**Post-Publishing:**
- [ ] Test page loads without errors
- [ ] Links work (internal + external)
- [ ] Images render correctly
- [ ] Mobile responsive (test at 375px)
- [ ] Submit to Google Search Console

**Timeline:** 1 business day

---

## PRODUCT CREATION SOP

### Step 1: Product Definition

**Owner:** Product Manager  
**Input:** Product specification document

**Required Information:**
- Product name (max 50 characters)
- SKU/model number
- Product category (dropdown)
- Short description (max 160 chars)
- Long description (500–1,000 words)
- Key specifications (table format)
- Certifications (ISO, EN, DIN)
- Price range (if applicable)
- Lead time
- Related products (2–3)
- Related industries (2–3)

---

### Step 2: Create Product in WordPress

**Owner:** Product Manager  
**Tool:** WordPress admin → Products → New Product

**Fields to Complete:**

**Basic:**
- Product Title
- URL Slug (auto-generated, verify)
- Product Category (select hierarchy: e.g., "Cooling Systems > Chillers")
- Short Description

**Description:**
- Long description (1,000+ words recommended)
- Use H2 for sections ("Specifications", "Applications", "Certifications")
- Include internal links to related solutions/industries

**ACF Fields** (Custom Product Fields):
- `field_arc_sku` — Model number
- `field_arc_capacity` — Cooling capacity (units)
- `field_arc_temp_min` — Minimum temperature
- `field_arc_temp_max` — Maximum temperature
- `field_arc_power_consumption` — kW (estimated)
- `field_arc_dimensions` — Height × Width × Depth (meters)
- `field_arc_certifications` — Multi-select (ISO 22000, EN 12941, etc.)
- `field_arc_datasheet` — File upload (PDF)
- `field_arc_related_products` — Link to related products (2–3)
- `field_arc_related_industries` — Link to industries (2–3)

**SEO Metadata** (Yoast SEO):
- Focus Keyword: Product name + category (e.g., "chilled storage cabinet")
- Meta Title: "[Product Name] | ARCOPAN" (50–60 chars)
- Meta Description: Benefit + proof + CTA (145–158 chars)
- Slug: `/products/[product-slug]/`

**Example:**
```
Title: Chilled Storage Cabinet XL-5000
Slug: chilled-cabinet-xl-5000
Focus Keyword: chilled storage cabinet
Meta Title: "Chilled Storage Cabinet XL-5000 | ARCOPAN"
Meta Desc: "Industrial chilled cabinets 500–2000 units/hr. 
           ISO 22000 certified. Request quote."
```

**Featured Image:**
- Minimum 1920×1080px
- Product photo or hero image
- WebP format preferred
- Alt text: "[Product name] | ARCOPAN"

---

### Step 3: Product Page Review

**Owner:** Content Lead + Product Manager

**Checklist:**
- [ ] Description 1,000+ words
- [ ] H2 subheadings (3–5)
- [ ] Specifications table complete
- [ ] Related products linked (2–3)
- [ ] Related industries linked (2–3)
- [ ] Certifications visible
- [ ] Datasheet upload successful
- [ ] Images optimized
- [ ] SEO metadata set
- [ ] Mobile preview tested

---

### Step 4: Publish & Promote

**Owner:** Content Manager

**Steps:**
1. Set status to "Publish"
2. Submit to Google Search Console
3. Create social media post (LinkedIn)
4. Add to related pages (link bidirectionally)

**Post-Publish Tasks:**
- [ ] Monitor Search Console for crawl errors
- [ ] Track keyword rankings (30-day window)
- [ ] Monitor traffic via Google Analytics
- [ ] Add product to email newsletter (if applicable)

---

## SEO UPDATE PROCESS

### Quarterly SEO Audit

**Owner:** SEO Specialist  
**Frequency:** Every 90 days  
**Duration:** 1 week

**Steps:**

1. **Keyword Ranking Check**
   - Tool: Ahrefs or Semrush
   - Report: Which keywords dropped in ranking
   - Target: Maintain top 10 for primary keywords

2. **Content Gap Analysis**
   - Tool: Ahrefs Site Explorer
   - Check: Which competitor pages rank for our keywords
   - Action: Identify content to create

3. **Search Console Review**
   - Check: Indexation status
   - Check: Coverage errors (404s, robots.txt blocks)
   - Action: Fix critical errors

4. **Backlink Audit**
   - Tool: Ahrefs Backlink Profile
   - Check: New referring domains
   - Check: Lost backlinks
   - Action: Outreach to recover lost links

5. **Technical SEO Audit**
   - Tool: Lighthouse
   - Check: Core Web Vitals (LCP, FID, CLS)
   - Check: Mobile usability
   - Check: SSL security
   - Action: Fix issues >5% of pages

**Output:** SEO Audit Report (document findings + action items)

---

### Content Optimization Process

**Trigger:** Page ranking 11–50 for primary keyword (page 2–5 in SERPs)  
**Owner:** Content Lead  
**Goal:** Improve ranking to top 10

**Process:**

1. **Analyze Current Content**
   - Readability: Does copy flow naturally?
   - Length: Are competitors longer/shorter?
   - Structure: Are headings clear?
   - Evidence: Include numbers, case studies, proof?

2. **Competitive Analysis**
   - Compare to top 3 ranking pages
   - What's in their content that we're missing?
   - How's their internal linking?

3. **Optimization Plan**
   - Expand content (add 200–500 words if needed)
   - Add missing sections (from competitors)
   - Strengthen internal links (add 1–2 more contextual links)
   - Improve readability (shorter paragraphs, more lists)

4. **Update Page**
   - Edit in WordPress or markdown
   - Add new sections with H2 headings
   - Insert internal links naturally
   - Update featured image if stale

5. **Monitor Results**
   - Wait 2–4 weeks (Google re-crawls)
   - Check ranking in Search Console
   - Goal: Move from position 11–50 to top 10

---

### Monthly SEO Checklist

**Owner:** SEO Specialist  
**Time:** 2–3 hours/month

- [ ] Monitor top 10 keyword rankings (Ahrefs/Semrush)
- [ ] Check Search Console for crawl errors
- [ ] Review Core Web Vitals (Google Search Console)
- [ ] Audit 3–5 underperforming pages (low CTR)
- [ ] Check for new 404 errors
- [ ] Review internal links (broken link checker)
- [ ] Update 1–2 pages with seasonal/trending keywords
- [ ] Report findings to team (Slack/email)

---

## RFQ LEAD HANDLING PROCESS

### Step 1: Form Submission → Email Alert

**Trigger:** Customer submits RFQ form  
**System:** Gravity Forms → n8n → HubSpot + Email

**Automated Actions:**
1. WordPress receives form submission (gform_after_submission hook)
2. Admin email sent (plain text summary of fields)
3. Webhook payload sent to n8n
4. n8n creates contact in HubSpot
5. Deal created in HubSpot (stage: "New Lead")
6. Customer receives confirmation email with reference number

**Timeline:** <1 minute from submission to HubSpot

---

### Step 2: Lead Review in HubSpot

**Owner:** Sales Support Specialist  
**Frequency:** Check every 2 hours (during business hours)

**Process:**
1. Log into HubSpot CRM
2. Go to **Deals** → Pipeline → **New Lead** stage
3. Review each new deal:
   - Company name
   - Contact email/phone
   - Project type (solution/industry/product)
   - Budget range
   - Timeline
   - Key constraints

4. **Qualification Questions:**
   - Is contact information complete?
   - Is timeline feasible (ASAP vs. 6+ months)?
   - Is budget realistic for scope?

5. **Initial Response:**
   - Call or email within 2 hours (peak business hours)
   - Acknowledge inquiry
   - Ask clarifying questions (if needed)
   - Propose next steps

---

### Step 3: Lead Engagement

**Owner:** Sales Account Manager  
**Timeline:** 24–48 hours from submission

**Process:**

1. **Needs Analysis Call** (15–30 min)
   - Confirm project requirements
   - Understand constraints/limitations
   - Identify decision makers
   - Determine budget authority

2. **Proposal Creation**
   - Use form data to pre-populate quote template
   - Add product recommendations
   - Calculate total cost (capacity + options)
   - Include delivery timeline

3. **Send Proposal**
   - Email proposal with cover letter
   - Include company brochure (optional)
   - Include 2–3 relevant case studies
   - Set follow-up call (24–48 hours)

4. **Deal Status Update**
   - HubSpot: Move to "Proposal Sent" stage
   - Log call notes in CRM
   - Set follow-up date

---

### Step 4: Follow-Up & Closing

**Owner:** Sales Account Manager  
**Timeline:** 48–72 hours after proposal

**Process:**

1. **Follow-Up Call**
   - Confirm proposal received
   - Answer technical questions
   - Address budget/timeline concerns
   - Move toward decision

2. **Negotiation** (if needed)
   - Discuss payment terms
   - Discuss delivery schedule
   - Discuss warranty/service

3. **Closing**
   - Send final quote (if revised)
   - Request purchase order
   - Move deal to "Closed Won" (when PO received)

4. **Handoff to Implementation**
   - Send customer to fulfillment team
   - Create project management task
   - Schedule installation/commissioning

---

### SLA & Metrics

**Service Level Agreements:**

| Milestone | Target | Status |
|---|---|---|
| Email alert to team | <1 min | Automated |
| Initial response | <2 hours (business hours) | Sales team |
| Needs analysis call | <24 hours | Sales manager |
| Proposal sent | <48 hours | Sales manager |
| Follow-up call | <72 hours | Sales manager |
| Closing call | <7 days | Sales manager |

**Performance Metrics (tracked in HubSpot):**
- Average response time (goal: <2 hours)
- Proposal close rate (goal: >30%)
- Average deal value (goal: >€50k)
- Sales cycle length (goal: <30 days)

---

### RFQ Response Email Template

**Subject:** "We Received Your Cold Storage Quote Request"

**Body:**
```
Dear [First Name],

Thank you for your interest in ARCOPAN cold storage systems.

We received your request on [Date] at [Time] (Reference: [Entry ID]).

Here's what happens next:

1. Our technical team reviews your requirements
2. We prepare a customized quote (24–48 hours)
3. We schedule a call to discuss details
4. We send final proposal with timeline and pricing

Questions in the meantime? Reply to this email or call us:
📞 +49 [Phone Number]
📧 sales@arcopan.com

We look forward to helping with your project.

Best regards,
ARCOPAN Sales Team
```

---

## HANDOFF CHECKLIST: NEW EMPLOYEE

**For any new team member joining:**

### Week 1: Onboarding
- [ ] WordPress admin access created
- [ ] User role assigned (Admin/Editor/Contributor)
- [ ] Docs read: 00-system-overview.md + role-specific guides
- [ ] HubSpot access (if applicable)
- [ ] Slack channel added
- [ ] Team intro meeting (30 min)

### Week 2: Training
- [ ] Shadow existing team member (1 full day)
- [ ] Complete first task under supervision
- [ ] Review feedback + corrections
- [ ] Repeat task independently

### Week 3: Independent Work
- [ ] Assigned to first independent project
- [ ] Check-ins with manager (daily first week, then weekly)
- [ ] Escalation path documented

---

**Next:** Operations guide complete. Team can execute daily tasks with clear SOPs.

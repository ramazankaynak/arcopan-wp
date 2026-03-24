# CONVERSION RATE OPTIMIZATION

**Project:** ARCOPAN Cold Storage WordPress  
**Document:** CRO strategy, funnel mapping, trust elements, A/B testing  
**Updated:** March 25, 2026

---

## CONVERSION FUNNEL ARCHITECTURE

All visitor journeys converge on **one conversion goal:** RFQ form submission.

### Funnel Stages

**Stage 1: Awareness (Entry)**
- Visitor lands on page (organic search, ads, referral)
- Goal: Communicate value proposition in <5 seconds
- Elements: Hero section, H1, subtitle, trust badges

**Stage 2: Consideration (Engagement)**
- Visitor explores product/solution details
- Goal: Build confidence through evidence
- Elements: Spec tables, certifications, client counts, tech specs

**Stage 3: Evaluation (Comparison)**
- Visitor compares against competitors
- Goal: Establish differentiation
- Elements: Side-by-side comparisons, unique features, ROI calculator

**Stage 4: Decision (Conversion)**
- Visitor decides to request quote
- Goal: Remove friction from RFQ form
- Elements: Clear CTA, minimal form fields, trust messaging

### Conversion Paths

**Path A: Direct Product Route**
```
Homepage 
  → Product Category Page 
  → Product Detail Page 
  → RFQ Form (sticky CTA)
  → Confirmation
```

**Path B: Industry-First Route**
```
Homepage 
  → Industry Page 
  → Industry-Specific Solutions 
  → Related Products 
  → RFQ Form (context CTA)
  → Confirmation
```

**Path C: Solution-First Route**
```
Homepage 
  → Solutions Hub 
  → Solution Detail Page 
  → Recommended Industries/Products 
  → RFQ Form (contextual)
  → Confirmation
```

---

## PRIMARY CALL-TO-ACTION (CTA) SYSTEM

All pages must have **minimum 3 CTA exposures** (above fold, mid-page, footer/sticky).

### CTA Placement Rules

**Above the Fold (Hero Section)**
- Primary CTA: "Request Quote" button (teal, 48px height)
- Secondary CTA: "Explore Solutions" link (ghost style)
- Copy: "Get your custom quote in 5 minutes"
- Visibility: Must not require scrolling

**Mid-Page (After Value Prop)**
- Primary CTA: "Send Quote Request" button
- Copy: "Let's discuss your specific needs"
- Placement: After 3rd section (post-engagement)

**Sticky Footer / Sidebar (Desktop Only)**
- Mini CTA: Floating "RFQ" button (corner, 40×40px)
- Copy: Hover tooltip = "Quick quote request"
- Behavior: Follows scroll, remains visible

**Footer Section**
- Primary CTA: "Start Your Quote" button
- Copy: "Complete the form below to get started"
- Placement: Above footer navigation

### CTA Button Styling

```css
.btn.btn--cta-primary {
  background: var(--color-primary);      /* #0D6E6E */
  color: white;
  padding: 14px 32px;
  border-radius: 4px;
  font-weight: 600;
  font-size: 16px;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(13, 110, 110, 0.15);
  transition: all 0.2s ease;
}

.btn.btn--cta-primary:hover {
  background: var(--color-primary-dark);  /* #094F4F */
  box-shadow: 0 4px 12px rgba(13, 110, 110, 0.25);
  transform: translateY(-1px);
}

.btn.btn--cta-secondary {
  background: transparent;
  color: var(--color-primary);
  border: 2px solid var(--color-primary);
  padding: 12px 28px;
  text-decoration: none;
  border-radius: 4px;
}

.btn.btn--cta-secondary:hover {
  background: var(--color-primary-light);  /* #E6F4F4 */
}
```

### CTA Copy Guidelines

All CTA text must follow:
- **Action verb** (Request, Get, Explore, Discover, Download)
- **Benefit** (Quote, Consultation, Checklist, Comparison)
- **Urgency** (optional, "Start today", "Free consultation")

**Examples:**
- ✅ "Request a Quote Today"
- ✅ "Get Free Consultation"
- ✅ "Download Spec Sheet"
- ❌ "Submit" (passive, no benefit)
- ❌ "Click Here" (generic, no action clarity)

---

## TRUST ELEMENTS

Trust directly impacts conversion. Every page must include:

### Element 1: Certification Badges
**Location:** Above hero section OR hero section (mobile)  
**Content:** Logo strip (ISO 22000, EN 12941, DIN, industry-specific)  
**Copy:** "Certified and Compliant with International Standards"  
**Update Frequency:** Quarterly

### Element 2: Customer Count & Experience
**Location:** Section 2 (post-hero)  
**Content:** "30+ years | 500+ projects | 5 continents"  
**Styling:** Large numerals (monospace font), dark BG  
**Update Frequency:** Annually

### Element 3: Client Logos (Optional)
**Location:** Trust bar section (after fold)  
**Content:** 8–12 recognizable customer logos (grayscale)  
**Note:** Only if permission obtained; never use without consent  
**Update Frequency:** When new major client signs agreement

### Element 4: Testimonial / Case Study
**Location:** Mid-page section (Section 4–5)  
**Content:** 
```
"Quote text (50–100 words)"
— Name, Title, Company
★★★★★ (5 stars)
```
**Update Frequency:** Quarterly (add 1 new case study)

### Element 5: Money-Back Guarantee or Service Promise
**Location:** Footer section  
**Copy:** "30-day performance guarantee" OR "24-hour response time"  
**Update Frequency:** Quarterly review

### Element 6: GDPR Privacy Notice
**Location:** Above RFQ form  
**Copy:** "We protect your data. Read our Privacy Policy."  
**Link Target:** `/legal/privacy-policy/`  
**Update Frequency:** Annually

---

## FORM FIELD OPTIMIZATION

RFQ form conversion depends on field design.

### Field Minimization
**Ideal:** 5 required fields (steps 1 + 4)  
**Current:** 8 required fields  
**Target:** Reduce required to 5 (consider steps 2–3 optional with pop-up)

### Required vs. Optional
**Phase 1 (Required):**
- Company Name
- Email
- Phone
- Project Timeline
- Budget Range

**Phase 2 (Optional, collapsible):**
- Industry
- Temperature Range
- Key Constraints
- Product Selection

### Form Field Copy
**Label:** Clear, single line (e.g., "What's your budget?")  
**Placeholder:** Example input (e.g., "e.g., €100–200k")  
**Help text:** Brief explanation (e.g., "Helps us tailor recommendation")  
**Error message:** Specific, actionable (e.g., "Please enter a valid email")

### Field Validation
**Real-time validation:** Check email/phone as user types  
**Error prevention:** Suggest corrections before submission  
**Success feedback:** Green checkmark when valid

---

## FRICTION AUDIT

All pages audited quarterly for friction points.

| Page | Friction Point | Impact | Solution |
|---|---|---|---|
| Product detail | 3 form steps before conversion | Medium | Reduce to 2-step wizard |
| Industries hub | No clear "which fits me?" CTA | Medium | Add assessment quiz (1 min) |
| About page | No trust metrics below fold | Low | Add numbers bar (30+ years, etc.) |
| Contact page | Form too long | High | Simplify to 3 fields, hide advanced |

**Quarterly Audit Checklist:**
- [ ] Form field count (target: ≤5 required)
- [ ] Page load speed (target: <2.5s)
- [ ] CTA clarity (no ambiguous language)
- [ ] Mobile responsiveness (375px minimum)
- [ ] Form success rate (target: >15% of visitors)
- [ ] Exit-intent popup performance (if deployed)

---

## A/B TESTING ROADMAP

All tests run minimum 2 weeks, minimum 100 conversions per variant.

### Test 1: CTA Button Color
**Hypothesis:** Darker button converts better  
**Variant A (Control):** Teal (#0D6E6E)  
**Variant B:** Dark teal (#094F4F)  
**Metric:** Click-through rate (CTR) on RFQ CTA  
**Duration:** 2 weeks  
**Target:** 5% CTR improvement

### Test 2: Form Field Count
**Hypothesis:** 3-field form converts better than 8-field  
**Variant A (Control):** 8-field form (current)  
**Variant B:** 3-field form (Company, Email, Budget)  
**Metric:** Form submission rate  
**Duration:** 3 weeks  
**Target:** 25% submission increase

### Test 3: Hero Copy
**Hypothesis:** Benefit-focused headline converts better  
**Variant A (Control):** "Industrial Cold Storage Solutions"  
**Variant B:** "Cut Food Waste by 30% with ARCOPAN Systems"  
**Metric:** Click-through to next section  
**Duration:** 2 weeks  
**Target:** 8% improvement

### Test 4: Trust Element Placement
**Hypothesis:** Cert badges above hero improve conversion  
**Variant A (Control):** Certs in mid-page section  
**Variant B:** Certs above hero (fixed header)  
**Metric:** Form submission rate  
**Duration:** 3 weeks  
**Target:** 12% improvement

---

## CONVERSION METRICS & REPORTING

**Primary Metric:** RFQ form submission rate (target: >0.5% of visitors)

### Monthly Dashboard
Report these metrics:

| Metric | Current | Target | Status |
|---|---|---|---|
| Visitors to site | — | 5,000 | TBD (post-launch) |
| RFQ form views | — | 1,500 (30% of visitors) | TBD |
| RFQ submissions | — | 25 (0.5% conversion) | TBD |
| Average form completion time | — | <5 min | TBD |
| Form abandonment rate | — | <40% | TBD |

### Page-Specific Metrics
- Product detail pages: Conversion to RFQ
- Industry pages: Bounce rate, time on page
- Solution pages: Click-through to related industry
- Contact page: Form submission vs. exit rate

### Traffic Source Breakdown
- Organic search (target: 60% of traffic)
- Direct (target: 20%)
- Referral (target: 15%)
- Paid ads (target: 5%)

---

## CART ABANDONMENT & FOLLOW-UP

Since there's no shopping cart, follow-up triggers on **form abandonment**.

### Form Abandonment Sequence

**After 10 min of form start (no submit):**
- [ ] Exit-intent popup: "Finish your quote request" (optional discount code)

**24 hours after abandonment:**
- [ ] Email: "We saw you started the RFQ form. Any questions?"

**72 hours after abandonment:**
- [ ] Email: "Last chance: Get your quote" (with form link)

**7 days after abandonment:**
- [ ] Email: "May we help?" (personal invite to call)

---

## CONVERSION RATE IMPROVEMENT TARGETS

**Current state** (pre-launch): Baseline unknown  
**30-day target:** Establish baseline metrics  
**90-day target:** Improve conversion by 15%  
**180-day target:** Improve conversion by 30%

**Improvement levers:**
1. Form field optimization (estimate: +20% conversion)
2. Trust element visibility (estimate: +10%)
3. CTA clarity and placement (estimate: +15%)
4. Page load speed (estimate: +8%)
5. Mobile optimization (estimate: +12%)

---

**Next:** Set up analytics tracking, deploy A/B tests, establish monthly reporting cadence.

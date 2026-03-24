# RFQ Engineering Configurator — Form Specification

## Implementation
Gravity Forms + GF Webhook Addon + Conditional Logic.
Webhook fires to: self-hosted n8n → HubSpot CRM pipeline.
Appears on: /contact (full), /solutions/* (inline variant), /products/* (sidebar mini-variant).

## Integration Architecture
Gravity Forms submission
  → GF Webhook Addon sends POST to n8n webhook URL
  → n8n workflow:
      1. Receives GF payload
      2. Maps fields to HubSpot Contact + Deal properties
      3. Creates/updates HubSpot Contact via HubSpot API
      4. Creates HubSpot Deal with project details
      5. Sets Deal stage: "New RFQ"
  → HubSpot stores lead for sales follow-up

## n8n Webhook Node Config
- Method: POST
- URL: https://n8n.yourdomain.com/webhook/arcopan-rfq
- Authentication: Header Auth (X-ARCOPAN-Secret: shared secret token)
- Response: Immediately

## HubSpot Field Mapping (n8n → HubSpot)
GF Field             → HubSpot Property
first_name           → firstname
last_name            → lastname
email                → email
phone                → phone
company              → company
job_title            → jobtitle
project_type         → arcopan_project_type (custom)
industry             → arcopan_industry (custom)
temperature_range    → arcopan_temperature (custom)
storage_capacity     → arcopan_capacity (custom)
location             → arcopan_location (custom)
products             → arcopan_products_interest (custom)
budget_range         → arcopan_budget (custom)
timeline             → arcopan_timeline (custom)
decision_maker       → arcopan_decision_maker (custom)
source_url           → hs_analytics_source_url
submission_date      → createdate

## Step 1 — Project Type
Fields:
- project_type (radio): New Build | Expansion | Replacement | Feasibility Study
- industry (select): Food & Beverage | Meat & Poultry | Dairy | Pharmaceuticals | Logistics & Cold Chain | Retail & Supermarkets | Other

## Step 2 — Temperature & Scale
Fields:
- temperature_range (radio): Chilled 0/+4°C | Frozen -18°C | Blast Freeze | Mixed | Not Sure
- storage_capacity (select): <100m² | 100–500m² | 500–2000m² | 2000m²+ | Not Sure
- location (text): Country / City
Conditional: show blast_freeze_note if temperature_range = Blast Freeze

## Step 3 — Products of Interest
Fields:
- products (checkbox multi): Cold Storage Panels | Cooling Systems | Condensing Units | Evaporators | Racking | Doors | Dock Levellers | Full System | Not Sure
- certifications_required (checkbox multi): ISO | CE | ATEX | GDP | GMP | None
Conditional: show atex_note if ATEX selected

## Step 4 — Timeline & Budget
Fields:
- timeline (radio): ASAP | 1–3 Months | 3–6 Months | 6–12 Months | Planning Phase
- budget_range (radio): <€50k | €50–200k | €200–500k | €500k+ | Prefer Not to Say
- decision_maker (radio): Yes I Am | I Am Influencing | Researching

## Step 5 — Contact Details
Fields:
- first_name (text, required)
- last_name (text, required)
- company (text, required)
- job_title (text)
- email (email, required)
- phone (tel, optional)
- message (textarea, optional)
- gdpr_consent (checkbox, required): I agree to ARCOPAN's privacy policy
- newsletter_opt_in (checkbox, optional): Keep me updated on ARCOPAN news
Conditional: show company_vat if budget_range = €200–500k or €500k+

## Data Flow on Submission
1. GF stores entry in database (source URL, language, timestamp)
2. GF Webhook Addon → POST to n8n webhook endpoint
3. n8n creates HubSpot Contact + Deal
4. Internal email → sales@arcopan.com
5. Confirmation email → submitter
6. Phase 4: Gravity PDF generates Project Brief Summary PDF

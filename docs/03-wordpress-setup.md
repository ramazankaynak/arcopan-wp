# WordPress Setup & Installation Guide

**Project:** ARCOPAN Cold Storage EPC Website  
**Date:** March 25, 2026  
**Audience:** DevOps / WordPress Administrator  
**Status:** Production-Ready Documentation

---

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Fresh WordPress Installation](#fresh-wordpress-installation)
3. [Plugin Installation & Activation](#plugin-installation--activation)
4. [Child Theme Activation](#child-theme-activation)
5. [ACF Field Group Import](#acf-field-group-import)
6. [Menu Configuration](#menu-configuration)
7. [Permalink & Rewrite Flush](#permalink--rewrite-flush)
8. [WPML Language Setup](#wpml-language-setup)
9. [Email Configuration](#email-configuration)
10. [Verification Checklist](#verification-checklist)

---

## Prerequisites

### Server Requirements

- **WordPress:** 6.4 or later
- **PHP:** 8.1 or later
- **MySQL:** 5.7 or later (8.0+ recommended)
- **Disk Space:** 2GB minimum (5GB recommended)
- **Memory:** 256MB minimum (512MB+ recommended for ACF + WPML)
- **SSL:** Required for production

### Software/Access Required

- SSH access to server (or hosting control panel)
- WP-CLI installed (optional but recommended)
- FTP/SFTP access OR file upload capability
- Domain configured and pointed to hosting

### Licensing Keys

Obtain before starting:

- Advanced Custom Fields Pro (ACF) license key
- Gravity Forms license key
- WPML license key (if multi-language)
- Yoast SEO Premium license key
- WP Rocket license key
- The7 theme license (check if already owned)

---

## Fresh WordPress Installation

### Step 1: Download WordPress Core

```bash
cd /var/www/html  # (or your web root)
wget https://wordpress.org/latest.tar.gz
tar -xzf latest.tar.gz
mv wordpress/* .
rm -rf wordpress/ latest.tar.gz
```

Or using WP-CLI:

```bash
wp core download --locale=en_US
```

### Step 2: Create wp-config.php

```bash
wp config create --dbname=arcopan_db --dbuser=arcopan_user --dbpass=secure_password --dbhost=localhost
```

**Manual method:** Copy `wp-config-sample.php` to `wp-config.php` and edit:

```php
define( 'DB_NAME', 'arcopan_db' );
define( 'DB_USER', 'arcopan_user' );
define( 'DB_PASSWORD', 'secure_password' );
define( 'DB_HOST', 'localhost' );

// Security keys (generate at https://api.wordpress.org/secret-key/1.1/salt/)
define('AUTH_KEY',         'your-generated-key-here');
define('SECURE_AUTH_KEY',  'your-generated-key-here');
// ... (add all 8 security keys)

// Multisite (if applicable)
define( 'WP_ALLOW_MULTISITE', false ); // Keep false for single-site

// Language
define( 'WPLANG', 'en_US' );
```

### Step 3: Create Database

```bash
mysql -u root -p
CREATE DATABASE arcopan_db;
CREATE USER 'arcopan_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON arcopan_db.* TO 'arcopan_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 4: Run WordPress Installation

Navigate to `https://yourdomain.com` in browser and complete setup:
- Site title: ARCOPAN
- Site tagline: Industrial Cold Storage & Refrigeration Solutions
- Admin username: (create strong username)
- Admin email: admin@yourdomain.com
- Password: (generate strong password)

Or via WP-CLI:

```bash
wp core install \
  --url="https://yourdomain.com" \
  --title="ARCOPAN" \
  --admin_user="admin" \
  --admin_email="admin@yourdomain.com" \
  --admin_password="secure_password"
```

---

## Plugin Installation & Activation

### Step 1: Upload Parent Theme (dt-the7)

The7 theme is commercial. Ensure you have a valid license.

```bash
# Via dashboard: Appearance → Themes → Upload Theme
# Select the7-*.zip file

# OR via FTP:
# Upload extracted dt-the7 folder to /wp-content/themes/
```

### Step 2: Install Plugins via Dashboard

Navigate to **Plugins → Add New** and search/upload:

| Plugin | Method | Priority |
|---|---|---|
| **ACF Pro** | Upload .zip (licensed) | 1 - FIRST |
| **Gravity Forms** | Upload .zip (licensed) | 2 - SECOND |
| **WPML Multilingual** | Upload .zip (licensed) | 3 |
| **Yoast SEO Premium** | Upload .zip (licensed) | 4 |
| **The7 Elementor Integration** | Upload (included with The7) | 5 |
| **WP Rocket** | Upload .zip (licensed) | 6 |
| **WP Mail SMTP** | Search & install (free) | 7 |

### Step 2B: Activation & License Keys

After uploading each plugin:

```
Dashboard → Plugins → (Plugin Name) → Activate
→ Enter License Key in plugin settings
```

**ACF Pro:**
```
Dashboard → Advanced Custom Fields → License
→ Paste license key → Activate License
```

**Gravity Forms:**
```
Dashboard → Forms → Settings → License Key
→ Paste license key → Validate License
```

**WPML:**
```
Dashboard → WPML → Settings → Account
→ Paste API key from account.wpml.org
```

**Yoast SEO:**
```
Dashboard → Yoast SEO → License
→ Paste license key → Activate
```

**WP Rocket:**
```
Dashboard → WP Rocket → Settings → License
→ Paste license key → Activate
```

### Step 3: Verify Plugin Activation

Go to **Plugins** and confirm all 7 show as "Deactivate" (meaning they're active):

```
✓ ACF Pro
✓ Gravity Forms
✓ WPML Multilingual
✓ Yoast SEO Premium
✓ The7 Elementor
✓ WP Rocket
✓ WP Mail SMTP
```

---

## Child Theme Activation

### Step 1: Upload Child Theme

```bash
# Via FTP/SFTP:
# Upload extracted arcopan-child folder to /wp-content/themes/

# Folder structure should be:
/wp-content/themes/arcopan-child/
├── functions.php
├── style.css
├── inc/
├── assets/
├── acf-json/
├── seo/
├── forms/
└── ...
```

### Step 2: Activate Child Theme

**Dashboard → Appearance → Themes**

Locate "ARCOPAN Child" theme and click **Activate**.

**Verify:**
- Dashboard should say "Active theme: ARCOPAN Child"
- Homepage should load without error

---

## ACF Field Group Import

### Step 1: Prepare JSON Files

Ensure these files exist in `/wp-content/themes/arcopan-child/acf-json/`:

```
group_arcopan_product_details.json
group_arcopan_project_details.json
group_arcopan_global_options.json
```

### Step 2: Import via Dashboard

**Dashboard → Advanced Custom Fields → Tools → Import Field Groups**

1. Click "Choose File"
2. Select `group_arcopan_product_details.json`
3. Click "Import"
4. Verify: "1 field group imported" message
5. Repeat for other 2 JSON files

### Step 3: Verify Field Groups

**Dashboard → Advanced Custom Fields → Field Groups**

Should show:

```
✓ Product Details (arcopan_product CPT)
✓ Project Details (arcopan_project CPT)
✓ Global Options (Options page)
```

---

## Menu Configuration

### Step 1: Create Main Navigation Menu

**Dashboard → Appearance → Menus → Create New Menu**

**Menu Name:** Main Navigation  
**Display location:** Primary Menu

### Step 2: Add Menu Items

Add items with these URLs:

```
1. Home                 → https://yourdomain.com/
2. Company              → # (no link, dropdown parent)
   ├─ About             → /about
   ├─ Team              → /team
   ├─ Certifications    → /certifications
   └─ Sustainability    → /sustainability
3. Products             → # (mega menu parent)
   [See 01-page-matrix.md for full structure]
4. Solutions            → # (dropdown parent)
   ├─ Chilled Storage   → /solutions/cold-storage/chilled
   ├─ Frozen Storage    → /solutions/cold-storage/frozen
   ├─ Blast Freezing    → /solutions/cold-storage/blast-freezing
   └─ Food Logistics    → /solutions/cold-storage/food-logistics
5. Industries           → # (dropdown parent)
   ├─ Food & Beverage   → /industries/food-beverage
   ├─ Meat & Poultry    → /industries/meat-poultry
   ├─ Dairy             → /industries/dairy
   ├─ Pharmaceuticals   → /industries/pharmaceuticals
   ├─ Logistics         → /industries/logistics-cold-chain
   └─ Retail            → /industries/retail-supermarkets
6. Projects             → /projects
7. Resources            → # (dropdown parent)
   ├─ Datasheets        → /resources/datasheets
   ├─ Guides            → /resources/installation-guides
   ├─ Certificates      → /resources/certificates
   └─ FAQ               → /resources/faq
8. Contact              → /contact
```

### Step 3: Enable Mega Menu (Products)

Products menu requires 4-column mega menu layout:

**Dashboard → Appearance → Menus → Main Navigation → Products Item Settings**

- Check "Mega Menu" (if supported by theme)
- Ensure category parents (Insulation, Cooling, Racking, Accessories) have icons

### Step 4: Save Menu

Click **Save Menu**. Verify menu appears on homepage.

---

## Permalink & Rewrite Flush

### Step 1: Set Permalink Structure

**Dashboard → Settings → Permalinks**

Select: **Post name** (/%postname%/)

This enables:
```
/products/cold-storage-insulation/panels
/solutions/cold-storage/chilled
/industries/food-beverage
etc.
```

### Step 2: Flush Rewrite Rules

This must be done AFTER CPTs are registered (arcopan-child/inc/cpt.php).

**Option A - Dashboard:**
1. Dashboard → Settings → Permalinks
2. Click **Save Changes** (this flushes rules automatically)

**Option B - WP-CLI:**
```bash
wp rewrite flush
```

**Option C - Code:**
In `functions.php`:
```php
// Called on theme activation
register_activation_hook( __FILE__, function() {
    flush_rewrite_rules();
});
```

**Verify:** Visit `/products/` - should load correctly (not 404).

---

## WPML Language Setup

### Step 1: Activate WPML

**Plugins → WPML → Activate**

### Step 2: Configure Languages

**Dashboard → WPML → Languages → Add Languages**

Add:
```
English (default)
Turkish
French
German
Russian
```

### Step 3: Language Switcher

**Dashboard → Appearance → Widgets**

Add WPML Language Switcher widget to primary sidebar/header area.

### Step 4: Translate Core Pages

**Dashboard → WPML → Translation Management**

Create English versions of all 36+ pages first, then:
1. Select pages
2. Click "Create translations"
3. Assign to Turkish, French, German, Russian translators

---

## Email Configuration

### Step 1: Install WP Mail SMTP

**Plugins → WP Mail SMTP → Activate**

### Step 2: Configure SMTP

**Dashboard → WP Mail SMTP → Settings**

Choose provider (recommended: SendGrid / AWS SES):

**SendGrid Example:**
```
From Email: noreply@yourdomain.com
From Name: ARCOPAN

Mailer: SendGrid
API Key: [from SendGrid account]
```

**Test Email:**
Click "Send Test Email" to verify configuration.

### Step 3: Configure Gravity Forms Notifications

**Dashboard → Forms → Edit [RFQ Form]**

→ Settings → Notifications

```
To: sales@yourdomain.com
Subject: New RFQ Submission - {name}
From: noreply@yourdomain.com
```

---

## Verification Checklist

### Pre-Launch Verification

After completing all setup steps:

- [ ] WordPress dashboard accessible
- [ ] All plugins installed and activated
- [ ] ACF field groups imported (3 total)
- [ ] Child theme active and CSS loads
- [ ] Main navigation menu created (8 items)
- [ ] Permalinks set to /%postname%/ and flushed
- [ ] All 36+ URLs return 200 OK (check in Google Search Console)
- [ ] Homepage loads without PHP warnings (F12 console)
- [ ] Contact form visible on /contact
- [ ] RFQ form submission works (test with Gravity Forms preview)
- [ ] SMTP email test passes (WP Mail SMTP)
- [ ] WPML language switcher visible (if activated)
- [ ] Logo/branding loads correctly
- [ ] Mobile menu works (375px breakpoint)
- [ ] Lighthouse audit >90 on Core Web Vitals

### Post-Launch Monitoring

- [ ] Google Search Console setup + sitemap submitted
- [ ] Google Analytics 4 connected
- [ ] Uptime monitoring enabled (Pingdom / UptimeRobot)
- [ ] Daily backup automation configured
- [ ] Error log monitoring active
- [ ] WP Rocket cache stats tracked

---

## Troubleshooting

### Issue: "Fatal Error - plugins could not be activated"

**Solution:**
1. Check PHP version (must be 8.1+)
2. Check plugin compatibility with WordPress version
3. Enable DEBUG in wp-config.php to see error

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

Check `/wp-content/debug.log` for errors.

### Issue: "CPT pages return 404"

**Solution:**
1. Verify CPT registration in `inc/cpt.php`
2. Flush rewrite rules (Settings → Permalinks → Save)
3. Check post_type is published and public

```bash
wp post list --post_type=arcopan_product
```

### Issue: "ACF fields not showing on post edit screen"

**Solution:**
1. Verify ACF Pro is activated
2. Check ACF field group location rules (should match CPT)
3. Clear browser cache + WordPress cache (WP Rocket)

### Issue: "Menu shows blank or doesn't load"

**Solution:**
1. Verify menu created and assigned to "Primary Menu" location
2. Check theme supports menu locations (usually automatic for The7)
3. Try Theme Builder (The7 Elementor) for custom menu layout

---

## Support & Documentation

- **WordPress Codex:** https://developer.wordpress.org/
- **ACF Pro Docs:** https://www.advancedcustomfields.com/resources/
- **Gravity Forms Docs:** https://docs.gravityforms.com/
- **The7 Support:** https://the7.io/support/
- **WPML Docs:** https://wpml.org/documentation/

---

**Completed Installation?** Proceed to `04-rfq-configurator.md` for form setup.

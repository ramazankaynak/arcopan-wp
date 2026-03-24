# Schema Templates

These JSON-LD templates are loaded by inc/seo.php.

## Usage
- schema-organization.json — Output once in wp_head on all pages
- schema-product.json — Output on arcopan_product post type pages. Replace {{placeholders}} with PHP values before echoing.
- schema-breadcrumb.json — Output on all pages except homepage. {{breadcrumb_items}} replaced with PHP-generated ListItem array.

## Placeholder format
Each item in breadcrumb_items follows:
{"@type":"ListItem","position":1,"name":"Home","item":"https://arcopan.com"}
{"@type":"ListItem","position":2,"name":"Products","item":"https://arcopan.com/products"}
{"@type":"ListItem","position":3,"name":"Cold Storage Panels","item":"https://arcopan.com/products/cold-storage-insulation/panels"}

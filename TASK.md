# Task: Full rebuild of gornji-milanovac.com WordPress portal

Read SITE_CONTEXT.md first for all details.

## What to build:

### 1. Child Theme Redesign (wp-theme/astra-child/)
Create a modern Serbian city portal theme. Files needed:
- `style.css` - Complete redesign with CSS variables for:
  - Primary: #1B5E20 (deep forest green - municipality color)
  - Secondary: #F57F17 (warm gold)
  - Dark: #1A1A1A
  - Light: #F8F9FA
  - Modern typography, responsive grid, card components
- `functions.php` - Add:
  - Custom post type: `gm_blog` (editorial blog, separate from news)
  - Taxonomy: `gm_blog_category`
  - Widget areas: sidebar-news, sidebar-listings, footer-1, footer-2, footer-3
  - Custom nav menus: primary, mobile, footer
  - Remove the broken `update_focus_keywords` function (queries ALL posts on every page load - performance killer)
  - Serbian language helpers
  - Enqueue custom JS for slider
- `header.php` - Modern portal header with:
  - Logo + site name "Gornji Milanovac"
  - Top bar: date, weather widget placeholder, breaking news ticker
  - Main nav: Vesti | Blog | Oglasi | Poslovni Imenik | O Gradu | Kontakt
  - Mobile hamburger menu
- `footer.php` - 3-column footer with links, social, newsletter signup
- `front-page.php` - Homepage layout:
  - Hero: Breaking news slider (top 5 posts from 'Vesti' category)
  - 2-column grid: Latest news (auto from crawler) + Sidebar (weather, quick links)
  - Featured categories: Sport, Kultura, Ekonomija
  - Listings teaser (3 featured classified listings)
  - About the city section
- `archive-vesti.php` - News archive template (paginated grid)
- `archive-gm_blog.php` - Blog archive template
- `page-blog.php` - Blog page template (use gm_blog post type query)
- `inc/walker-nav.php` - Custom nav walker for dropdown menus
- `js/portal.js` - Slider, mobile menu, smooth scroll
- `screenshot.png` placeholder (just note it needs to be created)

### 2. Custom Plugin (wp-plugins/gm-portal/gm-portal.php)
Create plugin: `GM Portal - Core Functions`
```
Plugin Name: GM Portal
Description: Core functionality for Gornji Milanovac portal
Version: 1.0.0
```

Include:
- **Serbian Language Setup**: Set WPLANG to sr_RS, register strings for translation
- **Blog Page Setup**: Create `/blog` page mapped to gm_blog CPT archive on activation
- **Listings Categories**: On activation, create classified listing categories:
  - Nekretnine (Real Estate)
  - Zaposlenje (Jobs)
  - Usluge (Services)
  - Prodaja (For Sale)
  - Dogadjaji (Events)
  - Poslovni Imenik (Business Directory)
- **News Category Cleanup**: Create main category "Vesti iz Gornjeg Milanovca" (slug: vesti-gornji-milanovac), reorganize crawler categories under it
- **CRM Ready**: Register custom post type for leads (gm_lead) with fields: ime, email, telefon, grad, tip_upita (newsletter/oglas/kontakt)
- **WP Content Crawler keyword filter**: Hook into crawler posts to add tag "gornji-milanovac" automatically
- **Shortcodes**:
  - `[gm_breaking_news]` - Breaking news ticker
  - `[gm_weather]` - Weather widget (links to yr.no for Gornji Milanovac)
  - `[gm_latest_news count="6"]` - Latest news grid
  - `[gm_featured_listings count="3"]` - Featured classified listings
- **REST API endpoints**:
  - GET /wp-json/gm/v1/stats - Post counts, listing counts
  - POST /wp-json/gm/v1/lead - CRM lead capture

### 3. SQL Setup Script (wp-config/setup.sql)
MySQL script to run on server:
```sql
-- Set language
UPDATE y0H2MHc8_options SET option_value='sr_RS' WHERE option_name='WPLANG';

-- Update blog name/description to Serbian
UPDATE y0H2MHc8_options SET option_value='Gornji Milanovac - Digitalni Portal' WHERE option_name='blogname';
UPDATE y0H2MHc8_options SET option_value='Sve vesti, oglasi i informacije o Gornjem Milanovcu' WHERE option_name='blogdescription';

-- Create /blog page (for editorial content)
INSERT INTO y0H2MHc8_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_status, comment_status, ping_status, post_name, post_type, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, menu_order)
VALUES (1, NOW(), UTC_TIMESTAMP(), '', 'Blog', 'publish', 'open', 'open', 'blog', 'page', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 0)
ON DUPLICATE KEY UPDATE post_title='Blog';

-- Fix permalink structure to be cleaner
UPDATE y0H2MHc8_options SET option_value='/%postname%/' WHERE option_name='permalink_structure';

-- Activate classified-listing plugin
-- (done via PHP, not SQL)
```

### 4. Activation Script (wp-config/activate-plugins.php)
PHP script to run via WP-CLI or direct include:
```php
<?php
// Activate needed plugins
$plugins_to_activate = [
    'classified-listing/classified-listing.php',
    'fluent-crm/fluent-crm.php', // if available, else note to install
];
// Set language
update_option('WPLANG', 'sr_RS');
// Register blog page for gm_blog CPT
```

### 5. Documentation (docs/README.md)
Complete setup guide in Serbian:
- Šta je instalirano
- Kako dodati novi blog post (vs vesti)
- Kako dodati oglas
- Kako pristupiti CRM leadovima
- Kako podesiti WooCommerce prodavnicu
- Instrukcije za instalaciju FluentCRM

### 6. GitHub Actions (.github/workflows/deploy.yml)
Auto-deploy to server on push to main:
```yaml
# SSH to server and rsync theme + plugin files
# Server: 65.21.238.89
# Path: /var/www/vhosts/gornji-milanovac.com/httpdocs/wp-content/
```

## Important Notes:
- ALL user-facing text must be in SERBIAN (Latin script)
- Remove the `update_focus_keywords` function - it's a performance bomb
- The news at /vesti comes from WP Content Crawler (DO NOT break this)
- /blog is separate (editorial, manual posts using gm_blog CPT)
- Classified listings are at /oglasi (plugin already installed)
- WooCommerce is NOT installed yet - note it in docs as "to install"
- FluentCRM is NOT installed - note to install

## When done:
- All files committed to git
- Push to https://github.com/magnetoid/gornji-milanovac
- Run: openclaw system event --text "Done: Gornji Milanovac portal redesign complete - theme, plugin, SQL scripts, docs all pushed to GitHub" --mode now

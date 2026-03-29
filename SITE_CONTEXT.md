# Gornji Milanovac WordPress Site - Full Context

## Site Details
- URL: https://gornji-milanovac.com
- WordPress + Astra theme + Elementor Pro
- DB prefix: y0H2MHc8_
- DB: admin_wp_5dl9d / user: admin_wp_qkpa3 / pass: 4fsCmV9o$4iSrxh#
- WP path on server: /var/www/vhosts/gornji-milanovac.com/httpdocs
- WP Admin: magnetoid / quadratronic33101..

## Current State
- 5552 published posts (auto-imported via WP Content Crawler from 7 Serbian news sources)
- News sources: GM Info, Telegraf, Blic, GMPRESS, Glas Šumadije, Nova RS, OzonPress
- Theme: Astra (child theme exists: astra-child)
- Active plugins: fluent-smtp, astra-addon, classic-editor, elementor-pro, elementor, feedzy-rss-feeds, google-site-kit, wp-content-crawler, wp-rocket, nextgen-gallery

## Installed (inactive) plugins relevant to new features:
- classified-listing (listings/oglasi)
- buddypress (community)
- bbpress (forums)
- user-registration
- mailpoet
- wp-content-crawler (7 active sources for news)

## Existing Pages
- /home (frontpage) - "Dobrodošli u Gornji Milanovac" (ID: 5240)
- /vesti (ID: 23) - currently set as posts page (blog index) 
- /oglasi (ID: 123014) - listings page exists
- /listing-form (ID: 123015)
- /istorija-gornjeg-milanovca
- /vazni-telefoni, /zdravlje, /poslovi-gornji-milanovac
- /kontakt, /o-gradu, /o-nama

## What Needs to Be Done (owner's request):
1. **Serbian language** - set WPLANG to sr_RS, translate all UI
2. **Redesign** - modern portal design for city portal (using child theme CSS + functions.php)
3. **/vesti** - stays as auto-news from crawler (keyword filtered for Gornji Milanovac)
4. **/blog** - NEW page for editorial/authored content, separate from crawler news
5. **Listings** - activate Classified Listing plugin, add categories for: businesses, jobs, real estate, services, events
6. **Multivendor shop** - install & configure WooCommerce + plugin for vendors (Dokan if available, else WC Marketplace)
7. **CRM** - install FluentCRM, configure for leads, newsletter signups from the portal
8. **News auto-fetch** - WP Content Crawler already has 7 sources. Add keyword filter "Gornji Milanovac" + create proper category structure
9. **Push to GitHub** - all theme files, plugin configs, custom code

## Design Direction
- City portal / local news site aesthetic
- Colors: deep green (municipality colors) + white + warm gold accents
- Serbian language throughout
- Modern, clean, mobile-first
- Sections: Breaking news slider, Local news grid, Weather widget, Business directory teaser, Events, About city

## GitHub Repo
https://github.com/magnetoid/gornji-milanovac
Token: ghp_REDACTED

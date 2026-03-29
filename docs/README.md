# Gornji Milanovac - Digitalni Portal

## Dokumentacija

Ovo je kompletna dokumentacija za WordPress portal Gornji Milanovac.

---

## Sadržaj

1. [Pregled sistema](#pregled-sistema)
2. [Instalacija](#instalacija)
3. [Struktura sajta](#struktura-sajta)
4. [Upravljanje sadržajem](#upravljanje-sadržajem)
5. [Dodavanje blog članaka](#dodavanje-blog-članaka)
6. [Dodavanje oglasa](#dodavanje-oglasa)
7. [CRM i leadovi](#crm-i-leadovi)
8. [WP Content Crawler](#wp-content-crawler)
9. [Dodatni pluginovi](#dodatni-pluginovi)
10. [Tehnička podrška](#tehnička-podrška)

---

## Pregled sistema

### Šta je instalirano

| Komponenta | Opis |
|------------|------|
| **WordPress** | CMS platforma |
| **Astra Child Theme** | Custom tema za portal |
| **GM Portal Plugin** | Core funkcionalnost portala |
| **WP Content Crawler** | Automatsko preuzimanje vesti |
| **Classified Listing** | Sistem za oglase |
| **Elementor Pro** | Page builder |

### URL struktura

| URL | Opis |
|-----|------|
| `/` | Početna stranica |
| `/vesti/` | Arhiva vesti (automatski preuzete) |
| `/blog/` | Autorski blog članci |
| `/oglasi/` | Lista oglasa |
| `/listing-form/` | Forma za postavljanje oglasa |
| `/poslovni-imenik/` | Poslovni imenik |
| `/o-gradu/` | Informacije o gradu |
| `/kontakt/` | Kontakt stranica |

---

## Instalacija

### Korak 1: Upload fajlova

1. Kopirajte `wp-theme/astra-child/` u `/wp-content/themes/`
2. Kopirajte `wp-plugins/gm-portal/` u `/wp-content/plugins/`

### Korak 2: Pokrenite SQL skriptu

```bash
mysql -u admin_wp_qkpa3 -p admin_wp_5dl9d < wp-config/setup.sql
```

### Korak 3: Aktivirajte plugin i temu

Opcija A - Preko WP-CLI:
```bash
wp eval-file wp-config/activate-plugins.php
```

Opcija B - Preko WP Admin:
1. Idite na **Plugins → Installed Plugins**
2. Aktivirajte "GM Portal" i "Classified Listing"
3. Idite na **Appearance → Themes**
4. Aktivirajte "Astra Child - Gornji Milanovac Portal"

### Korak 4: Podesite permalinke

1. Idite na **Settings → Permalinks**
2. Izaberite "Post name" (`/%postname%/`)
3. Kliknite "Save Changes"

---

## Struktura sajta

### Custom Post Types

| Tip | Slug | Opis |
|-----|------|------|
| Posts | `post` | Standardni WordPress postovi (vesti) |
| GM Blog | `gm_blog` | Autorski blog članci |
| Listings | `rtcl_listing` | Oglasi |
| Leads | `gm_lead` | CRM leadovi |

### Kategorije

**Vesti kategorije:**
- Sport
- Kultura
- Ekonomija
- Društvo
- Hronika
- Politika

**Oglasi kategorije:**
- Nekretnine
- Zaposlenje
- Usluge
- Prodaja
- Događaji
- Poslovni Imenik

---

## Upravljanje sadržajem

### Razlika između Vesti i Bloga

| Vesti (`/vesti/`) | Blog (`/blog/`) |
|-------------------|-----------------|
| Automatski preuzete od izvora | Ručno napisani članci |
| Post type: `post` | Post type: `gm_blog` |
| Koristi WP Content Crawler | Koristi WP Editor |
| Prikazuje izvor | Prikazuje autora |

---

## Dodavanje blog članaka

1. Idite na **Blog → Dodaj novi** u WP Admin
2. Unesite naslov članka
3. Napišite sadržaj u editoru
4. Dodajte naslovnu sliku (Featured Image)
5. Izaberite kategoriju u "Blog kategorije"
6. Kliknite **Objavi**

### Saveti za pisanje

- Naslov: maksimalno 70 karaktera
- Uvodni paragraf: jasno objasni temu
- Koristite podnaslove (H2, H3) za struktuiranje
- Dodajte relevantne slike
- Završite sa pozivom na akciju

---

## Dodavanje oglasa

### Za administratore

1. Idite na **Listings → Add New Listing**
2. Popunite sve obavezne podatke
3. Dodajte slike
4. Izaberite kategoriju
5. Postavite cenu (ako je relevantno)
6. Kliknite **Publish**

### Za korisnike

1. Korisnici mogu dodati oglas na `/listing-form/`
2. Potrebna je registracija
3. Oglasi prolaze moderaciju pre objavljivanja

### Kategorije oglasa

- **Nekretnine** - kuće, stanovi, zemljište
- **Zaposlenje** - ponude za posao
- **Usluge** - razne usluge
- **Prodaja** - prodaja artikala
- **Događaji** - najave događaja
- **Poslovni Imenik** - firme i preduzeća

---

## CRM i leadovi

### Gde se čuvaju leadovi

Svi leadovi se čuvaju u **CRM Leadovi** meniju u WP Admin.

### Tipovi leadova

| Tip | Izvor |
|-----|-------|
| `newsletter` | Prijava na newsletter |
| `kontakt` | Kontakt forma |
| `oglas` | Registracija za oglase |

### Polja leadova

- **Email** - obavezno
- **Ime** - opcionalno
- **Telefon** - opcionalno
- **Grad** - default: Gornji Milanovac
- **Tip upita** - kategorija leada

### API za leadove

```
POST /wp-json/gm/v1/lead

Body:
{
  "email": "korisnik@email.com",
  "ime": "Ime Prezime",
  "telefon": "060123456",
  "tip_upita": "newsletter"
}
```

---

## WP Content Crawler

### Aktivni izvori vesti

Crawler automatski preuzima vesti sa sledećih izvora:

1. GM Info
2. Telegraf
3. Blic
4. GMPRESS
5. Glas Šumadije
6. Nova RS
7. OzonPress

### Podešavanja

- Crawler se pokreće automatski (cron)
- Filter: vesti koje sadrže "Gornji Milanovac"
- Automatski dodaje tag: `gornji-milanovac`

### Provera statusa

1. Idite na **WP Content Crawler → Dashboard**
2. Proverite poslednje preuzimanje za svaki izvor
3. Ako ima grešaka, proverite logove

---

## Dodatni pluginovi

### Za instalaciju

#### FluentCRM (preporučeno)

Za napredne CRM funkcije i email marketing.

1. Idite na **Plugins → Add New**
2. Pretražite "FluentCRM"
3. Instalirajte i aktivirajte
4. Podesite SMTP postavke

#### WooCommerce (opcionalno)

Za e-commerce funkcionalnost.

1. Idite na **Plugins → Add New**
2. Pretražite "WooCommerce"
3. Instalirajte i aktivirajte
4. Pokrenite setup wizard

Za multivendor funkcionalnost (prodavci):
- Instalirajte "Dokan" ili "WC Vendors"

---

## Shortcodes

Koristite ove shortcode-ove na stranicama:

```
[gm_breaking_news]
- Prikazuje traku sa najnovijim vestima

[gm_weather]
- Prikazuje widget za vreme (link na yr.no)

[gm_latest_news count="6"]
- Prikazuje grid sa najnovijim vestima
- count: broj vesti (default: 6)

[gm_featured_listings count="3"]
- Prikazuje istaknute oglase
- count: broj oglasa (default: 3)
```

---

## API Endpointi

### GET /wp-json/gm/v1/stats

Vraća statistiku sajta:

```json
{
  "success": true,
  "data": {
    "posts": { "total": 5552, "draft": 10 },
    "blog": { "total": 15 },
    "listings": { "total": 42 },
    "leads": { "total": 128 }
  }
}
```

### POST /wp-json/gm/v1/lead

Kreira novi lead (vidi CRM sekciju).

---

## Tehnička podrška

### Server info

- **Server:** 65.21.238.89
- **Path:** /var/www/vhosts/gornji-milanovac.com/httpdocs/
- **DB Prefix:** y0H2MHc8_

### Kontakt

Za tehničku podršku kontaktirajte administratora sajta.

### Git Repository

https://github.com/magnetoid/gornji-milanovac

---

## Changelog

### v2.0.0 (2024)

- Kompletan redizajn portala
- Nova child tema sa modernim dizajnom
- GM Portal plugin sa CRM funkcijama
- Razdvojen Blog od Vesti
- REST API za statistiku i leadove
- Shortcodes za dinamičan sadržaj
- Poboljšane performanse (uklonjen broken update_focus_keywords)

---

*Dokumentacija kreirana za Gornji Milanovac - Digitalni Portal*

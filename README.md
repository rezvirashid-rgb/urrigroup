# Urri Group — Website (Phase 1)

A modern, lightweight, fully responsive corporate website for **Urri Group**, a diversified Bangladeshi business group.

> **One Group. Every Direction Forward.**

Phase 1 focuses on a strong corporate presentation, fast performance, mobile
responsiveness, basic SEO and a single piece of backend functionality: a
**contact form** that stores submissions in a database and emails a
notification. No CMS, admin panel, login or user accounts — by design.

---

## Tech stack

| Layer     | Technology                          |
|-----------|-------------------------------------|
| Markup    | HTML5 (semantic)                    |
| Styling   | CSS3 (single stylesheet, no framework) |
| Behaviour | Vanilla JavaScript (no dependencies)|
| Backend   | PHP 7.4+ / 8.x (contact form only)  |
| Database  | MySQL 5.7+ / MariaDB 10.3+          |
| Fonts     | Poppins + Anton (Google Fonts)      |

Works on standard **shared / cPanel hosting**. No build step required.

---

## Project structure

```
urrigroup/
├── index.html              # Homepage
├── about.html              # About the Group
├── companies.html          # All 20 companies, grouped by pillar
├── contact.html            # Contact info + contact form
├── 404.html                # Friendly error page
│
├── companies/              # Individual pages for the 5 launch businesses
│   ├── urri-travels.html
│   ├── urri-international.html
│   ├── urri-sourcing.html
│   ├── urri-consultancy.html
│   └── urri-media.html
│
├── assets/
│   ├── css/style.css       # Full design system
│   ├── js/config.js        # ← Edit social links & public contact info here
│   ├── js/main.js          # Nav, scroll reveal, form handling
│   ├── images/og-image.png # Social share image (1200×630)
│   └── icons/favicon.svg
│
├── forms/
│   └── contact.php         # Contact form processor (DB + email + security)
│
├── config/
│   └── config.example.php  # ← Copy to config.php and fill in credentials
│
├── database/
│   └── database.sql        # Database schema (import once)
│
├── robots.txt
├── sitemap.xml
├── .htaccess               # HTTPS, caching, security, 404 (Apache)
├── .gitignore
├── README.md
└── INSTALLATION.md         # Step-by-step deployment guide
```

---

## Quick start (local or hosting)

1. Upload all files to your web root (e.g. `public_html`).
2. Create a MySQL/MariaDB database and import `database/database.sql`.
3. Copy `config/config.example.php` → `config/config.php` and fill in your
   database credentials, admin email and site URL.
4. (Optional) Edit `assets/js/config.js` to add your real social-media URLs.
5. Visit your domain — the site is live.

See **[INSTALLATION.md](INSTALLATION.md)** for the detailed walkthrough.

---

## Where to edit common things

| I want to change…              | Edit this file                              |
|--------------------------------|---------------------------------------------|
| Social media links             | `assets/js/config.js` (and `config/config.php`) |
| Public email / phone shown     | `assets/js/config.js`                       |
| Where form emails are sent     | `config/config.php` → `ADMIN_EMAIL`         |
| Database credentials           | `config/config.php`                         |
| Colours / typography           | `assets/css/style.css` (`:root` variables)  |
| Company list / statuses        | `companies.html`                            |

> **Note:** `config/config.php` is intentionally **not** included and is
> git-ignored. It must be created on the server from the example file.

---

## The contact form

The only backend feature in Phase 1. When a visitor submits the form:

1. Fields are validated **client-side** (instant feedback) and again
   **server-side** (authoritative).
2. The submission is stored in the `contact_submissions` table using a
   **prepared statement** (no SQL injection).
3. An **email notification** is sent to `ADMIN_EMAIL`.
4. The visitor sees a success message.

**Security measures built in:**

- Prepared/parameterised SQL statements (PDO)
- Server-side validation + input sanitisation
- CSRF protection (session token, with same-origin fallback for no-JS)
- Honeypot anti-spam field
- Rate limiting / submission throttling
- Email format validation & header-injection guards
- Database errors are logged server-side, never shown to visitors
- No passwords are stored (there is no login in Phase 1)

Submissions can be viewed directly in the database (e.g. via phpMyAdmin) —
no admin interface is required for Phase 1.

---

## Accessibility & SEO

- Semantic HTML, proper heading hierarchy, form labels, alt/aria text
- Keyboard navigation, visible focus states, reduced-motion support
- Per-page titles, meta descriptions, canonical URLs, Open Graph tags
- `sitemap.xml`, `robots.txt`, favicon, JSON-LD organisation data

---

## Built for Phase 2

The frontend is structured cleanly so a CMS/admin backend can be added later
(admin login, company management, blog/news, careers, newsletter, multiple
languages, analytics) **without rebuilding the frontend**. None of that is
implemented now — Phase 1 stays intentionally simple.

---

© Urri Group. All rights reserved.

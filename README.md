# Urri Group — Website (Phase 1)

A modern, lightweight, fully responsive corporate website for **Urri Group**, a diversified Bangladeshi business group.

> **One Group. Every Direction Forward.**

Phase 1 is a fast, accessible, SEO-ready corporate site with one piece of
backend functionality: a **contact form** that stores submissions in MySQL and
emails a notification. No CMS, admin panel, login or user accounts — by design.

---

## The short version

- **To put the site online:** upload the contents of **`public/`** to your
  hosting and follow **[INSTALLATION.md](INSTALLATION.md)**.
- **To change content:** edit files in **`src/`**, then run
  `php build/build.php` to regenerate `public/`.

---

## Project layout

```
urrigroup/
├── public/                 ← THE WEBSITE. Upload this folder's contents. (generated)
│
├── src/                    ← Edit here
│   ├── data/
│   │   ├── site.php        # name, email, phone, URL, navigation
│   │   └── companies.php   # all 20 companies, pillars, statuses, detail pages
│   ├── pages/              # page templates (home, about, companies, contact, 404, company)
│   ├── partials/           # shared layout (head, header, footer) + icons
│   ├── assets/             # CSS + JS sources (minified into public/ on build)
│   └── static/             # copied into public/ as-is: form backend, config
│                           #   example, .htaccess, fonts, icons, images
│
├── build/
│   ├── build.php           # generator — `php build/build.php`
│   └── make-images.js      # regenerates icons + share image (only if the logo changes)
│
├── database/database.sql   # import once into MySQL/MariaDB
├── README.md
└── INSTALLATION.md
```

`public/` is committed, so the site can be uploaded without running anything.
`database/` and the docs live **outside** `public/`, so they are never
downloadable from the live site.

---

## Where to change common things

| I want to change…                     | Edit                                   | Then |
|---------------------------------------|----------------------------------------|------|
| A company's status, description, services | `src/data/companies.php`           | rebuild |
| Email / phone / site URL              | `src/data/site.php`                    | rebuild |
| Page wording                          | `src/pages/*.php`                      | rebuild |
| Header / footer / navigation          | `src/partials/layout.php`, `src/data/site.php` | rebuild |
| Colours / typography                  | `src/assets/css/style.css` (`:root`)   | rebuild |
| Social media links                    | `public/assets/js/config.js` (also `src/static/assets/js/config.js`) | no rebuild needed |
| Database / email settings             | `config/config.php` **on the server**  | — |

Company counts on the homepage and About page ("20 businesses", "4 launching
now", …) are calculated from `companies.php`, so they can't drift out of date.

**Rebuild:** `php build/build.php` (PHP 7.4+, no other tools). It rewrites
`public/` but keeps a local `public/config/config.php` if you have one.

---

## The contact form

1. Fields are validated in the browser for instant feedback, then again on the
   server (authoritative).
2. The message is stored in `contact_submissions` with a prepared statement.
3. A notification email goes to `ADMIN_EMAIL`, via PHP `mail()` or, for better
   delivery, your own mailbox over **SMTP** (set `MAIL_TRANSPORT` to `'smtp'`).
4. The visitor sees a success message.

**Protections built in**

- Prepared SQL statements (no SQL injection); output escaping (no XSS)
- CSRF token, with a same-origin check for visitors without JavaScript
- Honeypot field and a minimum fill time (instant bot submissions are ignored)
- Rate limits per visitor **and per IP address** (stored in the database, so
  cookie-less bots can't bypass them); the IP comes from the real connection
  and can't be spoofed with headers
- Email headers are encoded and sanitised: Bangla names and subjects arrive
  intact, and nothing typed in the form can add recipients
- Hardened session cookie (HttpOnly, SameSite, Secure on HTTPS)
- Internal errors are logged, never shown to visitors; no passwords stored

View submissions in phpMyAdmin (`contact_submissions` table). No admin
interface is needed for Phase 1.

---

## Performance, SEO and accessibility

- Fonts (Poppins, Anton) are **self-hosted** and preloaded: no third-party
  requests, faster first paint on mobile networks
- Minified CSS/JS with cache-busting versions; long browser caching
- Per-page titles, descriptions, canonical URLs, Open Graph/Twitter cards,
  JSON-LD organisation data, `sitemap.xml` (with dates), `robots.txt`
- Favicon set: `.ico`, SVG, Apple touch icon, Android/manifest icons
- Semantic HTML, labelled fields with linked error messages, keyboard-safe
  mobile menu, visible focus, WCAG-AA text colours, reduced-motion support

---

## Built for Phase 2

Content already lives in data files and templates, so a CMS or admin backend
(company management, news, careers, multiple languages) can later replace
`src/data/*.php` without redesigning the frontend.

---

© Urri Group. All rights reserved.

# Installation Guide — Urri Group Website

This guide puts the website live on standard **shared / cPanel hosting**
(Spaceship, Namecheap and most Bangladeshi hosts).

```
Upload public/ → Create MySQL database → Import database.sql →
Create config.php → Configure email → Open urrigroup.com → Live
```

About 10–15 minutes. No coding required.

---

## Before you start

You need:

- Access to your hosting control panel (cPanel or similar).
- Your domain (`urrigroup.com`) pointed at the hosting account
  (see *Connecting the domain* below).
- This project. On GitHub: **Code → Download ZIP**, then unzip it on your computer.

---

## Step 1 — Upload the website

Upload **the contents of the `public/` folder**, not the whole project.

**Option A · File Manager (easiest)**

1. On your computer, open the `public` folder, select everything inside it
   and compress it into a `.zip`.
   *(Make sure hidden files are included: the `.htaccess` file matters.)*
2. In your hosting control panel open **File Manager** → `public_html`.
3. Upload the zip, then **Extract** it there.
4. Check that `public_html/index.html` exists (not
   `public_html/public/index.html`).

**Option B · FTP** — connect with FileZilla and upload the contents of
`public/` into `public_html`. Enable "show hidden files" so `.htaccess` goes too.

You should now see `index.html`, `about.html`, `favicon.ico`, `.htaccess`
and the `assets/`, `companies/`, `forms/` and `config/` folders in `public_html`.

---

## Step 2 — Create a MySQL / MariaDB database

In the control panel open **MySQL® Databases** (or **MySQL Database Wizard**):

1. Create a database, e.g. `urrigrp_main`.
2. Create a database user, e.g. `urrigrp_user`, with a strong password. Save it.
3. Add the user to the database with **ALL PRIVILEGES**.

Note the database host (usually `localhost`), name, user and password.

---

## Step 3 — Import the database table

1. Open **phpMyAdmin** and select your new database.
2. **Import** → choose `database/database.sql` from the project
   (on your computer; this file is not uploaded to the website).
3. Click **Go**. A `contact_submissions` table appears.

---

## Step 4 — Create config.php

1. In File Manager, open `public_html/config/`.
2. Copy `config.example.php` and name the copy **`config.php`**.
3. Edit `config.php` and enter the database details from Step 2:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'urrigrp_main');
define('DB_USER', 'urrigrp_user');
define('DB_PASS', 'your-strong-password');
define('SITE_URL', 'https://urrigroup.com');
```

`config.php` is blocked from web access by `.htaccess` and is never
committed to Git.

---

## Step 5 — Configure email

In the control panel, create a mailbox on your domain to send from, e.g.
**`no-reply@urrigroup.com`** (Email Accounts). Then in `config.php`:

```php
define('ADMIN_EMAIL', 'admin@urrigroup.com');     // where messages arrive
define('MAIL_FROM',   'no-reply@urrigroup.com');  // the mailbox you created
```

**Recommended — send through that mailbox (SMTP).** Messages are far less
likely to land in spam. Find the "outgoing server" details under your
mailbox's *Connect Devices* / *Mail Client Settings*:

```php
define('MAIL_TRANSPORT', 'smtp');
define('SMTP_HOST',   'mail.urrigroup.com');   // outgoing server shown by your host
define('SMTP_PORT',   465);                    // 465 with 'ssl'  (or 587 with 'tls')
define('SMTP_SECURE', 'ssl');
define('SMTP_USER',   'no-reply@urrigroup.com');
define('SMTP_PASS',   'the mailbox password');
```

Or keep `define('MAIL_TRANSPORT', 'mail');` to use the server's built-in mail.

Either way, every message is **saved to the database first**, so nothing is
lost if an email fails.

---

## Step 6 — (Optional) Add your social links

Edit `public_html/assets/js/config.js` and replace the `#` placeholders:

```js
social: {
  facebook:  "https://facebook.com/yourpage",
  instagram: "https://instagram.com/yourpage",
  linkedin:  "https://linkedin.com/company/yourpage"
}
```

Also copy the change into `src/static/assets/js/config.js` in the project, so
the next rebuild keeps it.

---

## Step 7 — Open your website

Visit **https://urrigroup.com**. The site is live.

---

## Post-launch checklist

- [ ] Home, About, Our Companies and Contact load; the five company pages open.
- [ ] The mobile menu works on a phone.
- [ ] On Our Companies, an **Inquire** button opens Contact with the business
      area already selected.
- [ ] Send a test message → success message appears.
- [ ] The message appears in `contact_submissions` (phpMyAdmin).
- [ ] The notification arrives at `admin@urrigroup.com` (check spam the first time).
- [ ] `/sitemap.xml` and `/robots.txt` load; a made-up URL shows the 404 page.
- [ ] `https://urrigroup.com/config/config.php` shows **not found**
      (proof your credentials are protected).

Testing tip: the form deliberately limits each connection to one message every
30 seconds and 5 per hour (adjustable in `config.php`). Wait between test
messages.

---

## Connecting the domain (Namecheap → Spaceship hosting)

- **Easiest:** in Namecheap → Domain List → *Manage* → **Nameservers** →
  *Custom DNS*, enter the nameservers shown in your Spaceship hosting dashboard.
- **Or:** in Namecheap → **Advanced DNS**, add an **A record** for `@` pointing
  to your hosting IP, and a **CNAME** for `www` pointing to `urrigroup.com`.

DNS changes take from a few minutes to a few hours. Once the domain works,
enable the free SSL certificate in your hosting panel (often "SSL/TLS
Status" or "AutoSSL"); the site then redirects to `https://` automatically.

---

## Troubleshooting

**Form says "not fully configured yet."** → `config/config.php` doesn't
exist yet (Step 4).

**Form says it can't save the message.** → Database details in `config.php`
are wrong, or `database.sql` wasn't imported (Steps 2–4).

**Message saved but no email.** → With `mail`, try SMTP (Step 5). With
SMTP, double-check host, port/secure pair (465+`ssl` or 587+`tls`), and the
mailbox password. Errors are written to the server's PHP error log.

**"Sending messages too quickly."** → The anti-spam limit is working. Wait
30 seconds (or adjust `RATE_LIMIT_SECONDS` / `RATE_LIMIT_PER_HOUR`).

**500 error after upload.** → Rare with this `.htaccess`, but you can rename
it temporarily to test, then ask your host which Apache module is disabled.

**Pages look unstyled.** → The `assets/` folder didn't upload fully, or files
aren't directly inside `public_html`.

---

## Making changes later

Edit files in `src/`, run `php build/build.php`, and upload the changed files
from `public/`. See [README.md](README.md) for which file controls what.

© Urri Group. All rights reserved.

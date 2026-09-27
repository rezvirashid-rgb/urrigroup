# Installation Guide — Urri Group Website

This guide deploys the website on standard **shared / cPanel hosting**
(the setup used by Spaceship, Namecheap, and most Bangladeshi hosts).

```
Upload files → Create MySQL database → Import database.sql →
Configure database credentials → Configure email → Open urrigroup.com → Live
```

Total time: about 10–15 minutes. No coding required.

---

## Before you start

You will need:

- Access to your hosting control panel (cPanel or similar).
- Your domain (`urrigroup.com`) pointed at the hosting account.
- The website files from this project.

---

## Step 1 — Upload the files

**Option A · cPanel File Manager (easiest)**

1. Log in to your hosting control panel.
2. Open **File Manager** → go to `public_html`.
3. Upload the project as a `.zip`, then **Extract** it.
4. Make sure the files sit **directly inside `public_html`**, i.e.
   `public_html/index.html` — not `public_html/urrigroup/index.html`.

**Option B · FTP**

Connect with FileZilla using the FTP details from your host and upload the
whole folder's contents into `public_html`.

> After uploading you should see `index.html`, `about.html`, the `assets/`,
> `companies/`, `forms/`, `config/`, and `database/` folders in `public_html`.

---

## Step 2 — Create a MySQL / MariaDB database

In cPanel, open **MySQL® Databases** (or **MySQL Database Wizard**):

1. **Create a database**, e.g. `urrigrp_main`.
2. **Create a database user**, e.g. `urrigrp_user`, with a strong password.
   *(Save this password — you'll need it in Step 4.)*
3. **Add the user to the database** and grant **ALL PRIVILEGES**.

Write down these four values:

- Database host — usually `localhost`
- Database name — e.g. `urrigrp_main`
- Database user — e.g. `urrigrp_user`
- Database password — the one you just created

---

## Step 3 — Import the database schema

1. In cPanel, open **phpMyAdmin**.
2. Select your new database on the left.
3. Click the **Import** tab.
4. Choose the file `database/database.sql` from this project.
5. Click **Go / Import**.

You should now see a `contact_submissions` table. That's all that's needed.

---

## Step 4 — Configure database credentials

1. In **File Manager**, go to the `config/` folder.
2. Copy `config.example.php` and rename the copy to **`config.php`**.
   *(In File Manager: right-click → Copy, then rename.)*
3. Edit `config.php` and fill in the values from Step 2:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'urrigrp_main');
define('DB_USER', 'urrigrp_user');
define('DB_PASS', 'your-strong-password');
```

Save the file.

> **Important:** keep the file named `config.php`. It is protected from direct
> web access by `.htaccess`, and it is never committed to source control.

---

## Step 5 — Configure email

Still in `config.php`, set where contact-form notifications go and the
address they are sent *from*:

```php
define('ADMIN_EMAIL', 'admin@urrigroup.com');
define('MAIL_FROM',   'no-reply@urrigroup.com');   // use YOUR domain
define('SITE_URL',    'https://urrigroup.com');
```

> **Deliverability tip:** the `MAIL_FROM` address should be an address on your
> own domain (create `no-reply@urrigroup.com` in cPanel → Email Accounts).
> Sending "from" a Gmail/Yahoo address on shared hosting often lands in spam.
> If email is unreliable on your host, ask them to enable mail() or consider
> SMTP in Phase 2 — form submissions are always saved to the database
> regardless of email delivery.

Save the file.

---

## Step 6 — (Optional) Add your social links

Open `assets/js/config.js` and replace the `#` placeholders with your real
profile URLs:

```js
social: {
  facebook:  "https://facebook.com/yourpage",
  instagram: "https://instagram.com/yourpage",
  linkedin:  "https://linkedin.com/company/yourpage"
}
```

Leave any you don't have yet as `"#"`. Do the same in `config/config.php`
if you want the server-side copy to match.

---

## Step 7 — Open your website

Visit **https://urrigroup.com** — the website is live.

Test the checklist below.

---

## Post-launch checklist

- [ ] Home, About, Our Companies and Contact pages all load.
- [ ] The five company pages open from the Companies page.
- [ ] The mobile menu works on a phone.
- [ ] Submit the contact form with a real message.
- [ ] The success message appears.
- [ ] The row appears in `contact_submissions` (phpMyAdmin).
- [ ] The notification email arrives at `admin@urrigroup.com`
      (check spam the first time).
- [ ] `https://urrigroup.com/sitemap.xml` and `/robots.txt` load.
- [ ] Visiting a made-up URL shows the 404 page.

---

## Connecting the domain (Namecheap → hosting)

If your domain is at **Namecheap** and hosting is elsewhere (e.g. Spaceship):

- **Easiest:** in Namecheap, set the domain's **nameservers** to the ones your
  host gives you (Domain → Nameservers → Custom DNS). Your host's dashboard
  shows the exact nameservers.
- **Or (DNS records):** in Namecheap → **Advanced DNS**, add an **A record**
  (`@`) pointing to your hosting IP, and a **CNAME** (`www`) pointing to your
  domain. Your host provides the IP.

DNS changes can take a few minutes to a few hours to take effect.

---

## Troubleshooting

**The form says "not fully configured yet."**
→ You have not created `config/config.php` from `config.example.php` (Step 4).

**The form says it can't save the message.**
→ Database credentials in `config.php` are wrong, or `database.sql` wasn't
imported. Re-check Steps 2–4.

**No email arrives, but the message is saved.**
→ The database part works; email is a hosting/deliverability issue. Confirm
`ADMIN_EMAIL`, use a `MAIL_FROM` on your own domain, and check spam. Contact
your host to confirm PHP `mail()` is enabled.

**A 500 error appears after upload.**
→ Usually an `.htaccess` module your host disabled. The file is written
defensively, but if needed you can temporarily rename `.htaccess` to test.

**Pages load but look unstyled.**
→ The `assets/` folder didn't upload fully, or files aren't directly in
`public_html`. Re-check Step 1.

---

© Urri Group. All rights reserved.

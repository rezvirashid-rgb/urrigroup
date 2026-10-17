<?php
/**
 * Shared page layout: <head>, header, footer.
 * Available: $SITE, $NAV, $PILLARS, $page, $content, $r (path back to site root), $V (asset version)
 */
$canonical = $SITE['url'] . '/' . $page['path'];
$ogImage   = $SITE['url'] . $SITE['og_image'];
$ogTitle   = $page['og_title'] ?? $page['title'];
$current   = $page['nav'] ?? '';
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <script>document.documentElement.className+=" js";</script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= e($page['title']) ?></title>
  <meta name="description" content="<?= e($page['description']) ?>" />
<?php if (!empty($page['noindex'])): ?>
  <meta name="robots" content="noindex, follow" />
<?php else: ?>
  <link rel="canonical" href="<?= e($canonical) ?>" />
<?php endif; ?>
  <meta name="theme-color" content="<?= e($SITE['theme_color']) ?>" />

  <meta property="og:type" content="website" />
  <meta property="og:site_name" content="<?= e($SITE['name']) ?>" />
  <meta property="og:title" content="<?= e($ogTitle) ?>" />
  <meta property="og:description" content="<?= e($page['description']) ?>" />
  <meta property="og:url" content="<?= e($canonical) ?>" />
  <meta property="og:image" content="<?= e($ogImage) ?>" />
  <meta property="og:image:width" content="1200" />
  <meta property="og:image:height" content="630" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="<?= e($ogTitle) ?>" />
  <meta name="twitter:description" content="<?= e($page['description']) ?>" />
  <meta name="twitter:image" content="<?= e($ogImage) ?>" />

  <link rel="icon" href="<?= $r ?>favicon.ico" sizes="32x32" />
  <link rel="icon" href="<?= $r ?>assets/icons/favicon.svg" type="image/svg+xml" />
  <link rel="apple-touch-icon" href="<?= $r ?>assets/icons/apple-touch-icon.png" />
  <link rel="manifest" href="<?= $r ?>site.webmanifest" />

  <link rel="preload" href="<?= $r ?>assets/fonts/anton-400.woff2" as="font" type="font/woff2" crossorigin />
  <link rel="preload" href="<?= $r ?>assets/fonts/poppins-400.woff2" as="font" type="font/woff2" crossorigin />
  <link rel="stylesheet" href="<?= $r ?>assets/css/style.css?v=<?= $V ?>" />
<?php if (!empty($page['jsonld'])): ?>
  <script type="application/ld+json"><?= json_encode($page['jsonld'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?></script>
<?php endif; ?>
</head>
<body>
  <a class="skip-link" href="#main">Skip to content</a>

  <header class="site-header">
    <div class="container header-inner">
      <a class="brand" href="<?= href('', $r) ?>" aria-label="Urri Group home">
        <?= icon('logo') ?>
        <span class="brand-text">URRI<span>GROUP</span></span>
      </a>

      <button class="nav-toggle" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="primary-nav">
        <?= icon('menu') ?><?= icon('close') ?>
      </button>

      <nav class="nav" id="primary-nav" aria-label="Primary">
<?php foreach ($NAV as $key => [$label, $path]):
        $isPage   = ($page['path'] === $path);
        $isActive = ($current === $key); ?>
        <a class="nav-link<?= $isActive ? ' is-active' : '' ?>" href="<?= href($path, $r) ?>"<?= $isPage ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
<?php endforeach; ?>
        <a class="btn btn-primary nav-cta" href="<?= href('contact.html', $r) ?>">Contact Us</a>
      </nav>

      <div class="header-cta">
        <a class="btn btn-primary" href="<?= href('contact.html', $r) ?>">Contact Us</a>
      </div>
    </div>
  </header>

  <main id="main">
<?= $content ?>
  </main>

  <footer class="site-footer">
    <div class="container">
      <div class="footer-grid">
        <div class="footer-brand">
          <span class="brand-text">URRI<span>GROUP</span></span>
          <p><?= e($SITE['short_about']) ?></p>
<?= social_links('socials') ?>
        </div>

        <div>
          <h2 class="footer-h">Navigation</h2>
          <ul class="footer-links">
<?php foreach ($NAV as [$label, $path]): ?>
            <li><a href="<?= href($path, $r) ?>"><?= e($label) ?></a></li>
<?php endforeach; ?>
          </ul>
        </div>

        <div>
          <h2 class="footer-h">Business Areas</h2>
          <ul class="footer-links">
<?php foreach ($PILLARS as $pkey => $p): ?>
            <li><a href="<?= href('companies.html', $r) ?>#<?= $pkey ?>"><?= e($p['short']) ?></a></li>
<?php endforeach; ?>
          </ul>
        </div>

        <div class="footer-contact">
          <h2 class="footer-h">Contact</h2>
          <ul class="footer-links">
            <li><a data-config="email" href="mailto:<?= e($SITE['email']) ?>"><?= e($SITE['email']) ?></a></li>
            <li><a data-config="phone" href="tel:<?= e($SITE['phone']) ?>"><?= e($SITE['phone']) ?></a></li>
            <li><a data-config="website" href="<?= e($SITE['url']) ?>"><?= e(preg_replace('#^https?://#', '', $SITE['url'])) ?></a></li>
          </ul>
        </div>
      </div>

      <div class="footer-bottom">
        <div>&copy; <span data-year><?= date('Y') ?></span> Urri Group. All rights reserved.</div>
        <div><?= e($SITE['tagline']) ?></div>
      </div>
    </div>
  </footer>

  <script src="<?= $r ?>assets/js/config.js?v=<?= $V ?>" defer></script>
  <script src="<?= $r ?>assets/js/main.js?v=<?= $V ?>" defer></script>
</body>
</html>

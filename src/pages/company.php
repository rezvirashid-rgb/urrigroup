<?php
/** Detail page template — rendered once per company that has a 'detail' block. */
$d = $c['detail'];
$p = $PILLARS[$c['pillar']];
$page = [
    'title'       => $d['meta_title'] . ' | Urri Group',
    'og_title'    => $c['name'] . ' — ' . $d['tagline'],
    'description' => $d['lead'],
    'nav'         => 'companies',
    'path'        => 'companies/' . $c['slug'] . '.html',
];
$inquire = inquire_url($c, $r);
?>
    <section class="page-hero">
      <div class="container">
        <div class="page-hero-inner">
          <nav class="breadcrumb" aria-label="Breadcrumb"><a href="../">Home</a><span aria-hidden="true">/</span><a href="../companies.html">Our Companies</a><span aria-hidden="true">/</span><?= e($c['name']) ?></nav>
          <div class="detail-head">
            <?= status_badge($c['status']) ?>
            <span class="eyebrow on-dark"><?= e($p['name']) ?></span>
          </div>
          <h1><?= e($c['name']) ?></h1>
          <p><?= e($d['tagline']) ?></p>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="container company-body">
        <div class="reveal">
          <span class="eyebrow">What We Do</span>
          <h2><?= e($d['heading']) ?></h2>
          <p class="lead"><?= e($d['lead']) ?></p>
          <p><?= e($d['body']) ?></p>

          <h3 class="services-h">Services</h3>
          <ul class="service-list">
<?php foreach ($d['services'] as $s): ?>
            <li><?= e($s) ?></li>
<?php endforeach; ?>
          </ul>
        </div>

        <aside class="reveal" aria-label="Business snapshot">
          <div class="aside-card">
            <h2 class="aside-h">Business Snapshot</h2>
            <dl>
              <div><dt>Status</dt><dd><?= e($STATUSES[$c['status']]['label']) ?></dd></div>
              <div><dt>Pillar</dt><dd><?= e($p['name']) ?></dd></div>
              <div><dt>Focus</dt><dd><?= e($d['focus']) ?></dd></div>
            </dl>
            <a class="btn btn-primary btn-block" href="<?= $inquire ?>">Make an Inquiry <span class="arrow" aria-hidden="true">&rarr;</span></a>
            <p class="aside-note">Prefer to talk? Email <a data-config="email" data-fill="false" href="mailto:<?= e($SITE['email']) ?>"><?= e($SITE['email']) ?></a> or call <a data-config="phone" data-fill="false" href="tel:<?= e($SITE['phone']) ?>"><?= e($SITE['phone']) ?></a>.</p>
          </div>
        </aside>
      </div>
    </section>

    <section class="section">
      <div class="container">
        <div class="cta-band reveal">
          <h2><?= e($d['cta_h']) ?></h2>
          <p><?= e($d['cta_p']) ?></p>
          <div class="cta-actions">
            <a class="btn btn-coral btn-lg" href="<?= $inquire ?>">Contact <?= e($c['name']) ?> <span class="arrow" aria-hidden="true">&rarr;</span></a>
            <a class="btn btn-ghost-light btn-lg" href="../companies.html">Back to Companies</a>
          </div>
        </div>
      </div>
    </section>

<?php
$n = roster_counts();
$page = [
    'title'       => 'Our Companies — The Urri Group Family of Businesses',
    'description' => 'Explore the ' . $n['total'] . ' businesses of Urri Group across five pillars — Mobility & People, Trade & Industry, Infrastructure & Property, Financial & Professional Services, and Technology, Brand & Care.',
    'nav'         => 'companies',
];
$numberWords = [20 => 'Twenty'];
$totalWord   = $numberWords[$n['total']] ?? (string) $n['total'];
?>
    <section class="page-hero">
      <div class="container">
        <div class="page-hero-inner">
          <nav class="breadcrumb" aria-label="Breadcrumb"><a href="./">Home</a><span aria-hidden="true">/</span>Our Companies</nav>
          <span class="eyebrow on-dark">The Urri Family</span>
          <h1><?= e($totalWord) ?> businesses. Five pillars. One direction.</h1>
          <p>From established travel services to businesses launching now and on the horizon — here is the growing Urri Group family.</p>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="container">
        <nav class="filters" aria-label="Jump to business pillar">
<?php foreach ($PILLARS as $key => $p): ?>
          <a class="filter-btn" href="#<?= $key ?>"><?= e($p['short']) ?></a>
<?php endforeach; ?>
        </nav>

<?php $i = 0; foreach ($PILLARS as $key => $p): $i++; ?>
        <div class="pillar-group <?= $p['accent'] ?>" id="<?= $key ?>">
          <div class="section-head reveal">
            <span class="eyebrow pillar-eyebrow">Pillar <?= sprintf('%02d', $i) ?></span>
            <h2><?= e($p['name']) ?></h2>
            <p><?= e($p['desc']) ?></p>
          </div>
          <div class="company-grid">
<?php foreach ($COMPANIES as $c) { if ($c['pillar'] === $key) echo company_card($c, ''); } ?>
          </div>
        </div>

<?php endforeach; ?>
      </div>
    </section>

    <section class="section">
      <div class="container">
        <div class="cta-band reveal">
          <h2>Interested in one of our businesses?</h2>
          <p>Tell us which business area you are interested in and our team will get back to you.</p>
          <div class="cta-actions">
            <a class="btn btn-coral btn-lg" href="contact.html">Contact Us <span class="arrow" aria-hidden="true">&rarr;</span></a>
          </div>
        </div>
      </div>
    </section>

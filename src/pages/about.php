<?php
$n = roster_counts();
$page = [
    'title'       => 'About Urri Group — Our Story, Mission & Values',
    'description' => 'Urri Group started with one promise — get people where they need to go — and is growing into a family of Bangladeshi businesses across travel, trade, technology, property, finance and care.',
    'nav'         => 'about',
];
$values = [
    ['Vibrant & Modern',        'Embracing new ideas, technology and changing customer expectations.'],
    ['Professional & Trusted',  'Building relationships through reliability and responsible execution.'],
    ['Human & Approachable',    'Keeping people and relationships at the heart of business.'],
    ['Premium, but Accessible', 'Delivering high standards without unnecessary complexity.'],
];
?>
    <section class="page-hero">
      <div class="container">
        <div class="page-hero-inner">
          <nav class="breadcrumb" aria-label="Breadcrumb"><a href="./">Home</a><span aria-hidden="true">/</span>About</nav>
          <span class="eyebrow on-dark">About the Group</span>
          <h1>Built to move Bangladesh forward.</h1>
          <p>One promise became a family of businesses — each one carrying the same standards of trust, professionalism and forward motion.</p>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="container split">
        <div class="reveal">
          <span class="eyebrow">Our Story</span>
          <h2>It started with a simple promise.</h2>
          <p class="lead">Urri Group started with one promise — get people where they need to go — and is growing into a family of Bangladeshi businesses built on the same idea: move people, goods, ideas and opportunities forward, reliably.</p>
        </div>
        <div class="reveal">
          <div class="stat-card">
            <span class="eyebrow">Mission</span>
            <p class="mission">To build trusted, professionally run businesses across sectors that shape everyday life — travel, trade, technology, property, finance and care.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="section section-alt">
      <div class="container">
        <div class="section-head center reveal">
          <span class="eyebrow">Our Values</span>
          <h2>The standards behind every Urri company.</h2>
          <p>Four principles keep the whole Group pointed in the same direction.</p>
        </div>
        <div class="values">
<?php foreach ($values as $i => [$title, $text]): ?>
          <div class="value reveal">
            <div class="v-num"><?= sprintf('%02d', $i + 1) ?></div>
            <h3><?= e($title) ?></h3>
            <p><?= e($text) ?></p>
          </div>
<?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="container split">
        <div class="reveal">
          <span class="eyebrow">Leadership</span>
          <h2>Built by an experienced leadership team.</h2>
          <p class="lead">Urri Group brings together professionals with broad experience across travel, technology, financial services, marketing, international business, trade, operations and commercial management.</p>
          <p>We started where trust matters most — helping people travel, work, and study abroad — and we are building outward into the industries that keep Bangladesh moving: trade, technology, property, finance, and care.</p>
        </div>
        <div class="reveal">
          <div class="stat-card">
            <div class="stat-grid">
              <div class="stat"><div class="n"><?= $n['total'] ?></div><div class="l">Businesses in the family</div></div>
              <div class="stat"><div class="n"><?= $n['pillars'] ?></div><div class="l">Strategic pillars</div></div>
              <div class="stat"><div class="n"><?= $n['active'] ?></div><div class="l">Active business<?= $n['active'] === 1 ? '' : 'es' ?></div></div>
              <div class="stat"><div class="n"><?= $n['launching'] ?></div><div class="l">Launching now</div></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="container">
        <div class="cta-band reveal">
          <h2>Want to work with us?</h2>
          <p>Explore the businesses in the Urri family, or reach out to start a conversation.</p>
          <div class="cta-actions">
            <a class="btn btn-coral btn-lg" href="companies.html">Explore Our Companies <span class="arrow" aria-hidden="true">&rarr;</span></a>
            <a class="btn btn-ghost-light btn-lg" href="contact.html">Contact Us</a>
          </div>
        </div>
      </div>
    </section>

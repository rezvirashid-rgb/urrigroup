<?php
$n = roster_counts();
$page = [
    'title'       => 'Urri Group — One Group. Every Direction Forward.',
    'description' => 'Urri Group is a diversified Bangladeshi business group — a connected family of businesses across travel, trade, technology, property, finance and care, each one moving people, goods and ideas forward.',
    'nav'         => 'home',
    'jsonld'      => [
        '@context'    => 'https://schema.org',
        '@type'       => 'Organization',
        'name'        => $SITE['name'],
        'url'         => $SITE['url'] . '/',
        'email'       => $SITE['email'],
        'telephone'   => $SITE['phone'],
        'description' => 'A diversified Bangladeshi business group spanning travel, trade, technology, property, finance and care.',
        'logo'        => $SITE['url'] . '/assets/icons/icon-512.png',
        'address'     => ['@type' => 'PostalAddress', 'addressCountry' => 'BD'],
    ],
];
// Hero graphic: one route per pillar, fanning forward from a single origin.
$routes = [
    ['Mobility',       60,  '#a57bff'],
    ['Trade',          135, '#F97171'],
    ['Infrastructure', 210, '#6aaef5'],
    ['Finance',        285, '#45d0a6'],
    ['Technology',     360, '#FFD319'],
];
?>
    <section class="hero">
      <div class="container hero-grid">
        <div class="hero-inner">
          <span class="eyebrow on-dark">Diversified Bangladeshi Business Group</span>
          <h1>One Group.<br />Every Direction <span class="accent">Forward.</span></h1>
          <p class="hero-sub">From travel to trade, technology to real estate — Urri Group is building a connected family of Bangladeshi businesses, each one moving people, goods, and ideas forward.</p>
          <div class="hero-actions">
            <a class="btn btn-coral btn-lg" href="companies.html">Explore Our Companies <span class="arrow" aria-hidden="true">&rarr;</span></a>
            <a class="btn btn-ghost-light btn-lg" href="contact.html">Contact Us</a>
          </div>
          <div class="hero-meta">
            <div><strong><?= $n['total'] ?></strong> Businesses in the family</div>
            <div><strong><?= $n['pillars'] ?></strong> Strategic pillars</div>
            <div><strong>1</strong> Shared direction — forward</div>
          </div>
        </div>

        <svg class="hero-art" viewBox="0 0 480 420" aria-hidden="true" focusable="false">
<?php foreach ($routes as [$label, $y, $color]): ?>
          <path class="route" d="M48 210 C 190 210, 210 <?= $y ?>, 322 <?= $y ?>" stroke="<?= $color ?>" />
          <path d="M314 <?= $y - 8 ?> l10 8 -10 8" fill="none" stroke="<?= $color ?>" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
          <text x="342" y="<?= $y + 5 ?>"><?= e($label) ?></text>
<?php endforeach; ?>
          <circle cx="48" cy="210" r="22" fill="none" stroke="rgba(255,255,255,.18)" stroke-width="2" />
          <circle cx="48" cy="210" r="10" fill="#fff" />
        </svg>
      </div>
    </section>

    <section class="section">
      <div class="container split">
        <div class="reveal">
          <span class="eyebrow">Who We Are</span>
          <h2>A growing family of businesses, moving in one direction.</h2>
          <p class="lead">Urri Group is a growing family of businesses built around a simple idea: moving people, goods, ideas and opportunities forward. Starting with travel and mobility, the Group is expanding into complementary sectors that serve the evolving needs of individuals, businesses and communities.</p>
          <a class="btn btn-outline" href="about.html">About the Group <span class="arrow" aria-hidden="true">&rarr;</span></a>
        </div>
        <div class="reveal">
          <div class="stat-card">
            <div class="stat-grid">
              <div class="stat"><div class="n">Travel</div><div class="l">Where we started — helping people move</div></div>
              <div class="stat"><div class="n">Trade</div><div class="l">Connecting buyers, makers and markets</div></div>
              <div class="stat"><div class="n">Tech</div><div class="l">Digital solutions, media and care</div></div>
              <div class="stat"><div class="n">Property</div><div class="l">Real estate, energy and infrastructure</div></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="section section-alt">
      <div class="container">
        <div class="section-head center reveal">
          <span class="eyebrow">Our Business Pillars</span>
          <h2>Five directions. One connected group.</h2>
          <p>Every Urri company belongs to one of five strategic pillars — a structure that keeps the Group focused while it grows.</p>
        </div>
        <div class="pillars">
<?php foreach ($PILLARS as $key => $p): ?>
          <a class="pillar <?= $p['accent'] ?> reveal" href="companies.html#<?= $key ?>">
            <span class="p-icon" aria-hidden="true"><?= icon($p['icon']) ?></span>
            <h3><?= e($p['name']) ?></h3>
            <p><?= e($p['desc']) ?></p>
            <span class="p-link">View companies <span class="arrow" aria-hidden="true">&rarr;</span></span>
          </a>
<?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="container split">
        <div class="reveal">
          <span class="eyebrow">Built by an experienced leadership team</span>
          <h2>Professionals who know how to build and run real businesses.</h2>
          <p class="lead">Urri Group brings together professionals with broad experience across travel, technology, financial services, marketing, international business, trade, operations and commercial management.</p>
          <p>We started where trust matters most — helping people travel, work, and study abroad — and we are building outward into the industries that keep Bangladesh moving: trade, technology, property, finance, and care.</p>
        </div>
        <div class="reveal">
          <div class="stat-card">
            <ul class="service-list">
              <li>Professionally run companies</li>
              <li>Shared standards across every business</li>
              <li>Long-term thinking over short-term shortcuts</li>
              <li>People and relationships at the centre</li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="container">
        <div class="cta-band reveal">
          <h2>Let's move forward together.</h2>
          <p>Whether you are a customer, partner, supplier or investor — we would love to hear from you.</p>
          <div class="cta-actions">
            <a class="btn btn-coral btn-lg" href="contact.html">Contact Us <span class="arrow" aria-hidden="true">&rarr;</span></a>
            <a class="btn btn-ghost-light btn-lg" href="companies.html">Explore Our Companies</a>
          </div>
        </div>
      </div>
    </section>

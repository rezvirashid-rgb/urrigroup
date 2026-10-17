<?php
$page = [
    'title'       => "Contact Urri Group — Let's Move Forward Together",
    'og_title'    => 'Contact Urri Group',
    'description' => 'Get in touch with Urri Group. Email ' . $SITE['email'] . ' or call ' . $SITE['phone'] . ', or send us a message using the contact form.',
    'nav'         => 'contact',
];

/** One labelled form field with an error slot wired up via aria-describedby. */
function field(string $name, string $label, bool $required, string $control, bool $full = false): string {
    $req = $required ? ' <span class="req" aria-hidden="true">*</span>' : '';
    $cls = $full ? 'field full' : 'field';
    return <<<HTML
                <div class="$cls">
                  <label for="$name">$label$req</label>
                  $control
                  <span class="error-msg" id="$name-error" aria-live="polite"></span>
                </div>

HTML;
}
function input(string $name, string $type, string $autocomplete, int $max, bool $required): string {
    $req = $required ? ' required aria-required="true"' : '';
    $ac  = $autocomplete ? " autocomplete=\"$autocomplete\"" : '';
    return "<input type=\"$type\" id=\"$name\" name=\"$name\"$ac maxlength=\"$max\"$req aria-describedby=\"$name-error\" />";
}

$options = "<option value=\"\" disabled selected>Select an area…</option>\n";
foreach ($BUSINESS_AREAS as $a) {
    $options .= '                    <option value="' . e($a) . '">' . e($a) . "</option>\n";
}
$select = "<select id=\"business_area\" name=\"business_area\" required aria-required=\"true\" aria-describedby=\"business_area-error\">\n                    $options                  </select>";
$textarea = '<textarea id="message" name="message" maxlength="4000" required aria-required="true" aria-describedby="message-error"></textarea>';
?>
    <section class="page-hero">
      <div class="container">
        <div class="page-hero-inner">
          <nav class="breadcrumb" aria-label="Breadcrumb"><a href="./">Home</a><span aria-hidden="true">/</span>Contact</nav>
          <span class="eyebrow on-dark">Get in Touch</span>
          <h1>Let's move forward together.</h1>
          <p>Whether you are a customer, partner, supplier or investor — tell us how we can help and our team will get back to you.</p>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="container contact-grid">
        <div class="reveal">
          <div class="info-card">
            <h2>Urri Group</h2>
            <p class="muted">Reach us directly through any of the channels below.</p>
            <div class="info-item">
              <span class="ii-icon"><?= icon('mail') ?></span>
              <div><div class="ii-label">Email</div><div class="ii-value"><a data-config="email" href="mailto:<?= e($SITE['email']) ?>"><?= e($SITE['email']) ?></a></div></div>
            </div>
            <div class="info-item">
              <span class="ii-icon"><?= icon('phone') ?></span>
              <div><div class="ii-label">Phone</div><div class="ii-value"><a data-config="phone" href="tel:<?= e($SITE['phone']) ?>"><?= e($SITE['phone']) ?></a></div></div>
            </div>
            <div class="info-item">
              <span class="ii-icon"><?= icon('globe') ?></span>
              <div><div class="ii-label">Website</div><div class="ii-value"><a data-config="website" href="<?= e($SITE['url']) ?>"><?= e(preg_replace('#^https?://#', '', $SITE['url'])) ?></a></div></div>
            </div>
<?= social_links('socials socials-light') ?>
          </div>
        </div>

        <div class="reveal">
          <div class="form-card">
            <h2>Send us a message</h2>
            <p class="muted">Fields marked <span class="req" aria-hidden="true">*</span> are required.</p>

            <div class="form-status" tabindex="-1"></div>

            <form id="contact-form" action="forms/contact.php" method="post" novalidate data-token-url="forms/contact.php?action=token">
              <!-- Honeypot: must remain empty (hidden from people, visible to bots) -->
              <div class="hp-field" aria-hidden="true">
                <label for="website">Website (leave blank)</label>
                <input type="text" id="website" name="website" tabindex="-1" autocomplete="off" />
              </div>
              <!-- CSRF token (filled in by JavaScript; the server also checks the request's origin) -->
              <input type="hidden" name="csrf_token" value="" />

              <div class="form-grid">
<?= field('name', 'Name', true, input('name', 'text', 'name', 120, true)) ?>
<?= field('company', 'Company', false, input('company', 'text', 'organization', 150, false)) ?>
<?= field('email', 'Email', true, input('email', 'email', 'email', 150, true)) ?>
<?= field('phone', 'Phone', false, input('phone', 'tel', 'tel', 40, false)) ?>
<?= field('business_area', 'Business Area', true, $select) ?>
<?= field('subject', 'Subject', true, input('subject', 'text', '', 160, true)) ?>
<?= field('message', 'Message', true, $textarea, true) ?>
              </div>

              <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg">
                  <span class="btn-label">Send Message</span>
                  <span class="arrow" aria-hidden="true">&rarr;</span>
                </button>
                <span class="form-note">We use your details only to reply to your message.</span>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>

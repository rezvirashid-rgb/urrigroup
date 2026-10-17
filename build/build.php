<?php
/**
 * =====================================================================
 *  URRI GROUP — site generator
 * ---------------------------------------------------------------------
 *  Builds the complete, ready-to-upload website into  public/
 *  from the sources in  src/.
 *
 *      php build/build.php
 *
 *  - Pages share one header/footer (src/partials/layout.php).
 *  - Company cards, detail pages, counts and the sitemap come from
 *    src/data/companies.php.
 *  - CSS and JS are minified; files get a cache-busting version.
 *
 *  Never edit files in public/ by hand (except assets/js/config.js and
 *  config/config.php on the server) — they are overwritten on build.
 *  Requires only PHP 7.4+ (CLI). No npm, no dependencies.
 * =====================================================================
 */

declare(strict_types=1);

$ROOT = dirname(__DIR__);
$SRC  = "$ROOT/src";
$OUT  = "$ROOT/public";

require "$SRC/data/site.php";
require "$SRC/data/companies.php";
require "$SRC/partials/icons.php";

// ---------------------------------------------------------------------
// Template helpers
// ---------------------------------------------------------------------

function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/** Link to a site path ('' = home) from a page whose root prefix is $r. */
function href(string $path, string $r): string {
    if ($path === '') {
        return $r === '' ? './' : $r;
    }
    return $r . $path;
}

/** Contact page link that pre-selects a business area (and subject). */
function inquire_url(array $c, string $r): string {
    $q = http_build_query(['area' => $c['area'], 'about' => $c['name']]);
    return href('contact.html', $r) . '?' . e($q);
}

function social_links(string $class): string {
    $out = "          <div class=\"$class\">\n";
    foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram', 'linkedin' => 'LinkedIn'] as $key => $label) {
        $out .= "            <a href=\"#\" data-social=\"$key\" aria-label=\"Urri Group on $label\" rel=\"noopener\">" . icon($key) . "</a>\n";
    }
    return $out . "          </div>\n";
}

function status_badge(string $status): string {
    global $STATUSES;
    $s = $STATUSES[$status];
    return '<span class="badge ' . $s['class'] . '">' . e($s['label']) . '</span>';
}

/** One company card for the Companies grid. */
function company_card(array $c, string $r): string {
    global $PILLARS;
    $hasPage = isset($c['detail']);
    $url     = $hasPage ? href('companies/' . $c['slug'] . '.html', $r) : inquire_url($c, $r);
    $name    = $hasPage ? '<a href="' . $url . '">' . e($c['name']) . '</a>' : e($c['name']);
    $cta     = $hasPage ? 'Learn more' : 'Inquire';
    $ctaLbl  = $hasPage ? '' : ' aria-label="Inquire about ' . e($c['name']) . '"';
    $badge   = status_badge($c['status']);
    $pillar  = e($PILLARS[$c['pillar']]['name']);
    $desc    = e($c['desc']);
    return <<<HTML
            <article class="company-card reveal">
              <div class="cc-top">
                <h3>$name</h3>
                $badge
              </div>
              <span class="pillar-tag">$pillar</span>
              <p>$desc</p>
              <div class="cc-foot"><a class="cc-link" href="$url"$ctaLbl>$cta <span class="arrow" aria-hidden="true">&rarr;</span></a></div>
            </article>

HTML;
}

/** Counts derived from the roster (never hand-typed). */
function roster_counts(): array {
    global $COMPANIES, $PILLARS;
    $by = array_count_values(array_column($COMPANIES, 'status'));
    return [
        'total'     => count($COMPANIES),
        'pillars'   => count($PILLARS),
        'active'    => $by['active'] ?? 0,
        'launching' => $by['launching'] ?? 0,
    ];
}

// ---------------------------------------------------------------------
// Minifiers (deliberately conservative — correctness over bytes)
// ---------------------------------------------------------------------

function minify_css(string $css): string {
    $css = preg_replace('#/\*.*?\*/#s', '', $css);          // comments
    $css = preg_replace('/\s+/', ' ', $css);                 // collapse whitespace
    $css = preg_replace('/\s*([{};,>])\s*/', '$1', $css);    // around punctuation
    $css = preg_replace('/:\s+/', ':', $css);                // after colons
    $css = str_replace(';}', '}', $css);
    return trim($css) . "\n";
}

/** Strips comment-only lines, block comments that start a line, and indentation. */
function minify_js(string $js): string {
    $js = preg_replace('#^\s*/\*.*?\*/\s*$#ms', '', $js);
    $lines = [];
    foreach (explode("\n", $js) as $line) {
        $t = trim($line);
        if ($t === '' || strpos($t, '//') === 0) {
            continue;
        }
        $lines[] = $t;
    }
    return implode("\n", $lines) . "\n";
}

// ---------------------------------------------------------------------
// Filesystem helpers
// ---------------------------------------------------------------------

function rrmdir(string $dir): void {
    if (!is_dir($dir)) return;
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($it as $f) {
        $f->isDir() ? rmdir($f->getPathname()) : unlink($f->getPathname());
    }
    rmdir($dir);
}

function rcopy(string $from, string $to): void {
    @mkdir($to, 0755, true);
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($from, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($it as $f) {
        $dest = $to . '/' . $it->getSubPathname();
        $f->isDir() ? @mkdir($dest, 0755, true) : copy($f->getPathname(), $dest);
    }
}

function write(string $path, string $contents): void {
    @mkdir(dirname($path), 0755, true);
    file_put_contents($path, $contents);
}

// ---------------------------------------------------------------------
// Build
// ---------------------------------------------------------------------

// Keep the server's real config.php if the developer has one locally.
$keepConfig = is_file("$OUT/config/config.php") ? file_get_contents("$OUT/config/config.php") : null;
rrmdir($OUT);
rcopy("$SRC/static", $OUT);
if ($keepConfig !== null) {
    write("$OUT/config/config.php", $keepConfig);
}

// Business-area whitelist shared with the form processor.
write("$OUT/forms/lib/business_areas.php",
    "<?php\n// GENERATED by build/build.php from src/data/companies.php — do not edit.\nreturn " .
    var_export($BUSINESS_AREAS, true) . ";\n");

// Minified assets
$css = minify_css(file_get_contents("$SRC/assets/css/style.css"));
$js  = minify_js(file_get_contents("$SRC/assets/js/main.js"));
write("$OUT/assets/css/style.css", $css);
write("$OUT/assets/js/main.js", $js);
$V = substr(sha1($css . $js . file_get_contents("$SRC/static/assets/js/config.js")), 0, 10);

/** Render one page template into public/. */
function render(string $template, string $outPath, string $r, array $vars = []): void {
    global $SITE, $NAV, $PILLARS, $COMPANIES, $STATUSES, $BUSINESS_AREAS, $OUT, $SRC, $V;
    extract($vars);
    $page = [];
    ob_start();
    include "$SRC/pages/$template";
    $content = ob_get_clean();
    if (!isset($page['path'])) {
        $page['path'] = $outPath === 'index.html' ? '' : $outPath;
    }
    ob_start();
    include "$SRC/partials/layout.php";
    write("$OUT/$outPath", ob_get_clean());
}

$built = [];
foreach (['index', 'about', 'companies', 'contact'] as $p) {
    render("$p.php", "$p.html", '');
    $built[] = "$p.html";
}
render('404.php', '404.html', '/');
$built[] = '404.html';

foreach ($COMPANIES as $c) {
    if (isset($c['detail'])) {
        render('company.php', "companies/{$c['slug']}.html", '../', ['c' => $c]);
        $built[] = "companies/{$c['slug']}.html";
    }
}

// sitemap.xml
$today = date('Y-m-d');
$urls  = ['' => '1.0', 'about.html' => '0.8', 'companies.html' => '0.9', 'contact.html' => '0.8'];
foreach ($COMPANIES as $c) {
    if (isset($c['detail'])) $urls["companies/{$c['slug']}.html"] = '0.7';
}
$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
foreach ($urls as $path => $prio) {
    $xml .= "  <url><loc>{$SITE['url']}/$path</loc><lastmod>$today</lastmod><priority>$prio</priority></url>\n";
}
write("$OUT/sitemap.xml", $xml . "</urlset>\n");

// robots.txt
write("$OUT/robots.txt", "User-agent: *\nAllow: /\nDisallow: /config/\nDisallow: /forms/\n\nSitemap: {$SITE['url']}/sitemap.xml\n");

// Web app manifest (home-screen icon on Android)
write("$OUT/site.webmanifest", json_encode([
    'name'             => $SITE['name'],
    'short_name'       => 'Urri',
    'icons'            => [
        ['src' => '/assets/icons/icon-192.png', 'sizes' => '192x192', 'type' => 'image/png'],
        ['src' => '/assets/icons/icon-512.png', 'sizes' => '512x512', 'type' => 'image/png'],
    ],
    'theme_color'      => $SITE['theme_color'],
    'background_color' => $SITE['theme_color'],
    'display'          => 'browser',
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");

// Report
$counts = roster_counts();
echo "Built " . count($built) . " pages into public/ (asset version $V)\n";
echo "  Roster: {$counts['total']} companies, {$counts['pillars']} pillars, {$counts['active']} active, {$counts['launching']} launching\n";
printf("  CSS %.1f KB -> %.1f KB, JS %.1f KB -> %.1f KB\n",
    filesize("$SRC/assets/css/style.css") / 1024, strlen($css) / 1024,
    filesize("$SRC/assets/js/main.js") / 1024, strlen($js) / 1024);

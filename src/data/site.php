<?php
/**
 * Urri Group — site-wide settings used by the page generator.
 * Edit here, then run:  php build/build.php
 *
 * (Social links and the public email/phone can ALSO be changed after
 *  upload, without rebuilding, in public/assets/js/config.js.)
 */

$SITE = [
    'name'        => 'Urri Group',
    'url'         => 'https://urrigroup.com',   // no trailing slash
    'email'       => 'admin@urrigroup.com',
    'phone'       => '+8801895610102',
    'tagline'     => 'One Group. Every Direction Forward.',
    'short_about' => 'A connected family of Bangladeshi businesses, moving people, goods and ideas forward.',
    'theme_color' => '#120B24',
    'og_image'    => '/assets/images/og-image.jpg',
];

/** Primary navigation: key => [label, path relative to site root]. */
$NAV = [
    'home'      => ['Home', ''],
    'about'     => ['About', 'about.html'],
    'companies' => ['Our Companies', 'companies.html'],
    'contact'   => ['Contact', 'contact.html'],
];

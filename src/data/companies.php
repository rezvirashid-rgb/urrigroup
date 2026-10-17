<?php
/**
 * Urri Group — company roster (single source of truth).
 *
 * The Companies page, the five detail pages, the homepage/About counts,
 * the contact-form "Inquire" links and the sitemap are all generated
 * from this file. Edit here, then run:  php build/build.php
 *
 * 'area'   = which option to pre-select in the contact form's
 *            "Business Area" field when someone clicks Inquire.
 * 'detail' = present only for companies that have their own page.
 */

$STATUSES = [
    'active'      => ['label' => 'Active',                  'class' => 'badge-active'],
    'launching'   => ['label' => 'Launching Now',           'class' => 'badge-launching'],
    'soon'        => ['label' => 'Coming Soon',             'class' => 'badge-soon'],
    'regulated'   => ['label' => 'Coming Soon — Regulated', 'class' => 'badge-regulated'],
    'development' => ['label' => 'Under Development',       'class' => 'badge-development'],
];

$PILLARS = [
    'mobility' => [
        'name'   => 'Mobility & People',
        'short'  => 'Mobility & People',
        'desc'   => 'Travel, international mobility, education and people-focused services.',
        'accent' => 'accent-mobility',
        'icon'   => 'plane',
    ],
    'trade' => [
        'name'   => 'Trade, Industry & Consumer',
        'short'  => 'Trade, Industry & Consumer',
        'desc'   => 'Sourcing, manufacturing, agriculture, retail and international trade.',
        'accent' => 'accent-trade',
        'icon'   => 'box',
    ],
    'infrastructure' => [
        'name'   => 'Infrastructure & Property',
        'short'  => 'Infrastructure & Property',
        'desc'   => 'Real estate, construction, energy, logistics and hospitality.',
        'accent' => 'accent-infra',
        'icon'   => 'building',
    ],
    'finance' => [
        'name'   => 'Financial & Professional Services',
        'short'  => 'Financial & Professional',
        'desc'   => 'Consultancy, financial technology and professional services.',
        'accent' => 'accent-finance',
        'icon'   => 'chart',
    ],
    'technology' => [
        'name'   => 'Technology, Brand & Care',
        'short'  => 'Technology, Brand & Care',
        'desc'   => 'Technology, media, digital solutions and care-focused services.',
        'accent' => 'accent-tech',
        'icon'   => 'monitor',
    ],
];

$COMPANIES = [
    // ---------------- Mobility & People ----------------
    [
        'name' => 'Urri Travels', 'slug' => 'urri-travels', 'pillar' => 'mobility', 'status' => 'active',
        'desc' => 'Flight and hotel booking, visa assistance and tour packages.',
        'area' => 'Travel & Mobility',
        'detail' => [
            'meta_title' => 'Urri Travels — Flights, Hotels, Visas & Tours',
            'tagline'    => 'Travel made simple — from booking to boarding.',
            'heading'    => 'Where Urri Group started.',
            'lead'       => 'Urri Travels is where the Group began — helping people travel with confidence. We handle the details of the trip so our customers can focus on the journey ahead.',
            'body'       => 'From a single flight booking to a fully planned holiday, our team brings flights, hotels, visa support and tours together in one place.',
            'services'   => [
                'Flight booking — domestic & international',
                'Hotel reservations',
                'Visa assistance & documentation',
                'Tour & holiday packages',
                'Group & corporate travel',
                'Travel advice & support',
            ],
            'focus'      => 'Travel & mobility services',
            'cta_h'      => 'Planning a trip?',
            'cta_p'      => 'Tell us where you want to go and we will help you get there.',
        ],
    ],
    [
        'name' => 'Urri International', 'slug' => 'urri-international', 'pillar' => 'mobility', 'status' => 'launching',
        'desc' => 'Overseas work-visa processing, migration and recruitment services.',
        'area' => 'International Recruitment',
        'detail' => [
            'meta_title' => 'Urri International — Overseas Work Visas, Migration & Recruitment',
            'tagline'    => 'Your pathway to working abroad, handled professionally.',
            'heading'    => 'Opportunities abroad, managed with care.',
            'lead'       => 'Urri International supports people seeking opportunities overseas — managing work-visa processing, migration and recruitment with transparency and care.',
            'body'       => 'From the first consultation to departure, our team guides candidates and employers through documentation, compliance and matching, so the process is clear at every step.',
            'services'   => [
                'Overseas work-visa processing',
                'Migration guidance & documentation',
                'International recruitment',
                'Employer & candidate matching',
                'Pre-departure support',
                'Compliance & paperwork handling',
            ],
            'focus'      => 'International mobility & recruitment',
            'cta_h'      => 'Looking to work abroad?',
            'cta_p'      => 'Start your journey with a team that handles the details.',
        ],
    ],
    [
        'name' => 'Urri Education', 'slug' => 'urri-education', 'pillar' => 'mobility', 'status' => 'soon',
        'desc' => 'Test preparation, vocational training and pre-departure orientation.',
        'area' => 'Education',
    ],

    // ---------------- Trade, Industry & Consumer ----------------
    [
        'name' => 'Urri Sourcing', 'slug' => 'urri-sourcing', 'pillar' => 'trade', 'status' => 'launching',
        'desc' => 'B2B sourcing services connecting buyers with manufacturers.',
        'area' => 'Sourcing',
        'detail' => [
            'meta_title' => 'Urri Sourcing — B2B Sourcing & Procurement',
            'tagline'    => 'Connecting global buyers with trusted manufacturers.',
            'heading'    => 'Sourcing, simplified end to end.',
            'lead'       => 'Urri Sourcing is a B2B sourcing partner that links buyers with reliable manufacturers, streamlining procurement from enquiry to delivery.',
            'body'       => 'We help buyers find suitable suppliers, agree fair terms, check quality and stay informed through production — reducing the risk that comes with cross-border sourcing.',
            'services'   => [
                'Supplier identification & vetting',
                'Price negotiation',
                'Sample coordination',
                'Quality inspection support',
                'Order & production follow-up',
                'Logistics coordination',
            ],
            'focus'      => 'B2B sourcing & procurement',
            'cta_h'      => 'Sourcing from Bangladesh?',
            'cta_p'      => 'Tell us what you need and we will connect you with suitable manufacturers.',
        ],
    ],
    [
        'name' => 'Urri Trade', 'slug' => 'urri-trade', 'pillar' => 'trade', 'status' => 'soon',
        'desc' => 'General import/export trading house.',
        'area' => 'Trade',
    ],
    [
        'name' => 'Urri Manufacturing', 'slug' => 'urri-manufacturing', 'pillar' => 'trade', 'status' => 'soon',
        'desc' => 'Garments / RMG production.',
        'area' => 'Trade',
    ],
    [
        'name' => 'Urri Agro', 'slug' => 'urri-agro', 'pillar' => 'trade', 'status' => 'soon',
        'desc' => 'Farm-to-market distribution and packaged food products.',
        'area' => 'Trade',
    ],
    [
        'name' => 'Urri Retail', 'slug' => 'urri-retail', 'pillar' => 'trade', 'status' => 'soon',
        'desc' => 'Consumer retail and e-commerce.',
        'area' => 'Trade',
    ],
    [
        'name' => 'Urri Global', 'slug' => 'urri-global', 'pillar' => 'trade', 'status' => 'soon',
        'desc' => 'Cross-border trade and international market-entry services.',
        'area' => 'Trade',
    ],

    // ---------------- Infrastructure & Property ----------------
    [
        'name' => 'Urri Real Estate', 'slug' => 'urri-real-estate', 'pillar' => 'infrastructure', 'status' => 'soon',
        'desc' => 'Land development, residential and commercial property.',
        'area' => 'Real Estate',
    ],
    [
        'name' => 'Urri Construction', 'slug' => 'urri-construction', 'pillar' => 'infrastructure', 'status' => 'soon',
        'desc' => 'Building construction and project execution.',
        'area' => 'Construction',
    ],
    [
        'name' => 'Urri Energy', 'slug' => 'urri-energy', 'pillar' => 'infrastructure', 'status' => 'soon',
        'desc' => 'Renewable energy and power solutions.',
        'area' => 'Energy',
    ],
    [
        'name' => 'Urri Logistics', 'slug' => 'urri-logistics', 'pillar' => 'infrastructure', 'status' => 'soon',
        'desc' => 'Freight, courier and warehousing.',
        'area' => 'Logistics',
    ],
    [
        'name' => 'Urri Hospitality', 'slug' => 'urri-hospitality', 'pillar' => 'infrastructure', 'status' => 'soon',
        'desc' => 'Hotels and resorts.',
        'area' => 'Hospitality',
    ],

    // ---------------- Financial & Professional Services ----------------
    [
        'name' => 'Urri Consultancy', 'slug' => 'urri-consultancy', 'pillar' => 'finance', 'status' => 'launching',
        'desc' => 'Corporate strategy, brand and market-entry advisory.',
        'area' => 'Consultancy',
        'detail' => [
            'meta_title' => 'Urri Consultancy — Strategy, Brand & Market-Entry Advisory',
            'tagline'    => 'Strategy, brand and market-entry advisory.',
            'heading'    => 'Practical advice for the next stage of growth.',
            'lead'       => 'Urri Consultancy helps businesses plan, position and grow — offering corporate strategy, brand and market-entry advisory.',
            'body'       => 'Whether you are entering a new market, refreshing a brand or planning your next stage of growth, we focus on advice that can actually be carried out.',
            'services'   => [
                'Corporate & growth strategy',
                'Brand strategy & positioning',
                'Market-entry advisory',
                'Business planning',
                'Operational consulting',
                'Go-to-market support',
            ],
            'focus'      => 'Strategy & advisory',
            'cta_h'      => 'Planning your next move?',
            'cta_p'      => 'Let us help you plan, position and grow with confidence.',
        ],
    ],
    [
        'name' => 'Urri Pay', 'slug' => 'urri-pay', 'pillar' => 'finance', 'status' => 'regulated',
        'desc' => 'Digital payments and remittance.',
        'area' => 'Other',
    ],
    [
        'name' => 'Urri Insurance', 'slug' => 'urri-insurance', 'pillar' => 'finance', 'status' => 'regulated',
        'desc' => 'Life and general insurance.',
        'area' => 'Other',
    ],

    // ---------------- Technology, Brand & Care ----------------
    [
        'name' => 'Urri Media', 'slug' => 'urri-media', 'pillar' => 'technology', 'status' => 'launching',
        'desc' => 'Content production and digital marketing.',
        'area' => 'Media',
        'detail' => [
            'meta_title' => 'Urri Media — Content Production & Digital Marketing',
            'tagline'    => 'Content and digital marketing that moves brands forward.',
            'heading'    => 'Stories that reach the right people.',
            'lead'       => 'Urri Media produces content and runs digital marketing that helps brands tell their story and reach the right audiences.',
            'body'       => 'From creative production to performance campaigns, we combine clear storytelling with measurable marketing to help brands grow their presence.',
            'services'   => [
                'Content production',
                'Social media management',
                'Digital marketing campaigns',
                'Brand & creative design',
                'Video & photography',
                'Performance marketing',
            ],
            'focus'      => 'Media & digital marketing',
            'cta_h'      => 'Ready to grow your brand?',
            'cta_p'      => 'Let us help you tell your story and reach the right audience.',
        ],
    ],
    [
        'name' => 'Urri Solutions', 'slug' => 'urri-solutions', 'pillar' => 'technology', 'status' => 'soon',
        'desc' => 'Software and digital-systems implementation.',
        'area' => 'Technology',
    ],
    [
        'name' => 'Urri Care', 'slug' => 'urri-care', 'pillar' => 'technology', 'status' => 'development',
        'desc' => 'Future healthcare, elder-care or health-data services.',
        'area' => 'Other',
    ],
];

/** Business-area options for the contact form (must match forms/contact.php). */
$BUSINESS_AREAS = [
    'Travel & Mobility', 'International Recruitment', 'Education', 'Sourcing', 'Trade',
    'Real Estate', 'Construction', 'Energy', 'Logistics', 'Hospitality',
    'Consultancy', 'Technology', 'Media', 'Other',
];

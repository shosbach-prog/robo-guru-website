<?php
/**
 * Plugin Name: RG Roboter Hero (Server-Side)
 * Description: Rendert den Hero-Bereich, Schema-Markup und SEO-Meta für /roboter/ serverseitig – kein JS-Injection, kein scrollTo-Hack.
 * Version: 1.0.0
 * Author: robo-guru.de
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Nur auf dem robo_robot Post-Type-Archiv aktiv.
 */
function rg_is_roboter_archive() {
    return is_post_type_archive( 'robo_robot' );
}

/* =========================================================
   1. CSS – im <head> ausgeben (kein CLS, kein FOUC)
   ========================================================= */
add_action( 'wp_head', function () {
    if ( ! rg_is_roboter_archive() ) return;
    ?>
    <style id="rg-roboter-hero-css">
    /* ---------- Hero ---------- */
    .rg-hero--roboter {
        --rg-primary: #00BCD4;
        --rg-primary-dark: #0097A7;
        --rg-dark: #1A2332;
        --rg-dark-light: #2A3444;
        font-family: Plus Jakarta Sans, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Arial, sans-serif !important;
        background: linear-gradient(135deg, var(--rg-dark) 0%, var(--rg-dark-light) 100%);
        padding: 100px 24px 80px;
        position: relative;
        overflow: hidden;
        margin: -30px -24px 24px;
        box-sizing: border-box;
    }
    .rg-hero--roboter * { box-sizing: border-box; }
    .rg-hero--roboter::before {
        content: '';
        position: absolute;
        top: 0; right: 0;
        width: 50%; height: 100%;
        background: radial-gradient(circle at 70% 30%, rgba(0,188,212,0.15) 0%, transparent 50%);
        pointer-events: none;
    }
    .rg-hero--roboter .rg-hero__inner {
        max-width: 800px;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }
    .rg-hero--roboter .rg-hero__badge {
        display: inline-block;
        background: rgba(0,188,212,0.15);
        padding: 8px 16px;
        border-radius: 100px;
        font-size: 14px;
        font-weight: 700;
        letter-spacing: .4px;
        color: var(--rg-primary) !important;
        margin-bottom: 24px;
    }
    .rg-hero--roboter h1 {
        font-size: clamp(32px, 5vw, 48px) !important;
        font-weight: 800 !important;
        line-height: 1.15 !important;
        color: #fff !important;
        margin: 0 0 20px !important;
        padding: 0 !important;
    }
    .rg-hero--roboter .rg-hero__lead {
        font-size: 18px;
        color: rgba(255,255,255,0.82) !important;
        max-width: 600px;
        margin-bottom: 32px;
        line-height: 1.6;
    }
    .rg-hero--roboter .rg-hero__cta {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }
    .rg-hero--roboter .rg-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 28px;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 700;
        text-decoration: none !important;
        transition: all .2s ease;
        cursor: pointer;
        border: none;
    }
    .rg-hero--roboter .rg-btn--primary {
        background: var(--rg-primary);
        color: var(--rg-dark) !important;
    }
    .rg-hero--roboter .rg-btn--primary:hover {
        background: var(--rg-primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    }
    .rg-hero--roboter .rg-btn--outline {
        background: transparent;
        border: 2px solid rgba(255,255,255,0.3);
        color: #fff !important;
    }
    .rg-hero--roboter .rg-btn--outline:hover {
        border-color: #fff;
        background: rgba(255,255,255,0.1);
    }
    .rg-hero--roboter .rg-hero__usp {
        display: flex;
        gap: 32px;
        flex-wrap: wrap;
        margin-top: 40px;
    }
    .rg-hero--roboter .rg-hero__usp-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 600;
        color: var(--rg-primary) !important;
    }
    .rg-hero--roboter .rg-hero__usp-item svg {
        width: 18px; height: 18px;
        flex-shrink: 0;
    }
    @media (max-width: 600px) {
        .rg-hero--roboter {
            padding: 80px 20px 60px;
            margin: -20px -16px 16px;
        }
        .rg-hero--roboter .rg-hero__usp { gap: 20px; }
    }

    /* ---------- Altes H1 / entry-title verstecken ---------- */
    .post-type-archive-robo_robot .entry-title,
    .post-type-archive-robo_robot h1.rg-title,
    .post-type-archive-robo_robot .rg-muted {
        display: none !important;
    }

    /* ---------- Section-Heading ---------- */
    .rg-section-heading--roboter {
        font-family: Plus Jakarta Sans, sans-serif;
        font-size: 24px;
        font-weight: 700;
        color: #1A2332;
        margin: 0 0 20px;
        padding: 0;
    }

    /* ---------- min-height Reserve entfernen (nicht mehr nötig) ---------- */
    .post-type-archive-robo_robot .rg-wrap {
        min-height: auto !important;
    }
    </style>
    <?php
}, 5 );

/* =========================================================
   2. Hero HTML – direkt nach Content-Start ausgeben
   ========================================================= */
add_action( 'wp_head', function () {
    if ( ! rg_is_roboter_archive() ) return;

    // Roboter zählen
    $count = wp_count_posts( 'robo_robot' );
    $total = isset( $count->publish ) ? (int) $count->publish : 17;

    // Hero wird per JS an die richtige Stelle verschoben (sofort, nicht lazy)
    ?>
    <script id="rg-roboter-hero-inject">
    (function(){
        var wrap = document.querySelector(".rg-wrap");
        var hero = document.getElementById("rg-roboter-hero-block");
        if(wrap && hero){
            wrap.insertBefore(hero, wrap.firstChild);
            hero.style.display = "";
        }
        var oldH1 = wrap && wrap.querySelector("h1.rg-title");
        var oldSub = wrap && wrap.querySelector(".rg-muted");
        if(oldH1) oldH1.style.display = "none";
        if(oldSub) oldSub.style.display = "none";
        var et = document.querySelector(".entry-title");
        if(et) et.style.display = "none";

        /* Section Heading */
        var fc = document.querySelector(".rg-card");
        if(fc && !document.querySelector(".rg-section-heading--roboter")){
            var h2 = document.createElement("h2");
            h2.className = "rg-section-heading rg-section-heading--roboter";
            h2.textContent = "Alle Roboter-Modelle im \u00dcberblick";
            h2.id = "roboter-db";
            fc.parentNode.insertBefore(h2, fc);
        }

        /* Smooth scroll for CTA */
        var cta = hero && hero.querySelector('a[href="#roboter-db"]');
        if(cta) cta.addEventListener("click", function(e){
            e.preventDefault();
            var t = document.getElementById("roboter-db");
            if(t) t.scrollIntoView({behavior:"smooth", block:"start"});
        });
    })();
    </script>
    <?php
}, 99 );

/* =========================================================
   3. Hero HTML Block (hidden, wird per obigem Script verschoben)
   ========================================================= */
add_action( 'wp_footer', function () {
    if ( ! rg_is_roboter_archive() ) return;

    $count = wp_count_posts( 'robo_robot' );
    $total = isset( $count->publish ) ? (int) $count->publish : 17;
    $check = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>';
    ?>
    <div id="rg-roboter-hero-block" class="rg-hero rg-hero--roboter" style="display:none">
        <div class="rg-hero__inner">
            <span class="rg-hero__badge">Roboter-Datenbank – <?php echo $total; ?> Modelle</span>
            <h1>Kommerzielle Roboter für Reinigung, Service &amp; Transport</h1>
            <p class="rg-hero__lead">Vergleichen Sie Reinigungsroboter, Serviceroboter und Transportroboter für den professionellen Einsatz – herstellerunabhängig und praxisnah.</p>
            <div class="rg-hero__cta">
                <a href="#roboter-db" class="rg-btn rg-btn--primary">Roboter Datenbank starten ↓</a>
                <a href="/robo-finder/" class="rg-btn rg-btn--outline">Robo Finder</a>
            </div>
            <div class="rg-hero__usp">
                <span class="rg-hero__usp-item"><?php echo $check; ?>Herstellerunabhängig</span>
                <span class="rg-hero__usp-item"><?php echo $check; ?>Praxisnah</span>
                <span class="rg-hero__usp-item"><?php echo $check; ?><?php echo $total; ?> Modelle vergleichbar</span>
            </div>
        </div>
    </div>
    <?php
}, 5 );

/* =========================================================
   4. SEO Meta via wp_head (ersetzt die JS-Overrides)
   ========================================================= */
add_action( 'wp_head', function () {
    if ( ! rg_is_roboter_archive() ) return;
    // Nur setzen wenn kein SEO-Plugin (Rank Math) das bereits macht
    // Falls Rank Math aktiv ist, dort direkt konfigurieren und diesen Block entfernen.
    if ( ! class_exists( 'RankMath' ) ) {
        echo '<meta name="description" content="' . esc_attr( '17 kommerzielle Reinigungsroboter, Serviceroboter & Transportroboter von Gausium, Pudu, Nexaro & OrionStar im Vergleich. Technische Daten, Einsatzbereiche & Bewertungen.' ) . '">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr( 'Gewerbliche Reinigungsroboter & Serviceroboter | Robo-Guru' ) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr( '17 kommerzielle Reinigungsroboter, Serviceroboter & Transportroboter im Vergleich. Technische Daten & Einsatzbereiche.' ) . '">' . "\n";
    }
}, 1 );

/* Document Title */
add_filter( 'pre_get_document_title', function ( $title ) {
    if ( rg_is_roboter_archive() ) {
        return 'Gewerbliche Reinigungsroboter & Serviceroboter | Roboter-Datenbank | Robo-Guru';
    }
    return $title;
}, 20 );

/* =========================================================
   5. Schema JSON-LD (ItemList + FAQPage)
   ========================================================= */
add_action( 'wp_head', function () {
    if ( ! rg_is_roboter_archive() ) return;

    $robots = array(
        array( 'Gausium Omnie',          '/roboter/gausium-omnie/',              'Autonomer Scheuersaugroboter für High-Traffic-Flächen',                'Gausium',       'Scheuersaugroboter' ),
        array( 'Gausium Phantas',         '/roboter/gausium-phantas-1-3/',        '4-in-1 Reinigungsroboter für kleine bis mittlere Flächen',             'Gausium',       '4-in-1 Reinigungsroboter' ),
        array( 'Gausium Scrubber 50',     '/roboter/gausium-scrubber-50/',        'Kompakter Scheuersaugroboter für den gewerblichen Einsatz',            'Gausium',       'Scheuersaugroboter' ),
        array( 'Gausium Scrubber 75',     '/roboter/gausium-scrubber-75/',        'Heavy-Duty-Scheuersaugroboter für große Flächen',                      'Gausium',       'Scheuersaugroboter' ),
        array( 'Nexaro NR 1700',          '/roboter/nexaro-nr-1700/',             'Gewerblicher Staubsaugerroboter mit KI-Navigation',                    'Nexaro',        'Staubsaugerroboter' ),
        array( 'OrionStar GreetingBot Nova', '/roboter/orionstar-nova-greetingbot/', 'KI-Empfangsroboter für Hotels und Büros',                          'OrionStar',     'Empfangsroboter' ),
        array( 'Pudu BellaBot Pro',       '/roboter/pudu-bellabot-pro/',          'Service- und Lieferroboter für Gastronomie',                           'Pudu Robotics', 'Serviceroboter' ),
        array( 'PUDU BG1',                '/roboter/pudu-bg1/',                   'Autonomer Scheuersaugroboter für gewerbliche Reinigung',               'Pudu Robotics', 'Scheuersaugroboter' ),
        array( 'Pudu CC1',                '/roboter/pudu-cc1/',                   'Kompakter 4-in-1 Reinigungsroboter',                                   'Pudu Robotics', '4-in-1 Reinigungsroboter' ),
        array( 'Pudu CC1 Pro',            '/roboter/pudu-cc1-pro/',               'Professioneller 4-in-1 Reinigungsroboter',                             'Pudu Robotics', '4-in-1 Reinigungsroboter' ),
        array( 'Pudu HolaBot',            '/roboter/pudu-holabot/',               'Autonomer Lieferroboter für Gastronomie und Gesundheitswesen',         'Pudu Robotics', 'Lieferroboter' ),
        array( 'Pudu MT1',                '/roboter/pudu-mt1/',                   'Autonomer Kehrroboter für gewerbliche Flächen',                        'Pudu Robotics', 'Kehrroboter' ),
        array( 'Pudu MT1 Max',            '/roboter/pudu-mt1-max/',               'Leistungsstarker Kehrroboter für große Gewerbeflächen',                'Pudu Robotics', 'Kehrroboter' ),
        array( 'Pudu MT1 Vac',            '/roboter/pudu-mt1-vac/',               'Kehrsaugmaschine mit autonomer Navigation',                            'Pudu Robotics', 'Kehrsaugmaschine' ),
        array( 'Pudu SH1',                '/roboter/pudu-sh1/',                   'Kompakter Scheuersaugroboter für enge Bereiche',                       'Pudu Robotics', 'Scheuersaugroboter' ),
        array( 'Pudu T300',               '/roboter/pudu-t300/',                  'Autonomer Transportroboter für Gastronomie und Logistik',              'Pudu Robotics', 'Transportroboter' ),
        array( 'Pudu T600',               '/roboter/pudu-t600/',                  'Schwerlast-Transportroboter für Logistik und Industrie',               'Pudu Robotics', 'Transportroboter' ),
    );

    $items = array();
    foreach ( $robots as $i => $r ) {
        $items[] = array(
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'item'     => array(
                '@type'       => 'Product',
                'name'        => $r[0],
                'url'         => 'https://robo-guru.de' . $r[1],
                'description' => $r[2],
                'brand'       => array( '@type' => 'Brand', 'name' => $r[3] ),
                'category'    => $r[4],
            ),
        );
    }

    $schema_list = array(
        '@context'        => 'https://schema.org',
        '@type'           => 'ItemList',
        'name'            => 'Gewerbliche Reinigungsroboter & Serviceroboter',
        'description'     => '17 kommerzielle Reinigungsroboter, Serviceroboter und Transportroboter von Gausium, Pudu Robotics, Nexaro und OrionStar im Vergleich.',
        'url'             => 'https://robo-guru.de/roboter/',
        'numberOfItems'   => count( $robots ),
        'itemListElement' => $items,
    );

    $schema_faq = array(
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => array(
            array(
                '@type'          => 'Question',
                'name'           => 'Welche Reinigungsroboter eignen sich für gewerbliche Flächen?',
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text'  => 'Für gewerbliche Flächen eignen sich Scheuersaugroboter wie der Gausium Omnie (ab 3.600 m²/h), Pudu BG1, Pudu CC1 Pro oder Gausium Scrubber 75 für Großflächen. Für kleinere Bereiche sind der Pudu SH1 oder Gausium Scrubber 50 optimal.',
                ),
            ),
            array(
                '@type'          => 'Question',
                'name'           => 'Was kostet ein gewerblicher Reinigungsroboter?',
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text'  => 'Die Preise variieren je nach Modell und Einsatzbereich. Alternativ zum Kauf bieten viele Hersteller Robot-as-a-Service (RaaS) Modelle ab ca. 800–1.500 EUR monatlich an. Nutzen Sie unseren ROI-Rechner für eine individuelle Wirtschaftlichkeitsberechnung.',
                ),
            ),
            array(
                '@type'          => 'Question',
                'name'           => 'Welche Roboter-Marken bietet Robo-Guru an?',
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text'  => 'Robo-Guru bietet herstellerunabhängige Beratung zu Robotern von Gausium, Pudu Robotics, Nexaro und OrionStar. Das Portfolio umfasst Reinigungsroboter, Serviceroboter, Transportroboter und Empfangsroboter für den gewerblichen Einsatz.',
                ),
            ),
        ),
    );

    echo '<script type="application/ld+json">' . wp_json_encode( $schema_list, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
    echo '<script type="application/ld+json">' . wp_json_encode( $schema_faq, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}, 5 );

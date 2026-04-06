<?php
/**
 * Plugin Name: RG Roboter SEO & Schema
 * Description: SEO-Meta und Schema-Markup für /roboter/ – Hero wird direkt im Plugin-Template gerendert.
 * Version: 1.1.0
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
   1. SEO Meta via wp_head
   ========================================================= */
add_action( 'wp_head', function () {
    if ( ! rg_is_roboter_archive() ) return;
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
   2. Schema JSON-LD (ItemList + FAQPage)
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

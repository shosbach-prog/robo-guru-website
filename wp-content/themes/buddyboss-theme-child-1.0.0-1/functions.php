<?php
/**
 * @package BuddyBoss Child
 * The parent theme functions are located at /buddyboss-theme/inc/theme/functions.php
 * Add your own functions at the bottom of this file.
 */


/****************************** THEME SETUP ******************************/

/**
 * Sets up theme for translation
 *
 * @since BuddyBoss Child 1.0.0
 */
function buddyboss_theme_child_languages()
{
  // Translate text from the PARENT theme.
  load_theme_textdomain( 'buddyboss-theme', get_stylesheet_directory() . '/languages' );
}
// WP Rocket: BuddyBoss Menü-Scripts + jQuery von Delay JS ausschließen
add_filter( 'rocket_delay_js_exclusions', function( $excluded ) {
    $excluded[] = '/wp-includes/js/jquery/';
    $excluded[] = 'buddyboss-theme/assets/js/vendors/menu.js';
    $excluded[] = 'buddyboss-theme/assets/js/main.min.js';
    $excluded[] = 'buddyboss-theme/assets/js/vendors/panelslider.min.js';
    return $excluded;
} );

// WP Rocket: Mobile-Menü CSS vor "Remove Unused CSS" schützen.
// RUCSS crawlt mit Desktop-Viewport und entfernt mobile-only CSS-Regeln.
add_filter( 'rocket_rucss_safelist', function( $safelist ) {
    $safelist[] = '.bb-mobile-header(.*)';
    $safelist[] = '.bb-mobile-panel(.*)';
    $safelist[] = '.bb-left-panel(.*)';
    $safelist[] = '.bb-close-panel(.*)';
    $safelist[] = '.bb-mobile-panel-open(.*)';
    $safelist[] = '.push-left(.*)';
    return $safelist;
} );

// Elementor Pro entfernt den BuddyBoss Mobile-Header auf Seiten mit Elementor-Header-Template.
// Hier wird er wieder hinzugefügt, damit das Hamburger-Menü auf allen Seiten funktioniert.
add_action( 'wp', function() {
    if ( ! function_exists( 'buddyboss_theme_mobile_header' ) ) {
        return;
    }
    // Prüfe ob Elementor Pro den Mobile-Header entfernt hat
    $hook = defined( 'THEME_HOOK_PREFIX' ) ? THEME_HOOK_PREFIX : 'jesuspended_theme';
    if ( ! has_action( $hook . 'header', 'buddyboss_theme_mobile_header' ) ) {
        add_action( $hook . 'header', 'buddyboss_theme_mobile_header', 20 );
    }
} );

// Fallback: Mobiles Hamburger-Menü sofort initialisieren (ohne jQuery / WP Rocket Abhängigkeit)
// Verwendet onclick statt addEventListener, weil WP Rocket EventTarget.prototype.addEventListener
// global patcht und alle Listener bis zur ersten User-Interaktion verzögert.
add_action( 'wp_footer', function() {
    ?>
    <script data-no-optimize="1" data-no-defer="1" data-no-minify="1">
    (function(){
      var btn = document.querySelector('.bb-left-panel-mobile');
      var panel = document.querySelector('.bb-mobile-panel-wrapper');
      var closeBtn = document.querySelector('.bb-close-panel');
      if (!btn || !panel) return;

      btn.onclick = function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        panel.classList.toggle('closed');
        document.body.classList.toggle('bb-mobile-panel-open');
      };

      if (closeBtn) {
        closeBtn.onclick = function(e){
          e.preventDefault();
          e.stopImmediatePropagation();
          panel.classList.add('closed');
          document.body.classList.remove('bb-mobile-panel-open');
        };
      }

      // Menü-Links: Panel schließen beim Navigieren
      var navLinks = panel.querySelectorAll('.main-navigation a');
      for (var i = 0; i < navLinks.length; i++) {
        (function(link){
          var origClick = link.onclick;
          link.onclick = function(e){
            if (!e.target.classList.contains('bs-submenu-toggle')) {
              panel.classList.add('closed');
              document.body.classList.remove('bb-mobile-panel-open');
            }
            if (origClick) origClick.call(this, e);
          };
        })(navLinks[i]);
      }
    })();
    </script>
    <?php
}, 1 );

/**
 * Enqueues scripts and styles for child theme front-end.
 *
 * @since Boss Child Theme  1.0.0
 */
function buddyboss_theme_child_scripts_styles()
{
  // Cache-busting version based on file modification time
  $css_file = get_stylesheet_directory() . '/assets/css/custom.css';
  $js_file  = get_stylesheet_directory() . '/assets/js/custom.js';
  $css_ver  = file_exists($css_file) ? filemtime($css_file) : '1.0.0';
  $js_ver   = file_exists($js_file)  ? filemtime($js_file)  : '1.0.0';

  // Styles
  wp_enqueue_style( 'buddyboss-child-css', get_stylesheet_directory_uri().'/assets/css/custom.css', array(), $css_ver );

  // Javascript
  wp_enqueue_script( 'buddyboss-child-js', get_stylesheet_directory_uri().'/assets/js/custom.js', array(), $js_ver, true );
}
add_action( 'wp_enqueue_scripts', 'buddyboss_theme_child_scripts_styles', 9999 );


/****************************** CUSTOM FUNCTIONS ******************************/

// Admin-Bar aus (du hattest das schon)
add_filter('show_admin_bar', '__return_false', 9999);


/**
 * Helper: Check if we are on Robo-Finder page.
 * (Slug + Template; du kannst hier auch zusätzlich mit Page-ID absichern)
 */
function bb_child_is_robo_finder_page(): bool {
  return ( is_page('robo-finder') || is_page_template('page-robo-finder.php') );
}


/**
 * 1) RankMath auf Robo-Finder Seite möglichst komplett entfernen (Scripts/Styles)
 *    Früh genug, damit möglichst nichts mehr registriert wird.
 */
add_action('wp_enqueue_scripts', function () {

  if ( ! bb_child_is_robo_finder_page() ) {
    return;
  }

  // Rank Math Handles (können je nach Version/Setup variieren)
  $handles = [
    'rank-math',
    'rank-math-frontend',
    'rank-math-common',
    'rank-math-schema',
    'rank-math-admin-bar',
    'rank-math-analyzer',
    'rank-math-app',
    'rank-math-content-ai',

    // Alternative Handles, die manchmal verwendet werden
    'rankmath-frontend',
    'rank-math-jsonld',
    'rank-math-sidebar',
    'rank-math-seo-score',
  ];

  foreach ($handles as $h) {
    wp_dequeue_script($h);
    wp_deregister_script($h);
    wp_dequeue_style($h);
    wp_deregister_style($h);
  }

}, 20);


/**
 * 2) Notbremse: Falls RankMath (oder ein anderer Code) trotzdem
 *    den nervigen Request /wp-json/rankmath/v1/an/post/undefined abfeuert,
 *    fangen wir ihn auf dieser Seite ab, damit es keine Layout-Reflows/„Springen“ gibt.
 *
 *    Das ist absichtlich eng gefiltert NUR auf genau diese URL.
 */
add_action('wp_footer', function () {

  if ( ! bb_child_is_robo_finder_page() ) {
    return;
  }
  ?>
  <script>
  (function(){
    // Wenn fetch nicht existiert, nichts tun
    if (!window.fetch) return;

    const BLOCK_PART = '/wp-json/rankmath/v1/an/post/undefined';
    const origFetch = window.fetch;

    window.fetch = function(input, init){
      try {
        const url = (typeof input === 'string')
          ? input
          : (input && input.url ? input.url : '');

        // Nur exakt den undefined-Call blocken
        if (url && url.indexOf(BLOCK_PART) !== -1) {
          return Promise.resolve(
            new Response("{}", {
              status: 200,
              headers: { "Content-Type": "application/json" }
            })
          );
        }
      } catch (e) {}

      return origFetch.apply(this, arguments);
    };
  })();
  </script>
  <?php

}, 9999);



// Add your own custom functions here
/**
 * Robo-Guru – Zentrale SearchWP Suche
 */
function rg_searchwp_form() {
    if (shortcode_exists('searchwp_form')) {
        echo do_shortcode('[searchwp_form id="3"]');
    }
}


/****************************** ROI-RECHNER SEO CONTENT ******************************/

/**
 * Helper: Check if we are on the ROI-Rechner page.
 */
function bb_child_is_roi_rechner_page(): bool {
    return is_page('roi-rechner');
}

/**
 * 1) Title Tag – Override via document_title_parts + RankMath filter.
 */
add_filter('document_title_parts', function ( $title_parts ) {
    if ( ! bb_child_is_roi_rechner_page() ) {
        return $title_parts;
    }
    $title_parts['title'] = 'ROI-Rechner Reinigungsroboter – kostenlos & unabhängig';
    return $title_parts;
});

add_filter('rank_math/frontend/title', function ( $title ) {
    if ( ! bb_child_is_roi_rechner_page() ) {
        return $title;
    }
    return 'ROI-Rechner Reinigungsroboter – kostenlos & unabhängig';
});

/**
 * 2) Meta Description – via wp_head + RankMath filter.
 */
add_filter('rank_math/frontend/description', function ( $description ) {
    if ( ! bb_child_is_roi_rechner_page() ) {
        return $description;
    }
    return 'Herstellerunabhängiger ROI-Rechner für Reinigungsroboter ✓ Amortisation ✓ Break-Even ✓ 5-Jahres-Projektion ✓ Kauf vs. Leasing – jetzt kostenlos berechnen.';
});

add_action('wp_head', function () {
    if ( ! bb_child_is_roi_rechner_page() ) {
        return;
    }
    // Fallback meta description in case RankMath does not output one
    if ( ! class_exists('RankMath') ) {
        echo '<meta name="description" content="Herstellerunabhängiger ROI-Rechner für Reinigungsroboter ✓ Amortisation ✓ Break-Even ✓ 5-Jahres-Projektion ✓ Kauf vs. Leasing – jetzt kostenlos berechnen." />' . "\n";
    }
}, 1);

/**
 * 3) FAQ (below calculator) via the_content filter.
 */
add_filter('the_content', function ( $content ) {
    if ( ! bb_child_is_roi_rechner_page() || ! is_main_query() || ! in_the_loop() ) {
        return $content;
    }

    // --- FAQ Section ---
    $faq = '<section class="rg-seo-faq" style="max-width: 960px; margin: 2rem auto; padding: 0 1rem;">
  <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">Häufige Fragen zum ROI von Reinigungsrobotern</h2>

  <details style="margin-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem;">
    <summary style="cursor: pointer; font-weight: 600; font-size: 1.05rem; margin-bottom: 0.5rem;">Was bedeutet ROI bei Reinigungsrobotern?</summary>
    <p style="margin-top: 0.5rem; line-height: 1.6;">ROI steht für Return on Investment – also das Verhältnis zwischen Ihrer Investition in einen Reinigungsroboter und dem finanziellen Nutzen, den Sie daraus ziehen. Der ROI berücksichtigt Anschaffungskosten, laufende Betriebskosten (Wartung, Strom, Verbrauchsmaterial) und die eingesparten Personalkosten. Ein positiver ROI bedeutet, dass der Roboter mehr einspart, als er kostet.</p>
  </details>

  <details style="margin-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem;">
    <summary style="cursor: pointer; font-weight: 600; font-size: 1.05rem; margin-bottom: 0.5rem;">Wie schnell amortisiert sich ein Reinigungsroboter?</summary>
    <p style="margin-top: 0.5rem; line-height: 1.6;">Die Amortisationszeit hängt stark vom Einsatzszenario ab. Bei gewerblichen Flächen ab 1.000 m² und täglichem Einsatz liegt die typische Amortisation zwischen 12 und 24 Monaten. Entscheidende Faktoren sind die eingesparten Personalstunden, der Stundenlohn Ihrer Reinigungskräfte und das gewählte Finanzierungsmodell. Unser Rechner zeigt Ihnen den exakten Break-Even-Punkt für Ihre Konfiguration.</p>
  </details>

  <details style="margin-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem;">
    <summary style="cursor: pointer; font-weight: 600; font-size: 1.05rem; margin-bottom: 0.5rem;">Kauf oder Leasing – was lohnt sich mehr?</summary>
    <p style="margin-top: 0.5rem; line-height: 1.6;">Das hängt von Ihrer Liquiditätssituation und dem Planungshorizont ab. Beim Kauf ist die Gesamtinvestition über die Lebensdauer in der Regel günstiger, dafür binden Sie Kapital. Leasing verteilt die Kosten gleichmäßig und schont das Budget, führt aber über die Laufzeit zu höheren Gesamtkosten. Im Rechner können Sie beide Varianten direkt gegenüberstellen und die Auswirkung auf den ROI vergleichen.</p>
  </details>

  <details style="margin-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem;">
    <summary style="cursor: pointer; font-weight: 600; font-size: 1.05rem; margin-bottom: 0.5rem;">Welche Kosten werden im ROI-Rechner berücksichtigt?</summary>
    <p style="margin-top: 0.5rem; line-height: 1.6;">Der Rechner erfasst alle relevanten Kostenpositionen: Anschaffungspreis oder Leasingrate, Docking-/Ladestation, Zubehör, Implementierung, Servicepakete, Stromkosten, Verbrauchsmaterial und HUB-Lizenzen. Auf der Einsparungsseite werden die eingesparten Personalstunden auf Basis Ihres Stundenlohns kalkuliert. Optional können Sie Krankheits- und Urlaubszuschläge sowie jährliche Lohnsteigerungen einbeziehen.</p>
  </details>

  <details style="margin-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem;">
    <summary style="cursor: pointer; font-weight: 600; font-size: 1.05rem; margin-bottom: 0.5rem;">Ist der ROI-Rechner wirklich herstellerunabhängig?</summary>
    <p style="margin-top: 0.5rem; line-height: 1.6;">Ja. Robo-Guru ist eine unabhängige Beratungsplattform und kein Händler oder Hersteller. Die hinterlegten Roboterdaten basieren auf öffentlich verfügbaren Herstellerangaben. Sie können alle Werte manuell überschreiben und an Ihre tatsächlichen Konditionen anpassen. Es werden keine Produkte bevorzugt oder benachteiligt.</p>
  </details>

  <details style="margin-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem;">
    <summary style="cursor: pointer; font-weight: 600; font-size: 1.05rem; margin-bottom: 0.5rem;">Für welche Branchen eignet sich der Rechner?</summary>
    <p style="margin-top: 0.5rem; line-height: 1.6;">Der ROI-Rechner ist branchenübergreifend einsetzbar. Vorkonfigurierte Branchenprofile gibt es für Industrie, Logistik, Krankenhäuser, Einzelhandel und Bürogebäude. Die Berechnung funktioniert überall dort, wo autonome Bodenreinigung manuelle Arbeit ersetzen oder ergänzen kann – von der Produktionshalle über das Einkaufszentrum bis zum Hotel.</p>
  </details>

  <details style="margin-bottom: 1rem;">
    <summary style="cursor: pointer; font-weight: 600; font-size: 1.05rem; margin-bottom: 0.5rem;">Kann ich die Ergebnisse als PDF exportieren?</summary>
    <p style="margin-top: 0.5rem; line-height: 1.6;">Registrierte Mitglieder können eine Executive-PDF mit Investitionsvergleich, Break-Even-Timeline und Einsparpotenzial herunterladen. Die PDF eignet sich als Entscheidungsvorlage für die Geschäftsführung oder als Anlage für interne Business Cases. Die Registrierung ist kostenlos.</p>
  </details>
</section>';

    return $content . $faq;
});

/**
 * 4) JSON-LD Structured Data: FAQPage + WebApplication schemas.
 */
add_action('wp_head', function () {
    if ( ! bb_child_is_roi_rechner_page() ) {
        return;
    }

    // FAQPage Schema
    $faq_schema = [
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => [
            [
                '@type'          => 'Question',
                'name'           => 'Was bedeutet ROI bei Reinigungsrobotern?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => 'ROI steht für Return on Investment – also das Verhältnis zwischen Ihrer Investition in einen Reinigungsroboter und dem finanziellen Nutzen, den Sie daraus ziehen. Der ROI berücksichtigt Anschaffungskosten, laufende Betriebskosten (Wartung, Strom, Verbrauchsmaterial) und die eingesparten Personalkosten. Ein positiver ROI bedeutet, dass der Roboter mehr einspart, als er kostet.',
                ],
            ],
            [
                '@type'          => 'Question',
                'name'           => 'Wie schnell amortisiert sich ein Reinigungsroboter?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => 'Die Amortisationszeit hängt stark vom Einsatzszenario ab. Bei gewerblichen Flächen ab 1.000 m² und täglichem Einsatz liegt die typische Amortisation zwischen 12 und 24 Monaten. Entscheidende Faktoren sind die eingesparten Personalstunden, der Stundenlohn Ihrer Reinigungskräfte und das gewählte Finanzierungsmodell.',
                ],
            ],
            [
                '@type'          => 'Question',
                'name'           => 'Kauf oder Leasing – was lohnt sich mehr?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => 'Das hängt von Ihrer Liquiditätssituation und dem Planungshorizont ab. Beim Kauf ist die Gesamtinvestition über die Lebensdauer in der Regel günstiger, dafür binden Sie Kapital. Leasing verteilt die Kosten gleichmäßig und schont das Budget, führt aber über die Laufzeit zu höheren Gesamtkosten.',
                ],
            ],
            [
                '@type'          => 'Question',
                'name'           => 'Welche Kosten werden im ROI-Rechner berücksichtigt?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => 'Der Rechner erfasst alle relevanten Kostenpositionen: Anschaffungspreis oder Leasingrate, Docking-/Ladestation, Zubehör, Implementierung, Servicepakete, Stromkosten, Verbrauchsmaterial und HUB-Lizenzen. Auf der Einsparungsseite werden die eingesparten Personalstunden auf Basis Ihres Stundenlohns kalkuliert.',
                ],
            ],
            [
                '@type'          => 'Question',
                'name'           => 'Ist der ROI-Rechner wirklich herstellerunabhängig?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => 'Ja. Robo-Guru ist eine unabhängige Beratungsplattform und kein Händler oder Hersteller. Die hinterlegten Roboterdaten basieren auf öffentlich verfügbaren Herstellerangaben. Sie können alle Werte manuell überschreiben und an Ihre tatsächlichen Konditionen anpassen.',
                ],
            ],
            [
                '@type'          => 'Question',
                'name'           => 'Für welche Branchen eignet sich der Rechner?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => 'Der ROI-Rechner ist branchenübergreifend einsetzbar. Vorkonfigurierte Branchenprofile gibt es für Industrie, Logistik, Krankenhäuser, Einzelhandel und Bürogebäude. Die Berechnung funktioniert überall dort, wo autonome Bodenreinigung manuelle Arbeit ersetzen oder ergänzen kann.',
                ],
            ],
            [
                '@type'          => 'Question',
                'name'           => 'Kann ich die Ergebnisse als PDF exportieren?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => 'Registrierte Mitglieder können eine Executive-PDF mit Investitionsvergleich, Break-Even-Timeline und Einsparpotenzial herunterladen. Die PDF eignet sich als Entscheidungsvorlage für die Geschäftsführung oder als Anlage für interne Business Cases. Die Registrierung ist kostenlos.',
                ],
            ],
        ],
    ];

    // WebApplication Schema
    $webapp_schema = [
        '@context'            => 'https://schema.org',
        '@type'               => 'WebApplication',
        'name'                => 'ROI-Rechner für Reinigungsroboter',
        'url'                 => 'https://robo-guru.de/roi-rechner/',
        'applicationCategory' => 'BusinessApplication',
        'operatingSystem'     => 'Web',
        'offers'              => [
            '@type'         => 'Offer',
            'price'         => '0',
            'priceCurrency' => 'EUR',
        ],
        'description'         => 'Herstellerunabhängiger ROI-Rechner für Reinigungsroboter mit Amortisation, Break-Even-Analyse, 5-Jahres-Projektion und Kauf-vs-Leasing-Vergleich.',
        'provider'            => [
            '@type' => 'Organization',
            'name'  => 'Robo-Guru',
            'url'   => 'https://robo-guru.de',
        ],
    ];

    echo '<script type="application/ld+json">' . wp_json_encode( $faq_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
    echo '<script type="application/ld+json">' . wp_json_encode( $webapp_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
}, 5);

?>

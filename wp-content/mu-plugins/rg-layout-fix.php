<?php
/**
 * Plugin Name: RG Layout Fix
 * Description: Behebt Scrollbar-Sprung, horizontalen Overflow auf Mobile und sorgt für einheitliche Breiten/Abstände auf allen Hauptseiten.
 * Version: 1.0.0
 * Author: robo-guru.de
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'wp_head', function () {
    ?>
    <style id="rg-layout-fix">

    /* =============================================
       1. Scrollbar-Sprung verhindern (Desktop)
       ============================================= */
    html {
        scrollbar-gutter: stable;
    }

    /* =============================================
       2. Horizontalen Overflow global unterbinden
       ============================================= */
    body {
        overflow-x: hidden !important;
    }

    /* =============================================
       3. Robo-Finder Modal: 100vw-Bug auf Mobile
       ============================================= */
    @media (max-width: 720px) {
        .rf-modal {
            width: 100% !important;
        }
    }

    /* =============================================
       4. Navigation overflow nur auf Desktop
       ============================================= */
    .site-header .main-navigation .primary-menu.bb-primary-overflow {
        overflow: hidden;
    }
    @media (min-width: 821px) {
        .site-header .main-navigation .primary-menu.bb-primary-overflow {
            overflow: visible !important;
        }
    }

    /* =============================================
       5. Einheitliche Content-Breite & Padding
          auf allen Seiten (mit und ohne Buddypanel)
       ============================================= */

    /* Desktop: Konsistente max-width für den Content-Bereich */
    @media (min-width: 821px) {
        .site-content .container,
        .site-content .bb-grid.site-content-grid {
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Einheitliches Seitenpadding */
        .site-content:not(.maintenance-content) {
            padding-left: 20px;
            padding-right: 20px;
        }
    }

    /* Tablet */
    @media (min-width: 600px) and (max-width: 820px) {
        .site-content:not(.maintenance-content) {
            padding-left: 16px;
            padding-right: 16px;
        }

        .site-content .container {
            max-width: 100%;
        }
    }

    /* Mobile: Gleicher Abstand auf allen Seiten */
    @media (max-width: 599px) {
        .site-content:not(.maintenance-content) {
            padding-left: 12px;
            padding-right: 12px;
        }

        .site-content .container {
            max-width: 100%;
            padding-left: 0;
            padding-right: 0;
        }

        /* Entry-Content nicht über Viewport hinaus */
        .entry-content,
        .entry-content > * {
            max-width: 100%;
            box-sizing: border-box;
        }

        /* Bilder & Embeds responsive halten */
        .entry-content img,
        .entry-content iframe,
        .entry-content video {
            max-width: 100% !important;
            height: auto;
        }

        /* Tabellen horizontal scrollbar statt Overflow */
        .entry-content table {
            display: block;
            overflow-x: auto;
            max-width: 100%;
        }
    }

    </style>
    <?php
}, 9999 ); // Spät laden = überschreibt Theme-CSS

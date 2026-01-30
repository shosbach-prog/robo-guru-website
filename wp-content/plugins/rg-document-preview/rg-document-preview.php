<?php
/**
 * Plugin Name: RG Document Preview
 * Description: Enhanced document preview with PDF thumbnails and lightbox for BuddyBoss
 * Version: 1.0.0
 * Author: Robo-Guru
 * Text Domain: rg-doc-preview
 */

if (!defined('ABSPATH')) { exit; }

final class RG_Document_Preview {
    const VERSION = '1.0.0';

    public static function init() {
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
        add_action('wp_footer', [__CLASS__, 'render_lightbox']);
    }

    public static function enqueue_assets() {
        // Only load on BuddyBoss document pages
        if (!function_exists('bp_is_user') && !function_exists('bp_is_group')) {
            return;
        }

        $base_url = plugin_dir_url(__FILE__);

        // PDF.js for rendering PDF previews
        wp_enqueue_script(
            'pdfjs',
            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js',
            [],
            '3.11.174',
            true
        );

        // Our enhancement script
        wp_enqueue_script(
            'rg-doc-preview',
            $base_url . 'assets/preview.js',
            ['jquery', 'pdfjs'],
            self::VERSION,
            true
        );

        // Our styles
        wp_enqueue_style(
            'rg-doc-preview',
            $base_url . 'assets/preview.css',
            [],
            self::VERSION
        );

        wp_localize_script('rg-doc-preview', 'rgDocPreview', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'pdfWorkerSrc' => 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js',
        ]);
    }

    public static function render_lightbox() {
        if (!function_exists('bp_is_user') && !function_exists('bp_is_group')) {
            return;
        }
        ?>
        <div id="rg-pdf-lightbox" class="rg-pdf-lightbox" style="display:none;">
            <div class="rg-pdf-lightbox__backdrop"></div>
            <div class="rg-pdf-lightbox__container">
                <div class="rg-pdf-lightbox__header">
                    <span class="rg-pdf-lightbox__title"></span>
                    <div class="rg-pdf-lightbox__controls">
                        <button class="rg-pdf-lightbox__btn" data-action="zoom-out" title="Verkleinern">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                        </button>
                        <span class="rg-pdf-lightbox__zoom">100%</span>
                        <button class="rg-pdf-lightbox__btn" data-action="zoom-in" title="Vergrößern">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                        </button>
                        <button class="rg-pdf-lightbox__btn" data-action="download" title="Herunterladen">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        </button>
                        <button class="rg-pdf-lightbox__btn rg-pdf-lightbox__close" data-action="close" title="Schließen">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>
                </div>
                <div class="rg-pdf-lightbox__body">
                    <button class="rg-pdf-lightbox__nav rg-pdf-lightbox__prev" data-action="prev" title="Vorherige Seite">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    </button>
                    <div class="rg-pdf-lightbox__canvas-wrap">
                        <canvas id="rg-pdf-canvas"></canvas>
                        <div class="rg-pdf-lightbox__loading">
                            <div class="rg-pdf-lightbox__spinner"></div>
                            <span>PDF wird geladen...</span>
                        </div>
                    </div>
                    <button class="rg-pdf-lightbox__nav rg-pdf-lightbox__next" data-action="next" title="Nächste Seite">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>
                <div class="rg-pdf-lightbox__footer">
                    <span class="rg-pdf-lightbox__page-info">Seite <span class="rg-pdf-page-current">1</span> von <span class="rg-pdf-page-total">1</span></span>
                </div>
            </div>
        </div>
        <?php
    }
}

RG_Document_Preview::init();

<?php
/**
 * Plugin Name: Robo-Guru ROI Kalkulator
 * Description: Einfacher ROI-Kalkulator für Reinigungsrobotik inkl. PDF-Download, Druckansicht und Versand per E-Mail (PDF-Anhang). Shortcode: [rg_roi_calculator]
 * Version: 3.0.0
 * Author: Robo-Guru
 * Text Domain: rg-roi
 */

if (!defined('ABSPATH')) { exit; }

final class RG_ROI_Calculator {
    const VERSION = '3.2.0';
    const NONCE_ACTION = 'rg_roi_nonce';
    const OPTION_GROUP = 'rg_roi_options';
    const OPTION_CC_EMAIL = 'rg_roi_cc_email';
    const OPTION_ROBOTS = 'rg_roi_robots';

    // Leasing parameters
    const LEASING_RESIDUAL_PERCENT = 5;   // 5% Restwert
    const LEASING_INTEREST_RATE = 6;      // 6% p.a. Verzinsung

    // Avoid strict return types for broader PHP compatibility on WordPress hosts
    public static function init() {
        add_shortcode('rg_roi_calculator', [__CLASS__, 'shortcode']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_assets']);

        add_action('wp_ajax_rg_send_roi_report', [__CLASS__, 'ajax_send_report']);
        add_action('wp_ajax_nopriv_rg_send_roi_report', [__CLASS__, 'ajax_send_report']);

        add_action('wp_ajax_rg_save_roi_to_profile', [__CLASS__, 'ajax_save_to_profile']);

        // Logo upload for customers
        add_action('wp_ajax_rg_upload_logo', [__CLASS__, 'ajax_upload_logo']);
        add_action('wp_ajax_rg_delete_logo', [__CLASS__, 'ajax_delete_logo']);

        // Admin robot management
        add_action('wp_ajax_rg_save_robot', [__CLASS__, 'ajax_save_robot']);
        add_action('wp_ajax_rg_delete_robot', [__CLASS__, 'ajax_delete_robot']);

        add_action('admin_menu', [__CLASS__, 'admin_menu']);
        add_action('admin_init', [__CLASS__, 'register_settings']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'admin_enqueue_scripts']);

        // REST API for robots
        add_action('rest_api_init', [__CLASS__, 'register_rest_routes']);
    }

    /**
     * Register REST API routes for robot data
     */
    public static function register_rest_routes() {
        register_rest_route('rg-roi/v1', '/robots', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'rest_get_robots'],
            'permission_callback' => '__return_true',
        ]);
    }

    /**
     * REST API: Get all robots from CPT robo_robot with ROI data
     */
    public static function rest_get_robots() {
        $robots = [];

        $query = new WP_Query([
            'post_type' => 'robo_robot',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
        ]);

        while ($query->have_posts()) {
            $query->the_post();
            $id = get_the_ID();

            // Get thumbnail
            $thumb_id = get_post_thumbnail_id($id);
            $thumb_url = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'thumbnail') : '';

            // Get ROI meta fields
            $list_price = floatval(get_post_meta($id, '_rf_list_price', true));
            $m2h = floatval(get_post_meta($id, '_rf_m2h', true));
            $battery_hours = floatval(get_post_meta($id, '_rf_battery_hours', true)) ?: 4;
            $power_watts = floatval(get_post_meta($id, '_rf_power_watts', true));
            $consumables = floatval(get_post_meta($id, '_rf_consumables_per_1000m2', true));
            $recommended_area = get_post_meta($id, '_rf_recommended_area', true);

            // Service costs
            $service_basic = floatval(get_post_meta($id, '_rf_service_basic', true));
            $service_standard = floatval(get_post_meta($id, '_rf_service_standard', true));
            $service_premium = floatval(get_post_meta($id, '_rf_service_premium', true));

            // One-time costs
            $docking_station = floatval(get_post_meta($id, '_rf_docking_station', true));
            $accessories_cost = floatval(get_post_meta($id, '_rf_accessories_cost', true));
            $implementation_cost = floatval(get_post_meta($id, '_rf_implementation_cost', true));

            // Calculate power cost per year (Watts × hours/day × 260 days × 0.30 €/kWh)
            $power_yearly = 0;
            if ($power_watts > 0) {
                $power_yearly = round(($power_watts / 1000) * $battery_hours * 260 * 0.30, 2);
            }

            // Calculate leasing rates
            $lease24 = self::calculate_leasing_rate($list_price, 24);
            $lease36 = self::calculate_leasing_rate($list_price, 36);
            $lease48 = self::calculate_leasing_rate($list_price, 48);
            $lease60 = self::calculate_leasing_rate($list_price, 60);

            $robots[] = [
                'id' => $id,
                'name' => get_the_title(),
                'slug' => get_post_field('post_name', $id),
                'thumbnail' => $thumb_url,
                'manufacturer' => get_post_meta($id, '_rf_manufacturer', true),
                'segment' => get_post_meta($id, '_rf_segment', true),
                'list_price' => $list_price,
                'm2h' => $m2h,
                'battery_hours' => $battery_hours,
                'power_watts' => $power_watts,
                'power_yearly' => $power_yearly,
                'consumables_per_1000m2' => $consumables,
                'recommended_area' => $recommended_area,
                'service_basic' => $service_basic,
                'service_standard' => $service_standard,
                'service_premium' => $service_premium,
                'docking_station' => $docking_station,
                'accessories_cost' => $accessories_cost,
                'implementation_cost' => $implementation_cost,
                'lease24' => $lease24,
                'lease36' => $lease36,
                'lease48' => $lease48,
                'lease60' => $lease60,
            ];
        }
        wp_reset_postdata();

        return rest_ensure_response($robots);
    }

    /**
     * Calculate monthly leasing rate
     * @param float $price Purchase price
     * @param int $months Lease term in months
     * @return float Monthly rate
     */
    public static function calculate_leasing_rate($price, $months = 36) {
        if ($price <= 0 || $months <= 0) return 0;

        $residual_percent = self::LEASING_RESIDUAL_PERCENT / 100;
        $annual_interest = self::LEASING_INTEREST_RATE / 100;
        $monthly_interest = $annual_interest / 12;

        // Residual value at end of lease
        $residual_value = $price * $residual_percent;

        // Amount to be financed (depreciation)
        $depreciation_total = $price - $residual_value;
        $monthly_depreciation = $depreciation_total / $months;

        // Interest on average outstanding balance
        $average_balance = ($price + $residual_value) / 2;
        $monthly_interest_amount = $average_balance * $monthly_interest;

        // Total monthly rate
        return round($monthly_depreciation + $monthly_interest_amount, 2);
    }

    /**
     * Get all robots from options
     */
    public static function get_robots() {
        $robots = get_option(self::OPTION_ROBOTS, []);
        return is_array($robots) ? $robots : [];
    }

    /**
     * Enqueue admin scripts
     */
    public static function admin_enqueue_scripts($hook) {
        if ($hook !== 'settings_page_rg-roi') return;

        wp_enqueue_style('rg-roi-admin', plugin_dir_url(__FILE__) . 'assets/admin.css', [], self::VERSION);
        wp_enqueue_script('rg-roi-admin', plugin_dir_url(__FILE__) . 'assets/admin.js', ['jquery'], self::VERSION, true);
        wp_localize_script('rg-roi-admin', 'rgRoiAdmin', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('rg_roi_admin_nonce'),
            'leasingResidual' => self::LEASING_RESIDUAL_PERCENT,
            'leasingInterest' => self::LEASING_INTEREST_RATE,
        ]);
    }

    public static function enqueue_assets() {
        if (!is_singular() && !is_page()) return;

        // Only enqueue when shortcode is present on the current post content
        $post = get_post();
        if (!$post || strpos((string)$post->post_content, '[rg_roi_calculator') === false) return;

        $base_url = plugin_dir_url(__FILE__);
        $base_path = plugin_dir_path(__FILE__);

        // jsPDF + autoTable (CDN). You can override via filters if needed.
        $jspdf = apply_filters('rg_roi_jspdf_url', 'https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js');
        $autotable = apply_filters('rg_roi_autotable_url', 'https://cdn.jsdelivr.net/npm/jspdf-autotable@3.5.29/dist/jspdf.plugin.autotable.min.js');
        $chartjs = apply_filters('rg_roi_chartjs_url', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js');
        $qrcode = apply_filters('rg_roi_qrcode_url', 'https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js');

        wp_enqueue_style('rg-roi', $base_url . 'assets/roi.css', [], self::VERSION);

        wp_enqueue_script('rg-jspdf', $jspdf, [], null, true);
        wp_enqueue_script('rg-jspdf-autotable', $autotable, ['rg-jspdf'], null, true);
        wp_enqueue_script('rg-chartjs', $chartjs, [], null, true);
        wp_enqueue_script('rg-qrcode', $qrcode, [], null, true);

        wp_enqueue_script(
            'rg-roi',
            $base_url . 'assets/roi.js',
            ['rg-jspdf', 'rg-jspdf-autotable', 'rg-chartjs', 'rg-qrcode'],
            self::VERSION,
            true
        );

        $user_name = '';
        $user_logo_url = '';
        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            $user_name = trim($current_user->display_name);
            if (empty($user_name)) {
                $user_name = trim($current_user->first_name . ' ' . $current_user->last_name);
            }
            if (empty($user_name)) {
                $user_name = $current_user->user_login;
            }
            // Get user's custom logo
            $logo_id = get_user_meta(get_current_user_id(), 'rg_customer_logo_id', true);
            if ($logo_id) {
                $user_logo_url = wp_get_attachment_image_url($logo_id, 'medium');
            }
        }

        wp_localize_script('rg-roi', 'rgRoi', [
            'ajaxUrl'    => admin_url('admin-ajax.php'),
            'restUrl'    => rest_url('rg-roi/v1/'),
            'nonce'      => wp_create_nonce(self::NONCE_ACTION),
            'restNonce'  => wp_create_nonce('wp_rest'),
            'ccEmail'    => get_option(self::OPTION_CC_EMAIL, ''),
            'siteName'   => get_bloginfo('name'),
            'isLoggedIn' => is_user_logged_in() ? '1' : '0',
            'hasBbDocs'  => function_exists('bp_document_add') ? '1' : '0',
            'userName'   => $user_name,
            'userLogoUrl' => $user_logo_url,
        ]);
    }

    public static function shortcode($atts = []) {
        $atts = shortcode_atts([
            'title' => 'ROI-Kalkulator für Reinigungsroboter',
            'subtitle' => 'Unabhängige Beispielrechnung zur Wirtschaftlichkeit',
        ], $atts, 'rg_roi_calculator');

        ob_start();
        ?>
        <div class="rg-roi rg-roi-page" data-rg-roi>

            <!-- Beratermodus Toggle (top bar) -->
            <div class="rg-advisor-bar">
                <label class="rg-advisor-toggle">
                    <input type="checkbox" data-rg="advisorMode">
                    <span class="rg-advisor-toggle__slider"></span>
                    <span class="rg-advisor-toggle__label">Beratermodus</span>
                    <span class="rg-tooltip" data-tip="Aktiviert Branchenprofile und Schnell-Szenarien im Ergebnis-Panel, um verschiedene Szenarien schnell durchzuspielen." tabindex="0" role="img" aria-label="Hilfe">?</span>
                </label>
            </div>

            <div class="rg-roi-layout">

            <!-- LEFT COLUMN: Inputs -->
            <div class="rg-roi-inputs">
            <?php if (is_user_logged_in()) : ?>
            <!-- Logo Upload für PDF-Berichte -->
            <div class="rg-logo-upload-box">
                <h5>&#128247; Ihr Firmenlogo für PDF-Berichte</h5>
                <div class="rg-logo-preview" id="rg_logo_preview">
                    <?php
                    $logo_id = get_user_meta(get_current_user_id(), 'rg_customer_logo_id', true);
                    if ($logo_id) {
                        echo wp_get_attachment_image($logo_id, 'medium', false, ['style' => 'max-height:80px;']);
                    } else {
                        echo '<span class="rg-logo-placeholder">Kein Logo hochgeladen</span>';
                    }
                    ?>
                </div>
                <div class="rg-logo-actions">
                    <input type="file" id="rg_logo_upload" accept="image/png,image/jpeg,image/webp" style="display:none;">
                    <button type="button" class="rg-btn rg-btn--small" id="rg_logo_select">Logo auswählen</button>
                    <button type="button" class="rg-btn rg-btn--small rg-btn--primary" id="rg_logo_save" style="display:none;">Speichern</button>
                    <?php if ($logo_id) : ?>
                    <button type="button" class="rg-btn rg-btn--small rg-btn--danger" id="rg_logo_delete">Entfernen</button>
                    <?php endif; ?>
                </div>
                <p class="rg-logo-hint">PNG, JPG oder WebP, max. 2 MB. Das Logo erscheint oben rechts in Ihrem PDF-Bericht.</p>
            </div>
            <?php else : ?>
            <!-- Hinweis für nicht-eingeloggte Benutzer -->
            <div class="rg-login-hint">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <div>
                    <strong><a href="<?php echo esc_url(wp_login_url(get_permalink())); ?>">Jetzt einloggen</a></strong> &ndash; Ihr Firmenlogo im PDF anzeigen, Berechnungen im Profil speichern und mehr.
                </div>
            </div>
            <?php endif; ?>

            <!-- Comparison table moved to section below inputs -->

                <!-- Section 1: Szenario -->
                <div class="rg-section rg-section--open" data-rg-section="scenario">
                    <h4 class="rg-section__title">Szenario</h4>

                    <div class="rg-robot-section">
                        <div class="rg-robot-header">
                            <p class="rg-robot-subtitle">Roboter wählen – Werte werden automatisch befüllt.</p>
                        </div>
                        <div class="rg-robot-grid" data-rg-robots>
                            <div class="rg-robot-loading">Roboter werden geladen...</div>
                        </div>
                    </div>

                    <div class="rg-scenario-row">
                        <div class="rg-field-group rg-field-group--qty">
                            <label>Anzahl Roboter</label>
                            <div class="rg-stepper">
                                <button type="button" class="rg-stepper__btn" data-rg-stepper="qty" data-dir="-1" aria-label="Weniger">−</button>
                                <input type="number" class="rg-in rg-stepper__input" data-rg="qty" value="1" min="1" step="1">
                                <button type="button" class="rg-stepper__btn" data-rg-stepper="qty" data-dir="1" aria-label="Mehr">+</button>
                            </div>
                        </div>
                        <div class="rg-field-group rg-field-group--setup">
                            <label>Implementierung <span class="rg-tooltip" data-tip="Einmalig: Setup-Kosten fallen nur 1x an. Pro Roboter: Setup-Kosten pro Gerät." tabindex="0" role="img" aria-label="Hilfe">?</span></label>
                            <div class="rg-toggle-switch" data-rg-setup-mode role="group" aria-label="Implementierungsmodus">
                                <button type="button" class="rg-toggle-switch__opt is-active" data-val="once" aria-pressed="true">Einmalig</button>
                                <button type="button" class="rg-toggle-switch__opt" data-val="per_robot" aria-pressed="false">Pro Roboter</button>
                                <input type="hidden" class="rg-in" data-rg="setupMode" value="once">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Kernannahmen -->
                <div class="rg-section rg-section--open" data-rg-section="core">
                    <h4 class="rg-section__title">Kernannahmen</h4>
                    <div class="rg-section__body">
                        <label>Zu reinigende Fläche pro Tag (m²) <span class="rg-tooltip" data-tip="Wie viele Quadratmeter sollen täglich gereinigt werden? Bei Roboterauswahl wird daraus die benötigte Zeit abgeleitet." tabindex="0" role="img" aria-label="Hilfe">?</span>
                            <input type="number" class="rg-in" data-rg="areaSqmPerDay" value="1500" min="0" step="50" aria-describedby="sqmHint">
                            <span class="rg-field-warn" data-rg-warn="areaSqmPerDay"></span>
                        </label>
                        <label>Eingesparte Stunden pro Tag / Roboter <span class="rg-tooltip" data-tip="Wie viele Arbeitsstunden pro Tag kann ein Roboter einsparen? Wird bei Roboterauswahl automatisch aus Fläche und m²/h berechnet." tabindex="0" role="img" aria-label="Hilfe">?</span>
                            <input type="number" class="rg-in" data-rg="hoursPerDay" value="2.5" min="0" step="0.1">
                            <span class="rg-field-warn" data-rg-warn="hoursPerDay"></span>
                        </label>
                        <label>Lohnkosten pro Stunde inkl. Nebenkosten (€) <span class="rg-tooltip" data-tip="Vollkosten einer Reinigungskraft pro Stunde, inklusive Lohnnebenkosten (Sozialabgaben, Versicherung etc.). Üblich: 18–35 €/h." tabindex="0" role="img" aria-label="Hilfe">?</span>
                            <input type="number" class="rg-in" data-rg="hourlyRate" value="22" min="0" step="0.5">
                            <span class="rg-field-warn" data-rg-warn="hourlyRate"></span>
                        </label>
                        <label>Arbeitstage pro Jahr <span class="rg-tooltip" data-tip="Anzahl der Tage im Jahr, an denen gereinigt wird. Typisch: 260 (Mo-Fr) oder 365 (7 Tage/Woche)." tabindex="0" role="img" aria-label="Hilfe">?</span>
                            <input type="number" class="rg-in" data-rg="daysPerYear" value="260" min="0" step="1">
                        </label>
                        <div class="rg-note" data-rg-out="sqmHint">Basierend auf Ihrer Fläche und der Reinigungsleistung des Roboters.</div>
                    </div>
                </div>

                <!-- Section 3: Erweiterte Einstellungen (Akkordeon) -->
                <div class="rg-section rg-section--accordion" data-rg-section="advanced">
                    <button type="button" class="rg-section__toggle" aria-expanded="false">
                        <h4 class="rg-section__title">Erweiterte Einstellungen</h4>
                        <span class="rg-section__chevron" aria-hidden="true"></span>
                    </button>
                    <div class="rg-section__body rg-section__body--collapsed" aria-hidden="true">

                        <!-- Finanzierung -->
                        <fieldset class="rg-fieldset">
                            <legend>Finanzierung</legend>
                            <label>Modell <span class="rg-tooltip" data-tip="Kauf: einmalige Investition. Leasing: monatliche Rate über eine feste Laufzeit (5 % Restwert, 6 % p.a. Zins)." tabindex="0" role="img" aria-label="Hilfe">?</span>
                                <select class="rg-in rg-select" data-rg="mode" aria-label="Finanzierungsmodell">
                                    <option value="purchase" selected>Kauf</option>
                                    <option value="lease">Leasing</option>
                                </select>
                            </label>
                            <div class="rg-mode rg-mode--purchase" data-rg-mode="purchase">
                                <label>Kaufpreis pro Roboter (€) <span class="rg-tooltip" data-tip="Einmalige Anschaffungskosten für den Roboter. Docking-Station, Zubehör und Setup kommen als Einmalkosten hinzu." tabindex="0" role="img" aria-label="Hilfe">?</span>
                                    <input type="number" class="rg-in" data-rg="price" value="25000" min="0" step="100">
                                </label>
                                <div class="rg-note">Gesamtinvestition = Kaufpreis + Einmalkosten (Docking, Zubehör, Setup) weiter unten.</div>
                            </div>
                            <div class="rg-mode rg-mode--lease rg-hide" data-rg-mode="lease">
                                <label>Leasingrate pro Roboter / Monat (€) <span class="rg-tooltip" data-tip="Monatliche Leasingrate nur für den Roboter. Docking-Station, Zubehör und Setup sind nicht im Leasing enthalten und fallen separat als Einmalkosten an." tabindex="0" role="img" aria-label="Hilfe">?</span>
                                    <input type="number" class="rg-in" data-rg="leaseRateMonthly" value="725" min="0" step="10">
                                </label>
                                <label>Laufzeit (Monate) <span class="rg-tooltip" data-tip="Leasinglaufzeit. Kürzere Laufzeiten haben höhere Monatsraten, aber niedrigere Gesamtkosten." tabindex="0" role="img" aria-label="Hilfe">?</span>
                                    <select class="rg-in rg-select" data-rg="leaseTermMonths" aria-label="Leasinglaufzeit in Monaten">
                                        <option value="24">24 Monate</option>
                                        <option value="36" selected>36 Monate</option>
                                        <option value="48">48 Monate</option>
                                        <option value="60">60 Monate</option>
                                    </select>
                                </label>
                                <div class="rg-note" style="color:var(--rg-warning);font-weight:600">&#9888; Einmalkosten (Docking, Zubehör, Setup) sind nicht im Leasing enthalten und fallen zusätzlich an.</div>
                            </div>

                            <!-- Auto/Manual fields -->
                            <div class="rg-am-field" data-rg-am="dockingStation">
                                <div class="rg-am-field__head">
                                    <label>Docking-/Ladestation (€) <span class="rg-tooltip" data-tip="Einmalige Kosten für die Lade-/Dockingstation. Auto = Wert aus Roboterdatenbank. Manuell = eigener Wert." tabindex="0" role="img" aria-label="Hilfe">?</span></label>
                                    <div class="rg-am-switch" role="group" aria-label="Eingabemodus">
                                        <button type="button" class="rg-am-switch__btn is-active" data-am="auto" aria-pressed="true" title="Wert wird automatisch aus der Roboterdatenbank geladen">Auto</button>
                                        <button type="button" class="rg-am-switch__btn" data-am="manual" aria-pressed="false" title="Wert manuell eingeben">Manuell</button>
                                    </div>
                                </div>
                                <input type="number" class="rg-in" data-rg="dockingStation" value="0" min="0" step="100" disabled aria-label="Docking-/Ladestation Kosten">
                                <span class="rg-am-field__source">Quelle: Roboterdatenbank</span>
                            </div>
                            <div class="rg-am-field" data-rg-am="accessoriesCost">
                                <div class="rg-am-field__head">
                                    <label>Zubehör einmalig (€) <span class="rg-tooltip" data-tip="Einmalige Kosten für Zubehör (Bürsten, Pads, Erstausstattung etc.)." tabindex="0" role="img" aria-label="Hilfe">?</span></label>
                                    <div class="rg-am-switch" role="group" aria-label="Eingabemodus">
                                        <button type="button" class="rg-am-switch__btn is-active" data-am="auto" aria-pressed="true" title="Wert wird automatisch aus der Roboterdatenbank geladen">Auto</button>
                                        <button type="button" class="rg-am-switch__btn" data-am="manual" aria-pressed="false" title="Wert manuell eingeben">Manuell</button>
                                    </div>
                                </div>
                                <input type="number" class="rg-in" data-rg="accessoriesCost" value="0" min="0" step="100" disabled aria-label="Zubehör Kosten">
                                <span class="rg-am-field__source">Quelle: Roboterdatenbank</span>
                            </div>
                            <div class="rg-am-field" data-rg-am="implementationCost">
                                <div class="rg-am-field__head">
                                    <label>Implementierung / Setup (€) <span class="rg-tooltip" data-tip="Einmalige Kosten für Einrichtung, Kartierung und Schulung. Auto = Wert aus Roboterdatenbank." tabindex="0" role="img" aria-label="Hilfe">?</span></label>
                                    <div class="rg-am-switch" role="group" aria-label="Eingabemodus">
                                        <button type="button" class="rg-am-switch__btn is-active" data-am="auto" aria-pressed="true" title="Wert wird automatisch aus der Roboterdatenbank geladen">Auto</button>
                                        <button type="button" class="rg-am-switch__btn" data-am="manual" aria-pressed="false" title="Wert manuell eingeben">Manuell</button>
                                    </div>
                                </div>
                                <input type="number" class="rg-in" data-rg="implementationCost" value="0" min="0" step="100" disabled aria-label="Implementierung Kosten">
                                <span class="rg-am-field__source">Quelle: Roboterdatenbank</span>
                            </div>
                        </fieldset>

                        <!-- Reinigungsleistung -->
                        <fieldset class="rg-fieldset">
                            <legend>Reinigungsleistung <span class="rg-tooltip" data-tip="Auto/Manuell-Schalter: &laquo;Auto&raquo; = Wert wird automatisch aus der Roboterdatenbank geladen. &laquo;Manuell&raquo; = Sie k&ouml;nnen den Wert selbst eingeben." tabindex="0" role="img" aria-label="Hilfe">?</span></legend>
                            <div class="rg-am-field" data-rg-am="m2h">
                                <div class="rg-am-field__head">
                                    <label>Reinigungsleistung Roboter (m²/h)</label>
                                    <div class="rg-am-switch">
                                        <button type="button" class="rg-am-switch__btn is-active" data-am="auto">Auto</button>
                                        <button type="button" class="rg-am-switch__btn" data-am="manual">Manuell</button>
                                    </div>
                                </div>
                                <input type="number" class="rg-in" data-rg="m2h" value="1200" min="0" step="50" disabled>
                                <span class="rg-am-field__source">Quelle: Roboterdatenbank</span>
                            </div>
                        </fieldset>

                        <!-- Betriebskosten -->
                        <fieldset class="rg-fieldset">
                            <legend>Betriebskosten <span class="rg-tooltip" data-tip="Auto/Manuell-Schalter: &laquo;Auto&raquo; = Wert wird automatisch berechnet. &laquo;Manuell&raquo; = eigener Wert. Blaue Farbe = Auto-Modus aktiv." tabindex="0" role="img" aria-label="Hilfe">?</span></legend>
                            <label>Servicepaket <span class="rg-tooltip" data-tip="Monatliche Servicekosten. Basic/Standard/Premium werden aus der Roboterdatenbank geladen. 'Eigener Wert' erlaubt manuelle Eingabe." tabindex="0" role="img" aria-label="Hilfe">?</span>
                                <select class="rg-in rg-select" data-rg="servicePreset" aria-label="Servicepaket wählen">
                                    <option value="0">Kein Paket / bereits enthalten</option>
                                    <option value="basic">Basic</option>
                                    <option value="standard" selected>Standard</option>
                                    <option value="premium">Premium</option>
                                    <option value="-1">Eigener Wert</option>
                                </select>
                            </label>
                            <div class="rg-am-field" data-rg-am="serviceMonthly">
                                <div class="rg-am-field__head">
                                    <label>Servicekosten pro Roboter / Monat (€)</label>
                                    <div class="rg-am-switch">
                                        <button type="button" class="rg-am-switch__btn is-active" data-am="auto">Auto</button>
                                        <button type="button" class="rg-am-switch__btn" data-am="manual">Manuell</button>
                                    </div>
                                </div>
                                <input type="number" class="rg-in" data-rg="serviceMonthly" value="179" min="0" step="5" disabled>
                                <span class="rg-am-field__source">Quelle: Servicepaket-Auswahl</span>
                            </div>
                            <div class="rg-am-field" data-rg-am="powerPerYear">
                                <div class="rg-am-field__head">
                                    <label>Stromkosten pro Roboter / Jahr (€)</label>
                                    <div class="rg-am-switch">
                                        <button type="button" class="rg-am-switch__btn is-active" data-am="auto">Auto</button>
                                        <button type="button" class="rg-am-switch__btn" data-am="manual">Manuell</button>
                                    </div>
                                </div>
                                <input type="number" class="rg-in" data-rg="powerPerYear" value="350" min="0" step="10" disabled>
                                <span class="rg-am-field__source">Quelle: Roboterdatenbank</span>
                            </div>
                            <div class="rg-am-field" data-rg-am="consumablesPerYear">
                                <div class="rg-am-field__head">
                                    <label>Verbrauchsmaterial pro Jahr (€)</label>
                                    <div class="rg-am-switch">
                                        <button type="button" class="rg-am-switch__btn is-active" data-am="auto">Auto</button>
                                        <button type="button" class="rg-am-switch__btn" data-am="manual">Manuell</button>
                                    </div>
                                </div>
                                <input type="number" class="rg-in" data-rg="consumablesPerYear" value="0" min="0" step="50" disabled>
                                <span class="rg-am-field__source">Quelle: Flächenberechnung</span>
                            </div>
                        </fieldset>
                        <div class="rg-note" data-rg-out="investHint">–</div>
                    </div>
                </div>

                <!-- Section 4: Einwände & Szenarien (Akkordeon) -->
                <div class="rg-section rg-section--accordion" data-rg-section="objections">
                    <button type="button" class="rg-section__toggle" aria-expanded="false">
                        <h4 class="rg-section__title">Einwände & Szenarien</h4>
                        <span class="rg-section__chevron" aria-hidden="true"></span>
                    </button>
                    <div class="rg-section__body rg-section__body--collapsed" aria-hidden="true">
                        <label class="rg-toggle-label">
                            <input type="checkbox" data-rg="toggleAbsence">
                            <span>Krankheit & Urlaub berücksichtigen (+15 % Personalkosten)</span>
                        </label>
                        <label class="rg-toggle-label">
                            <input type="checkbox" data-rg="toggleWageGrowth">
                            <span>Lohnsteigerung 3 % p.a. simulieren (3-Jahres-Projektion)</span>
                        </label>
                    </div>
                </div>

                <!-- Section 5: Transparenz (immer sichtbar) -->
                <div class="rg-section rg-section--accordion" data-rg-section="transparency">
                    <button type="button" class="rg-section__toggle" aria-expanded="true">
                        <h4 class="rg-section__title">Transparenz / Annahmen</h4>
                        <span class="rg-section__chevron" aria-hidden="true"></span>
                    </button>
                    <div class="rg-section__body rg-section__body--expanded" aria-hidden="false">
                        <ul class="rg-assumptions__list">
                            <li>Konstanter Betrieb über das Jahr (Arbeitstage laut Eingabe).</li>
                            <li>Personalkosten basieren auf dem eingegebenen Stundensatz.</li>
                            <li>Service- und Stromkosten basieren auf Ihren Angaben.</li>
                            <li>Keine Förderungen, Steuern oder Restwerte berücksichtigt.</li>
                            <li>Diese Werte sind überschlägig und ersetzen kein individuelles Angebot.</li>
                        </ul>
                    </div>
                </div>

                <!-- Comparison Table: 2 Spalten -->
                <div class="rg-comparison" data-rg-comparison>
                    <h4>Mensch vs. Roboter</h4>
                    <div class="rg-comparison-table-wrap">
                        <table class="rg-comparison-table">
                            <thead>
                                <tr>
                                    <th>Kostenfaktor</th>
                                    <th class="rg-col-human">Manuell (gesamt)</th>
                                    <th class="rg-col-robot" data-rg-robot-cols>Roboterszenario</th>
                                </tr>
                            </thead>
                            <tbody data-rg-comparison-body></tbody>
                        </table>
                    </div>
                </div>

                <!-- Metadata -->
                <div class="rg-meta-row">
                    <div class="rg-meta-card">
                        <div class="rg-meta-field">
                            <label>Die Berechnung ist für Firma:
                                <input type="text" class="rg-in" data-rg="companyName" placeholder="z.B. Musterfirma GmbH">
                            </label>
                        </div>
                        <?php if (!is_user_logged_in()) : ?>
                        <div class="rg-meta-field">
                            <label>Erstellt von <span class="rg-optional">(optional)</span>
                                <input type="text" class="rg-in" data-rg="creatorName" placeholder="Vor- und Nachname">
                            </label>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div><!-- /.rg-roi-inputs -->

            <!-- RIGHT COLUMN: Sticky Summary -->
            <div class="rg-roi-summary" data-rg-summary>
                <div class="rg-summary-inner">

                    <div class="rg-result__head">
                        <h4>Ergebnis</h4>
                        <div class="rg-result__tag">Unabhängige Beispielrechnung</div>
                    </div>

                    <!-- Beratermodus: Presets + Szenarien (hidden by default, shown at top when active) -->
                    <div class="rg-advisor-panel rg-hide" data-rg-advisor-panel>
                        <div class="rg-quickprofiles">
                            <label>Branchenprofil <span class="rg-tooltip" data-tip="Wählen Sie ein Branchenprofil, um Fläche, Stundenlohn, Arbeitstage und Stunden/Tag automatisch mit branchentypischen Werten vorzubelegen." tabindex="0" role="img" aria-label="Hilfe">?</span>
                                <select class="rg-in rg-select" data-rg="presetProfile">
                                    <option value="">— Profil wählen —</option>
                                    <option value="industry">Industrie 1.500 m²</option>
                                    <option value="logistics">Logistik 3.000 m²</option>
                                    <option value="hospital">Krankenhaus 2.000 m²</option>
                                    <option value="retail">Einzelhandel 800 m²</option>
                                    <option value="office">Bürogebäude 1.200 m²</option>
                                </select>
                            </label>
                        </div>
                        <div class="rg-scenario-buttons">
                            <span class="rg-scenario-label">Schnell-Szenarien: <span class="rg-tooltip" data-tip="Klicken Sie auf ein Szenario, um einzelne Werte schnell anzupassen und deren Auswirkung auf das Ergebnis zu sehen." tabindex="0" role="img" aria-label="Hilfe">?</span></span>
                            <button type="button" class="rg-scenario-btn" data-rg-scenario="plus200" title="Erhöht die tägliche Reinigungsfläche um 200 m²">+200 m²</button>
                            <button type="button" class="rg-scenario-btn" data-rg-scenario="plus1h" title="Erhöht die eingesparten Stunden pro Tag um 1 Stunde">+1,0 h/Tag</button>
                            <button type="button" class="rg-scenario-btn" data-rg-scenario="twoRobots" title="Setzt die Anzahl auf 2 Roboter">2 Roboter</button>
                            <button type="button" class="rg-scenario-btn" data-rg-scenario="leaseMode" title="Wechselt das Finanzierungsmodell auf Leasing (36 Monate)">Leasing 36M</button>
                            <button type="button" class="rg-scenario-btn rg-scenario-btn--reset" data-rg-scenario="reset" title="Setzt Fläche, Stunden, Roboteranzahl und Finanzierungsmodell auf die ursprünglichen Werte zurück">&#8634; Zurücksetzen</button>
                        </div>
                    </div>

                    <!-- ROI Dashboard -->
                    <div class="rg-roi-dashboard">
                        <div class="rg-roi-highlight">
                            <h2 data-rg-out="netHighlight">– €</h2>
                            <p class="rg-roi-highlight__sub">Netto-Ersparnis pro Jahr</p>
                            <p class="rg-roi-highlight__monthly">&#8776; <span data-rg-out="monthlyHighlight">–</span> / Monat</p>
                        </div>
                        <div class="rg-roi-keyfacts">
                            <div class="rg-keyfact">
                                <span class="rg-keyfact__value" data-rg-out="breakEvenMonths">–</span>
                                <span class="rg-keyfact__label">Break-even (Monate)</span>
                            </div>
                            <div class="rg-keyfact">
                                <span class="rg-keyfact__value" data-rg-out="roi">–</span>
                                <span class="rg-keyfact__label">ROI</span>
                            </div>
                            <div class="rg-keyfact">
                                <span class="rg-keyfact__value" data-rg-out="fteEquivalent">–</span>
                                <span class="rg-keyfact__label">FTE</span>
                            </div>
                        </div>
                    </div>

                    <!-- Status Ampel -->
                    <div class="rg-rating" data-rg-out="ratingWrap" data-level="ok">
                        <div class="rg-rating__dot" aria-hidden="true"></div>
                        <div class="rg-rating__content">
                            <div class="rg-rating__label" data-rg-out="ratingLabel">–</div>
                            <div class="rg-rating__text" data-rg-out="ratingText">–</div>
                        </div>
                    </div>

                    <div class="rg-warn" data-rg-out="warn" style="display:none;">
                        Hinweis: Mit den aktuellen Angaben entsteht keine positive Netto-Ersparnis.
                        <div class="rg-warn__detail" data-rg-out="warnDetail">Mögliche Ursachen: Die Betriebskosten (Service, Strom, Leasing) übersteigen die eingesparten Personalkosten. Prüfen Sie Stundenlohn, Einsatzstunden oder Roboterkosten.</div>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="rg-actions">
                        <button class="rg-btn rg-btn--primary" data-rg-btn="pdf" disabled><span class="rg-ico">&#128196;</span><span>PDF</span></button>
                        <button class="rg-btn" data-rg-btn="print" disabled><span class="rg-ico">&#128424;</span><span>Drucken</span></button>
                        <?php if (is_user_logged_in() && function_exists('bp_document_add')) : ?>
                        <button class="rg-btn rg-btn--save" data-rg-btn="save" disabled><span class="rg-ico">&#128190;</span><span>Im Profil speichern</span></button>
                        <?php endif; ?>
                    </div>
                    <div class="rg-hint" data-rg-out="hint">
                        Export ist aktiv, sobald eine positive Netto-Ersparnis berechnet wurde.
                    </div>

                    <!-- Break-even Chart -->
                    <div class="rg-chart-container">
                        <canvas id="rgBreakEvenChart" data-rg-chart="breakeven"></canvas>
                    </div>

                    <!-- Details -->
                    <details class="rg-details">
                        <summary>Details der Berechnung</summary>
                        <div class="rg-details__grid">
                            <div class="rg-kpi"><div class="rg-k">Finanzierung</div><div class="rg-v" data-rg-out="finModel">–</div></div>
                            <div class="rg-kpi"><div class="rg-k">Investition / Vertragsvolumen</div><div class="rg-v" data-rg-out="invest">–</div></div>
                            <div class="rg-kpi"><div class="rg-k">Ersparnis/Jahr (brutto)</div><div class="rg-v" data-rg-out="gross">–</div></div>
                            <div class="rg-kpi"><div class="rg-k">Service+Strom/Jahr</div><div class="rg-v" data-rg-out="ops">–</div></div>
                            <div class="rg-kpi"><div class="rg-k">Leasingkosten/Jahr</div><div class="rg-v" data-rg-out="leaseYear">–</div></div>
                            <div class="rg-kpi"><div class="rg-k">Netto-Ersparnis/Jahr</div><div class="rg-v" data-rg-out="net">–</div></div>
                            <div class="rg-kpi"><div class="rg-k">Fläche (m²/Tag)</div><div class="rg-v" data-rg-out="area">–</div></div>
                            <div class="rg-kpi"><div class="rg-k">abgeleitet (m²/h)</div><div class="rg-v" data-rg-out="sqmPerHour">–</div></div>
                        </div>
                    </details>

                    <div class="rg-be" aria-live="polite">
                        <div class="rg-be__badge">Break-even</div>
                        <div class="rg-be__text" data-rg-out="beText">–</div>
                    </div>

                    <div class="rg-disclaimer">
                        Dieser Kalkulator dient zur überschlägigen Bewertung und ersetzt keine individuelle Projektprüfung.
                    </div>

                </div><!-- /.rg-summary-inner -->

                <!-- Mobile bottom-sheet handle -->
                <div class="rg-summary-handle" data-rg-summary-handle>
                    <div class="rg-summary-handle__bar"></div>
                    <div class="rg-summary-handle__peek">
                        <span data-rg-out="netPeek">– €</span> / Jahr
                    </div>
                </div>

            </div><!-- /.rg-roi-summary -->

            </div><!-- /.rg-roi-layout -->
        </div>
        <?php
        return (string)ob_get_clean();
    }

    public static function admin_menu() {
        add_options_page(
            'Robo-Guru ROI Kalkulator',
            'Robo-Guru ROI',
            'manage_options',
            'rg-roi',
            [__CLASS__, 'render_settings']
        );
    }

    public static function register_settings() {
        register_setting(self::OPTION_GROUP, self::OPTION_CC_EMAIL, [
            'type' => 'string',
            'sanitize_callback' => 'sanitize_email',
            'default' => '',
        ]);
    }

    public static function render_settings() {
        if (!current_user_can('manage_options')) return;
        $robots = self::get_robots();
        ?>
        <div class="wrap rg-roi-admin">
            <h1>Robo-Guru ROI Kalkulator</h1>

            <div class="nav-tab-wrapper">
                <a href="#tab-robots" class="nav-tab nav-tab-active" data-tab="robots">Roboter verwalten</a>
                <a href="#tab-settings" class="nav-tab" data-tab="settings">Einstellungen</a>
            </div>

            <!-- Roboter Tab -->
            <div id="tab-robots" class="rg-tab-content active">
                <h2>Roboter-Listenpreise</h2>
                <p class="description">Hier können Sie Roboter mit ihren Listenpreisen und Spezifikationen verwalten. Die Leasingrate wird automatisch berechnet (<?php echo self::LEASING_RESIDUAL_PERCENT; ?>% Restwert, <?php echo self::LEASING_INTEREST_RATE; ?>% p.a. Verzinsung).</p>

                <table class="wp-list-table widefat fixed striped" id="rg-robots-table">
                    <thead>
                        <tr>
                            <th style="width:200px;">Name</th>
                            <th style="width:120px;">Listenpreis (€)</th>
                            <th style="width:100px;">Leistung (m²/h)</th>
                            <th style="width:100px;">Service (€/Mon)</th>
                            <th style="width:100px;">Strom (€/Jahr)</th>
                            <th style="width:120px;">Leasing 36M</th>
                            <th style="width:120px;">Leasing 48M</th>
                            <th style="width:80px;">Aktionen</th>
                        </tr>
                    </thead>
                    <tbody id="rg-robots-list">
                        <?php if (empty($robots)) : ?>
                        <tr class="no-robots">
                            <td colspan="8">Noch keine Roboter hinzugefügt.</td>
                        </tr>
                        <?php else : ?>
                        <?php foreach ($robots as $id => $robot) :
                            $lease36 = self::calculate_leasing_rate($robot['price'], 36);
                            $lease48 = self::calculate_leasing_rate($robot['price'], 48);
                        ?>
                        <tr data-id="<?php echo esc_attr($id); ?>">
                            <td><strong><?php echo esc_html($robot['name']); ?></strong></td>
                            <td><?php echo number_format($robot['price'], 0, ',', '.'); ?> €</td>
                            <td><?php echo esc_html($robot['performance'] ?? '-'); ?> m²/h</td>
                            <td><?php echo number_format($robot['service_monthly'] ?? 0, 0, ',', '.'); ?> €</td>
                            <td><?php echo number_format($robot['power_yearly'] ?? 0, 0, ',', '.'); ?> €</td>
                            <td><?php echo number_format($lease36, 2, ',', '.'); ?> €/Mon</td>
                            <td><?php echo number_format($lease48, 2, ',', '.'); ?> €/Mon</td>
                            <td>
                                <button type="button" class="button button-small rg-edit-robot" data-id="<?php echo esc_attr($id); ?>">Bearbeiten</button>
                                <button type="button" class="button button-small button-link-delete rg-delete-robot" data-id="<?php echo esc_attr($id); ?>">Löschen</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <h3>Roboter hinzufügen / bearbeiten</h3>
                <form id="rg-robot-form" class="rg-robot-form">
                    <input type="hidden" name="robot_id" id="robot_id" value="">
                    <table class="form-table">
                        <tr>
                            <th><label for="robot_name">Name *</label></th>
                            <td><input type="text" name="robot_name" id="robot_name" class="regular-text" required placeholder="z.B. Pudu CC1 Pro"></td>
                        </tr>
                        <tr>
                            <th><label for="robot_price">Listenpreis (€) *</label></th>
                            <td>
                                <input type="number" name="robot_price" id="robot_price" class="regular-text" required min="0" step="100" placeholder="z.B. 25000">
                                <p class="description">Netto-Listenpreis des Roboters</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="robot_performance">Reinigungsleistung (m²/h)</label></th>
                            <td><input type="number" name="robot_performance" id="robot_performance" class="regular-text" min="0" step="50" placeholder="z.B. 1500"></td>
                        </tr>
                        <tr>
                            <th><label for="robot_service">Servicekosten (€/Monat)</label></th>
                            <td><input type="number" name="robot_service" id="robot_service" class="regular-text" min="0" step="10" placeholder="z.B. 179"></td>
                        </tr>
                        <tr>
                            <th><label for="robot_power">Stromkosten (€/Jahr)</label></th>
                            <td><input type="number" name="robot_power" id="robot_power" class="regular-text" min="0" step="10" placeholder="z.B. 350"></td>
                        </tr>
                    </table>

                    <div class="rg-leasing-preview" id="leasing-preview" style="display:none;">
                        <h4>Berechnete Leasingraten</h4>
                        <p>Bei <strong id="preview-price">0</strong> € Listenpreis:</p>
                        <ul>
                            <li>36 Monate: <strong id="preview-lease36">0</strong> €/Monat</li>
                            <li>48 Monate: <strong id="preview-lease48">0</strong> €/Monat</li>
                            <li>60 Monate: <strong id="preview-lease60">0</strong> €/Monat</li>
                        </ul>
                        <p class="description">Berechnung: <?php echo self::LEASING_RESIDUAL_PERCENT; ?>% Restwert, <?php echo self::LEASING_INTEREST_RATE; ?>% p.a. Verzinsung</p>
                    </div>

                    <p class="submit">
                        <button type="submit" class="button button-primary" id="rg-save-robot">Roboter speichern</button>
                        <button type="button" class="button" id="rg-cancel-edit" style="display:none;">Abbrechen</button>
                    </p>
                </form>
            </div>

            <!-- Einstellungen Tab -->
            <div id="tab-settings" class="rg-tab-content">
                <form method="post" action="options.php">
                    <?php settings_fields(self::OPTION_GROUP); ?>
                    <table class="form-table" role="presentation">
                        <tr>
                            <th scope="row"><label for="<?php echo esc_attr(self::OPTION_CC_EMAIL); ?>">CC E-Mail (optional)</label></th>
                            <td>
                                <input type="email" name="<?php echo esc_attr(self::OPTION_CC_EMAIL); ?>" id="<?php echo esc_attr(self::OPTION_CC_EMAIL); ?>" value="<?php echo esc_attr(get_option(self::OPTION_CC_EMAIL, '')); ?>" class="regular-text" />
                                <p class="description">Optional: Eine Kopie jeder Bericht-E-Mail wird neutral in CC gesendet (z. B. an dein Team).</p>
                            </td>
                        </tr>
                    </table>
                    <?php submit_button(); ?>
                </form>
            </div>
        </div>
        <?php
    }

    /**
     * AJAX: Save robot
     */
    public static function ajax_save_robot() {
        check_ajax_referer('rg_roi_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Keine Berechtigung.'], 403);
        }

        $robot_id = sanitize_text_field($_POST['robot_id'] ?? '');
        $name = sanitize_text_field($_POST['robot_name'] ?? '');
        $price = floatval($_POST['robot_price'] ?? 0);
        $performance = floatval($_POST['robot_performance'] ?? 0);
        $service = floatval($_POST['robot_service'] ?? 0);
        $power = floatval($_POST['robot_power'] ?? 0);

        if (empty($name) || $price <= 0) {
            wp_send_json_error(['message' => 'Name und Preis sind erforderlich.'], 400);
        }

        $robots = self::get_robots();

        $robot_data = [
            'name' => $name,
            'price' => $price,
            'performance' => $performance,
            'service_monthly' => $service,
            'power_yearly' => $power,
        ];

        if (empty($robot_id)) {
            // New robot - generate ID
            $robot_id = 'robot_' . time() . '_' . wp_rand(1000, 9999);
        }

        $robots[$robot_id] = $robot_data;
        update_option(self::OPTION_ROBOTS, $robots);

        // Calculate leasing rates for response
        $lease36 = self::calculate_leasing_rate($price, 36);
        $lease48 = self::calculate_leasing_rate($price, 48);

        wp_send_json_success([
            'message' => 'Roboter gespeichert.',
            'robot_id' => $robot_id,
            'robot' => $robot_data,
            'lease36' => $lease36,
            'lease48' => $lease48,
        ]);
    }

    /**
     * AJAX: Delete robot
     */
    public static function ajax_delete_robot() {
        check_ajax_referer('rg_roi_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Keine Berechtigung.'], 403);
        }

        $robot_id = sanitize_text_field($_POST['robot_id'] ?? '');
        if (empty($robot_id)) {
            wp_send_json_error(['message' => 'Roboter-ID fehlt.'], 400);
        }

        $robots = self::get_robots();
        if (isset($robots[$robot_id])) {
            unset($robots[$robot_id]);
            update_option(self::OPTION_ROBOTS, $robots);
            wp_send_json_success(['message' => 'Roboter gelöscht.']);
        } else {
            wp_send_json_error(['message' => 'Roboter nicht gefunden.'], 404);
        }
    }

    /**
     * AJAX: Upload customer logo
     */
    public static function ajax_upload_logo() {
        // Only for logged-in users
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Nicht autorisiert.'], 401);
        }

        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], self::NONCE_ACTION)) {
            wp_send_json_error(['message' => 'Sicherheitsprüfung fehlgeschlagen.'], 403);
        }

        // Check for file
        if (!isset($_FILES['logo']) || empty($_FILES['logo']['tmp_name'])) {
            wp_send_json_error(['message' => 'Keine Datei übergeben.'], 400);
        }

        // Validate file type
        $allowed_types = ['image/png', 'image/jpeg', 'image/webp'];
        $file_type = wp_check_filetype($_FILES['logo']['name']);
        if (!in_array($_FILES['logo']['type'], $allowed_types)) {
            wp_send_json_error(['message' => 'Nur PNG, JPG oder WebP erlaubt.'], 400);
        }

        // Validate file size (max 2MB)
        if ($_FILES['logo']['size'] > 2 * 1024 * 1024) {
            wp_send_json_error(['message' => 'Datei zu groß (max. 2 MB).'], 400);
        }

        // Include required files
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        // Handle the upload
        $attachment_id = media_handle_upload('logo', 0);

        if (is_wp_error($attachment_id)) {
            wp_send_json_error(['message' => 'Upload fehlgeschlagen: ' . $attachment_id->get_error_message()], 500);
        }

        // Delete old logo if exists
        $old_logo_id = get_user_meta(get_current_user_id(), 'rg_customer_logo_id', true);
        if ($old_logo_id && $old_logo_id != $attachment_id) {
            wp_delete_attachment($old_logo_id, true);
        }

        // Save new logo ID to user meta
        update_user_meta(get_current_user_id(), 'rg_customer_logo_id', $attachment_id);

        wp_send_json_success([
            'message' => 'Logo erfolgreich gespeichert.',
            'image_url' => wp_get_attachment_image_url($attachment_id, 'medium'),
            'attachment_id' => $attachment_id,
        ]);
    }

    /**
     * AJAX: Delete customer logo
     */
    public static function ajax_delete_logo() {
        // Only for logged-in users
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Nicht autorisiert.'], 401);
        }

        // Verify nonce
        $payload = json_decode(file_get_contents('php://input'), true);
        $nonce = isset($payload['nonce']) ? sanitize_text_field($payload['nonce']) : '';
        if (!$nonce || !wp_verify_nonce($nonce, self::NONCE_ACTION)) {
            wp_send_json_error(['message' => 'Sicherheitsprüfung fehlgeschlagen.'], 403);
        }

        $logo_id = get_user_meta(get_current_user_id(), 'rg_customer_logo_id', true);

        if ($logo_id) {
            // Delete the attachment
            wp_delete_attachment($logo_id, true);
            // Remove user meta
            delete_user_meta(get_current_user_id(), 'rg_customer_logo_id');
        }

        wp_send_json_success(['message' => 'Logo entfernt.']);
    }

    public static function ajax_send_report() {
        $payload = json_decode(file_get_contents('php://input'), true);
        $nonce = isset($payload['nonce']) ? sanitize_text_field($payload['nonce']) : '';
        if (!$nonce || !wp_verify_nonce($nonce, self::NONCE_ACTION)) {
            wp_send_json_error(['message' => 'Sicherheitsprüfung fehlgeschlagen.'], 403);
        }

        $email = isset($payload['email']) ? sanitize_email($payload['email']) : '';
        $calc = isset($payload['calc']) && is_array($payload['calc']) ? $payload['calc'] : [];
        $pdf_base64 = isset($payload['pdfBase64']) ? (string)$payload['pdfBase64'] : '';

        if (!is_email($email)) {
            wp_send_json_error(['message' => 'Bitte eine gültige E-Mail-Adresse eingeben.'], 400);
        }

        $mode = isset($calc['mode']) ? sanitize_text_field($calc['mode']) : 'purchase';
        $invest = floatval($calc['invest'] ?? 0); // Kauf: Investition gesamt, Leasing: Vertragsvolumen (überschlägig)
        $net = floatval($calc['net'] ?? 0);
        $lease_rate = floatval($calc['leaseRateMonthly'] ?? 0);
        $lease_term = intval($calc['leaseTermMonths'] ?? 0);

        if ($net <= 0) {
            wp_send_json_error(['message' => 'Versand nur bei positiver Netto-Ersparnis möglich.'], 400);
        }
        if ($mode === 'purchase' && $invest <= 0) {
            wp_send_json_error(['message' => 'Bitte einen Kaufpreis eingeben.'], 400);
        }
        if ($mode === 'lease' && ($lease_rate <= 0 || $lease_term <= 0)) {
            wp_send_json_error(['message' => 'Bitte Leasingrate und Laufzeit eingeben.'], 400);
        }

        // Handle various data URI formats from jsPDF (with or without filename parameter)
        if (preg_match('/^data:application\/pdf[^,]*,/', $pdf_base64, $matches)) {
            $pdf_base64 = substr($pdf_base64, strlen($matches[0]));
        }
        if (!$pdf_base64) {
            wp_send_json_error(['message' => 'PDF-Daten fehlen. Bitte zuerst PDF erzeugen.'], 400);
        }

        // Simple rate limit: 1 request per 5 minutes per email+ip
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $rate_key = 'rg_roi_mail_' . md5($ip . '|' . $email);
        if (get_transient($rate_key)) {
            wp_send_json_error(['message' => 'Bitte warte kurz, bevor du erneut sendest (Rate-Limit).'], 429);
        }
        set_transient($rate_key, 1, 5 * MINUTE_IN_SECONDS);

        $bytes = base64_decode($pdf_base64, true);
        if (!$bytes) {
            wp_send_json_error(['message' => 'PDF konnte nicht gelesen werden.'], 400);
        }

        $upload_dir = wp_upload_dir();
        $tmp_dir = trailingslashit($upload_dir['basedir']) . 'rg-roi';
        if (!wp_mkdir_p($tmp_dir)) {
            wp_send_json_error(['message' => 'Server kann temporären Ordner nicht erstellen.'], 500);
        }

        $filename = 'ROI-Berechnung-Robo-Guru-' . date('Y-m-d') . '-' . wp_generate_password(6, false, false) . '.pdf';
        $path = trailingslashit($tmp_dir) . $filename;
        $written = file_put_contents($path, $bytes);

        if (!$written || !file_exists($path)) {
            wp_send_json_error(['message' => 'PDF konnte nicht gespeichert werden.'], 500);
        }

        $subject = 'ROI-Berechnung Reinigungsrobotik | Robo-Guru';
        $message = self::mail_text($calc);

        $headers = ['Content-Type: text/plain; charset=UTF-8'];
        $cc = get_option(self::OPTION_CC_EMAIL, '');
        if ($cc && is_email($cc)) {
            $headers[] = 'Cc: ' . $cc;
        }

        $sent = wp_mail($email, $subject, $message, $headers, [$path]);

        @unlink($path);

        if ($sent) {
            wp_send_json_success(['message' => 'Der Bericht wurde per E-Mail versendet.']);
        } else {
            wp_send_json_error(['message' => 'E-Mail konnte nicht versendet werden.'], 500);
        }
    }

    public static function ajax_save_to_profile() {
        // Only logged-in users (no nopriv hook registered)
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Bitte zuerst einloggen.'], 401);
        }

        if (!function_exists('bp_document_add')) {
            wp_send_json_error(['message' => 'Dokument-Funktion ist nicht verfügbar.'], 501);
        }

        $payload = json_decode(file_get_contents('php://input'), true);
        $nonce = isset($payload['nonce']) ? sanitize_text_field($payload['nonce']) : '';

        // Debug nonce issue
        $nonce_valid = wp_verify_nonce($nonce, self::NONCE_ACTION);
        if (!$nonce || !$nonce_valid) {
            wp_send_json_error([
                'message' => 'Sicherheitsprüfung fehlgeschlagen.',
                'debug' => [
                    'nonce_received' => !empty($nonce) ? substr($nonce, 0, 5) . '...' : 'EMPTY',
                    'nonce_result' => $nonce_valid,
                    'user_id' => get_current_user_id(),
                    'is_logged_in' => is_user_logged_in(),
                    'action' => self::NONCE_ACTION,
                ]
            ], 403);
        }

        $pdf_base64 = isset($payload['pdfBase64']) ? (string)$payload['pdfBase64'] : '';
        // Handle various data URI formats from jsPDF (with or without filename parameter)
        if (preg_match('/^data:application\/pdf[^,]*,/', $pdf_base64, $matches)) {
            $pdf_base64 = substr($pdf_base64, strlen($matches[0]));
        }
        if (!$pdf_base64) {
            wp_send_json_error(['message' => 'PDF-Daten fehlen.'], 400);
        }

        // Rate limit temporarily disabled for debugging
        $user_id = get_current_user_id();
        // $rate_key = 'rg_roi_save_' . $user_id;
        // if (get_transient($rate_key)) {
        //     wp_send_json_error(['message' => 'Bitte 10 Sekunden warten bevor du erneut speicherst.'], 429);
        // }
        // set_transient($rate_key, 1, 10);

        // Clean base64 string: remove whitespace
        $pdf_base64 = preg_replace('/\s+/', '', $pdf_base64);

        // Try decoding (non-strict mode for better compatibility)
        $bytes = base64_decode($pdf_base64, false);
        if (!$bytes || strlen($bytes) < 100) {
            // Fallback: try with URL-safe characters replaced
            $pdf_base64_clean = str_replace(['-', '_'], ['+', '/'], $pdf_base64);
            $bytes = base64_decode($pdf_base64_clean, false);
        }
        if (!$bytes || strlen($bytes) < 100) {
            wp_send_json_error(['message' => 'PDF konnte nicht dekodiert werden. Bitte erneut versuchen.'], 400);
        }

        // Verify PDF magic bytes
        if (substr($bytes, 0, 4) !== '%PDF') {
            wp_send_json_error(['message' => 'Ungültiges PDF-Format.'], 400);
        }

        // Get upload directory - use BuddyBoss bb_documents folder for proper integration
        $upload_dir = wp_upload_dir();
        $bb_docs_dir = trailingslashit($upload_dir['basedir']) . 'bb_documents';
        $bb_docs_url = trailingslashit($upload_dir['baseurl']) . 'bb_documents';

        // Create bb_documents directory if it doesn't exist
        if (!wp_mkdir_p($bb_docs_dir)) {
            wp_send_json_error(['message' => 'Dokumenten-Ordner konnte nicht erstellt werden.'], 500);
        }

        $filename = 'ROI-Berechnung-Robo-Guru-' . date('Y-m-d') . '.pdf';
        // Use unique filename to avoid overwriting
        $unique_filename = wp_unique_filename($bb_docs_dir, $filename);
        $dest_path = trailingslashit($bb_docs_dir) . $unique_filename;

        // Write PDF directly to destination
        $written = file_put_contents($dest_path, $bytes);

        if (!$written || !file_exists($dest_path)) {
            wp_send_json_error(['message' => 'PDF konnte nicht gespeichert werden.'], 500);
        }

        // Create WordPress attachment with correct path in bb_documents
        $filetype = wp_check_filetype($unique_filename, null);
        $attachment_data = [
            'post_mime_type' => $filetype['type'] ? $filetype['type'] : 'application/pdf',
            'post_title'     => sanitize_file_name($filename),
            'post_content'   => '',
            'post_status'    => 'inherit',
            'guid'           => trailingslashit($bb_docs_url) . $unique_filename,
        ];

        // Insert attachment with relative path for BuddyBoss compatibility
        $attachment_id = wp_insert_attachment($attachment_data, $dest_path);
        if (is_wp_error($attachment_id)) {
            @unlink($dest_path);
            wp_send_json_error(['message' => 'Attachment konnte nicht erstellt werden.'], 500);
        }

        // Generate attachment metadata
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $attach_data = wp_generate_attachment_metadata($attachment_id, $dest_path);
        wp_update_attachment_metadata($attachment_id, $attach_data);

        // Set the correct relative path for BuddyBoss (bb_documents/filename.pdf)
        update_post_meta($attachment_id, '_wp_attached_file', 'bb_documents/' . $unique_filename);

        // Mark as BuddyBoss document upload
        update_post_meta($attachment_id, 'bp_document_upload', 1);
        update_post_meta($attachment_id, 'bp_document_saved', 1);

        // Get or create "ROI Berechnung" folder for this user
        $folder_id = 0;
        $folder_name = 'ROI Berechnung';

        // Debug logging
        $debug_log = [];
        $debug_log[] = 'User ID: ' . $user_id;
        $debug_log[] = 'Attachment ID: ' . $attachment_id;
        $debug_log[] = 'File saved to: ' . $dest_path;

        // Use bp_folder_add (the correct BuddyBoss function)
        if (function_exists('bp_folder_add') && function_exists('buddypress')) {
            $debug_log[] = 'bp_folder_add exists: YES';

            // Check if folder already exists using BuddyBoss table
            global $wpdb;
            $bp = buddypress();
            if (isset($bp->document->table_name_folder)) {
                $folder_table = $bp->document->table_name_folder;
                $debug_log[] = 'Folder table: ' . $folder_table;

                $existing_folder = $wpdb->get_var($wpdb->prepare(
                    "SELECT id FROM {$folder_table} WHERE user_id = %d AND title = %s LIMIT 1",
                    $user_id,
                    $folder_name
                ));
                $debug_log[] = 'Existing folder query result: ' . ($existing_folder ?: 'NULL');

                if ($existing_folder) {
                    $folder_id = (int) $existing_folder;
                    $debug_log[] = 'Using existing folder: ' . $folder_id;
                } else {
                    // Create the folder using bp_folder_add
                    $new_folder_id = bp_folder_add([
                        'user_id'  => $user_id,
                        'title'    => $folder_name,
                        'privacy'  => 'onlyme',
                    ]);
                    $debug_log[] = 'bp_folder_add result: ' . (is_wp_error($new_folder_id) ? 'ERROR: ' . $new_folder_id->get_error_message() : $new_folder_id);

                    if ($new_folder_id && !is_wp_error($new_folder_id)) {
                        $folder_id = $new_folder_id;
                    }
                }
            } else {
                $debug_log[] = 'Folder table NOT set in buddypress()->document';
            }
        } else {
            $debug_log[] = 'bp_folder_add exists: NO';
            $debug_log[] = 'buddypress exists: ' . (function_exists('buddypress') ? 'YES' : 'NO');
        }

        $debug_log[] = 'Final folder_id: ' . $folder_id;

        // Create BuddyBoss document entry (with folder if available)
        // Determine the published status for BuddyBoss documents
        $doc_status = 'published';
        if (function_exists('bb_document_get_published_status')) {
            $doc_status = bb_document_get_published_status();
        }

        // Remove .pdf extension from title (BuddyBoss stores without extension)
        $doc_title = preg_replace('/\.pdf$/i', '', $filename);

        $doc_args = [
            'attachment_id' => $attachment_id,
            'user_id'       => $user_id,
            'title'         => $doc_title,
            'privacy'       => 'onlyme',
            'status'        => $doc_status,
            'blog_id'       => get_current_blog_id(),
            'group_id'      => 0,
            'activity_id'   => 0,
            'menu_order'    => 0,
            'error_type'    => 'wp_error',
        ];
        if ($folder_id > 0) {
            $doc_args['folder_id'] = $folder_id;
        }
        $debug_log[] = 'Document status: ' . $doc_status;
        $debug_log[] = 'bp_document_add args: ' . json_encode($doc_args);

        if (function_exists('bp_document_add')) {
            $doc_id = bp_document_add($doc_args);
            $debug_log[] = 'bp_document_add result: ' . (is_wp_error($doc_id) ? 'ERROR: ' . $doc_id->get_error_message() : $doc_id);

            // If document was created successfully, create activity and link everything
            if (!is_wp_error($doc_id) && $doc_id) {
                global $wpdb;
                $bp = buddypress();

                // Create BuddyPress activity for the document (required for visibility)
                $activity_id = 0;
                if (function_exists('bp_activity_add') && bp_is_active('activity')) {
                    $activity_id = bp_activity_add([
                        'user_id'       => $user_id,
                        'component'     => 'document',
                        'type'          => 'activity_update',
                        'action'        => sprintf('%s uploaded a document', bp_core_get_user_displayname($user_id)),
                        'content'       => '',
                        'primary_link'  => '',
                        'item_id'       => 0,
                        'hide_sitewide' => 1, // Hide from activity feed since it's private
                        'privacy'       => 'onlyme',
                        'status'        => $doc_status,
                    ]);
                    $debug_log[] = 'Activity created: ' . ($activity_id ? $activity_id : 'FAILED');

                    if ($activity_id) {
                        // Set activity meta for the document
                        bp_activity_update_meta($activity_id, 'bp_document_ids', $doc_id);

                        // Set attachment meta for parent activity
                        update_post_meta($attachment_id, 'bp_document_parent_activity_id', $activity_id);
                        $debug_log[] = 'bp_document_parent_activity_id meta set: ' . $activity_id;
                    }
                }

                if (isset($bp->document->table_name)) {
                    $doc_table = $bp->document->table_name;

                    // Update document: folder_id, status, and activity_id
                    $update_data = ['status' => $doc_status];
                    if ($folder_id > 0) {
                        $update_data['folder_id'] = $folder_id;
                    }
                    if ($activity_id > 0) {
                        $update_data['activity_id'] = $activity_id;
                    }

                    $updated = $wpdb->update(
                        $doc_table,
                        $update_data,
                        ['id' => $doc_id],
                        array_fill(0, count($update_data), is_int(reset($update_data)) ? '%d' : '%s'),
                        ['%d']
                    );
                    $debug_log[] = 'Direct DB update result: ' . ($updated !== false ? 'SUCCESS' : 'FAILED - ' . $wpdb->last_error);

                    // Verify what's actually in the database now - get ALL columns
                    $doc_row = $wpdb->get_row($wpdb->prepare(
                        "SELECT * FROM {$doc_table} WHERE id = %d",
                        $doc_id
                    ), ARRAY_A);
                    if ($doc_row) {
                        $debug_log[] = 'Our document record: ' . json_encode($doc_row);
                    }

                    // Compare with an existing working document in the same folder (if any)
                    $existing_doc = $wpdb->get_row($wpdb->prepare(
                        "SELECT * FROM {$doc_table} WHERE folder_id = %d AND id != %d LIMIT 1",
                        $folder_id,
                        $doc_id
                    ), ARRAY_A);
                    if ($existing_doc) {
                        $debug_log[] = 'Existing working document: ' . json_encode($existing_doc);
                    } else {
                        // Get any working document from the same user
                        $any_doc = $wpdb->get_row($wpdb->prepare(
                            "SELECT * FROM {$doc_table} WHERE user_id = %d AND id != %d LIMIT 1",
                            $user_id,
                            $doc_id
                        ), ARRAY_A);
                        if ($any_doc) {
                            $debug_log[] = 'Other user document for comparison: ' . json_encode($any_doc);
                        }
                    }
                }

                // Clear BuddyBoss document caches
                if (function_exists('bp_core_reset_incrementor')) {
                    bp_core_reset_incrementor('bp_document');
                    bp_core_reset_incrementor('bp_document_folder');
                    $debug_log[] = 'Cache reset: done';
                }

                // Also try clearing object cache
                wp_cache_flush_group('bp_document');
                wp_cache_delete('bp_document_' . $doc_id, 'bp');

                // Update folder modification time
                if ($folder_id > 0 && function_exists('bp_document_update_folder_modified_date')) {
                    bp_document_update_folder_modified_date($folder_id);
                    $debug_log[] = 'Folder modified date updated';
                }
            }
        } else {
            $debug_log[] = 'bp_document_add does NOT exist';
            $doc_id = new WP_Error('no_function', 'bp_document_add function not available');
        }

        // Log to error log for debugging
        error_log('RG ROI Calculator - Document Save Debug: ' . implode(' | ', $debug_log));

        if (is_wp_error($doc_id)) {
            wp_send_json_error([
                'message' => 'Dokument konnte nicht im Profil gespeichert werden: ' . $doc_id->get_error_message(),
                'debug' => $debug_log
            ], 500);
        }

        // Build documents URL for the user (link to ROI Berechnung folder if exists)
        // BuddyBoss URL structure: /mitglieder/{username}/document/folders/{folder_id}/
        $docs_url = '';
        if (function_exists('bp_core_get_user_domain')) {
            $base_url = bp_core_get_user_domain($user_id);
            if ($folder_id > 0) {
                $docs_url = $base_url . 'document/folders/' . $folder_id . '/';
            } else {
                $docs_url = $base_url . 'document/';
            }
        } else {
            $docs_url = home_url('/mitglieder/' . wp_get_current_user()->user_nicename . '/document/');
        }

        wp_send_json_success([
            'message' => 'ROI-Berechnung wurde in deinem Profil gespeichert.',
            'doc_id' => $doc_id,
            'folder_id' => $folder_id,
            'docs_url' => $docs_url,
            'filename' => $filename,
            'debug' => $debug_log, // Temporary for debugging
        ]);
    }

    private static function mail_text(array $calc) {
        $net = floatval($calc['net'] ?? 0);
        $invest = floatval($calc['invest'] ?? 0);
        $monthly = $net / 12;
        $be = ($monthly > 0) ? max(1, (int)ceil($invest / $monthly)) : null;

        $fmt = function($v) {
            return number_format((float)$v, 0, ',', '.') . ' €';
        };

        $lines = [];
        $lines[] = 'Vielen Dank für Ihr Interesse an der Wirtschaftlichkeit von Reinigungsrobotern.';
        $lines[] = '';
        $lines[] = 'Im Anhang finden Sie Ihre ROI-Berechnung als PDF.';
        $lines[] = '';
        $lines[] = 'Kurz zusammengefasst:';
        $lines[] = '– Geschätzte Netto-Ersparnis pro Jahr: ' . $fmt($net);
        $lines[] = '– Geschätzte Netto-Ersparnis pro Monat: ' . $fmt($monthly);
        if ($be) {
            $lines[] = '– Break-even: ab Monat ' . $be . ' ist die Investition rechnerisch wieder drin.';
        }
        $lines[] = '';
        $lines[] = 'Hinweis: Diese Berechnung stellt eine vereinfachte Modellrechnung dar und ersetzt keine individuelle Projektprüfung.';
        $lines[] = '';
        $lines[] = 'Freundliche Grüße';
        $lines[] = 'Robo-Guru';
        $lines[] = 'Wissensplattform für Servicerobotik';
        $lines[] = home_url('/');

        // Use real newlines (no escaping outside a PHP string)
        return implode("\n", $lines);
    }
}

RG_ROI_Calculator::init();

<?php
/**
 * Plugin Name: Robo-Guru ROI Kalkulator
 * Description: Einfacher ROI-Kalkulator für Reinigungsrobotik inkl. PDF-Download, Druckansicht und Versand per E-Mail (PDF-Anhang). Shortcode: [rg_roi_calculator]
 * Version: 1.1.0
 * Author: Robo-Guru
 * Text Domain: rg-roi
 */

if (!defined('ABSPATH')) { exit; }

final class RG_ROI_Calculator {
    const VERSION = '1.3.0';
    const NONCE_ACTION = 'rg_roi_nonce';
    const OPTION_GROUP = 'rg_roi_options';
    const OPTION_CC_EMAIL = 'rg_roi_cc_email';

    // Avoid strict return types for broader PHP compatibility on WordPress hosts
    public static function init() {
        add_shortcode('rg_roi_calculator', [__CLASS__, 'shortcode']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_assets']);

        add_action('wp_ajax_rg_send_roi_report', [__CLASS__, 'ajax_send_report']);
        add_action('wp_ajax_nopriv_rg_send_roi_report', [__CLASS__, 'ajax_send_report']);

        add_action('wp_ajax_rg_save_to_profile', [__CLASS__, 'ajax_save_to_profile']);

        add_action('admin_menu', [__CLASS__, 'admin_menu']);
        add_action('admin_init', [__CLASS__, 'register_settings']);
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

        wp_enqueue_style('rg-roi', $base_url . 'assets/roi.css', [], self::VERSION);

        wp_enqueue_script('rg-jspdf', $jspdf, [], null, true);
        wp_enqueue_script('rg-jspdf-autotable', $autotable, ['rg-jspdf'], null, true);

        wp_enqueue_script(
            'rg-roi',
            $base_url . 'assets/roi.js',
            ['rg-jspdf', 'rg-jspdf-autotable'],
            self::VERSION,
            true
        );

        wp_localize_script('rg-roi', 'rgRoi', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce(self::NONCE_ACTION),
            'ccEmail' => get_option(self::OPTION_CC_EMAIL, ''),
            'siteName'=> get_bloginfo('name'),
        ]);
    }

    public static function shortcode($atts = []) {
        $atts = shortcode_atts([
            'title' => 'ROI-Kalkulator für Reinigungsroboter',
            'subtitle' => 'Unabhängige Beispielrechnung zur Wirtschaftlichkeit',
        ], $atts, 'rg_roi_calculator');

        ob_start();
        ?>
        <div class="rg-roi" data-rg-roi>
            <div class="rg-roi__head">
                <h3><?php echo esc_html($atts['title']); ?></h3>
                <p><?php echo esc_html($atts['subtitle']); ?></p>
            </div>

            
            <div class="rg-grid">
                <div class="rg-card">
                    <h4>Finanzierung</h4>

                    <label>Modell
                        <select class="rg-in rg-select" data-rg="mode">
                            <option value="purchase" selected>Kauf</option>
                            <option value="lease">Leasing</option>
                        </select>
                    </label>

                    <div class="rg-mode rg-mode--purchase" data-rg-mode="purchase">
                        <label>Kaufpreis pro Roboter (€)
                            <input type="number" class="rg-in" data-rg="price" value="25000" min="0" step="100">
                        </label>
                    </div>

                    <div class="rg-mode rg-mode--lease rg-hide" data-rg-mode="lease">
                        <label>Leasingrate pro Roboter / Monat (€)
                            <input type="number" class="rg-in" data-rg="leaseRateMonthly" value="950" min="0" step="10">
                        </label>
                        <label>Laufzeit (Monate)
                            <select class="rg-in rg-select" data-rg="leaseTermMonths">
                                <option value="24">24</option>
                                <option value="36" selected>36</option>
                                <option value="48">48</option>
                                <option value="60">60</option>
                            </select>
                        </label>
                        <div class="rg-note">Hinweis: Leasing wird als monatliche Rate × Laufzeit gerechnet (überschlägig).</div>
                    </div>

                    <label>Anzahl Roboter
                        <input type="number" class="rg-in" data-rg="qty" value="1" min="1" step="1">
                    </label>

                    <div class="rg-note" data-rg-out="investHint">–</div>
                </div>

                <div class="rg-card">
                    <h4>Nutzung & Einsparung</h4>
                    <label>Eingesparte Stunden pro Tag (pro Roboter)
                        <input type="number" class="rg-in" data-rg="hoursPerDay" value="2.5" min="0" step="0.1">
                    </label>
                    <label>Lohnkosten pro Stunde (inkl. Lohnnebenkosten) (€)
                        <input type="number" class="rg-in" data-rg="hourlyRate" value="22" min="0" step="0.5">
                    </label>
                    <label>Arbeitstage pro Jahr
                        <input type="number" class="rg-in" data-rg="daysPerYear" value="260" min="0" step="1">
                    </label>

                    <label>Zu reinigende Fläche pro Tag (m²) <span class="rg-optional">(optional)</span>
                        <input type="number" class="rg-in" data-rg="areaSqmPerDay" value="0" min="0" step="50">
                    </label>
                    <div class="rg-note" data-rg-out="sqmHint">Tipp: Wird zur Einordnung genutzt (keine harte Rechenbasis).</div>
                </div>

                <div class="rg-card">
                    <h4>Service & Betriebskosten</h4>

                    <label>Servicepaket
                        <select class="rg-in rg-select" data-rg="servicePreset">
                            <option value="0">Kein Paket / bereits enthalten</option>
                            <option value="99">Basic (ab 99 €/Monat)</option>
                            <option value="179" selected>Standard (ab 179 €/Monat)</option>
                            <option value="255">Premium (ab 255 €/Monat)</option>
                            <option value="-1">Eigener Wert</option>
                        </select>
                    </label>

                    <label>Servicekosten pro Roboter / Monat (€)
                        <input type="number" class="rg-in" data-rg="serviceMonthly" value="179" min="0" step="5">
                    </label>

                    <label>Stromkosten pro Roboter / Jahr (€)
                        <input type="number" class="rg-in" data-rg="powerPerYear" value="350" min="0" step="10">
                    </label>

                    <div class="rg-note">Hinweis: Servicepaket kann je nach Anbieter/Modell variieren. Strom ist meist ein kleiner Hebel.</div>
                </div>

                <div class="rg-card rg-result">
                    <div class="rg-result__head">
                        <h4>Ergebnis</h4>
                        <div class="rg-result__tag">Unabhängige Beispielrechnung</div>
                    </div>

                    <div class="rg-hero">
                        <div class="rg-hero__label">Geschätzte Netto-Ersparnis / Jahr</div>
                        <div class="rg-hero__value" data-rg-out="net">–</div>
                        <div class="rg-hero__sub">entspricht ca. <span data-rg-out="monthly">–</span> pro Monat</div>
                    </div>

                    <div class="rg-metrics">
                        <div class="rg-metric">
                            <div class="rg-metric__k">Amortisationszeit</div>
                            <div class="rg-metric__v" data-rg-out="payback">–</div>
                            <div class="rg-metric__s">Monate</div>
                        </div>
                        <div class="rg-metric">
                            <div class="rg-metric__k">ROI</div>
                            <div class="rg-metric__v" data-rg-out="roi">–</div>
                            <div class="rg-metric__s">vereinfachte Jahresbetrachtung</div>
                        </div>
                    </div>


                    <div class="rg-rating" data-rg-out="ratingWrap" data-level="ok">
                        <div class="rg-rating__dot" aria-hidden="true"></div>
                        <div class="rg-rating__content">
                            <div class="rg-rating__label" data-rg-out="ratingLabel">–</div>
                            <div class="rg-rating__text" data-rg-out="ratingText">–</div>
                        </div>
                    </div>

                    <div class="rg-be" aria-live="polite">
                        <div class="rg-be__badge">Break-even</div>
                        <div class="rg-be__text" data-rg-out="beText">–</div>
                    </div>

                    <div class="rg-warn" data-rg-out="warn" style="display:none;">
                        Hinweis: Mit den aktuellen Angaben entsteht keine positive Netto-Ersparnis.
                    </div>

                    <div class="rg-actions">
                        <button class="rg-btn rg-btn--primary" data-rg-btn="pdf" disabled><span class="rg-ico">📄</span><span>PDF herunterladen</span></button>
                        <button class="rg-btn" data-rg-btn="print" disabled><span class="rg-ico">🖨</span><span>Drucken</span></button>
                        <?php if (is_user_logged_in() && function_exists('bp_is_active') && bp_is_active('document')) : ?>
                        <button class="rg-btn rg-btn--save" data-rg-btn="save" disabled><span class="rg-ico">💾</span><span>In Profil speichern</span></button>
                        <?php endif; ?>

                        <div class="rg-hint" data-rg-out="hint">
                            Export ist aktiv, sobald eine positive Netto-Ersparnis berechnet wurde.
                        </div>
                        <div class="rg-save-status" data-rg-out="saveStatus" style="display:none;"></div>
                    </div>

                    <details class="rg-details">
                        <summary>Details der Berechnung</summary>
                        <div class="rg-details__grid">
                            <div class="rg-kpi"><div class="rg-k">Finanzierung</div><div class="rg-v" data-rg-out="finModel">–</div></div>
                            <div class="rg-kpi"><div class="rg-k">Investition / Vertragsvolumen</div><div class="rg-v" data-rg-out="invest">–</div></div>
                            <div class="rg-kpi"><div class="rg-k">Ersparnis/Jahr (brutto)</div><div class="rg-v" data-rg-out="gross">–</div></div>
                            <div class="rg-kpi"><div class="rg-k">Service+Strom/Jahr</div><div class="rg-v" data-rg-out="ops">–</div></div>
                            <div class="rg-kpi"><div class="rg-k">Leasingkosten/Jahr</div><div class="rg-v" data-rg-out="leaseYear">–</div></div>
                            <div class="rg-kpi"><div class="rg-k">Fläche (m²/Tag)</div><div class="rg-v" data-rg-out="area">–</div></div>
                            <div class="rg-kpi"><div class="rg-k">abgeleitet (m²/h)</div><div class="rg-v" data-rg-out="sqmPerHour">–</div></div>
                        
                        </div>

                        <div class="rg-assumptions">
                            <div class="rg-assumptions__title">Annahmen der Berechnung</div>
                            <ul class="rg-assumptions__list">
                                <li>Konstanter Betrieb über das Jahr (Arbeitstage laut Eingabe).</li>
                                <li>Personalkosten basieren auf dem eingegebenen Stundensatz.</li>
                                <li>Service- und Stromkosten basieren auf Ihren Angaben.</li>
                                <li>Keine Förderungen, Steuern oder Restwerte berücksichtigt.</li>
                            </ul>
                        </div>
                    </details>


                    <div class="rg-disclaimer">
                        Dieser Kalkulator dient zur überschlägigen Bewertung und ersetzt keine individuelle Projektprüfung.
                    </div>
                </div>
            </div>
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
        ?>
        <div class="wrap">
            <h1>Robo-Guru ROI Kalkulator</h1>
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
        <?php
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

        if (strpos($pdf_base64, 'data:application/pdf;base64,') === 0) {
            $pdf_base64 = substr($pdf_base64, strlen('data:application/pdf;base64,'));
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

    public static function ajax_save_to_profile() {
        // Check if user is logged in
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Bitte melden Sie sich an, um Dokumente zu speichern.'], 401);
        }

        // Check if BuddyBoss document component is active
        if (!function_exists('bp_is_active') || !bp_is_active('document')) {
            wp_send_json_error(['message' => 'Dokumentenfunktion ist nicht verfügbar.'], 400);
        }

        $payload = json_decode(file_get_contents('php://input'), true);
        $nonce = isset($payload['nonce']) ? sanitize_text_field($payload['nonce']) : '';

        if (!$nonce || !wp_verify_nonce($nonce, self::NONCE_ACTION)) {
            wp_send_json_error(['message' => 'Sicherheitsprüfung fehlgeschlagen.'], 403);
        }

        $pdf_base64 = isset($payload['pdfBase64']) ? (string)$payload['pdfBase64'] : '';

        // Strip data URI prefix (handles various formats like data:application/pdf;base64, or data:application/pdf;filename=xxx;base64,)
        if (preg_match('/^data:application\/pdf[^,]*,/', $pdf_base64, $matches)) {
            $pdf_base64 = substr($pdf_base64, strlen($matches[0]));
        }

        if (!$pdf_base64) {
            wp_send_json_error(['message' => 'PDF-Daten fehlen.'], 400);
        }

        $bytes = base64_decode($pdf_base64, true);
        if (!$bytes) {
            wp_send_json_error(['message' => 'PDF konnte nicht dekodiert werden.'], 400);
        }

        $user_id = get_current_user_id();
        $folder_name = 'ROI Berechnung';

        // Find or create the folder
        $folder_id = self::get_or_create_folder($user_id, $folder_name);
        if (!$folder_id) {
            wp_send_json_error(['message' => 'Ordner konnte nicht erstellt werden.'], 500);
        }

        // Save the PDF file
        $upload_dir = wp_upload_dir();
        $filename = 'ROI-Berechnung-' . date('Y-m-d-His') . '.pdf';
        $file_path = trailingslashit($upload_dir['path']) . $filename;

        if (!file_put_contents($file_path, $bytes)) {
            wp_send_json_error(['message' => 'Datei konnte nicht gespeichert werden.'], 500);
        }

        // Create WordPress attachment
        $file_type = wp_check_filetype($filename, null);
        $attachment = array(
            'post_mime_type' => $file_type['type'],
            'post_title'     => sanitize_file_name($filename),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );

        $attachment_id = wp_insert_attachment($attachment, $file_path);
        if (is_wp_error($attachment_id)) {
            @unlink($file_path);
            wp_send_json_error(['message' => 'Anhang konnte nicht erstellt werden.'], 500);
        }

        // Generate attachment metadata
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attachment_id, $file_path);
        wp_update_attachment_metadata($attachment_id, $attach_data);

        // Add to BuddyBoss documents
        $document_id = bp_document_add(array(
            'attachment_id' => $attachment_id,
            'user_id'       => $user_id,
            'title'         => 'ROI-Berechnung ' . date('d.m.Y H:i'),
            'folder_id'     => $folder_id,
            'privacy'       => 'onlyme',
            'error_type'    => 'wp_error',
        ));

        if (is_wp_error($document_id) || !$document_id) {
            wp_delete_attachment($attachment_id, true);
            wp_send_json_error(['message' => 'Dokument konnte nicht in BuddyBoss gespeichert werden.'], 500);
        }

        // Add required BuddyBoss document metadata
        if (function_exists('bp_document_update_meta')) {
            bp_document_update_meta($document_id, 'file_name', $filename);
            bp_document_update_meta($document_id, 'extension', '.pdf');
        }

        // Generate document previews if available
        if (function_exists('bp_document_generate_document_previews')) {
            bp_document_generate_document_previews($document_id);
        }

        // Get user profile documents URL
        $profile_url = bp_core_get_user_domain($user_id) . 'documents/';

        wp_send_json_success([
            'message' => 'Dokument wurde in Ihrem Profil gespeichert.',
            'document_id' => $document_id,
            'folder_id' => $folder_id,
            'profile_url' => $profile_url,
        ]);
    }

    private static function get_or_create_folder($user_id, $folder_name) {
        global $wpdb;
        $bp = buddypress();

        // Check if folder already exists
        $table = $bp->document->table_name_folder;
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table} WHERE user_id = %d AND title = %s AND group_id = 0",
            $user_id,
            $folder_name
        ));

        if ($existing) {
            return (int) $existing;
        }

        // Create new folder
        $folder_id = bp_folder_add(array(
            'user_id'  => $user_id,
            'title'    => $folder_name,
            'privacy'  => 'onlyme',
            'group_id' => 0,
        ));

        return $folder_id ? (int) $folder_id : false;
    }
}

RG_ROI_Calculator::init();

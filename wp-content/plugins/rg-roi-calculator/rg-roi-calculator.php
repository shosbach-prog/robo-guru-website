<?php
/**
 * Plugin Name: Robo-Guru ROI Kalkulator
 * Description: Einfacher ROI-Kalkulator für Reinigungsrobotik inkl. PDF-Download, Druckansicht und Versand per E-Mail (PDF-Anhang). Shortcode: [rg_roi_calculator]
 * Version: 1.0.0
 * Author: Robo-Guru
 * Text Domain: rg-roi
 */

if (!defined('ABSPATH')) { exit; }

final class RG_ROI_Calculator {
    const VERSION = '1.0.0';
    const NONCE_ACTION = 'rg_roi_nonce';
    const OPTION_GROUP = 'rg_roi_options';
    const OPTION_CC_EMAIL = 'rg_roi_cc_email';

    // Avoid strict return types for broader PHP compatibility on WordPress hosts
    public static function init() {
        add_shortcode('rg_roi_calculator', [__CLASS__, 'shortcode']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_assets']);

        add_action('wp_ajax_rg_send_roi_report', [__CLASS__, 'ajax_send_report']);
        add_action('wp_ajax_nopriv_rg_send_roi_report', [__CLASS__, 'ajax_send_report']);

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
                    <h4>Investition</h4>
                    <label>Kaufpreis pro Roboter (€)
                        <input type="number" class="rg-in" data-rg="price" value="25000" min="0" step="100">
                    </label>
                    <label>Anzahl Roboter
                        <input type="number" class="rg-in" data-rg="qty" value="1" min="1" step="1">
                    </label>
                    <div class="rg-note">Investition gesamt = Kaufpreis × Anzahl</div>
                </div>

                <div class="rg-card">
                    <h4>Einsparung (manuelle Arbeit)</h4>
                    <label>Eingesparte Stunden pro Tag (pro Roboter)
                        <input type="number" class="rg-in" data-rg="hoursPerDay" value="2.5" min="0" step="0.1">
                    </label>
                    <label>Lohnkosten pro Stunde (inkl. Lohnnebenkosten) (€)
                        <input type="number" class="rg-in" data-rg="hourlyRate" value="22" min="0" step="0.5">
                    </label>
                    <label>Arbeitstage pro Jahr
                        <input type="number" class="rg-in" data-rg="daysPerYear" value="260" min="0" step="1">
                    </label>
                    <div class="rg-note">Tipp: 220–260 Tage (Mo–Fr) oder 300–365 (7-Tage-Betrieb)</div>
                </div>

                <div class="rg-card">
                    <h4>Betriebskosten</h4>
                    <label>Wartung/Service pro Roboter pro Jahr (€)
                        <input type="number" class="rg-in" data-rg="maintPerYear" value="1500" min="0" step="50">
                    </label>
                    <label>Stromkosten pro Roboter pro Jahr (€)
                        <input type="number" class="rg-in" data-rg="powerPerYear" value="350" min="0" step="10">
                    </label>
                    <div class="rg-note">Strom ist meist klein – Wartung & Personal sind die großen Hebel.</div>
                </div>

                <div class="rg-card rg-result">
                    <h4>Ergebnis</h4>

                    <div class="rg-kpi"><div class="rg-k">Investition gesamt</div><div class="rg-v" data-rg-out="invest">–</div></div>
                    <div class="rg-kpi"><div class="rg-k">Ersparnis/Jahr (brutto)</div><div class="rg-v" data-rg-out="gross">–</div></div>
                    <div class="rg-kpi"><div class="rg-k">Betriebskosten/Jahr</div><div class="rg-v" data-rg-out="ops">–</div></div>

                    <hr class="rg-hr">

                    <div class="rg-kpi"><div class="rg-k"><strong>Geschätzte Netto-Ersparnis pro Jahr</strong></div><div class="rg-v" data-rg-out="net"><strong>–</strong></div></div>
                    <div class="rg-kpi"><div class="rg-k">ROI (vereinfachte Jahresbetrachtung)</div><div class="rg-v" data-rg-out="roi">–</div></div>
                    <div class="rg-kpi"><div class="rg-k">Amortisationszeit (Monate)</div><div class="rg-v" data-rg-out="payback">–</div></div>
                    <div class="rg-kpi"><div class="rg-k">Break-even</div><div class="rg-v" data-rg-out="beText">–</div></div>

                    <div class="rg-warn" data-rg-out="warn" style="display:none;">
                        Hinweis: Mit den aktuellen Angaben entsteht keine positive Netto-Ersparnis.
                    </div>

                    <div class="rg-actions">
                        <button class="rg-btn" data-rg-btn="pdf" disabled>📄 PDF herunterladen</button>
                        <button class="rg-btn" data-rg-btn="print" disabled>🖨 Drucken</button>

                        <div class="rg-mail">
                            <input class="rg-mail__input" type="email" data-rg="email" placeholder="E-Mail für den Bericht">
                            <button class="rg-btn" data-rg-btn="mail" disabled>📧 Per E-Mail senden</button>
                        </div>

                        <div class="rg-hint" data-rg-out="hint">
                            Export ist aktiv, sobald eine positive Netto-Ersparnis berechnet wurde.
                        </div>
                    </div>

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

        $invest = floatval($calc['invest'] ?? 0);
        $net = floatval($calc['net'] ?? 0);
        if ($invest <= 0 || $net <= 0) {
            wp_send_json_error(['message' => 'Versand nur bei positiver Netto-Ersparnis möglich.'], 400);
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
}

RG_ROI_Calculator::init();

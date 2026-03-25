<?php
/**
 * Plugin Name: RG CardScan – Visitenkartenscanner
 * Plugin URI: https://robo-guru.de
 * Description: KI-gestützter Visitenkartenscanner für Messen. Shortcode: [rg_cardscan]
 * Version: 1.0.0
 * Author: RoboPlanet GmbH
 * Text Domain: rg-cardscan
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'RGCS_VERSION', '1.2.0' );
define( 'RGCS_PATH', plugin_dir_path( __FILE__ ) );
define( 'RGCS_URL', plugin_dir_url( __FILE__ ) );

/* Team E-Mail Mapping */
define( 'RGCS_TEAM_EMAILS', [
    'Pierre Holein'     => 'p.holein@robo-planet.de',
    'Alexander Kiss'    => 'a.kiss@robo-planet.de',
    'Sebastian Hosbach' => 's.hosbach@robo-planet.de',
    'Daniel Senkstock'  => 'd.senkstock@robo-planet.de',
    'Nicht bekannt'     => 'a.kiss@robo-planet.de',
] );

/* ──────────────────────────────────────────────
   1. ACTIVATION – Create DB table
   ────────────────────────────────────────────── */
register_activation_hook( __FILE__, 'rgcs_activate' );
function rgcs_activate() {
    rgcs_create_table();
}

/* DB upgrade on admin_init for updates without re-activation */
add_action( 'admin_init', 'rgcs_maybe_upgrade' );
function rgcs_maybe_upgrade() {
    if ( get_option( 'rgcs_db_version', '1.0.0' ) !== RGCS_VERSION ) {
        rgcs_create_table();
        update_option( 'rgcs_db_version', RGCS_VERSION );
    }
}

function rgcs_create_table() {
    global $wpdb;
    $table   = $wpdb->prefix . 'rgcs_contacts';
    $charset = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        name varchar(255) DEFAULT '',
        company varchar(255) DEFAULT '',
        position_title varchar(255) DEFAULT '',
        email varchar(255) DEFAULT '',
        phone varchar(100) DEFAULT '',
        mobile varchar(100) DEFAULT '',
        website varchar(255) DEFAULT '',
        address text DEFAULT '',
        notes text DEFAULT '',
        bemerkung text DEFAULT '',
        zustaendig varchar(255) DEFAULT 'Nicht bekannt',
        photo_path varchar(500) DEFAULT '',
        scanned_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset;";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
    add_option( 'rgcs_api_key', '' );
    add_option( 'rgcs_username', 'roboplanet' );
    add_option( 'rgcs_password', 'Alex123!' );
}

/* ──────────────────────────────────────────────
   2. ADMIN SETTINGS PAGE
   ────────────────────────────────────────────── */
add_action( 'admin_menu', 'rgcs_admin_menu' );
function rgcs_admin_menu() {
    add_options_page(
        'CardScan Einstellungen',
        'CardScan',
        'manage_options',
        'rg-cardscan',
        'rgcs_settings_page'
    );
}

add_action( 'admin_init', 'rgcs_register_settings' );
function rgcs_register_settings() {
    register_setting( 'rgcs_settings', 'rgcs_api_key' );
    register_setting( 'rgcs_settings', 'rgcs_username' );
    register_setting( 'rgcs_settings', 'rgcs_password' );
}

function rgcs_settings_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'rgcs_contacts';
    $count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table" );
    ?>
    <div class="wrap">
        <h1>🔍 CardScan – Visitenkartenscanner</h1>
        <form method="post" action="options.php">
            <?php settings_fields( 'rgcs_settings' ); ?>
            <table class="form-table">
                <tr>
                    <th>Anthropic API-Key</th>
                    <td>
                        <input type="password" name="rgcs_api_key" value="<?php echo esc_attr( get_option('rgcs_api_key') ); ?>" class="regular-text" />
                        <p class="description">Dein API-Key von <a href="https://console.anthropic.com/" target="_blank">console.anthropic.com</a></p>
                    </td>
                </tr>
                <tr>
                    <th>Login-Benutzername</th>
                    <td><input type="text" name="rgcs_username" value="<?php echo esc_attr( get_option('rgcs_username') ); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th>Login-Passwort</th>
                    <td><input type="text" name="rgcs_password" value="<?php echo esc_attr( get_option('rgcs_password') ); ?>" class="regular-text" /></td>
                </tr>
            </table>
            <?php submit_button( 'Einstellungen speichern' ); ?>
        </form>

        <hr />
        <h2>Gespeicherte Kontakte: <?php echo $count; ?></h2>
        <p>Shortcode: <code>[rg_cardscan]</code></p>

        <?php if ( $count > 0 ) : ?>
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <input type="hidden" name="action" value="rgcs_export_csv" />
                <?php wp_nonce_field( 'rgcs_export', 'rgcs_nonce' ); ?>
                <?php submit_button( 'Alle Kontakte als CSV exportieren', 'secondary' ); ?>
            </form>
        <?php endif; ?>

        <?php if ( $count > 0 ) : ?>
            <h3>Letzte Kontakte</h3>
            <table class="widefat striped">
                <thead><tr><th>Foto</th><th>Name</th><th>Firma</th><th>E-Mail</th><th>Zuständig</th><th>Bemerkung</th><th>Erfasst</th></tr></thead>
                <tbody>
                <?php
                $rows = $wpdb->get_results( "SELECT * FROM $table ORDER BY scanned_at DESC LIMIT 50" );
                $upload_url = wp_upload_dir()['baseurl'];
                foreach ( $rows as $r ) :
                    $photo_thumb = '';
                    if ( ! empty( $r->photo_path ) ) {
                        $photo_thumb = '<a href="' . esc_url( $upload_url . '/' . $r->photo_path ) . '" target="_blank"><img src="' . esc_url( $upload_url . '/' . $r->photo_path ) . '" style="width:60px;height:40px;object-fit:cover;border-radius:4px;" /></a>';
                    }
                ?>
                    <tr>
                        <td><?php echo $photo_thumb; ?></td>
                        <td><?php echo esc_html( $r->name ); ?></td>
                        <td><?php echo esc_html( $r->company ); ?></td>
                        <td><?php echo esc_html( $r->email ); ?></td>
                        <td><strong><?php echo esc_html( $r->zustaendig ); ?></strong></td>
                        <td><?php echo esc_html( mb_strimwidth( $r->bemerkung, 0, 60, '…' ) ); ?></td>
                        <td><?php echo esc_html( $r->scanned_at ); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php
}

/* ──────────────────────────────────────────────
   3. CSV EXPORT (Admin)
   ────────────────────────────────────────────── */
add_action( 'admin_post_rgcs_export_csv', 'rgcs_export_csv' );
function rgcs_export_csv() {
    if ( ! current_user_can('manage_options') || ! wp_verify_nonce( $_POST['rgcs_nonce'], 'rgcs_export' ) ) {
        wp_die( 'Nicht berechtigt.' );
    }
    global $wpdb;
    $table = $wpdb->prefix . 'rgcs_contacts';
    $rows  = $wpdb->get_results( "SELECT * FROM $table ORDER BY scanned_at DESC", ARRAY_A );

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=messekontakte_' . date('Y-m-d') . '.csv');
    $out = fopen('php://output', 'w');
    fprintf( $out, chr(0xEF) . chr(0xBB) . chr(0xBF) ); // BOM
    fputcsv( $out, ['Name','Firma','Position','E-Mail','Telefon','Mobil','Website','Adresse','Notizen','Bemerkung','Zuständig','Erfasst'], ';' );
    foreach ( $rows as $r ) {
        fputcsv( $out, [
            $r['name'], $r['company'], $r['position_title'], $r['email'],
            $r['phone'], $r['mobile'], $r['website'], $r['address'],
            $r['notes'], $r['bemerkung'], $r['zustaendig'], $r['scanned_at']
        ], ';' );
    }
    fclose( $out );
    exit;
}

/* ──────────────────────────────────────────────
   4. SHORTCODE [rg_cardscan]
   ────────────────────────────────────────────── */
add_shortcode( 'rg_cardscan', 'rgcs_shortcode' );
function rgcs_shortcode() {
    wp_enqueue_style( 'rgcs-style', RGCS_URL . 'assets/cardscan-style.css', [], RGCS_VERSION );
    wp_enqueue_script( 'rgcs-app', RGCS_URL . 'assets/cardscan-app.js', [], RGCS_VERSION, true );
    wp_localize_script( 'rgcs-app', 'rgcsData', [
        'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
        'nonce'     => wp_create_nonce( 'rgcs_nonce' ),
        'uploadUrl' => wp_upload_dir()['baseurl'],
    ] );
    return '<div id="rgcs-app"></div>';
}

/* ──────────────────────────────────────────────
   5. AJAX: Login check
   ────────────────────────────────────────────── */
add_action( 'wp_ajax_nopriv_rgcs_login', 'rgcs_ajax_login' );
add_action( 'wp_ajax_rgcs_login', 'rgcs_ajax_login' );
function rgcs_ajax_login() {
    check_ajax_referer( 'rgcs_nonce', 'nonce' );
    $user = sanitize_text_field( $_POST['username'] ?? '' );
    $pass = $_POST['password'] ?? '';
    if ( $user === get_option('rgcs_username') && $pass === get_option('rgcs_password') ) {
        // Set a session token
        $token = wp_generate_password( 32, false );
        set_transient( 'rgcs_session_' . $token, true, 12 * HOUR_IN_SECONDS );
        wp_send_json_success( [ 'token' => $token ] );
    }
    wp_send_json_error( 'Benutzername oder Passwort falsch.' );
}

/* helper: verify session token */
function rgcs_verify_token() {
    $token = sanitize_text_field( $_POST['token'] ?? $_GET['token'] ?? '' );
    if ( ! $token || ! get_transient( 'rgcs_session_' . $token ) ) {
        wp_send_json_error( 'Nicht angemeldet.', 401 );
    }
    return $token;
}

/* ──────────────────────────────────────────────
   5b. Increase PHP limits for large images
   ────────────────────────────────────────────── */
add_action( 'init', 'rgcs_increase_limits' );
function rgcs_increase_limits() {
    @ini_set( 'post_max_size', '20M' );
    @ini_set( 'upload_max_filesize', '20M' );
    @ini_set( 'memory_limit', '256M' );
    @ini_set( 'max_input_vars', 5000 );
}

/* ──────────────────────────────────────────────
   6. AJAX: Scan image (proxy to Anthropic API)
   ────────────────────────────────────────────── */
add_action( 'wp_ajax_nopriv_rgcs_scan', 'rgcs_ajax_scan' );
add_action( 'wp_ajax_rgcs_scan', 'rgcs_ajax_scan' );
function rgcs_ajax_scan() {
    check_ajax_referer( 'rgcs_nonce', 'nonce' );
    rgcs_verify_token();

    $api_key = get_option( 'rgcs_api_key' );
    if ( empty( $api_key ) ) {
        wp_send_json_error( 'API-Key nicht konfiguriert. Bitte im WP-Admin unter Einstellungen → CardScan eintragen.' );
    }

    $image_data = $_POST['image'] ?? '';
    if ( empty( $image_data ) ) {
        wp_send_json_error( 'Kein Bild übermittelt. Möglicherweise ist das Foto zu groß. Bitte erneut versuchen.' );
    }

    // Check base64 size — reject if over 4.5MB (API limit is ~5MB)
    $image_bytes = strlen( $image_data ) * 0.75; // base64 to raw bytes
    if ( $image_bytes > 4.5 * 1024 * 1024 ) {
        wp_send_json_error( 'Bild zu groß (' . round($image_bytes / 1024 / 1024, 1) . ' MB). Bitte näher an die Karte herangehen oder Foto in geringerer Auflösung aufnehmen.' );
    }

    $prompt = 'Du bist ein hochpräziser OCR-Spezialist für Visitenkarten mit jahrelanger Erfahrung.

DEINE AUFGABE:
Analysiere dieses Bild einer Visitenkarte extrem sorgfältig und extrahiere ALLE erkennbaren Kontaktdaten.

WICHTIGE REGELN FÜR MAXIMALE GENAUIGKEIT:
1. Lies JEDEN Buchstaben einzeln und genau ab. Verwechsle NICHT ähnliche Zeichen (0/O, l/1/I, rn/m, S/5, B/8).
2. E-Mail-Adressen: Prüfe die Domain besonders genau (.de, .com, .net etc.). Achte auf Punkte, Bindestriche und Unterstriche.
3. Telefonnummern: Übernimm sie EXAKT wie gedruckt, inklusive Vorwahl, Ländercode und Durchwahl. Unterscheide Festnetz (Tel/Phone/Fon) von Mobil (Mobil/Mobile/Handy/Cell).
4. Namen: Achte auf korrekte Groß-/Kleinschreibung und Sonderzeichen (ä, ö, ü, ß, é, etc.).
5. Firmenname: Übernimm die EXAKTE Schreibweise inkl. Rechtsform (GmbH, AG, SE, e.K., etc.).
6. Jobtitel/Position: Übernimm den kompletten Titel wie gedruckt.
7. Adresse: Straße mit Hausnummer, PLZ und Ort. Falls vorhanden auch Land.
8. Website: Komplette URL wie gedruckt.
9. Falls du ein Feld NICHT SICHER lesen kannst, setze es auf "" statt zu raten.
10. Unter "notes" erfasse ALLE weiteren Informationen die auf der Karte stehen und in kein anderes Feld passen (z.B. Abteilung, Faxnummer, Social Media Handles, LinkedIn, XING, weitere Standorte, USt-ID etc.).

Antworte AUSSCHLIESSLICH mit einem validen JSON-Objekt. Kein Markdown, keine Backticks, kein erklärender Text davor oder danach:
{
  "name": "Vor- und Nachname",
  "company": "Firmenname inkl. Rechtsform",
  "position": "Vollständiger Jobtitel",
  "email": "email@example.com",
  "phone": "Telefonnummer wie gedruckt",
  "mobile": "Mobilnummer wie gedruckt",
  "website": "www.example.com",
  "address": "Straße Hausnr., PLZ Ort, Land",
  "notes": "Alle weiteren Infos (Fax, Social Media, Abteilung etc.)"
}';

    $body = json_encode( [
        'model'      => 'claude-sonnet-4-20250514',
        'max_tokens' => 1500,
        'messages'   => [
            [
                'role'    => 'user',
                'content' => [
                    [ 'type' => 'image', 'source' => [
                        'type'       => 'base64',
                        'media_type' => 'image/jpeg',
                        'data'       => $image_data,
                    ] ],
                    [ 'type' => 'text', 'text' => $prompt ],
                ],
            ],
        ],
    ] );

    $response = wp_remote_post( 'https://api.anthropic.com/v1/messages', [
        'timeout' => 60,
        'headers' => [
            'Content-Type'      => 'application/json',
            'x-api-key'         => $api_key,
            'anthropic-version' => '2023-06-01',
        ],
        'body' => $body,
    ] );

    if ( is_wp_error( $response ) ) {
        wp_send_json_error( 'API-Fehler: ' . $response->get_error_message() );
    }

    $status = wp_remote_retrieve_response_code( $response );
    $data   = json_decode( wp_remote_retrieve_body( $response ), true );

    if ( $status !== 200 ) {
        $msg = $data['error']['message'] ?? 'HTTP ' . $status;
        wp_send_json_error( 'API-Fehler: ' . $msg );
    }

    $text = '';
    foreach ( ( $data['content'] ?? [] ) as $block ) {
        if ( ( $block['type'] ?? '' ) === 'text' ) $text .= $block['text'];
    }

    $text   = preg_replace( '/```json|```/', '', $text );
    $parsed = json_decode( trim( $text ), true );

    if ( ! $parsed ) {
        wp_send_json_error( 'KI-Antwort konnte nicht verarbeitet werden.' );
    }

    wp_send_json_success( $parsed );
}

/* ──────────────────────────────────────────────
   7. AJAX: Save contact (with photo + email)
   ────────────────────────────────────────────── */
add_action( 'wp_ajax_nopriv_rgcs_save', 'rgcs_ajax_save' );
add_action( 'wp_ajax_rgcs_save', 'rgcs_ajax_save' );
function rgcs_ajax_save() {
    check_ajax_referer( 'rgcs_nonce', 'nonce' );
    rgcs_verify_token();

    global $wpdb;
    $table = $wpdb->prefix . 'rgcs_contacts';

    // ── Save photo to uploads ──
    $photo_path = '';
    $photo_url  = '';
    $photo_b64  = $_POST['photo'] ?? '';
    if ( ! empty( $photo_b64 ) ) {
        $upload_dir = wp_upload_dir();
        $cardscan_dir = $upload_dir['basedir'] . '/cardscan';
        if ( ! file_exists( $cardscan_dir ) ) {
            wp_mkdir_p( $cardscan_dir );
            // Protect directory
            file_put_contents( $cardscan_dir . '/.htaccess', "Options -Indexes\n" );
        }
        $filename  = 'card_' . date('Y-m-d_H-i-s') . '_' . wp_generate_password(6, false) . '.jpg';
        $filepath  = $cardscan_dir . '/' . $filename;
        $raw_image = base64_decode( $photo_b64 );
        if ( $raw_image && file_put_contents( $filepath, $raw_image ) ) {
            $photo_path = 'cardscan/' . $filename;
            $photo_url  = $upload_dir['baseurl'] . '/cardscan/' . $filename;
        }
    }

    // ── Insert contact ──
    $contact_data = [
        'name'           => sanitize_text_field( $_POST['name'] ?? '' ),
        'company'        => sanitize_text_field( $_POST['company'] ?? '' ),
        'position_title' => sanitize_text_field( $_POST['position'] ?? '' ),
        'email'          => sanitize_email( $_POST['email'] ?? '' ),
        'phone'          => sanitize_text_field( $_POST['phone'] ?? '' ),
        'mobile'         => sanitize_text_field( $_POST['mobile'] ?? '' ),
        'website'        => esc_url_raw( $_POST['website'] ?? '' ),
        'address'        => sanitize_textarea_field( $_POST['address'] ?? '' ),
        'notes'          => sanitize_textarea_field( $_POST['notes'] ?? '' ),
        'bemerkung'      => sanitize_textarea_field( $_POST['bemerkung'] ?? '' ),
        'zustaendig'     => sanitize_text_field( $_POST['zustaendig'] ?? 'Nicht bekannt' ),
        'photo_path'     => $photo_path,
    ];
    $wpdb->insert( $table, $contact_data );
    $contact_id = $wpdb->insert_id;

    // ── Send email to assigned team member ──
    rgcs_send_notification( $contact_data, $contact_id, $photo_url, $filepath ?? '' );

    wp_send_json_success( [ 'id' => $contact_id ] );
}

/* ──────────────────────────────────────────────
   7b. Email notification to sales manager
   ────────────────────────────────────────────── */
function rgcs_send_notification( $c, $id, $photo_url, $photo_filepath ) {
    $zustaendig = $c['zustaendig'] ?? 'Nicht bekannt';
    $to = RGCS_TEAM_EMAILS[ $zustaendig ] ?? RGCS_TEAM_EMAILS['Nicht bekannt'];

    $name    = $c['name'] ?: 'Unbekannt';
    $company = $c['company'] ?: '—';
    $subject = "Neuer Messekontakt: {$name} ({$company})";

    $body  = "Hallo {$zustaendig},\n\n";
    $body .= "ein neuer Kontakt wurde auf der Messe für dich erfasst:\n\n";
    $body .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $body .= "Name:      {$c['name']}\n";
    $body .= "Firma:     {$c['company']}\n";
    $body .= "Position:  {$c['position_title']}\n";
    $body .= "E-Mail:    {$c['email']}\n";
    $body .= "Telefon:   {$c['phone']}\n";
    $body .= "Mobil:     {$c['mobile']}\n";
    $body .= "Website:   {$c['website']}\n";
    $body .= "Adresse:   {$c['address']}\n";
    $body .= "Notizen:   {$c['notes']}\n";
    $body .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    if ( ! empty( $c['bemerkung'] ) ) {
        $body .= "Bemerkung: {$c['bemerkung']}\n\n";
    }
    if ( ! empty( $photo_url ) ) {
        $body .= "Visitenkarten-Foto: {$photo_url}\n\n";
    }
    $body .= "Erfasst am: " . date('d.m.Y H:i') . " Uhr\n";
    $body .= "Kontakt-ID: #{$id}\n\n";
    $body .= "—\nCardScan – RoboPlanet Messe-Kontakterfassung\n";

    $headers = [ 'Content-Type: text/plain; charset=UTF-8' ];

    // Attach photo if available
    $attachments = [];
    if ( ! empty( $photo_filepath ) && file_exists( $photo_filepath ) ) {
        $attachments[] = $photo_filepath;
    }

    wp_mail( $to, $subject, $body, $headers, $attachments );
}

/* ──────────────────────────────────────────────
   8. AJAX: Get all contacts
   ────────────────────────────────────────────── */
add_action( 'wp_ajax_nopriv_rgcs_list', 'rgcs_ajax_list' );
add_action( 'wp_ajax_rgcs_list', 'rgcs_ajax_list' );
function rgcs_ajax_list() {
    check_ajax_referer( 'rgcs_nonce', 'nonce' );
    rgcs_verify_token();

    global $wpdb;
    $table = $wpdb->prefix . 'rgcs_contacts';
    $rows  = $wpdb->get_results( "SELECT * FROM $table ORDER BY scanned_at DESC", ARRAY_A );
    wp_send_json_success( $rows );
}

/* ──────────────────────────────────────────────
   9. AJAX: Update contact
   ────────────────────────────────────────────── */
add_action( 'wp_ajax_nopriv_rgcs_update', 'rgcs_ajax_update' );
add_action( 'wp_ajax_rgcs_update', 'rgcs_ajax_update' );
function rgcs_ajax_update() {
    check_ajax_referer( 'rgcs_nonce', 'nonce' );
    rgcs_verify_token();

    global $wpdb;
    $table = $wpdb->prefix . 'rgcs_contacts';
    $id    = intval( $_POST['id'] ?? 0 );
    if ( ! $id ) wp_send_json_error( 'Keine ID.' );

    $wpdb->update( $table, [
        'name'           => sanitize_text_field( $_POST['name'] ?? '' ),
        'company'        => sanitize_text_field( $_POST['company'] ?? '' ),
        'position_title' => sanitize_text_field( $_POST['position'] ?? '' ),
        'email'          => sanitize_email( $_POST['email'] ?? '' ),
        'phone'          => sanitize_text_field( $_POST['phone'] ?? '' ),
        'mobile'         => sanitize_text_field( $_POST['mobile'] ?? '' ),
        'website'        => esc_url_raw( $_POST['website'] ?? '' ),
        'address'        => sanitize_textarea_field( $_POST['address'] ?? '' ),
        'notes'          => sanitize_textarea_field( $_POST['notes'] ?? '' ),
        'bemerkung'      => sanitize_textarea_field( $_POST['bemerkung'] ?? '' ),
        'zustaendig'     => sanitize_text_field( $_POST['zustaendig'] ?? 'Nicht bekannt' ),
    ], [ 'id' => $id ] );

    wp_send_json_success();
}

/* ──────────────────────────────────────────────
   10. AJAX: Delete contact
   ────────────────────────────────────────────── */
add_action( 'wp_ajax_nopriv_rgcs_delete', 'rgcs_ajax_delete' );
add_action( 'wp_ajax_rgcs_delete', 'rgcs_ajax_delete' );
function rgcs_ajax_delete() {
    check_ajax_referer( 'rgcs_nonce', 'nonce' );
    rgcs_verify_token();

    global $wpdb;
    $table = $wpdb->prefix . 'rgcs_contacts';
    $id    = intval( $_POST['id'] ?? 0 );
    if ( ! $id ) wp_send_json_error( 'Keine ID.' );
    $wpdb->delete( $table, [ 'id' => $id ] );
    wp_send_json_success();
}

/* ──────────────────────────────────────────────
   11. AJAX: CSV export (frontend)
   ────────────────────────────────────────────── */
add_action( 'wp_ajax_nopriv_rgcs_csv', 'rgcs_ajax_csv' );
add_action( 'wp_ajax_rgcs_csv', 'rgcs_ajax_csv' );
function rgcs_ajax_csv() {
    check_ajax_referer( 'rgcs_nonce', 'nonce' );
    rgcs_verify_token();

    global $wpdb;
    $table = $wpdb->prefix . 'rgcs_contacts';
    $rows  = $wpdb->get_results( "SELECT * FROM $table ORDER BY scanned_at DESC", ARRAY_A );

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=messekontakte_' . date('Y-m-d') . '.csv');
    $out = fopen('php://output', 'w');
    fprintf( $out, chr(0xEF) . chr(0xBB) . chr(0xBF) );
    fputcsv( $out, ['Name','Firma','Position','E-Mail','Telefon','Mobil','Website','Adresse','Notizen','Bemerkung','Zuständig','Erfasst'], ';' );
    foreach ( $rows as $r ) {
        fputcsv( $out, [
            $r['name'], $r['company'], $r['position_title'], $r['email'],
            $r['phone'], $r['mobile'], $r['website'], $r['address'],
            $r['notes'], $r['bemerkung'], $r['zustaendig'], $r['scanned_at']
        ], ';' );
    }
    fclose( $out );
    exit;
}

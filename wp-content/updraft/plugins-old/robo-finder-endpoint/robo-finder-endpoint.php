<?php
/**
 * Plugin Name: Robo Finder Lead Endpoint
 * Description: REST API Endpoint to receive Robo-Finder leads from SureForms (Response URL).
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) exit;

class RoboFinder_Lead_Endpoint {
  const ROUTE_NS = 'robo-finder/v1';
  const ROUTE = '/lead';

  // ⚠️ Setze hier dein Secret (oder nutze wp-config.php / ENV)
  const SHARED_SECRET = 'rf_2026_7xK9pA3LqN2Z!';

  public static function init() {
    add_action('rest_api_init', [__CLASS__, 'register_routes']);
  }

  public static function register_routes() {
    register_rest_route(self::ROUTE_NS, self::ROUTE, [
      'methods'  => 'POST',
      'callback' => [__CLASS__, 'handle_lead'],
      'permission_callback' => '__return_true', // Auth via Secret-Header/Param
      'args' => [],
    ]);
  }

  private static function get_json_body(\WP_REST_Request $request) {
    $body = $request->get_body();
    if (!$body) return [];
    $data = json_decode($body, true);
    return is_array($data) ? $data : [];
  }

  private static function get_secret_from_request(\WP_REST_Request $request) {
    // 1) Header: X-RF-Secret: ...
    $header = $request->get_header('x-rf-secret');
    if (!empty($header)) return trim($header);

    // 2) Query param: ?secret=...
    $param = $request->get_param('secret');
    if (!empty($param)) return trim($param);

    return '';
  }

  private static function sanitize_deep($value) {
    if (is_array($value)) {
      $out = [];
      foreach ($value as $k => $v) {
        $out[sanitize_key((string)$k)] = self::sanitize_deep($v);
      }
      return $out;
    }
    if (is_bool($value) || is_int($value) || is_float($value)) return $value;
    return sanitize_text_field((string)$value);
  }

  private static function store_lead(array $lead) {
    // Einfacher Speicher: wp_options als "append-only" Liste
    // Für sehr viele Leads später besser: eigenes DB-Table oder CPT.

    $key = 'rf_leads';
    $existing = get_option($key, []);
    if (!is_array($existing)) $existing = [];

    $existing[] = $lead;

    // Limit, damit es nicht endlos wächst (z.B. letzte 500)
    $max = 500;
    if (count($existing) > $max) {
      $existing = array_slice($existing, -$max);
    }

    update_option($key, $existing, false);
  }

  public static function handle_lead(\WP_REST_Request $request) {
    // --- 1) Secret prüfen ---
    $secret = self::get_secret_from_request($request);
    if (!hash_equals(self::SHARED_SECRET, (string)$secret)) {
      return new \WP_REST_Response([
        'ok' => false,
        'error' => 'unauthorized',
        'message' => 'Missing or invalid secret.',
      ], 401);
    }

    // --- 2) Daten lesen ---
    $payload = self::get_json_body($request);

    // Manche Form-Tools schicken nested data — wir normalisieren leicht:
    // Wenn SureForms z.B. { "fields": {...}, "meta": {...} } sendet, ist das okay.
    $sanitized = self::sanitize_deep($payload);

    // --- 3) Lead-Objekt bauen ---
    $lead = [
      'received_at' => current_time('mysql'),
      'ip'          => isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : '',
      'user_agent'  => isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : '',
      'data'        => $sanitized,
    ];

    // --- 4) Speichern ---
    self::store_lead($lead);

    // --- 5) Optional: schnelle Sonderfall-Erkennung (Beispiel) ---
    // Wenn du "sonderfall" oder "kritisch" im Payload hast:
    $flag = false;
    if (isset($sanitized['sonderfall'])) $flag = (bool)$sanitized['sonderfall'];
    if (isset($sanitized['data']['sonderfall'])) $flag = (bool)$sanitized['data']['sonderfall'];

    return new \WP_REST_Response([
      'ok' => true,
      'stored' => true,
      'sonderfall' => $flag,
      'endpoint' => rest_url(self::ROUTE_NS . self::ROUTE),
    ], 200);
  }
}

RoboFinder_Lead_Endpoint::init();

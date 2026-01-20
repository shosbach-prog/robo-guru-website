<?php
/**
 * Plugin Name: RG FFmpeg Video Poster Generator
 * Description: Generates a poster image (frame grab) for self-hosted video uploads using ffmpeg and applies it as the poster attribute on WordPress video shortcodes/blocks.
 * Version: 1.0.0
 * Author: Robo-Guru Hotfix
 * License: GPLv2 or later
 */

if (!defined('ABSPATH')) { exit; }

class RG_FFmpeg_Video_Poster_Generator {

  const OPT_KEY = 'rg_ffmpeg_poster_settings';
  const META_POSTER_ID = '_rg_generated_poster_id';
  const META_POSTER_URL = '_rg_generated_poster_url';

  public static function init(): void {
    add_action('add_attachment', [__CLASS__, 'maybe_generate_poster_on_upload'], 20);
    add_filter('wp_video_shortcode', [__CLASS__, 'inject_poster_into_video_shortcode'], 10, 3);
    add_filter('render_block', [__CLASS__, 'inject_poster_into_core_video_block'], 10, 2);

    if (is_admin()) {
      add_action('admin_menu', [__CLASS__, 'register_settings_page']);
      add_action('admin_init', [__CLASS__, 'register_settings']);
    }
  }

  public static function default_settings(): array {
    return [
      'ffmpeg_bin' => 'ffmpeg',
      'timestamp' => '00:00:01',
      'quality' => 2,            // ffmpeg -q:v (lower is better)
      'suffix' => '-poster',      // output name suffix
      'force_regen' => false,
      'debug_log' => false,
    ];
  }

  public static function get_settings(): array {
    $saved = get_option(self::OPT_KEY, []);
    if (!is_array($saved)) { $saved = []; }
    return array_merge(self::default_settings(), $saved);
  }

  private static function exec_available(): bool {
    if (!function_exists('exec')) return false;
    $disabled = ini_get('disable_functions');
    if (!$disabled) return true;
    $disabled = array_map('trim', explode(',', (string)$disabled));
    return !in_array('exec', $disabled, true);
  }

  private static function log_msg(string $msg): void {
    $s = self::get_settings();
    if (!empty($s['debug_log'])) {
      error_log('[RG_FFmpeg_Poster] ' . $msg);
    }
  }

  public static function maybe_generate_poster_on_upload(int $attachment_id): void {
    $post = get_post($attachment_id);
    if (!$post || $post->post_type !== 'attachment') return;

    $mime = get_post_mime_type($attachment_id);
    if (!$mime || strpos($mime, 'video/') !== 0) return;

    $settings = self::get_settings();

    // Skip if already generated (unless forced)
    if (empty($settings['force_regen'])) {
      $existing = (int)get_post_meta($attachment_id, self::META_POSTER_ID, true);
      if ($existing > 0) return;
    }

    if (!self::exec_available()) {
      self::log_msg('exec() is not available. Skipping poster generation.');
      return;
    }

    $file = get_attached_file($attachment_id);
    if (!$file || !file_exists($file)) {
      self::log_msg('Video file missing for attachment ' . $attachment_id);
      return;
    }

    $pathinfo = pathinfo($file);
    $out_file = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . ($settings['suffix'] ?? '-poster') . '.jpg';

    $ffmpeg = trim((string)($settings['ffmpeg_bin'] ?? 'ffmpeg'));
    $timestamp = trim((string)($settings['timestamp'] ?? '00:00:01'));
    $quality = (int)($settings['quality'] ?? 2);
    if ($quality < 2) $quality = 2;
    if ($quality > 31) $quality = 31;

    $cmd = sprintf(
      '%s -y -ss %s -i %s -frames:v 1 -q:v %d %s 2>&1',
      escapeshellcmd($ffmpeg),
      escapeshellarg($timestamp),
      escapeshellarg($file),
      $quality,
      escapeshellarg($out_file)
    );

    self::log_msg('Running: ' . $cmd);
    $output = [];
    $ret = 0;
    @exec($cmd, $output, $ret);
    self::log_msg('ffmpeg return code: ' . $ret);

    if ($ret !== 0 || !file_exists($out_file)) {
      self::log_msg('ffmpeg failed. Output: ' . implode(' | ', $output));
      return;
    }

    $poster_id = self::attach_poster_as_attachment($attachment_id, $out_file);
    if (!$poster_id) {
      self::log_msg('Could not create poster attachment for ' . $out_file);
      return;
    }

    update_post_meta($attachment_id, self::META_POSTER_ID, $poster_id);
    update_post_meta($attachment_id, self::META_POSTER_URL, wp_get_attachment_url($poster_id));
  }

  private static function attach_poster_as_attachment(int $video_attachment_id, string $poster_abs_path): int {
    $poster_abs_path = wp_normalize_path($poster_abs_path);
    if (!file_exists($poster_abs_path)) return 0;

    // Create attachment post for the image
    $filetype = wp_check_filetype($poster_abs_path, null);
    $attachment = [
      'post_mime_type' => $filetype['type'] ?: 'image/jpeg',
      'post_title'     => sanitize_file_name(pathinfo($poster_abs_path, PATHINFO_FILENAME)),
      'post_content'   => '',
      'post_status'    => 'inherit',
      'post_parent'    => $video_attachment_id,
    ];

    $attach_id = wp_insert_attachment($attachment, $poster_abs_path);
    if (is_wp_error($attach_id) || !$attach_id) return 0;

    require_once ABSPATH . 'wp-admin/includes/image.php';
    $attach_data = wp_generate_attachment_metadata($attach_id, $poster_abs_path);
    wp_update_attachment_metadata($attach_id, $attach_data);

    // Back-link
    update_post_meta($attach_id, '_rg_generated_from_video', $video_attachment_id);

    return (int)$attach_id;
  }

  public static function inject_poster_into_video_shortcode(string $output, array $atts, $video_post_id): string {
    if (stripos($output, '<video') === false) return $output;

    $poster = $atts['poster'] ?? '';
    if ($poster) return $output;

    $vid_id = 0;
    if (!empty($atts['id'])) $vid_id = (int)$atts['id'];
    if (!$vid_id && is_numeric($video_post_id)) $vid_id = (int)$video_post_id;

    // Fall back to mapping src->attachment
    if (!$vid_id) {
      $src = $atts['src'] ?? ($atts['mp4'] ?? '');
      if ($src) $vid_id = (int)attachment_url_to_postid($src);
    }
    if (!$vid_id) return $output;

    $poster_url = (string)get_post_meta($vid_id, self::META_POSTER_URL, true);
    if (!$poster_url) {
      $poster_id = (int)get_post_meta($vid_id, self::META_POSTER_ID, true);
      if ($poster_id > 0) $poster_url = (string)wp_get_attachment_url($poster_id);
    }
    if (!$poster_url) return $output;

    // Inject poster + iOS friendly playsinline/preload
    if (preg_match('/<video\\b[^>]*>/i', $output, $m)) {
      $tag = $m[0];
      if (stripos($tag, ' playsinline') === false) {
        $tag = rtrim(substr($tag, 0, -1)) . ' playsinline>';
      }
      if (stripos($tag, ' preload=') === false) {
        $tag = rtrim(substr($tag, 0, -1)) . ' preload="metadata">';
      }
      $new_tag = preg_replace('/<video\\b/i', '<video poster="' . esc_url($poster_url) . '"', $tag, 1);
      return str_replace($m[0], $new_tag, $output);
    }

    return $output;
  }

  public static function inject_poster_into_core_video_block(string $block_content, array $block): string {
    if (empty($block['blockName']) || $block['blockName'] !== 'core/video') return $block_content;
    if (stripos($block_content, '<video') === false) return $block_content;
    if (stripos($block_content, ' poster=') !== false) return $block_content;

    $id = 0;
    if (!empty($block['attrs']['id'])) $id = (int)$block['attrs']['id'];
    if (!$id) return $block_content;

    $poster_url = (string)get_post_meta($id, self::META_POSTER_URL, true);
    if (!$poster_url) {
      $poster_id = (int)get_post_meta($id, self::META_POSTER_ID, true);
      if ($poster_id > 0) $poster_url = (string)wp_get_attachment_url($poster_id);
    }
    if (!$poster_url) return $block_content;

    $block_content = preg_replace('/<video\\b(?![^>]*\\bposter=)/i', '<video poster="' . esc_url($poster_url) . '"', $block_content, 1);
    if (stripos($block_content, ' playsinline') === false) {
      $block_content = preg_replace('/<video\\b/i', '<video playsinline', $block_content, 1);
    }
    if (stripos($block_content, ' preload=') === false) {
      $block_content = preg_replace('/<video\\b/i', '<video preload="metadata"', $block_content, 1);
    }
    return $block_content;
  }

  public static function register_settings_page(): void {
    add_options_page(
      'RG Video Poster (ffmpeg)',
      'RG Video Poster',
      'manage_options',
      'rg-ffmpeg-video-poster',
      [__CLASS__, 'render_settings_page']
    );
  }

  public static function register_settings(): void {
    register_setting('rg_ffmpeg_poster_group', self::OPT_KEY, [
      'type' => 'array',
      'sanitize_callback' => [__CLASS__, 'sanitize_settings'],
      'default' => self::default_settings(),
    ]);

    add_settings_section('rg_ffmpeg_main', 'FFmpeg Einstellungen', function () {
      echo '<p>Beim Upload einer Video-Datei wird mit <code>ffmpeg</code> automatisch ein Poster (Frame) erzeugt und als <code>poster</code>-Attribut genutzt.</p>';
    }, 'rg-ffmpeg-video-poster');

    add_settings_field('ffmpeg_bin', 'FFmpeg Binary', [__CLASS__, 'field_ffmpeg_bin'], 'rg-ffmpeg-video-poster', 'rg_ffmpeg_main');
    add_settings_field('timestamp', 'Frame Timestamp', [__CLASS__, 'field_timestamp'], 'rg-ffmpeg-video-poster', 'rg_ffmpeg_main');
    add_settings_field('quality', 'JPEG Qualität (q:v)', [__CLASS__, 'field_quality'], 'rg-ffmpeg-video-poster', 'rg_ffmpeg_main');
    add_settings_field('suffix', 'Datei-Suffix', [__CLASS__, 'field_suffix'], 'rg-ffmpeg-video-poster', 'rg_ffmpeg_main');
    add_settings_field('force_regen', 'Immer neu erzeugen', [__CLASS__, 'field_force_regen'], 'rg-ffmpeg-video-poster', 'rg_ffmpeg_main');
    add_settings_field('debug_log', 'Debug Log', [__CLASS__, 'field_debug_log'], 'rg-ffmpeg-video-poster', 'rg_ffmpeg_main');
  }

  public static function sanitize_settings($input): array {
    $d = self::default_settings();
    if (!is_array($input)) return $d;

    $out = [];
    $out['ffmpeg_bin'] = sanitize_text_field($input['ffmpeg_bin'] ?? $d['ffmpeg_bin']);
    $out['timestamp'] = sanitize_text_field($input['timestamp'] ?? $d['timestamp']);
    $out['quality'] = max(2, min(31, (int)($input['quality'] ?? $d['quality'])));
    $out['suffix'] = sanitize_text_field($input['suffix'] ?? $d['suffix']);
    $out['force_regen'] = !empty($input['force_regen']);
    $out['debug_log'] = !empty($input['debug_log']);
    return array_merge($d, $out);
  }

  public static function render_settings_page(): void {
    if (!current_user_can('manage_options')) return;
    echo '<div class="wrap"><h1>RG Video Poster (ffmpeg)</h1>';
    echo '<p><strong>Status:</strong> exec() ' . (self::exec_available() ? 'verfügbar ✅' : 'nicht verfügbar ❌') . '</p>';
    echo '<form method="post" action="options.php">';
    settings_fields('rg_ffmpeg_poster_group');
    do_settings_sections('rg-ffmpeg-video-poster');
    submit_button();
    echo '</form></div>';
  }

  public static function field_ffmpeg_bin(): void {
    $s = self::get_settings();
    printf('<input type="text" name="%s[ffmpeg_bin]" value="%s" class="regular-text" /> <p class="description">Standard: <code>ffmpeg</code>. In Plesk ggf. Pfad, z.B. <code>/usr/bin/ffmpeg</code>.</p>', esc_attr(self::OPT_KEY), esc_attr($s['ffmpeg_bin']));
  }

  public static function field_timestamp(): void {
    $s = self::get_settings();
    printf('<input type="text" name="%s[timestamp]" value="%s" class="regular-text" /> <p class="description">Format: <code>HH:MM:SS</code> (z.B. <code>00:00:01</code>).</p>', esc_attr(self::OPT_KEY), esc_attr($s['timestamp']));
  }

  public static function field_quality(): void {
    $s = self::get_settings();
    printf('<input type="number" min="2" max="31" name="%s[quality]" value="%d" /> <p class="description">ffmpeg <code>-q:v</code> (2 = beste, 31 = kleinste).</p>', esc_attr(self::OPT_KEY), (int)$s['quality']);
  }

  public static function field_suffix(): void {
    $s = self::get_settings();
    printf('<input type="text" name="%s[suffix]" value="%s" class="regular-text" /> <p class="description">Poster-Datei: <code>video%s.jpg</code></p>', esc_attr(self::OPT_KEY), esc_attr($s['suffix']), esc_html($s['suffix']));
  }

  public static function field_force_regen(): void {
    $s = self::get_settings();
    printf('<label><input type="checkbox" name="%s[force_regen]" %s /> bei Upload neu erzeugen</label>', esc_attr(self::OPT_KEY), checked(!empty($s['force_regen']), true, false));
  }

  public static function field_debug_log(): void {
    $s = self::get_settings();
    printf('<label><input type="checkbox" name="%s[debug_log]" %s /> in error_log schreiben</label>', esc_attr(self::OPT_KEY), checked(!empty($s['debug_log']), true, false));
  }
}

RG_FFmpeg_Video_Poster_Generator::init();

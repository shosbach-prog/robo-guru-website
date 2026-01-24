<?php
/**
 * Plugin Name: Robo Finder Pro
 * Description: Robo-Finder für Reinigungs- und Serviceroboter mit Roboter-Datenbank, Matching-Engine, Lead-Datenbank, Scraper, Dashboard-Analytics und Frontend-Grid/Compare. Shortcodes: [robo_finder], [robo_robot_grid], [robo_robot_compare ids="1,2,3"].
* Version: 4.8.18.18
 * Author: Robo-Guru / Sebastian
 * Text Domain: robo-finder-pro
 */





if (!defined('RF_PRO_VER')) { define('RF_PRO_VER','4.8.18.18'); }


/**
 * Simple template loader (editable files in /templates).
 * Usage: rf_pro_get_template('finder-header', ['title'=>'...', 'subtitle'=>'...']);
 */
if ( ! function_exists( 'rf_pro_get_template' ) ) {
  function rf_pro_get_template( $slug, $vars = array() ) {
    $file = plugin_dir_path( __FILE__ ) . 'templates/' . $slug . '.php';
    if ( ! file_exists( $file ) ) { return; }
    if ( is_array( $vars ) ) { extract( $vars, EXTR_SKIP ); }
    include $file;
  }
}

/**
 * Seed default taxonomy terms for the Robo Finder (runs once).
 */
function rfp_seed_default_terms() {
    $flag = get_option('rfp_seeded_terms_v1');
    if ($flag) return;

    $tax_terms = array(
        'robo_env' => array(
            'buero'        => 'Büro & Verwaltung',
            'einzelhandel' => 'Einzelhandel & Supermarkt',
            'logistik'     => 'Lager & Logistik',
            'produktion'   => 'Produktion & Industrie',
            'hotel'        => 'Hotel & Gastronomie',
            'klinik'       => 'Klinik & Pflege',
            'schule'       => 'Schule & Bildung',
            'parkhaus'     => 'Parkhaus & Tiefgarage',
        ),
        'robo_task' => array(
            'saugen'       => 'Saugen / Staub',
            'wischen'      => 'Wischen / Nass',
            'scheuern'     => 'Schrubben / Scheuersaugen',
            'kehren'       => 'Kehren',
            'transport'    => 'Transport / Lieferung',
            'service'      => 'Service / Abräumen',
        ),
        'robo_floor' => array(
            'fliesen'      => 'Fliesen',
            'beton'        => 'Beton / Industrie',
            'epoxid'       => 'Epoxid / Beschichtung',
            'vinyl'        => 'Vinyl / PVC',
            'teppich'      => 'Teppich',
            'holz'         => 'Holz / Laminat',
            'rampen'       => 'Rampen / Uneben',
        ),
    );

    foreach ($tax_terms as $tax => $pairs) {
        if (!taxonomy_exists($tax)) continue;
        $existing = get_terms(array('taxonomy'=>$tax,'hide_empty'=>false,'fields'=>'ids'));
        if (!is_wp_error($existing) && !empty($existing)) continue;

        foreach ($pairs as $slug => $name) {
            wp_insert_term($name, $tax, array('slug'=>$slug));
        }
    }

    update_option('rfp_seeded_terms_v1', 1, false);
}

register_activation_hook(__FILE__, 'rfp_seed_default_terms');

/**
 * Also seed lazily (first frontend hit) in case plugin was updated without activation.
 */
add_action('init', function(){
    if (is_admin() && !current_user_can('manage_options')) return;
    rfp_seed_default_terms();
}, 12);

class Robo_Finder_Pro_Plugin {

    private static $instance = null;
    private $settings_option = 'robo_finder_pro_settings';
    private $leads_table;

    public static function get_instance() {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        global $wpdb;
        $this->leads_table = $wpdb->prefix . 'robo_finder_leads';

        add_action( 'init', array( $this, 'register_cpt_and_taxonomies' ) );

        
        // Try to place the editor content in the desired position.
        // Note: some themes render robo_robot singles without calling the_content();
        // in that case we also inject via JS (see enqueue_assets()).
        // add_filter( 'the_content', array( $this, 'inject_robot_content_layout' ), 12 ); // disabled: theme controls content

        // Forum-Box auf Roboter-Detailseiten wieder anzeigen (nach dem Layout)
        // add_filter( 'the_content', array( $this, 'append_forum_box_to_robot' ), 22 ); // disabled

// Force Block Editor for our CPT (if theme/plugins disable it)
        add_filter( 'use_block_editor_for_post_type', array( $this, 'force_block_editor_for_robots' ), 10, 2 );

        add_action( 'add_meta_boxes', array( $this, 'register_robot_metabox' ) );
        add_action( 'save_post_robo_robot', array( $this, 'save_robot_contact_shortcode_meta' ), 10, 2 );

        add_filter( 'manage_edit-robo_robot_columns', array( $this, 'add_robot_columns' ) );
        add_action( 'manage_robo_robot_posts_custom_column', array( $this, 'render_robot_columns' ), 10, 2 );

        add_action( 'admin_menu', array( $this, 'register_admin_pages' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );

        // Use dedicated templates for robo_robot so theme single.php/archive.php stay untouched
        // add_filter( 'template_include', array( $this, 'maybe_use_robot_templates' ), 99 ); // disabled: do not touch theme templates


        // SEO / Meta overrides for archive + finder pages (works with/without Rank Math)
        add_filter( 'pre_get_document_title', array( $this, 'filter_document_title' ), 50 );
        add_filter( 'rank_math/frontend/title', array( $this, 'filter_rankmath_title' ), 50 );
        add_filter( 'rank_math/frontend/description', array( $this, 'filter_rankmath_description' ), 50 );
        add_filter( 'rank_math/frontend/canonical', array( $this, 'filter_rankmath_canonical' ), 50 );
        add_action( 'wp_head', array( $this, 'maybe_output_meta_description' ), 2 );

        add_shortcode( 'robo_finder', array( $this, 'render_robo_finder' ) );
        add_shortcode( 'robo_robot_grid', array( $this, 'render_robot_grid' ) );
        add_shortcode( 'robo_robot_compare', array( $this, 'render_robot_compare' ) );

        add_action( 'wp_ajax_robo_finder_recommend', array( $this, 'handle_recommendation' ) );
        add_action( 'wp_ajax_nopriv_robo_finder_recommend', array( $this, 'handle_recommendation' ) );
        add_action( 'wp_ajax_robo_finder_lead', array( $this, 'handle_lead' ) );
        add_action( 'wp_ajax_nopriv_robo_finder_lead', array( $this, 'handle_lead' ) );

        add_action( 'wp_dashboard_setup', array( $this, 'register_dashboard_widget' ) );
    }

    public static function activate() {
        global $wpdb;
        $table = $wpdb->prefix . 'robo_finder_leads';
        $charset_collate = $wpdb->get_charset_collate();
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $sql = "CREATE TABLE {$table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            created_at DATETIME NOT NULL,
            name VARCHAR(255) NOT NULL,
            company VARCHAR(255) DEFAULT '' NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(255) DEFAULT '' NOT NULL,
            note TEXT,
            data LONGTEXT,
            PRIMARY KEY (id)
        ) {$charset_collate};";

        dbDelta( $sql );
    

        // Register CPT/taxonomies and flush rewrite rules (ensures /roboter/ archive works)
        if ( class_exists( 'Robo_Finder_Pro_Plugin' ) ) {
            $inst = self::get_instance();
            if ( $inst ) {
                $inst->register_cpt_and_taxonomies();
            }
        }
        flush_rewrite_rules();

}

    public function force_block_editor_for_robots( $use, $post_type ) {
        if ( $post_type === 'robo_robot' ) {
            return true;
        }
        return $use;
    }

    public function register_cpt_and_taxonomies() {
        $labels = array(
            'name'               => __( 'Roboter', 'robo-finder-pro' ),
            'singular_name'      => __( 'Roboter', 'robo-finder-pro' ),
            'add_new'            => __( 'Neuen Roboter hinzufügen', 'robo-finder-pro' ),
            'add_new_item'       => __( 'Neuen Roboter anlegen', 'robo-finder-pro' ),
            'edit_item'          => __( 'Roboter bearbeiten', 'robo-finder-pro' ),
            'new_item'           => __( 'Neuer Roboter', 'robo-finder-pro' ),
            'view_item'          => __( 'Produktseite ansehen', 'robo-finder-pro' ),
            'view_items'         => __( 'Roboter ansehen', 'robo-finder-pro' ),
            'search_items'       => __( 'Roboter durchsuchen', 'robo-finder-pro' ),
            'not_found'          => __( 'Keine Roboter gefunden', 'robo-finder-pro' ),
            'not_found_in_trash' => __( 'Keine Roboter im Papierkorb', 'robo-finder-pro' ),
            'menu_name'          => __( 'Roboter', 'robo-finder-pro' ),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_rest'       => true,
            'menu_icon'          => 'dashicons-robot',
            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
            'has_archive'        => 'roboter',
            'rewrite'            => array(
                'slug'       => 'roboter',
                'with_front' => false,
            ),
        );

        register_post_type( 'robo_robot', $args );

        register_taxonomy(
            'robo_category',
            'robo_robot',
            array(
                'label'             => __( 'Roboter-Kategorie', 'robo-finder-pro' ),
                'public'            => false,
                'show_ui'           => true,
                'show_in_rest'      => true,
                'hierarchical'      => true,
                'show_admin_column' => true,
            )
        );

        register_taxonomy(
            'robo_env',
            'robo_robot',
            array(
                'label'             => __( 'Einsatzumfeld', 'robo-finder-pro' ),
                'public'            => false,
                'show_ui'           => true,
                'show_in_rest'      => true,
                'hierarchical'      => false,
                'show_admin_column' => true,
            )
        );

        register_taxonomy(
            'robo_floor',
            'robo_robot',
            array(
                'label'             => __( 'Bodenarten', 'robo-finder-pro' ),
                'public'            => false,
                'show_ui'           => true,
                'show_in_rest'      => true,
                'hierarchical'      => false,
                'show_admin_column' => true,
            )
        );

        register_taxonomy(
            'robo_task',
            'robo_robot',
            array(
                'label'             => __( 'Aufgaben', 'robo-finder-pro' ),
                'public'            => false,
                'show_ui'           => true,
                'show_in_rest'      => true,
                'hierarchical'      => false,
                'show_admin_column' => true,
            )
        );

        register_taxonomy(
            'robo_budget',
            'robo_robot',
            array(
                'label'             => __( 'Budgetklasse', 'robo-finder-pro' ),
                'public'            => false,
                'show_ui'           => true,
                'show_in_rest'      => true,
                'hierarchical'      => false,
                'show_admin_column' => true,
            )
        );
    }



/**
 * Use dedicated templates only for robo_robot single/archive.
 * Keeps theme single.php/archive.php untouched for everything else.
 */
public function maybe_use_robot_templates($template){
        // Only affect robo_robot single + archive. Never touch other pages/posts.
        if (!(is_singular('robo_robot') || is_post_type_archive('robo_robot'))) {
            return $template;
        }

    // Only affect the robo_robot CPT.
    if ( is_singular( 'robo_robot' ) ) {
        $t = plugin_dir_path( __FILE__ ) . 'templates/single-robo_robot.php';
        if ( file_exists( $t ) ) return $t;
    }
    if ( is_post_type_archive( 'robo_robot' ) ) {
        $t = plugin_dir_path( __FILE__ ) . 'templates/archive-robo_robot.php';
        if ( file_exists( $t ) ) return $t;
    }
    return $template;
}
    public function register_robot_metabox() {
        // Keep editor clean: only a small info box.
        add_meta_box(
            'robo_robot_details_notice',
            __( 'Roboter-Details', 'robo-finder-pro' ),
            array( $this, 'render_robot_metabox_notice' ),
            'robo_robot',
            'side',
            'high'
        );

        // Per-robot contact form shortcode override (SureForms)
        add_meta_box(
            'robo_robot_contact_form',
            __( 'Beratung: Formular (SureForms)', 'robo-finder-pro' ),
            array( $this, 'render_robot_contact_form_metabox' ),
            'robo_robot',
            'side',
            'default'
        );
    }

    public function render_robot_metabox_notice( $post ) {
        $url = admin_url( 'edit.php?post_type=robo_robot&page=robo-finder-pro-robot-settings&robot_id=' . intval( $post->ID ) );
        echo '<p><strong>' . esc_html__( 'Technische Daten pflegen', 'robo-finder-pro' ) . '</strong></p>';
        echo '<p>' . esc_html__( 'Die technischen Roboter-Details werden in einer eigenen Einstellungsseite gepflegt – der Editor bleibt sauber für Text & Medien.', 'robo-finder-pro' ) . '</p>';
        echo '<p><a class="button button-primary" href="' . esc_url( $url ) . '">' . esc_html__( 'Roboter-Einstellungen öffnen', 'robo-finder-pro' ) . '</a></p>';
    }

    public function render_robot_contact_form_metabox( $post ) {
        $val = (string) get_post_meta( $post->ID, '_rf_contact_shortcode', true );
        wp_nonce_field( 'rf_save_contact_shortcode', 'rf_contact_shortcode_nonce' );
        echo '<p><label for="rf_contact_shortcode"><strong>' . esc_html__( 'Shortcode (optional)', 'robo-finder-pro' ) . '</strong></label></p>';
        echo '<textarea id="rf_contact_shortcode" name="rf_contact_shortcode" rows="3" style="width:100%;" placeholder="[sureforms id=\'13734\']
<div class="rf-spinner" aria-hidden="true" hidden></div>">' . esc_textarea( $val ) . '</textarea>';
        echo '<p class="description">' . esc_html__( 'Wenn leer, wird der globale Shortcode aus den Robo Finder Pro Einstellungen verwendet.', 'robo-finder-pro' ) . '</p>';
    }

    public function save_robot_contact_shortcode_meta( $post_id, $post ) {
        if ( ! ( $post instanceof WP_Post ) || $post->post_type !== 'robo_robot' ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
        if ( empty( $_POST['rf_contact_shortcode_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['rf_contact_shortcode_nonce'] ) ), 'rf_save_contact_shortcode' ) ) {
            return;
        }

        $shortcode = isset( $_POST['rf_contact_shortcode'] ) ? trim( (string) wp_unslash( $_POST['rf_contact_shortcode'] ) ) : '';
        if ( $shortcode === '' ) {
            delete_post_meta( $post_id, '_rf_contact_shortcode' );
            return;
        }
        // Store as plain text; rendering happens via do_shortcode later.
        update_post_meta( $post_id, '_rf_contact_shortcode', $shortcode );
    }

    /**
     * Resolve the contact shortcode for a robot (per-robot override -> global setting).
     */
    private function get_contact_shortcode_for_robot( $robot_id ) {
        $robot_id = intval( $robot_id );
        if ( $robot_id > 0 ) {
            $per = trim( (string) get_post_meta( $robot_id, '_rf_contact_shortcode', true ) );
            if ( $per !== '' ) {
                return $per;
            }
        }
        $settings = $this->get_settings();
        return isset( $settings['contact_shortcode'] ) ? trim( (string) $settings['contact_shortcode'] ) : '';
    }

    public function add_robot_columns( $columns ) {
        $new = array();
        foreach ( $columns as $key => $label ) {
            if ( 'cb' === $key || 'title' === $key ) {
                $new[ $key ] = $label;
            }
        }

        $new['rf_manufacturer'] = __( 'Hersteller', 'robo-finder-pro' );
        $new['rf_category']     = __( 'Kategorie', 'robo-finder-pro' );
        $new['rf_env']          = __( 'Einsatzumfeld', 'robo-finder-pro' );
        $new['rf_task']         = __( 'Aufgaben', 'robo-finder-pro' );
        $new['rf_budget']       = __( 'Budgetklasse', 'robo-finder-pro' );
        $new['rf_docking']      = __( 'Docking', 'robo-finder-pro' );

        foreach ( $columns as $key => $label ) {
            if ( ! isset( $new[ $key ] ) ) {
                $new[ $key ] = $label;
            }
        }

        return $new;
    }

    public function render_robot_columns( $column, $post_id ) {
        switch ( $column ) {
            case 'rf_manufacturer':
                echo esc_html( get_post_meta( $post_id, '_rf_manufacturer', true ) );
                break;

            case 'rf_category':
                $terms = get_the_terms( $post_id, 'robo_category' );
                echo $this->render_term_list( $terms );
                break;

            case 'rf_env':
                $terms = get_the_terms( $post_id, 'robo_env' );
                echo $this->render_term_list( $terms );
                break;

            case 'rf_task':
                $terms = get_the_terms( $post_id, 'robo_task' );
                echo $this->render_term_list( $terms );
                break;

            case 'rf_budget':
                $terms = get_the_terms( $post_id, 'robo_budget' );
                echo $this->render_term_list( $terms );
                break;

            case 'rf_docking':
                $has = get_post_meta( $post_id, '_rf_has_docking', true ) === '1';
                echo $has ? '✔' : '–';
                break;
        }
    }

    private function render_term_list( $terms ) {
        if ( ! is_array( $terms ) || empty( $terms ) || is_wp_error( $terms ) ) {
            return '—';
        }
        $names = wp_list_pluck( $terms, 'name' );
        return esc_html( implode( ', ', $names ) );
    }

    public function register_admin_pages() {
        add_options_page(
            __( 'Robo Finder Pro', 'robo-finder-pro' ),
            __( 'Robo Finder Pro', 'robo-finder-pro' ),
            'manage_options',
            'robo-finder-pro',
            array( $this, 'render_settings_page' )
        );

        add_submenu_page(
            'edit.php?post_type=robo_robot',
            __( 'Roboter-Einstellungen', 'robo-finder-pro' ),
            __( 'Roboter-Einstellungen', 'robo-finder-pro' ),
            'manage_options',
            'robo-finder-pro-robot-settings',
            array( $this, 'render_robot_settings_page' )
        );

        add_submenu_page(
            'edit.php?post_type=robo_robot',
            __( 'Robo Finder Leads', 'robo-finder-pro' ),
            __( 'Robo Finder Leads', 'robo-finder-pro' ),
            'manage_options',
            'robo-finder-pro-leads',
            array( $this, 'render_leads_page' )
        );

        add_submenu_page(
            'edit.php?post_type=robo_robot',
            __( 'RoboPlanet Scraper', 'robo-finder-pro' ),
            __( 'RoboPlanet Scraper', 'robo-finder-pro' ),
            'manage_options',
            'robo-finder-pro-scraper',
            array( $this, 'render_scraper_page' )
        );
    }

    public function get_settings() {
        $defaults = array(
            'target_email' => get_option( 'admin_email' ),
            'contact_shortcode' => '',
            'contact_button_text' => 'Jetzt Beratung anfragen',
            'enable_forum_link' => '0',
            'forum_widget_mode' => 'floating',
            // Meta / SEO
            'robots_archive_meta_title' => '',
            'robots_archive_meta_description' => '',
            'finder_meta_title' => '',
            'finder_meta_description' => '',
        );
        $settings = get_option( $this->settings_option, array() );
        if ( ! is_array( $settings ) ) {
            $settings = array();
        }
        return wp_parse_args( $settings, $defaults );
    }

    public function register_settings() {
        register_setting(
            'robo_finder_pro_group',
            $this->settings_option,
            array(
                'type'              => 'array',
                'sanitize_callback' => array( $this, 'sanitize_settings' ),
                'default'           => array(),
            )
        );

        add_settings_section(
            'robo_finder_pro_main',
            __( 'Allgemeine Einstellungen', 'robo-finder-pro' ),
            array( $this, 'render_settings_section_intro' ),
            'robo-finder-pro'
        );

        add_settings_field(
            'target_email',
            __( 'Ziel-E-Mail für Leads', 'robo-finder-pro' ),
            array( $this, 'field_target_email' ),
            'robo-finder-pro',
            'robo_finder_pro_main'
        );

        add_settings_field(
            'contact_shortcode',
            __( 'Kontaktformular Shortcode (Modal)', 'robo-finder-pro' ),
            array( $this, 'field_contact_shortcode' ),
            'robo-finder-pro',
            'robo_finder_pro_main'
        );

        add_settings_field(
            'contact_button_text',
            __( 'Button-Text: Beratung anfragen', 'robo-finder-pro' ),
            array( $this, 'field_contact_button_text' ),
            'robo-finder-pro',
            'robo_finder_pro_main'
        );

        add_settings_field(
            'enable_forum_link',
            __( 'Forum-Verknüpfung aktivieren', 'robo-finder-pro' ),
            array( $this, 'field_enable_forum_link' ),
            'robo-finder-pro',
            'robo_finder_pro_main'
        );

        add_settings_field(
            'forum_widget_mode',
            __( 'Forum-Widget Anzeige', 'robo-finder-pro' ),
            array( $this, 'field_forum_widget_mode' ),
            'robo-finder-pro',
            'robo_finder_pro_main'
        );

        add_settings_section(
            'robo_finder_pro_meta',
            __( 'Meta / SEO', 'robo-finder-pro' ),
            function() {
                echo '<p>' . esc_html__( 'Optional: Meta-Titel & Description für /roboter/ (CPT-Archiv) und die Robo-Finder-Seite überschreiben. Funktioniert mit Rank Math (Filter) und ohne SEO-Plugin (fallback meta description).', 'robo-finder-pro' ) . '</p>';
            },
            'robo-finder-pro'
        );

        add_settings_field(
            'robots_archive_meta_title',
            __( 'Meta Title: /roboter/', 'robo-finder-pro' ),
            array( $this, 'field_robots_archive_meta_title' ),
            'robo-finder-pro',
            'robo_finder_pro_meta'
        );

        add_settings_field(
            'robots_archive_meta_description',
            __( 'Meta Description: /roboter/', 'robo-finder-pro' ),
            array( $this, 'field_robots_archive_meta_description' ),
            'robo-finder-pro',
            'robo_finder_pro_meta'
        );

        add_settings_field(
            'finder_meta_title',
            __( 'Meta Title: Robo Finder', 'robo-finder-pro' ),
            array( $this, 'field_finder_meta_title' ),
            'robo-finder-pro',
            'robo_finder_pro_meta'
        );

        add_settings_field(
            'finder_meta_description',
            __( 'Meta Description: Robo Finder', 'robo-finder-pro' ),
            array( $this, 'field_finder_meta_description' ),
            'robo-finder-pro',
            'robo_finder_pro_meta'
        );
    }

    public function render_settings_section_intro() {
        echo '<p>' . esc_html__( 'Leads aus dem Robo Finder werden an diese E-Mail-Adresse gesendet und in der Datenbank gespeichert.', 'robo-finder-pro' ) . '</p>';
    }

    public function sanitize_settings( $input ) {
        $input = is_array( $input ) ? $input : array();

        $output = array();

        $output['target_email'] = isset( $input['target_email'] ) && $input['target_email']
            ? sanitize_email( $input['target_email'] )
            : get_option( 'admin_email' );

        $output['contact_shortcode'] = isset( $input['contact_shortcode'] ) ? wp_kses_post( $input['contact_shortcode'] ) : '';
        $output['contact_button_text'] = isset( $input['contact_button_text'] ) && $input['contact_button_text']
            ? sanitize_text_field( $input['contact_button_text'] )
            : 'Jetzt Beratung anfragen';

        $output['enable_forum_link'] = ! empty( $input['enable_forum_link'] ) ? '1' : '0';

        $mode = isset( $input['forum_widget_mode'] ) ? sanitize_key( $input['forum_widget_mode'] ) : 'floating';
        $output['forum_widget_mode'] = in_array( $mode, array( 'floating', 'inline_top', 'inline_bottom', 'inline_after_first_post' ), true ) ? $mode : 'floating';

        // Meta / SEO overrides
        $output['robots_archive_meta_title'] = isset( $input['robots_archive_meta_title'] ) ? sanitize_text_field( $input['robots_archive_meta_title'] ) : '';
        $output['robots_archive_meta_description'] = isset( $input['robots_archive_meta_description'] ) ? sanitize_textarea_field( $input['robots_archive_meta_description'] ) : '';
        $output['finder_meta_title'] = isset( $input['finder_meta_title'] ) ? sanitize_text_field( $input['finder_meta_title'] ) : '';
        $output['finder_meta_description'] = isset( $input['finder_meta_description'] ) ? sanitize_textarea_field( $input['finder_meta_description'] ) : '';

        return $output;
    }

    public function field_target_email() {
        $settings = $this->get_settings();
        ?>
        <input type="email" name="<?php echo esc_attr( $this->settings_option ); ?>[target_email]" value="<?php echo esc_attr( $settings['target_email'] ); ?>" class="regular-text" />
        <p class="description">
            <?php esc_html_e( 'Eingehende Leads aus dem Robo Finder Pro werden an diese Adresse gemailt.', 'robo-finder-pro' ); ?>
        </p>
        <?php
    }
    public function field_contact_shortcode() {
        $settings = $this->get_settings();
        ?>
        <input type="text" name="<?php echo esc_attr( $this->settings_option ); ?>[contact_shortcode]" value="<?php echo esc_attr( $settings['contact_shortcode'] ); ?>" class="regular-text" placeholder="[contact-form-7 id=&quot;123&quot; title=&quot;Kontakt&quot;]" />
        <p class="description">
            <?php esc_html_e( 'Dieser Shortcode wird in einem Modal geöffnet, wenn Nutzer auf „Beratung anfragen“ klicken (Finder, Grid, Roboter-Detail).', 'robo-finder-pro' ); ?>
        </p>
        <?php
    }

    public function field_contact_button_text() {
        $settings = $this->get_settings();
        ?>
        <input type="text" name="<?php echo esc_attr( $this->settings_option ); ?>[contact_button_text]" value="<?php echo esc_attr( $settings['contact_button_text'] ); ?>" class="regular-text" />
        <?php
    }

    public function field_enable_forum_link() {
        $settings = $this->get_settings();
        ?>
        <label>
            <input type="checkbox" name="<?php echo esc_attr( $this->settings_option ); ?>[enable_forum_link]" value="1" <?php checked( $settings['enable_forum_link'], '1' ); ?> />
            <?php esc_html_e( 'Forum-Widget auf Forum-Topics anzeigen und Forum-Box auf Roboter-Detailseiten einblenden (wenn verknüpft).', 'robo-finder-pro' ); ?>
        </label>
        <?php
    }

    public function field_forum_widget_mode() {
        $settings = $this->get_settings();
        $mode = isset( $settings['forum_widget_mode'] ) ? $settings['forum_widget_mode'] : 'floating';
        ?>
        <select name="<?php echo esc_attr( $this->settings_option ); ?>[forum_widget_mode]">
            <option value="floating" <?php selected( $mode, 'floating' ); ?>><?php esc_html_e( 'Rechts schwebend (Desktop)', 'robo-finder-pro' ); ?></option>
            <option value="inline_top" <?php selected( $mode, 'inline_top' ); ?>><?php esc_html_e( 'Inline oben im Topic', 'robo-finder-pro' ); ?></option>
            <option value="inline_after_first_post" <?php selected( $mode, 'inline_after_first_post' ); ?>><?php esc_html_e( 'Inline unter dem ersten Beitrag (empfohlen)', 'robo-finder-pro' ); ?></option>
            <option value="inline_bottom" <?php selected( $mode, 'inline_bottom' ); ?>><?php esc_html_e( 'Inline unten im Topic', 'robo-finder-pro' ); ?></option>
        </select>
        <?php
    }

    public function field_robots_archive_meta_title() {
        $settings = $this->get_settings();
        ?>
        <input type="text" name="<?php echo esc_attr( $this->settings_option ); ?>[robots_archive_meta_title]" value="<?php echo esc_attr( $settings['robots_archive_meta_title'] ); ?>" class="regular-text" placeholder="Roboter im Vergleich – Robo-Guru" />
        <p class="description"><?php esc_html_e( 'Überschreibt den Title auf dem CPT-Archiv /roboter/. Leer lassen = Standard von Theme/SEO-Plugin.', 'robo-finder-pro' ); ?></p>
        <?php
    }

    public function field_robots_archive_meta_description() {
        $settings = $this->get_settings();
        ?>
        <textarea name="<?php echo esc_attr( $this->settings_option ); ?>[robots_archive_meta_description]" rows="3" class="large-text" placeholder="Vergleiche Reinigungs-, Service- und Transportroboter für Profis – mit klaren Empfehlungen, ROI-Check und Praxis-Tipps."><?php echo esc_textarea( $settings['robots_archive_meta_description'] ); ?></textarea>
        <p class="description"><?php esc_html_e( 'Meta-Description für /roboter/. Bei Rank Math wird sie über Filter gesetzt, ohne SEO-Plugin als Fallback im Head ausgegeben.', 'robo-finder-pro' ); ?></p>
        <?php
    }

    public function field_finder_meta_title() {
        $settings = $this->get_settings();
        ?>
        <input type="text" name="<?php echo esc_attr( $this->settings_option ); ?>[finder_meta_title]" value="<?php echo esc_attr( $settings['finder_meta_title'] ); ?>" class="regular-text" placeholder="Robo Finder – passender Roboter in 2 Minuten" />
        <p class="description"><?php esc_html_e( 'Überschreibt den Title auf der Robo-Finder-Seite (Slug: robo-finder).', 'robo-finder-pro' ); ?></p>
        <?php
    }

    public function field_finder_meta_description() {
        $settings = $this->get_settings();
        ?>
        <textarea name="<?php echo esc_attr( $this->settings_option ); ?>[finder_meta_description]" rows="3" class="large-text" placeholder="Beantworte ein paar Fragen und erhalte eine klare Roboter-Empfehlung – inkl. Einsatz-Check, Features und Beratung."><?php echo esc_textarea( $settings['finder_meta_description'] ); ?></textarea>
        <?php
    }

    private function is_robot_archive_context() {
        return function_exists( 'is_post_type_archive' ) && is_post_type_archive( 'robo_robot' );
    }

    private function is_finder_page_context() {
        return function_exists( 'is_page' ) && is_page( 'robo-finder' );
    }

    public function filter_document_title( $title ) {
        $settings = $this->get_settings();
        if ( $this->is_robot_archive_context() && ! empty( $settings['robots_archive_meta_title'] ) ) {
            return (string) $settings['robots_archive_meta_title'];
        }
        if ( $this->is_finder_page_context() && ! empty( $settings['finder_meta_title'] ) ) {
            return (string) $settings['finder_meta_title'];
        }
        return $title;
    }

    public function filter_rankmath_title( $title ) {
        return $this->filter_document_title( $title );
    }

    public function filter_rankmath_description( $description ) {
        $settings = $this->get_settings();
        if ( $this->is_robot_archive_context() && ! empty( $settings['robots_archive_meta_description'] ) ) {
            return (string) $settings['robots_archive_meta_description'];
        }
        if ( $this->is_finder_page_context() && ! empty( $settings['finder_meta_description'] ) ) {
            return (string) $settings['finder_meta_description'];
        }
        return $description;
    }

    /**
     * Canonical handling for /roboter/?mfg=...&q=... duplicates.
     * If filter params are present, canonical points to the clean archive URL.
     */
    public function filter_rankmath_canonical( $canonical ) {
        if ( $this->is_robot_archive_context() ) {
            $has_filters = false;
            if ( isset( $_GET['mfg'] ) && (string) $_GET['mfg'] !== '' ) {
                $has_filters = true;
            }
            if ( isset( $_GET['q'] ) && (string) $_GET['q'] !== '' ) {
                $has_filters = true;
            }

            if ( $has_filters ) {
                return home_url( '/roboter/' );
            }
        }

        return $canonical;
    }

    public function maybe_output_meta_description() {
        // Fallback: if Rank Math is active, we rely on its frontend filters.
        if ( defined( 'RANK_MATH_VERSION' ) ) {
            return;
        }

        $settings = $this->get_settings();
        $desc = '';
        if ( $this->is_robot_archive_context() && ! empty( $settings['robots_archive_meta_description'] ) ) {
            $desc = (string) $settings['robots_archive_meta_description'];
        } elseif ( $this->is_finder_page_context() && ! empty( $settings['finder_meta_description'] ) ) {
            $desc = (string) $settings['finder_meta_description'];
        }

        if ( $desc ) {
            echo '<meta name="description" content="' . esc_attr( $desc ) . '" />\n';
        }
    }


    public function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Robo Finder Pro – Einstellungen', 'robo-finder-pro' ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'robo_finder_pro_group' );
                do_settings_sections( 'robo-finder-pro' );
                submit_button();
                ?>
            </div>
        </div>
        <?php
    }

    private function get_robot_fields_map() {
        // field_name => meta_key
        return array(
            'manufacturer'  => '_rf_manufacturer',
            'm2h'           => '_rf_m2h',
            'battery'       => '_rf_battery_hours',
            'tank'          => '_rf_tank_liters',
            'clean_water'   => '_rf_clean_water',
            'dirty_water'   => '_rf_dirty_water',
            'nav'           => '_rf_nav',
            'has_docking'   => '_rf_has_docking',
            'price_band'    => '_rf_price_band',
            'highlight_1'   => '_rf_highlight_1',
            'highlight_2'   => '_rf_highlight_2',
            'highlight_3'   => '_rf_highlight_3',
                        'ideal_for'    => '_rf_ideal_for',
            'not_ideal_for'=> '_rf_not_ideal_for',
'dimensions'    => '_rf_dimensions',
            'working_width' => '_rf_working_width',
            'noise'         => '_rf_noise',
            'product_url'   => '_rf_product_url',
            'video_url'     => '_rf_video_url',
        );
    }

    private function sanitize_robot_field( $key, $value ) {
        $value = wp_unslash( $value );

        $numeric_keys = array( 'm2h', 'battery', 'tank', 'clean_water', 'dirty_water' );
        if ( in_array( $key, $numeric_keys, true ) ) {
            $v = str_replace( ',', '.', $value );
            $v = preg_replace( '/[^0-9\.]/', '', $v );
            return $v === '' ? '' : floatval( $v );
        }

        if ( $key === 'has_docking' ) {
            return $value ? '1' : '';
        }

        if ( $key === 'product_url' || $key === 'video_url' ) {
            return esc_url_raw( $value );
        }

        return sanitize_text_field( $value );
    }

    public function render_robot_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $robot_id = isset( $_GET['robot_id'] ) ? intval( $_GET['robot_id'] ) : 0;
        $message  = '';
        $error    = '';

        // Handle save
        if ( isset( $_POST['rf_robot_settings_save'] ) ) {
            check_admin_referer( 'rf_robot_settings_save', 'rf_robot_settings_nonce' );

            $robot_id = isset( $_POST['rf_robot_id'] ) ? intval( $_POST['rf_robot_id'] ) : 0;
            $robot    = get_post( $robot_id );

            if ( ! $robot || $robot->post_type !== 'robo_robot' ) {
                $error = __( 'Ungültiger Roboter.', 'robo-finder-pro' );
            } else {
                $map = $this->get_robot_fields_map();
                foreach ( $map as $field => $meta_key ) {
                    if ( $field === 'has_docking' ) {
                        $val = isset( $_POST['rf_' . $field ] ) ? '1' : '';
                        update_post_meta( $robot_id, $meta_key, $val );
                        continue;
                    }

                    $raw = isset( $_POST['rf_' . $field ] ) ? $_POST['rf_' . $field ] : '';
                    $val = $this->sanitize_robot_field( $field, $raw );

                    if ( $val === '' ) {
                        delete_post_meta( $robot_id, $meta_key );
                    } else {
                        update_post_meta( $robot_id, $meta_key, $val );
                    }
                }

                // Forum-Verknüpfung speichern
                $forum_enabled = isset( $_POST['rf_forum_enabled'] ) ? '1' : '';
                $forum_url_raw = isset( $_POST['rf_forum_topic_url'] ) ? wp_unslash( $_POST['rf_forum_topic_url'] ) : '';
                $forum_url = $forum_url_raw ? esc_url_raw( $forum_url_raw ) : '';

                if ( $forum_enabled ) {
                    update_post_meta( $robot_id, '_rf_forum_enabled', '1' );
                } else {
                    delete_post_meta( $robot_id, '_rf_forum_enabled' );
                }

                if ( $forum_url ) {
                    update_post_meta( $robot_id, '_rf_forum_topic_url', $forum_url );
                } else {
                    delete_post_meta( $robot_id, '_rf_forum_topic_url' );
                }
                $message = __( 'Roboter-Details wurden gespeichert.', 'robo-finder-pro' );
            }
        }

        $robots = get_posts(
            array(
                'post_type'      => 'robo_robot',
                'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
                'posts_per_page' => 200,
                'orderby'        => 'title',
                'order'          => 'ASC',
            )
        );

        $robot = $robot_id ? get_post( $robot_id ) : null;

        $values = array();
        if ( $robot && $robot->post_type === 'robo_robot' ) {
            foreach ( $this->get_robot_fields_map() as $field => $meta_key ) {
                $values[ $field ] = get_post_meta( $robot_id, $meta_key, true );
            }
            $values['forum_enabled'] = get_post_meta( $robot_id, '_rf_forum_enabled', true );
            $values['forum_topic_url'] = get_post_meta( $robot_id, '_rf_forum_topic_url', true );
        }

        $self_url = admin_url( 'edit.php?post_type=robo_robot&page=robo-finder-pro-robot-settings' );

        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Roboter-Einstellungen', 'robo-finder-pro' ); ?></h1>
            <p><?php esc_html_e( 'Hier pflegst du die technischen Roboter-Details getrennt vom Editor. Der Editor bleibt für Text, Bilder & Praxisberichte.', 'robo-finder-pro' ); ?></p>

            <?php if ( $message ) : ?>
                <div class="notice notice-success"><p><?php echo esc_html( $message ); ?></p></div>
            <?php endif; ?>

            <?php if ( $error ) : ?>
                <div class="notice notice-error"><p><?php echo esc_html( $error ); ?></p></div>
            <?php endif; ?>

            <form method="get" style="margin: 12px 0 18px; display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                <input type="hidden" name="post_type" value="robo_robot" />
                <input type="hidden" name="page" value="robo-finder-pro-robot-settings" />
                <label for="robot_id"><strong><?php esc_html_e( 'Roboter auswählen:', 'robo-finder-pro' ); ?></strong></label>
                <select name="robot_id" id="robot_id">
                    <option value="0"><?php esc_html_e( '— bitte wählen —', 'robo-finder-pro' ); ?></option>
                    <?php foreach ( $robots as $r ) : ?>
                        <option value="<?php echo esc_attr( $r->ID ); ?>" <?php selected( $robot_id, $r->ID ); ?>>
                            <?php echo esc_html( $r->post_title ); ?> (ID <?php echo esc_html( $r->ID ); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php submit_button( __( 'Laden', 'robo-finder-pro' ), 'secondary', '', false ); ?>
            </form>

            <?php if ( $robot && $robot->post_type === 'robo_robot' ) : ?>
                <hr/>
                <h2 style="margin-top:16px;"><?php echo esc_html( $robot->post_title ); ?></h2>
                <p style="margin-top:4px;">
                    <a href="<?php echo esc_url( get_edit_post_link( $robot_id, '' ) ); ?>" class="button"><?php esc_html_e( 'Beitrag/Editor öffnen', 'robo-finder-pro' ); ?></a>
                    <a href="<?php echo esc_url( get_permalink( $robot_id ) ); ?>" class="button" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Produktseite ansehen', 'robo-finder-pro' ); ?></a>
                </p>

                <form method="post">
                    <?php wp_nonce_field( 'rf_robot_settings_save', 'rf_robot_settings_nonce' ); ?>
                    <input type="hidden" name="rf_robot_id" value="<?php echo esc_attr( $robot_id ); ?>" />

                    <h3><?php esc_html_e( 'Basisdaten', 'robo-finder-pro' ); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th><label for="rf_manufacturer"><?php esc_html_e( 'Hersteller', 'robo-finder-pro' ); ?></label></th>
                            <td><input type="text" id="rf_manufacturer" name="rf_manufacturer" value="<?php echo esc_attr( $values['manufacturer'] ?? '' ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label for="rf_price_band"><?php esc_html_e( 'Preis-/Segmentbeschreibung', 'robo-finder-pro' ); ?></label></th>
                            <td><input type="text" id="rf_price_band" name="rf_price_band" value="<?php echo esc_attr( $values['price_band'] ?? '' ); ?>" class="regular-text" placeholder="z. B. Premium, Mittelklasse, Einstieg" /></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'Dockingstation verfügbar', 'robo-finder-pro' ); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="rf_has_docking" value="1" <?php checked( ( $values['has_docking'] ?? '' ), '1' ); ?> />
                                    <?php esc_html_e( 'Ja', 'robo-finder-pro' ); ?>
                                </label>
                            </td>
                        </tr>
                    </table>

                    <h3><?php esc_html_e( 'Leistungsdaten', 'robo-finder-pro' ); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th><label for="rf_m2h"><?php esc_html_e( 'Flächenleistung (m²/h, ca.)', 'robo-finder-pro' ); ?></label></th>
                            <td><input type="text" id="rf_m2h" name="rf_m2h" value="<?php echo esc_attr( $values['m2h'] ?? '' ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label for="rf_battery"><?php esc_html_e( 'Akkulaufzeit (Std., ca.)', 'robo-finder-pro' ); ?></label></th>
                            <td><input type="text" id="rf_battery" name="rf_battery" value="<?php echo esc_attr( $values['battery'] ?? '' ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label for="rf_tank"><?php esc_html_e( 'Tankvolumen gesamt (Liter, ca.)', 'robo-finder-pro' ); ?></label></th>
                            <td><input type="text" id="rf_tank" name="rf_tank" value="<?php echo esc_attr( $values['tank'] ?? '' ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label for="rf_clean_water"><?php esc_html_e( 'Reinwasser (Liter, ca.)', 'robo-finder-pro' ); ?></label></th>
                            <td><input type="text" id="rf_clean_water" name="rf_clean_water" value="<?php echo esc_attr( $values['clean_water'] ?? '' ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label for="rf_dirty_water"><?php esc_html_e( 'Abwasser (Liter, ca.)', 'robo-finder-pro' ); ?></label></th>
                            <td><input type="text" id="rf_dirty_water" name="rf_dirty_water" value="<?php echo esc_attr( $values['dirty_water'] ?? '' ); ?>" class="regular-text" /></td>
                        </tr>
                    </table>

                    <h3><?php esc_html_e( 'Maße & Geräusch', 'robo-finder-pro' ); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th><label for="rf_dimensions"><?php esc_html_e( 'Abmessungen', 'robo-finder-pro' ); ?></label></th>
                            <td><input type="text" id="rf_dimensions" name="rf_dimensions" value="<?php echo esc_attr( $values['dimensions'] ?? '' ); ?>" class="regular-text" placeholder="z. B. 540 x 440 x 617 mm (L/B/H)" /></td>
                        </tr>
                        <tr>
                            <th><label for="rf_working_width"><?php esc_html_e( 'Arbeitsbreite', 'robo-finder-pro' ); ?></label></th>
                            <td><input type="text" id="rf_working_width" name="rf_working_width" value="<?php echo esc_attr( $values['working_width'] ?? '' ); ?>" class="regular-text" placeholder="z. B. 330 – 410 mm" /></td>
                        </tr>
                        <tr>
                            <th><label for="rf_noise"><?php esc_html_e( 'Geräuschpegel', 'robo-finder-pro' ); ?></label></th>
                            <td><input type="text" id="rf_noise" name="rf_noise" value="<?php echo esc_attr( $values['noise'] ?? '' ); ?>" class="regular-text" placeholder="z. B. < 65 dB" /></td>
                        </tr>
                    </table>

                    <h3><?php esc_html_e( 'Technik & Links', 'robo-finder-pro' ); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th><label for="rf_nav"><?php esc_html_e( 'Navigation / Sensorik', 'robo-finder-pro' ); ?></label></th>
                            <td><input type="text" id="rf_nav" name="rf_nav" value="<?php echo esc_attr( $values['nav'] ?? '' ); ?>" class="regular-text" placeholder="z. B. LiDAR + 3D-Kamera" /></td>
                        </tr>
                        <tr>
                            <th><label for="rf_product_url"><?php esc_html_e( 'Produkt-URL (extern)', 'robo-finder-pro' ); ?></label></th>
                            <td><input type="url" id="rf_product_url" name="rf_product_url" value="<?php echo esc_attr( $values['product_url'] ?? '' ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label for="rf_video_url"><?php esc_html_e( 'Video-URL (optional)', 'robo-finder-pro' ); ?></label></th>
                            <td><input type="url" id="rf_video_url" name="rf_video_url" value="<?php echo esc_attr( $values['video_url'] ?? '' ); ?>" class="regular-text" /></td>
                        </tr>
                    </table>


                    <h3><?php esc_html_e( 'Forum-Verknüpfung', 'robo-finder-pro' ); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th><?php esc_html_e( 'Im Forum anzeigen', 'robo-finder-pro' ); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="rf_forum_enabled" value="1" <?php checked( ( $values['forum_enabled'] ?? '' ), '1' ); ?> />
                                    <?php esc_html_e( 'Ja, Roboter-Card im Forum-Topic anzeigen und Forum-Box auf der Roboter-Detailseite einblenden.', 'robo-finder-pro' ); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="rf_forum_topic_url"><?php esc_html_e( 'Forum-Topic URL', 'robo-finder-pro' ); ?></label></th>
                            <td>
                                <input type="url" id="rf_forum_topic_url" name="rf_forum_topic_url" value="<?php echo esc_attr( $values['forum_topic_url'] ?? '' ); ?>" class="regular-text" placeholder="https://robo-guru.de/community/forum/.../" />
                                <p class="description"><?php esc_html_e( 'Die URL zum passenden Forum-Thema (z. B. Phantas S1 Pro Topic).', 'robo-finder-pro' ); ?></p>
                            </td>
                        </tr>
                    </table>

                    <h3><?php esc_html_e( 'Highlights', 'robo-finder-pro' ); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th><label for="rf_highlight_1"><?php esc_html_e( 'Highlight 1', 'robo-finder-pro' ); ?></label></th>
                            <td><input type="text" id="rf_highlight_1" name="rf_highlight_1" value="<?php echo esc_attr( $values['highlight_1'] ?? '' ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label for="rf_highlight_2"><?php esc_html_e( 'Highlight 2', 'robo-finder-pro' ); ?></label></th>
                            <td><input type="text" id="rf_highlight_2" name="rf_highlight_2" value="<?php echo esc_attr( $values['highlight_2'] ?? '' ); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label for="rf_highlight_3"><?php esc_html_e( 'Highlight 3', 'robo-finder-pro' ); ?></label></th>
                            <td><input type="text" id="rf_highlight_3" name="rf_highlight_3" value="<?php echo esc_attr( $values['highlight_3'] ?? '' ); ?>" class="regular-text" /></td>
                        </tr>
                    </table>

                    <h3><?php esc_html_e( 'Ideal / Nicht ideal', 'robo-finder-pro' ); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th><label for="rf_ideal_for"><?php esc_html_e( 'Ideal für', 'robo-finder-pro' ); ?></label></th>
                            <td>
                                <textarea id="rf_ideal_for" name="rf_ideal_for" class="large-text" rows="4" placeholder="Stichpunkte oder kurzer Text..."><?php echo esc_textarea( $values['ideal_for'] ?? '' ); ?></textarea>
                                <p class="description"><?php esc_html_e( 'Wird im Frontend als „Ideal für“ Box angezeigt.', 'robo-finder-pro' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="rf_not_ideal_for"><?php esc_html_e( 'Nicht ideal für', 'robo-finder-pro' ); ?></label></th>
                            <td>
                                <textarea id="rf_not_ideal_for" name="rf_not_ideal_for" class="large-text" rows="4" placeholder="Stichpunkte oder kurzer Text..."><?php echo esc_textarea( $values['not_ideal_for'] ?? '' ); ?></textarea>
                                <p class="description"><?php esc_html_e( 'Wird im Frontend als „Nicht ideal für“ Box angezeigt.', 'robo-finder-pro' ); ?></p>
                            </td>
                        </tr>
                    </table>

                    <?php submit_button( __( 'Speichern', 'robo-finder-pro' ), 'primary', 'rf_robot_settings_save' ); ?>
                </form>

            <?php else : ?>
                <h2 style="margin-top:18px;"><?php esc_html_e( 'Alle Roboter', 'robo-finder-pro' ); ?></h2>
                <table class="widefat striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Roboter', 'robo-finder-pro' ); ?></th>
                            <th><?php esc_html_e( 'Hersteller', 'robo-finder-pro' ); ?></th>
                            <th><?php esc_html_e( 'Status', 'robo-finder-pro' ); ?></th>
                            <th><?php esc_html_e( 'Aktion', 'robo-finder-pro' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ( ! empty( $robots ) ) : ?>
                            <?php foreach ( $robots as $r ) :
                                $man = get_post_meta( $r->ID, '_rf_manufacturer', true );
                                $link = $self_url . '&robot_id=' . intval( $r->ID );
                                ?>
                                <tr>
                                    <td><?php echo esc_html( $r->post_title ); ?> <span style="color:#6b7280;">(ID <?php echo esc_html( $r->ID ); ?>)</span></td>
                                    <td><?php echo esc_html( $man ? $man : '—' ); ?></td>
                                    <td><?php echo esc_html( $r->post_status ); ?></td>
                                    <td><a class="button button-small" href="<?php echo esc_url( $link ); ?>"><?php esc_html_e( 'Konfigurieren', 'robo-finder-pro' ); ?></a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr><td colspan="4"><?php esc_html_e( 'Noch keine Roboter vorhanden.', 'robo-finder-pro' ); ?></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        </div>
        <?php
    }

    public function render_leads_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        global $wpdb;
        $table = $this->leads_table;
        $leads = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY created_at DESC LIMIT 200" );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Robo Finder Leads', 'robo-finder-pro' ); ?></h1>
            <p><?php esc_html_e( 'Übersicht der Leads, die direkt aus dem Robo Finder generiert wurden.', 'robo-finder-pro' ); ?></p>

            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Datum', 'robo-finder-pro' ); ?></th>
                        <th><?php esc_html_e( 'Name', 'robo-finder-pro' ); ?></th>
                        <th><?php esc_html_e( 'Firma', 'robo-finder-pro' ); ?></th>
                        <th><?php esc_html_e( 'E-Mail', 'robo-finder-pro' ); ?></th>
                        <th><?php esc_html_e( 'Telefon', 'robo-finder-pro' ); ?></th>
                        <th><?php esc_html_e( 'Hinweis', 'robo-finder-pro' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php if ( ! empty( $leads ) ) : ?>
                    <?php foreach ( $leads as $lead ) : ?>
                        <tr>
                            <td><?php echo esc_html( $lead->created_at ); ?></td>
                            <td><?php echo esc_html( $lead->name ); ?></td>
                            <td><?php echo esc_html( $lead->company ); ?></td>
                            <td><a href="mailto:<?php echo esc_attr( $lead->email ); ?>"><?php echo esc_html( $lead->email ); ?></a></td>
                            <td><?php echo esc_html( $lead->phone ); ?></td>
                            <td><?php echo esc_html( substr( (string) $lead->note, 0, 80 ) . ( strlen( (string) $lead->note ) > 80 ? '…' : '' ) ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6"><?php esc_html_e( 'Noch keine Leads vorhanden.', 'robo-finder-pro' ); ?></td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public function render_scraper_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $message = '';
        $error   = '';

        if ( isset( $_POST['rf_scraper_submit'] ) ) {
            check_admin_referer( 'rf_scrape_robot', 'rf_scrape_nonce' );

            $url = isset( $_POST['rf_scraper_url'] ) ? esc_url_raw( wp_unslash( $_POST['rf_scraper_url'] ) ) : '';
            if ( empty( $url ) ) {
                $error = __( 'Bitte eine URL eingeben.', 'robo-finder-pro' );
            } else {
                $result = $this->scrape_robo_planet( $url );
                if ( is_wp_error( $result ) ) {
                    $error = $result->get_error_message();
                } else {
                    $edit_link = get_edit_post_link( $result, '' );
                    $message   = sprintf(
                        __( 'Roboter wurde als Entwurf angelegt. %sJetzt bearbeiten%s.', 'robo-finder-pro' ),
                        '<a href="' . esc_url( $edit_link ) . '">',
                        '</a>'
                    );
                }
            }
        }

        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'RoboPlanet Scraper', 'robo-finder-pro' ); ?></h1>
            <p><?php esc_html_e( 'Lege Roboter aus RoboPlanet-Produktseiten automatisch an (Basisdaten wie Titel, Hersteller, Abmessungen, Flächenleistung usw.).', 'robo-finder-pro' ); ?></p>

            <?php if ( $message ) : ?>
                <div class="notice notice-success"><p><?php echo wp_kses_post( $message ); ?></p></div>
            <?php endif; ?>

            <?php if ( $error ) : ?>
                <div class="notice notice-error"><p><?php echo esc_html( $error ); ?></p></div>
            <?php endif; ?>

            <form method="post">
                <?php wp_nonce_field( 'rf_scrape_robot', 'rf_scrape_nonce' ); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="rf_scraper_url"><?php esc_html_e( 'RoboPlanet-URL', 'robo-finder-pro' ); ?></label>
                        </th>
                        <td>
                            <input type="url" id="rf_scraper_url" name="rf_scraper_url" class="regular-text" placeholder="https://robo-planet.de/roboter/gausium-phantas/" />
                            <p class="description">
                                <?php esc_html_e( 'Die Seite wird analysiert; anschließend wird ein Roboter als Entwurf angelegt, den du im Detail bearbeiten kannst.', 'robo-finder-pro' ); ?>
                            </p>
                        </td>
                    </tr>
                </table>
                <?php submit_button( __( 'Roboter aus URL anlegen', 'robo-finder-pro' ), 'primary', 'rf_scraper_submit' ); ?>
            </form>
        </div>
        <?php
    }

    private function scrape_robo_planet( $url ) {
        $response = wp_remote_get( $url, array( 'timeout' => 15 ) );
        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'rf_scrape_http', __( 'Fehler beim Abrufen der URL.', 'robo-finder-pro' ) );
        }

        $code = wp_remote_retrieve_response_code( $response );
        if ( 200 !== $code ) {
            return new WP_Error( 'rf_scrape_http_code', sprintf( __( 'HTTP-Status %d', 'robo-finder-pro' ), (int) $code ) );
        }

        $body  = wp_remote_retrieve_body( $response );
        if ( ! $body ) {
            return new WP_Error( 'rf_scrape_empty', __( 'Leere Antwort.', 'robo-finder-pro' ) );
        }

        $plain = wp_strip_all_tags( $body );
        $plain = preg_replace( '/\s+/', ' ', $plain );
        $plain = trim( $plain );

        $title = '';
        if ( preg_match( '/<h1[^>]*>(.*?)<\/h1>/is', $body, $m ) ) {
            $title = trim( wp_strip_all_tags( $m[1] ) );
        }
        if ( ! $title && preg_match( '/<title[^>]*>(.*?)<\/title>/is', $body, $m ) ) {
            $title = trim( wp_strip_all_tags( $m[1] ) );
        }
        if ( ! $title ) {
            $title = __( 'Unbenannter Roboter (Scraper)', 'robo-finder-pro' );
        }

        $manufacturer = '';
        if ( stripos( $plain, 'Gausium' ) !== false ) {
            $manufacturer = 'Gausium';
        } elseif ( stripos( $plain, 'Pudu' ) !== false ) {
            $manufacturer = 'Pudu Robotics';
        } elseif ( stripos( $plain, 'Nexaro' ) !== false ) {
            $manufacturer = 'Nexaro';
        }

        $m2h = '';
        if ( preg_match( '/(\d[\d\.\,]*)\s*m²\s*\/\s*Std\./u', $plain, $m ) ) {
            $m2h = floatval( str_replace( array( '.', ',' ), array( '', '.' ), $m[1] ) );
        }

        $post_id = wp_insert_post(
            array(
                'post_type'   => 'robo_robot',
                'post_status' => 'draft',
                'post_title'  => $title,
                'post_content'=> '',
            )
        );

        if ( is_wp_error( $post_id ) || ! $post_id ) {
            return new WP_Error( 'rf_scrape_insert', __( 'Roboter konnte nicht angelegt werden.', 'robo-finder-pro' ) );
        }

        if ( $manufacturer ) {
            update_post_meta( $post_id, '_rf_manufacturer', $manufacturer );
        }
        if ( $m2h !== '' ) {
            update_post_meta( $post_id, '_rf_m2h', $m2h );
        }
        update_post_meta( $post_id, '_rf_product_url', esc_url_raw( $url ) );

        return $post_id;
    }

    public function enqueue_assets() {
        if ( is_admin() ) {
            return;
        }

        $settings = $this->get_settings();

        global $post;
        $has_shortcode = ( $post instanceof WP_Post ) && (
            has_shortcode( $post->post_content, 'robo_finder' ) ||
            has_shortcode( $post->post_content, 'robo_robot_grid' ) ||
            has_shortcode( $post->post_content, 'robo_robot_compare' )
        );

        $is_finder_page = function_exists('is_page') && is_page( 'robo-finder' );
        $req_uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
        $is_finder_uri = ( $req_uri !== '' && strpos( $req_uri, '/robo-finder' ) !== false );

        // Keep archive styling disabled (BuddyBoss/Theme usually handles that nicely).
        $is_robot_archive = false;

        // IMPORTANT: We DO want minimal, safe styling on single robot pages
        // (e.g. responsive embeds/videos on mobile).
        $is_robot_single = function_exists('is_singular') && is_singular( 'robo_robot' );

        if ( ! $has_shortcode && ! $is_finder_page && ! $is_finder_uri && ! $is_robot_single ) {
            return; // no robo-finder / robot context
        }


        $is_forum_context = false;
        if ( isset( $settings['enable_forum_link'] ) && $settings['enable_forum_link'] === '1' ) {
            $req_path = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
            $req_path = parse_url( $req_path, PHP_URL_PATH );
            if ( is_string( $req_path ) && strpos( $req_path, '/community/forum/' ) === 0 ) {
                $is_forum_context = true;
            }
        }

        // 1) Single robot pages: only load a tiny stylesheet (no finder JS).
        if ( $is_robot_single ) {
            $ver = defined('RF_PRO_VER') ? RF_PRO_VER : '4.8.5';
            wp_enqueue_style(
                'robo-finder-pro-robot-single-css',
                plugin_dir_url( __FILE__ ) . 'assets/css/robot-single.css',
                array(),
                $ver
            );
        }

        // 2) Finder UI (steps / validation / summary) – only when finder is actually present.
        if ( $has_shortcode || $is_finder_page || $is_finder_uri || $is_robot_archive || $is_forum_context ) {
            // IMPORTANT:
            // Some previous builds referenced external asset files that might not exist after plugin updates.
            // To avoid broken styling/JS (e.g. modal looks "zerschossen"), we ship the essential CSS/JS inline.
            // Use plugin version for cache busting.
            $ver = defined('RF_PRO_VER') ? RF_PRO_VER : '4.8.5';

            wp_enqueue_style(
                'robo-finder-pro-css',
                plugin_dir_url( __FILE__ ) . 'assets/css/finder.css',
                array(),
                $ver
            );

            // Finder UI logic (steps / validation / summary)
            // Declare jQuery as dependency so "$" works reliably in WP no-conflict.
            wp_enqueue_script(
                'robo-finder-pro-finder',
                plugin_dir_url( __FILE__ ) . 'assets/js/finder.js',
                array( 'jquery' ),
                $ver,
                true
            );

            $inline_css = <<<CSS
/* === Robo Finder Pro: Base === */
.rf-section{margin:18px 0}

/* === Modal / Lightbox (Robo-Guru Look) === */
.rf-modal-overlay{position:fixed;inset:0;background:rgba(7,18,35,.62);backdrop-filter:saturate(120%) blur(3px);z-index:99990}
.rf-modal{position:fixed;left:50%;top:50%;transform:translate(-50%,-50%);width:min(520px,calc(100vw - 32px));max-height:calc(100vh - 96px);background:#fff;border-radius:16px;box-shadow:0 20px 70px rgba(0,0,0,.35);z-index:99999;overflow:hidden}
.rf-modal-header{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 14px 14px 16px;background:linear-gradient(90deg,#00BCD4 0%,#ff8a00 100%);color:#fff}
.rf-modal-title{margin:0;font-size:18px;line-height:1.2;font-weight:800;letter-spacing:.2px}
.rf-modal-close{appearance:none;border:1px solid rgba(15,23,42,.10);background:rgba(255,255,255,.92);color:#0b1320;width:38px;height:38px;border-radius:12px;cursor:pointer;display:grid;place-items:center;font-size:20px;line-height:1;transition:transform .12s ease, background .12s ease}
.rf-modal-close:hover{transform:scale(1.03);background:#fff}
.rf-modal-close:active{transform:scale(.98)}
.rf-modal-body{padding:16px;overflow:auto;max-height:calc(100vh - 160px)}
.rf-modal-note{margin:0 0 10px 0;padding:10px 12px;border-radius:10px;background:#f3f7ff;border:1px solid rgba(0,188,212,.25);font-size:13px;line-height:1.35;color:#0b1320}
.rf-modal-note.is-success{background:#ecfff9;border-color:rgba(0,188,212,.45)}
.rf-modal-note.is-error{background:#fff3f3;border-color:rgba(255,138,0,.45)}

/* SureForms inside modal: tighten typography + make fields full width */
.rf-modal .srfm-form, .rf-modal form{font-size:14px;line-height:1.4}
.rf-modal label{font-weight:700;font-size:13px;line-height:1.25;margin-bottom:6px}
.rf-modal .srfm-field, .rf-modal .srfm-field-wrapper{margin-bottom:12px}
.rf-modal input[type="text"],
.rf-modal input[type="email"],
.rf-modal input[type="tel"],
.rf-modal input[type="number"],
.rf-modal textarea,
.rf-modal select{width:100%!important;max-width:100%!important;box-sizing:border-box;border-radius:10px;border:1px solid rgba(15,23,42,.18);padding:10px 12px;font-size:14px;line-height:1.3;outline:none}
.rf-modal textarea{min-height:110px;resize:vertical}
.rf-modal input:focus, .rf-modal textarea:focus, .rf-modal select:focus{border-color:rgba(0,188,212,.75);box-shadow:0 0 0 3px rgba(0,188,212,.16)}
.rf-modal .srfm-help, .rf-modal .srfm-desc, .rf-modal .srfm-field-description{font-size:12px;line-height:1.35;color:rgba(15,23,42,.72)}
.rf-modal button[type="submit"], .rf-modal input[type="submit"]{border:0;border-radius:12px;padding:12px 14px;font-weight:800;cursor:pointer;width:100%;max-width:100%}

/* Two-column layout (desktop) – auto-applied inside the modal */
@media (min-width: 821px){
  .rf-modal form.rf-2col{display:flex;flex-wrap:wrap;gap:12px}
  .rf-modal form.rf-2col .srfm-field, 
  .rf-modal form.rf-2col .srfm-field-wrapper,
  .rf-modal form.rf-2col .sureforms-field,
  .rf-modal form.rf-2col .sf-field{width:100%}
  /* 1–4 fields become 2-col (Firma/Vorname/Nachname/E-Mail in 2 Spalten), rest full width */
  .rf-modal form.rf-2col .srfm-field-wrapper:nth-of-type(1),
  .rf-modal form.rf-2col .srfm-field-wrapper:nth-of-type(2),
  .rf-modal form.rf-2col .srfm-field-wrapper:nth-of-type(3),
  .rf-modal form.rf-2col .srfm-field-wrapper:nth-of-type(4),
  .rf-modal form.rf-2col .sureforms-field:nth-of-type(1),
  .rf-modal form.rf-2col .sureforms-field:nth-of-type(2),
  .rf-modal form.rf-2col .sureforms-field:nth-of-type(3),
  .rf-modal form.rf-2col .sureforms-field:nth-of-type(4),
  .rf-modal form.rf-2col .sf-field:nth-of-type(1),
  .rf-modal form.rf-2col .sf-field:nth-of-type(2),
  .rf-modal form.rf-2col .sf-field:nth-of-type(3),
  .rf-modal form.rf-2col .sf-field:nth-of-type(4){width:calc(50% - 6px)}
}

/* Sticky submit button for long forms (inside modal scroll area) */
.rf-modal .rf-sticky-submit{position:sticky;bottom:-1px;left:0;right:0;padding:12px 0 0 0;margin-top:12px;background:linear-gradient(180deg,rgba(255,255,255,0) 0%, #fff 28%);}
.rf-modal .rf-sticky-submit button[type="submit"],
.rf-modal .rf-sticky-submit input[type="submit"]{box-shadow:0 10px 30px rgba(0,0,0,.18)}



/* Mobile fullscreen modal */
@media (max-width: 720px){
  .rf-modal{left:0;top:0;transform:none;width:100vw;max-height:100vh;height:100vh;border-radius:0}
  .rf-modal-body{max-height:calc(100vh - 72px)}
}

/* === RankMath FAQ Styling (Robo-Guru cards + checkmark) === */
.rank-math-list-item{position:relative;border:1px solid rgba(15,23,42,.10);border-radius:10px;padding:14px 14px 12px 14px;margin:12px 0;background:#fff;box-shadow:0 10px 26px rgba(0,0,0,.05);overflow:hidden}
.rank-math-list-item:before{content:"";position:absolute;left:0;top:0;bottom:0;width:5px;background:linear-gradient(180deg,#00BCD4 0%,#ff8a00 100%)}
.rank-math-list-item .rank-math-question{display:flex;align-items:flex-start;gap:10px;font-weight:900;margin:0 0 8px 0;font-size:15px;line-height:1.3}
.rank-math-list-item .rank-math-question:before{content:"✓";display:inline-grid;place-items:center;flex:0 0 auto;width:22px;height:22px;border-radius:7px;background:rgba(0,188,212,.14);border:1px solid rgba(0,188,212,.28);color:#0b1320;font-size:14px;line-height:1;margin-top:1px}
.rank-math-list-item .rank-math-answer{margin:0;color:rgba(15,23,42,.85);font-size:14px;line-height:1.6}
CSS;
            wp_add_inline_style( 'robo-finder-pro-css', $inline_css );
            wp_register_script( 'robo-finder-pro-js', false, array( 'jquery' ), $ver, true );
            wp_enqueue_script( 'robo-finder-pro-js' );

            $resolved_contact_shortcode = '';
            if ( $is_robot_single ) {
                $resolved_contact_shortcode = $this->get_contact_shortcode_for_robot( get_the_ID() );
            } else {
                $resolved_contact_shortcode = isset( $settings['contact_shortcode'] ) ? trim( (string) $settings['contact_shortcode'] ) : '';
            }

            wp_localize_script(
                'robo-finder-pro-js',
                'RoboFinderPro',
                array(
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'nonce'    => wp_create_nonce( 'robo_finder_nonce' ),
                    'contact_button_text' => (string) $settings['contact_button_text'],
                    'is_robot_single' => $is_robot_single ? '1' : '0',
                    // Some themes don't render the editor content for robo_robot singles.
                    // We pass the rendered article HTML to the frontend so JS can place it
                    // between "Ideal für" and "Technische Daten".
                    'robot_article_html' => $is_robot_single ? $this->get_robot_article_html() : '',
                    'has_contact_shortcode' => ( $resolved_contact_shortcode !== '' ) ? '1' : '0',
                )
            );

            $inline_js = <<<JS
(function($){
  function getModal(){ return document.getElementById('rf-contact-modal'); }
  function getOverlay(){ return document.getElementById('rf-modal-overlay'); }
  function noteEl(){
    var m = getModal();
    if(!m) return null;
    var n = m.querySelector('.rf-modal-note');
    if(!n){
      n = document.createElement('p');
      n.className = 'rf-modal-note';
      n.style.display = 'none';
      var body = m.querySelector('.rf-modal-body');
      if(body) body.insertAdjacentElement('afterbegin', n);
    }
    return n;
  }
  function showNote(msg, type){
    var n = noteEl();
    if(!n) return;
    n.classList.remove('is-success','is-error');
    if(type==='success') n.classList.add('is-success');
    if(type==='error') n.classList.add('is-error');
    n.textContent = msg;
    n.style.display = 'block';
  }
  function hideNote(){
    var n = noteEl();
    if(!n) return;
    n.style.display = 'none';
  }
  function openModal(){
    var m = getModal(), o = getOverlay();
    if(!m || !o) return;
    o.style.display = 'block';
    m.style.display = 'block';
    document.documentElement.classList.add('rf-modal-open');
    document.body.style.overflow = 'hidden';
    hideNote();
    enhanceModalForm();
    // Focus first field
    setTimeout(function(){
      var first = m.querySelector('input, textarea, select, button');
      if(first) first.focus({preventScroll:true});
    }, 50);
  }

  // Enhance SureForms inside the modal:
  // - apply 2-column layout on desktop
  // - create a sticky submit button wrapper for long forms
  function enhanceModalForm(){
    var m = getModal();
    if(!m) return;
    var form = m.querySelector('form');
    if(!form) return;
    // add class for CSS grid/flex rules
    form.classList.add('rf-2col');

    // Sticky submit wrapper
    var submit = form.querySelector('button[type="submit"],input[type="submit"]');
    if(submit){
      var wrap = form.querySelector('.rf-sticky-submit');
      if(!wrap){
        wrap = document.createElement('div');
        wrap.className = 'rf-sticky-submit';
        // Put sticky wrapper at the end of the form
        form.appendChild(wrap);
        wrap.appendChild(submit);
      }
    }
  }
  function closeModal(){
    var m = getModal(), o = getOverlay();
    if(!m || !o) return;
    m.style.display = 'none';
    o.style.display = 'none';
    document.documentElement.classList.remove('rf-modal-open');
    document.body.style.overflow = '';
  }

  // Open triggers (only on robot singles)
  function bindTriggers(){
    if(!document.body.classList.contains('single-robo_robot')) return;
    document.addEventListener('click', function(e){
      var t = e.target;
      if(!t) return;
      var btn = t.closest('.rf-open-contact-modal');
      if(btn){
        e.preventDefault();
        openModal();
        return;
      }
      // Also upgrade existing theme buttons that look like "Beratung" CTAs
      var maybe = t.closest('a,button');
      if(maybe && maybe.matches('a,button')){
        var txt = (maybe.textContent||'').toLowerCase();
        if(txt.includes('berat') || txt.includes('demo') || txt.includes('anfragen')){
          if(maybe.getAttribute('href') === '#' || maybe.classList.contains('rf-upgraded-cta')){
            e.preventDefault();
            openModal();
          }
        }
      }
    }, true);
  }

  function bindClose(){
    document.addEventListener('click', function(e){
      var t = e.target;
      if(!t) return;
      if(t.closest('.rf-modal-close')){ e.preventDefault(); closeModal(); return; }
      // Close only when clicking the overlay itself
      var o = getOverlay();
      if(o && t === o){ closeModal(); }
    });
    document.addEventListener('keydown', function(e){
      if(e.key === 'Escape'){ closeModal(); }
    });
  }

  // Prevent accidental close on submit button clicks (keep modal open)
  function guardInsideClicks(){
    document.addEventListener('click', function(e){
      var m = getModal();
      if(!m || m.style.display==='none') return;
      if(e.target && e.target.closest('#rf-contact-modal')){
        e.stopPropagation();
      }
    }, true);
  }

  // SureForms events: show a nice message and keep modal open
  function bindSureFormsEvents(){
    function onSuccess(ev){
      // Try to pull message from event.detail
      var msg = 'Danke! Wir haben deine Anfrage erhalten und melden uns kurzfristig.';
      try{
        if(ev && ev.detail && ev.detail.message){ msg = ev.detail.message; }
      }catch(_){ }
      showNote(msg, 'success');
    }
    function onFail(ev){
      showNote('Ups – das hat nicht geklappt. Bitte prüfe die Pflichtfelder oder versuche es erneut.', 'error');
    }
    // According to SureForms docs these events exist.
    document.addEventListener('srfm_on_show_success_message', onSuccess);
    window.addEventListener('srfm_on_show_success_message', onSuccess);
    document.addEventListener('srfm_on_trigger_form_submission_failure', onFail);
    window.addEventListener('srfm_on_trigger_form_submission_failure', onFail);
    document.addEventListener('srfm_on_trigger_form_submission', function(){
      showNote('Sende…', '');
    });
    window.addEventListener('srfm_on_trigger_form_submission', function(){
      showNote('Sende…', '');
    });
  }

  $(function(){
    bindTriggers();
    bindClose();
    guardInsideClicks();
    bindSureFormsEvents();
  });
})(jQuery);
JS;
            wp_add_inline_script( 'robo-finder-pro-js', $inline_js, 'after' );

            // Contact modal should only be available on robot detail pages.
            // Otherwise (e.g. forum pages) any accidental class overlap could trigger the modal.
            if ( $is_robot_single && $resolved_contact_shortcode !== '' ) {
                add_action( 'wp_footer', array( $this, 'render_contact_modal' ), 50 );
            }

            // Mount 'Diskussion im Forum' into #rg_forum_mount (Robo-Guru template).
            if ( $is_robot_single ) {
                add_action( 'wp_footer', array( $this, 'render_rg_forum_mount' ), 45 );
            }

            // Forum-Widget auf Forum-Seiten bleibt optional.
            if ( isset( $settings['enable_forum_link'] ) && $settings['enable_forum_link'] === '1' ) {
                add_action( 'wp_footer', array( $this, 'maybe_render_forum_widget' ), 40 );
            }
        }
    }

    /**
 * Robo-Guru Theme mount: writes the Forum-Box into <div id="rg_forum_mount"></div>.
 * This avoids theme-side meta key mismatches and ensures the forum link is shown consistently.
 * Also shows a lightweight "Beiträge" count if the linked URL points to a bbPress topic.
 */
public function render_rg_forum_mount() {
    if ( ! is_singular( 'robo_robot' ) ) {
        return;
    }

    global $post;
    if ( ! $post || empty( $post->ID ) ) {
        return;
    }

    $robot_id = (int) $post->ID;

    // Be tolerant: accept both underscored and non-underscored meta keys.
    $enabled = (string) get_post_meta( $robot_id, '_rf_forum_enabled', true );
    if ( $enabled !== '1' ) {
        $enabled = (string) get_post_meta( $robot_id, 'rf_forum_enabled', true );
    }

    $url = (string) get_post_meta( $robot_id, '_rf_forum_topic_url', true );
    if ( ! $url ) {
        $url = (string) get_post_meta( $robot_id, 'rf_forum_topic_url', true );
    }
    if ( ! $url ) {
        // A few legacy variants, just in case (safe reads).
        $url = (string) get_post_meta( $robot_id, 'rf_forum_topic', true );
        if ( ! $url ) {
            $url = (string) get_post_meta( $robot_id, '_rg_forum_topic_url', true );
        }
    }

    // If no explicit "enabled" flag exists but an URL is set, assume enabled (migration-safe).
    if ( $enabled !== '1' && $url ) {
        $enabled = '1';
    }

    // Optional: "Beiträge" count for bbPress topics.
    $posts_count = 0;
    if ( $url && function_exists( 'url_to_postid' ) ) {
        $topic_id = (int) url_to_postid( $url );
        if ( $topic_id > 0 ) {
            $ptype = get_post_type( $topic_id );
            // bbPress topic post type is usually "topic".
            if ( $ptype === 'topic' || $ptype === 'bbp_topic' ) {
                if ( function_exists( 'bbp_get_topic_reply_count' ) ) {
                    $replies = (int) bbp_get_topic_reply_count( $topic_id, true );
                    $posts_count = max( 1, $replies + 1 ); // +1 for initial topic post
                } else {
                    $posts_count = 1;
                }
            }
        }
    }

    // Build HTML (keep styling compatible with existing rf-forum-box).
    if ( $enabled === '1' && $url ) {
        $title = esc_html__( 'Diskussion im Forum', 'robo-finder-pro' );
        $desc  = esc_html__( 'Lies Erfahrungen, Fragen & Tipps – und spring direkt in die Diskussion.', 'robo-finder-pro' );

        $count_html = '';
        if ( $posts_count > 0 ) {
            $count_html = '<span class="rf-forum-count">' . esc_html( sprintf( _n( '%d Beitrag', '%d Beiträge', $posts_count, 'robo-finder-pro' ), $posts_count ) ) . '</span>';
        }

        $html  = '<div class="rf-forum-box rg-forum-box">';
        $html .= '<h3>' . $title . ' ' . $count_html . '</h3>';
        $html .= '<p>' . $desc . '</p>';
        $html .= '<div class="rf-forum-actions">';
        $html .= '<a class="primary" href="' . esc_url( $url ) . '">' . esc_html__( 'Zum Forum-Thema', 'robo-finder-pro' ) . '</a>';
        $html .= '<a href="' . esc_url( $url ) . '#reply">' . esc_html__( 'Beitrag schreiben', 'robo-finder-pro' ) . '</a>';
        $html .= '</div>';
        $html .= '</div>';
    } else {
        $html  = '<div class="rf-forum-box rg-forum-box">';
        $html .= '<h3>' . esc_html__( 'Diskussion im Forum', 'robo-finder-pro' ) . '</h3>';
        $html .= '<div class="rf-forum-empty">' . esc_html__( 'Noch keine Forum-Verknüpfung hinterlegt.', 'robo-finder-pro' ) . '</div>';
        $html .= '</div>';
    }

    // Inject into mount if present; do not create duplicate boxes.
    $payload = wp_json_encode( $html );

    echo "<script>(function(){try{var el=document.getElementById('rg_forum_mount');if(!el){return;}if(el.dataset&&el.dataset.rgForumMounted==='1'){return;}el.innerHTML=".$payload.";if(el.dataset){el.dataset.rgForumMounted='1';}}catch(e){}})();</script>";
}

/**
     * Frontend CTA button that opens the contact modal.
     */
    private function render_contact_button_html( $robot_id = 0 ) {
        $settings = $this->get_settings();
        $shortcode = $this->get_contact_shortcode_for_robot( $robot_id ? $robot_id : get_the_ID() );
        if ( ! $shortcode ) {
            return '';
        }

        $btn_text = isset( $settings['contact_button_text'] ) && $settings['contact_button_text']
            ? (string) $settings['contact_button_text']
            : __( 'Jetzt Beratung anfragen', 'robo-finder-pro' );

        // Robo-Guru Conversion Box (Türkis / Orange)
        $html  = '<section class="rf-section rf-cta" aria-label="Beratung">';
        $html .= '  <div class="rf-conv-box">';
        $html .= '    <div class="rf-conv-content">';
        $html .= '      <div class="rf-conv-badge">Robo-Guru Tipp</div>';
        $html .= '      <h3 class="rf-conv-title">Kurze Beratung anfragen</h3>';
        $html .= '      <p class="rf-conv-sub">Schnell prüfen, ob dieser Roboter zu deinen Flächen passt – inkl. ROI‑Check & Einsatz-Einschätzung.</p>';
        $html .= '      <ul class="rf-conv-list">';
        $html .= '        <li>✅ Empfehlung passend zu deinem Umfeld</li>';
        $html .= '        <li>✅ Klare Pro/Contra Einordnung</li>';
        $html .= '        <li>✅ Optional: ROI‑Check & Betriebskonzept</li>';
        $html .= '      </ul>';
        $html .= '    </div>';
        $html .= '    <div class="rf-conv-action">';
        $html .= '      <button type="button" class="rf-open-contact-modal rf-conv-btn">' . esc_html( $btn_text ) . '</button>';
        $html .= '      <div class="rf-conv-note">Kostenlos & unverbindlich</div>';
        $html .= '    </div>';
        $html .= '  </div>';
        $html .= '</section>';
        return $html;
    }

    // ===== Frontend (Finder / Grid / Compare) =====
    // To keep this reply short, we include the same rendering and ajax handlers as before (unchanged).

    public function render_robo_finder( $atts = array() ) {
    $atts = shortcode_atts( array(
        'sureforms_id' => 14490,
    ), $atts, 'robo_finder' );

    $sureforms_id = (int) $atts['sureforms_id'];

    ob_start();
    ?>
    <!-- data-rf-steps MUST match the number of .rf-step blocks (1..6). -->
    <div class="rf-wrap" data-rf="finder" data-rf-steps="6">
        <div class="rf-container">
        <div class="rf-card">
            <?php rf_pro_get_template('finder-header', array('title' => __('Robo Finder','robo-finder-pro'), 'subtitle' => __('Beantworte ein paar Fragen – am Ende forderst du dein Ergebnis per Formular an.','robo-finder-pro') ) ); ?>

            <div class="rf-progress">
                <div class="rf-progress-bar" style="width: 0%;"></div>
            </div>

            <div class="rf-topbar">
              <div class="rf-topbar__left">
                <span class="rf-topbar__step">Step <strong data-rf-step-n>1</strong>/<span data-rf-step-total>6</span></span>
              </div>
              <div class="rf-topbar__right" data-rf-step-copy></div>
            </div>

            <!-- IMPORTANT: do not wrap SureForms in another <form> (nested forms break). -->
            <div class="rf-form">
                <!-- Hidden values for lead form -->
                <input type="hidden" name="rf_aufgabe" value="" />
                <input type="hidden" name="rf_einsatzgebiet" value="" />
                <input type="hidden" name="rf_barrierefreiheit" value="" />
                <input type="hidden" name="rf_flaeche_qm" value="500" />
                <input type="hidden" name="rf_notes" value="" />
                <input type="hidden" name="rf_critical_notes" value="" />
                <input type="hidden" name="rf_manual_check_required" value="0" />

                <div class="rf-layout rf-layout--single" data-rf-layout>
                    <div class="rf-main">
                      <div class="rf-steps">

<!-- STEP 1: Aufgabe / Typ -->
<div class="rf-step is-active" data-step="1">
    <label class="rf-label"><?php esc_html_e( 'Was soll der Roboter tun?', 'robo-finder-pro' ); ?></label>
    <p class="rf-help"><?php esc_html_e( 'Damit wir direkt den passenden Robotertyp auswählen (Reinigung vs. Lieferung) – ohne Umwege.', 'robo-finder-pro' ); ?></p>

    <?php echo $this->render_tax_tiles( 'robo_task', 'rf_task', array(), 'radio' ); ?>

    <div class="rf-nav">
        <button type="button" class="button rf-next" data-rf-next><?php esc_html_e( 'Weiter', 'robo-finder-pro' ); ?></button>
    </div>
</div>

<!-- STEP 2: Einsatzumfeld -->
<div class="rf-step" data-step="2">
    <label class="rf-label"><?php esc_html_e( 'Wo soll der Roboter eingesetzt werden?', 'robo-finder-pro' ); ?></label>
    <p class="rf-help"><?php esc_html_e( 'Wo soll der Roboter fahren/arbeiten? Wähle 1–3 Bereiche, damit wir die Anforderungen realistisch einschätzen.', 'robo-finder-pro' ); ?></p>

    <?php echo $this->render_tax_tiles( 'robo_env', 'rf_env', array(), 'checkbox' ); ?>

    <div class="rf-nav">
        <button type="button" class="button rf-prev" data-rf-prev><?php esc_html_e( 'Zurück', 'robo-finder-pro' ); ?></button>
        <button type="button" class="button rf-next" data-rf-next><?php esc_html_e( 'Weiter', 'robo-finder-pro' ); ?></button>
    </div>
</div>

<!-- STEP 3: Barrierefreiheit -->
<div class="rf-step" data-step="3">
    <label class="rf-label"><?php esc_html_e( 'Barrierefreiheit', 'robo-finder-pro' ); ?></label>
    <p class="rf-help"><?php esc_html_e( 'Hindernisse entscheiden über Sensorik, Rampenfähigkeit und die richtige Modellklasse. Je ehrlicher, desto besser die Empfehlung.', 'robo-finder-pro' ); ?></p>

    <div class="rf-terra-box">
        <div class="rf-terra-inline-top">
            <strong><?php esc_html_e( 'Gibt es Hürden im Gebäude?', 'robo-finder-pro' ); ?></strong>
            <span class="rf-terra-pill"><?php esc_html_e( 'Bitte wählen', 'robo-finder-pro' ); ?></span>
        </div>

        <div class="rf-terra-cards" role="group" aria-label="Barrierefreiheit">
            <div class="rf-terra-card" data-key="stufen_treppen" tabindex="0" role="button" aria-pressed="false">
                <div>
                    <p class="rf-terra-card-title"><?php esc_html_e( 'Stufen / Treppen', 'robo-finder-pro' ); ?></p>
                    <p class="rf-terra-card-sub"><?php esc_html_e( 'Gibt es Stufen oder Treppen?', 'robo-finder-pro' ); ?></p>
                </div>
            </div>
            <div class="rf-terra-card" data-key="aufzug" tabindex="0" role="button" aria-pressed="false">
                <div>
                    <p class="rf-terra-card-title"><?php esc_html_e( 'Fahrstuhl / Aufzug', 'robo-finder-pro' ); ?></p>
                    <p class="rf-terra-card-sub"><?php esc_html_e( 'Falls Etagen: gibt es einen Aufzug/Fahrstuhl?', 'robo-finder-pro' ); ?></p>
                </div>
            </div>
            <div class="rf-terra-card" data-key="rampen_schwellen" tabindex="0" role="button" aria-pressed="false">
                <div>
                    <p class="rf-terra-card-title"><?php esc_html_e( 'Rampen / Schwellen', 'robo-finder-pro' ); ?></p>
                    <p class="rf-terra-card-sub"><?php esc_html_e( 'Rampen, Schwellen oder Kanten?', 'robo-finder-pro' ); ?></p>
                </div>
            </div>
            <div class="rf-terra-card" data-key="enge_tueren" tabindex="0" role="button" aria-pressed="false">
                <div>
                    <p class="rf-terra-card-title"><?php esc_html_e( 'Enge Türen', 'robo-finder-pro' ); ?></p>
                    <p class="rf-terra-card-sub"><?php esc_html_e( 'Schmale Durchgänge / Brandschutztüren?', 'robo-finder-pro' ); ?></p>
                </div>
            </div>
            <div class="rf-terra-card" data-key="tore" tabindex="0" role="button" aria-pressed="false">
                <div>
                    <p class="rf-terra-card-title"><?php esc_html_e( 'Tore / Rolltore', 'robo-finder-pro' ); ?></p>
                    <p class="rf-terra-card-sub"><?php esc_html_e( 'Gibt es Tore oder automatische Türen im Fahrweg?', 'robo-finder-pro' ); ?></p>
                </div>
            </div>
            <div class="rf-terra-card" data-key="alles_ebenerdig" tabindex="0" role="button" aria-pressed="false">
                <div>
                    <p class="rf-terra-card-title"><?php esc_html_e( 'Alles ebenerdig', 'robo-finder-pro' ); ?></p>
                    <p class="rf-terra-card-sub"><?php esc_html_e( 'Keine Stufen, keine Schwellen.', 'robo-finder-pro' ); ?></p>
                </div>
            </div>
        </div>

        <div class="rf-terra-inline-hint"><?php esc_html_e( 'Mehrfachauswahl möglich (außer „Alles ebenerdig“).', 'robo-finder-pro' ); ?></div>
    </div>

    <div class="rf-nextsteps" data-rf-nextsteps>
      <div class="rf-nextsteps__head">So geht’s weiter</div>
      <ul class="rf-nextsteps__list">
        <li>Wir prüfen deine Angaben (inkl. Barrieren & Wegeführung)</li>
        <li>Du bekommst eine klare Empfehlung – ohne Spam</li>
        <li>Optional: Demo & ROI-Check</li>
      </ul>
      <div class="rf-nextsteps__note" data-rf-fehlkauf hidden>
        <strong>Warum wir prüfen:</strong> Bei Stufen, Toren oder Rampen unterscheiden sich Roboter stark – eine kurze Prüfung verhindert teure Fehlkäufe.
      </div>
    </div>

    <div class="rf-nav">
        <button type="button" class="button rf-prev" data-rf-prev><?php esc_html_e( 'Zurück', 'robo-finder-pro' ); ?></button>
        <button type="button" class="button rf-next" data-rf-next><?php esc_html_e( 'Weiter', 'robo-finder-pro' ); ?></button>
    </div>
</div>

<!-- STEP 4: Fläche -->
<div class="rf-step" data-step="4">
    <label class="rf-label"><?php esc_html_e( 'Fläche (ca.)', 'robo-finder-pro' ); ?></label>
    <p class="rf-help"><?php esc_html_e( 'Wir fragen in Stufen, weil m² meist nur ca. bekannt sind – das reicht für eine saubere Vorauswahl & ROI-Schätzung.', 'robo-finder-pro' ); ?></p>

    <div class="rf-terra-box rf-area-box">
        <div class="rf-terra-inline-top">
            <strong><?php esc_html_e( 'Wie groß ist die Fläche?', 'robo-finder-pro' ); ?></strong>
            <span class="rf-terra-pill"><span data-rf-area-val>50–500</span>&nbsp;m²</span>
        </div>

        <div class="rf-area-steps" role="radiogroup" aria-label="Fläche in Stufen">
            <label class="rf-area-step">
                <input type="radio" name="rf_area_bucket" value="50-500" data-mid="275" checked>
                <span>50–500&nbsp;m²</span>
            </label>
            <label class="rf-area-step">
                <input type="radio" name="rf_area_bucket" value="500-1000" data-mid="750">
                <span>500–1.000&nbsp;m²</span>
            </label>
            <label class="rf-area-step">
                <input type="radio" name="rf_area_bucket" value="1000-2000" data-mid="1500">
                <span>1.000–2.000&nbsp;m²</span>
            </label>
            <label class="rf-area-step">
                <input type="radio" name="rf_area_bucket" value="2000-5000" data-mid="3500">
                <span>2.000–5.000&nbsp;m²</span>
            </label>
            <label class="rf-area-step">
                <input type="radio" name="rf_area_bucket" value="5000-10000" data-mid="7500">
                <span>5.000–10.000&nbsp;m²</span>
            </label>
            <label class="rf-area-step">
                <input type="radio" name="rf_area_bucket" value="10000-50000" data-mid="30000">
                <span>10.000–50.000&nbsp;m²</span>
            </label>
            <label class="rf-area-step rf-area-step--wide">
                <input type="radio" name="rf_area_bucket" value=">50000" data-mid="60000">
                <span><?php esc_html_e( 'Über 50.000 m²', 'robo-finder-pro' ); ?></span>
            </label>
        </div>

        <div class="rf-terra-inline-hint rf-area-hint"><?php esc_html_e( 'Schätzung reicht – wir nutzen den Wert für die Empfehlung.', 'robo-finder-pro' ); ?></div>
    </div>

    <div class="rf-nav">
        <button type="button" class="button rf-prev" data-rf-prev><?php esc_html_e( 'Zurück', 'robo-finder-pro' ); ?></button>
        <button type="button" class="button rf-next" data-rf-next><?php esc_html_e( 'Weiter', 'robo-finder-pro' ); ?></button>
    </div>
</div>

<!-- STEP 5: Hinweise + Kurz-Check -->
<div class="rf-step" data-step="5">
  <label class="rf-label">Fast geschafft (noch ca. 30 Sekunden)</label>
  <p class="rf-help">Gibt es Besonderheiten oder Wünsche? Optional – je genauer, desto passender wird unsere Empfehlung.</p>

  <div class="rf-special-hint" data-rf-special-hint hidden></div>

  <div class="rf-notes">
    <div class="rf-notes-top">
      <strong>Was ist dir besonders wichtig?</strong>
      <span class="rf-terra-pill">optional</span>
    </div>

    <div class="rf-tags" aria-label="Schnell-Auswahl">
      <button type="button" class="rf-tag" data-rf-add-note="Leise im Betrieb">leise</button>
      <button type="button" class="rf-tag" data-rf-add-note="Nachtbetrieb / außerhalb der Öffnungszeiten">Nachtbetrieb</button>
      <button type="button" class="rf-tag" data-rf-add-note="Hohe Hygieneanforderungen (z.B. Klinik / Lebensmittel)">Hygiene</button>
      <button type="button" class="rf-tag" data-rf-add-note="Enge Bereiche / schmale Gänge / Türen">enge Bereiche</button>
      <button type="button" class="rf-tag" data-rf-add-note="Viele Türen / Aufzüge / Brandschutztüren">viele Türen</button>
      <button type="button" class="rf-tag" data-rf-add-note="Service & Reaktionszeit sind kritisch">Service wichtig</button>
      <button type="button" class="rf-tag rf-tag--ghost" data-rf-clear-notes>Alles löschen</button>
    </div>

    <textarea class="rf-textarea" id="rf_notes" data-rf-notes rows="6" placeholder="z.B. Nachtreinigung, enge Gänge, Lärm, Hygiene, Aufzüge, Türen, Gästeverkehr, Servicezeiten …"></textarea>
    <div class="rf-counter"><span data-rf-notes-count>0</span>/400</div>
    <div class="rf-help rf-help--small">Tipp: Stichpunkte reichen – wir berücksichtigen das 1:1 in der Beratung.</div>

    <div class="rf-critical" data-rf-critical-wrap hidden>
      <div class="rf-critical-head">Wichtiger Hinweis zu deinem Objekt</div>
      <div class="rf-critical-sub">Deine Angaben deuten auf besondere Anforderungen hin. Damit wir dir nichts Falsches empfehlen, beschreibe bitte kurz, was wir unbedingt wissen müssen.</div>
      <textarea class="rf-textarea rf-textarea--critical" id="rf_critical_notes" data-rf-critical-notes rows="4" placeholder="z.B. Stufen ohne Aufzug, sehr enge Gänge, Nachtbetrieb, Lärmgrenzen, Aufzüge nur eingeschränkt nutzbar …"></textarea>
      <div class="rf-counter"><span data-rf-critical-count>0</span>/400</div>
      <div class="rf-validation" data-rf-critical-hint hidden>Bitte gib hier einen kurzen Hinweis ein (mind. 15 Zeichen).</div>
    </div>
  </div>

  <hr class="rf-divider" />

  <label class="rf-label">Kurz-Check</label>
  <p class="rf-help">Prüfe kurz deine Angaben. Wenn alles passt, kannst du jetzt das Angebot anfordern.</p>
  <div class="rf-summary" data-rf-summary></div>

  <div class="rf-nav rf-nav-sticky">
    <button type="button" class="button rf-prev" data-rf-prev>Zurück</button>
    <button type="button" class="button button-primary rf-next rf-to-form" data-rf-next>Angebot anfordern</button>
  </div>
</div>

<!-- STEP 6: Formular -->
<div class="rf-step" data-step="6">
    <label class="rf-label" data-rf-s6-label><?php esc_html_e( 'Angebot anfordern', 'robo-finder-pro' ); ?></label>
    <p class="rf-sub" data-rf-s6-sub><?php esc_html_e( 'Trag deine Kontaktdaten ein – wir melden uns mit einer passenden Empfehlung.', 'robo-finder-pro' ); ?></p>

    <div class="rf-result-teaser" data-rf-teaser hidden></div>


    <div class="rf-terra-final-form">
        <p class="rf-terra-final-title" data-rf-final-title><?php esc_html_e( 'Kontakt & Angebot', 'robo-finder-pro' ); ?></p>
        <?php
        if ( function_exists( 'do_shortcode' ) && shortcode_exists( 'sureforms' ) ) {
            echo do_shortcode( "[sureforms id='{$sureforms_id}']" );
        } else {
            echo '<p><em>SureForms ist nicht aktiv.</em></p>';
        }
        ?>
    </div>

    <div class="rf-nav">
        <button type="button" class="button rf-prev" data-rf-prev><?php esc_html_e( 'Zurück', 'robo-finder-pro' ); ?></button>
    </div>
</div>



</div><!-- /.rf-steps -->
                    </div><!-- /.rf-main -->

                    <aside class="rf-sidebar is-hidden" data-rf-sidebar aria-label="Sidebar">
                      <div class="rf-sidecard">
                        <div class="rf-sidehead">Dein Fortschritt</div>
                        <div class="rf-sideprogress">
                          <span>Step</span> <strong data-rf-step-n>1</strong>/<span data-rf-step-total>6</span>
                        </div>
                        <div class="rf-sidecopy" data-rf-step-copy></div>
                      </div>

                      <div class="rf-sidecard">
                        <div class="rf-sidehead">Deine Angaben</div>
                        <div class="rf-minisummary" data-rf-mini-summary></div>
                      </div>

                      <div class="rf-sidecard rf-trust">
                        <div class="rf-sidehead">So geht’s weiter</div>
                        <ul class="rf-trustlist">
                          <li>Wir prüfen deine Angaben</li>
                          <li>Du bekommst eine klare Empfehlung</li>
                          <li>Optional: Demo & ROI-Check</li>
                        </ul>
                      </div>
                    </aside>

                </div><!-- /.rf-layout -->
            </div><!-- /.rf-form -->
        </div><!-- /.rf-card -->
        </div><!-- /.rf-container -->
    </div><!-- /.rf-wrap -->
    <?php
    return ob_get_clean();
}

    private function render_tax_select( $taxonomy, $name ) {
        $terms = get_terms(
            array(
                'taxonomy'   => $taxonomy,
                'hide_empty' => false,
            )
        );

        $html  = '<select name="' . esc_attr( $name ) . '">';
        $html .= '<option value="">' . esc_html__( '— bitte wählen —', 'robo-finder-pro' ) . '</option>';
        if ( is_array( $terms ) && ! is_wp_error( $terms ) ) {
            foreach ( $terms as $t ) {
                $html .= '<option value="' . esc_attr( $t->slug ) . '">' . esc_html( $t->name ) . '</option>';
            }
        }
        $html .= '</select>';
        return $html;
    }

    private function render_tax_tiles( $taxonomy, $name, $icons = array(), $type = 'checkbox' ) {
        $terms = get_terms(
            array(
                'taxonomy'   => $taxonomy,
                'hide_empty' => false,
            )
        );

        if ( is_wp_error( $terms ) || empty( $terms ) ) {
            return '<p style="margin:0;color:#666;">' . esc_html__( 'Keine Optionen vorhanden.', 'robo-finder-pro' ) . '</p>';
        }

        $out = '<div class="rf-tiles" data-tax="' . esc_attr( $taxonomy ) . '">';
        foreach ( $terms as $t ) {
            $slug = $t->slug;
            $out .= '<label class="rf-tile">';
            $input_type = ($type === 'radio') ? 'radio' : 'checkbox';
            $input_name = ($input_type === 'checkbox') ? ($name . '[]') : $name;
            $out .= '<input type="' . esc_attr( $input_type ) . '" name="' . esc_attr( $input_name ) . '" value="' . esc_attr( $slug ) . '"/>';
            // Icons are intentionally omitted for a calmer, more consistent UI.
            $out .= '<span class="rf-txt"><span class="rf-name">' . esc_html( $t->name ) . '</span></span>';
            $out .= '</label>';
        }
        $out .= '</div>';
        return $out;
    }

    public function handle_recommendation() {
        check_ajax_referer( 'robo_finder_nonce', 'nonce' );

                // Multi-Select Inputs (arrays) + optionale Filter
        $envs    = isset( $_POST['env'] ) ? (array) wp_unslash( $_POST['env'] ) : array();
        $tasks   = isset( $_POST['task'] ) ? (array) wp_unslash( $_POST['task'] ) : array();
        $floors  = isset( $_POST['floor'] ) ? (array) wp_unslash( $_POST['floor'] ) : array();
        $budget  = isset( $_POST['budget'] ) ? sanitize_text_field( wp_unslash( $_POST['budget'] ) ) : '';

        $min_m2h = isset( $_POST['min_m2h'] ) ? floatval( wp_unslash( $_POST['min_m2h'] ) ) : 0;
        $need_docking = ! empty( $_POST['need_docking'] );

        $envs   = array_values( array_filter( array_map( 'sanitize_key', $envs ) ) );
        $tasks  = array_values( array_filter( array_map( 'sanitize_key', $tasks ) ) );
        $floors = array_values( array_filter( array_map( 'sanitize_key', $floors ) ) );

        // Erst breite Auswahl holen (damit es nicht zu streng ist)
        $q = new WP_Query(
            array(
                'post_type'      => 'robo_robot',
                'post_status'    => 'publish',
                'posts_per_page' => 80,
                'fields'         => 'ids',
                'no_found_rows'  => true,
            )
        );

        $scored = array();

        foreach ( (array) $q->posts as $pid ) {
            $score = 0;

            // Tax Matches (OR innerhalb, aber Kategorie zählt)
            if ( $envs ) {
                $terms = wp_get_post_terms( $pid, 'robo_env', array( 'fields' => 'slugs' ) );
                $score += array_intersect( (array) $terms, $envs ) ? 3 : 0;
            }
            if ( $tasks ) {
                $terms = wp_get_post_terms( $pid, 'robo_task', array( 'fields' => 'slugs' ) );
                $score += array_intersect( (array) $terms, $tasks ) ? 3 : 0;
            }
            if ( $floors ) {
                $terms = wp_get_post_terms( $pid, 'robo_floor', array( 'fields' => 'slugs' ) );
                $score += array_intersect( (array) $terms, $floors ) ? 2 : 0;
            }
            if ( $budget ) {
                $terms = wp_get_post_terms( $pid, 'robo_budget', array( 'fields' => 'slugs' ) );
                $score += in_array( $budget, (array) $terms, true ) ? 1 : 0;
            }

            // Meta Filters
            if ( $min_m2h > 0 ) {
                $m2h = floatval( get_post_meta( $pid, '_rf_m2h', true ) );
                if ( $m2h <= 0 || $m2h < $min_m2h ) {
                    continue;
                }
                $score += 1;
            }

            if ( $need_docking ) {
                $has = (string) get_post_meta( $pid, '_rf_has_docking', true );
                if ( $has !== '1' ) {
                    continue;
                }
                $score += 1;
            }

            // Mindestens irgendwas matchen, sonst später Fallback
            if ( $score > 0 ) {
                $scored[] = array( 'id' => $pid, 'score' => $score );
            }
        }

        usort( $scored, function( $a, $b ) {
            if ( $a['score'] === $b['score'] ) return 0;
            return ( $a['score'] > $b['score'] ) ? -1 : 1;
        } );

        $items = array();
        $top = array_slice( $scored, 0, 12 );
        foreach ( $top as $row ) {
            $items[] = $this->serialize_robot( $row['id'] );
        }

        // Fallback: wenn nichts passt, zeig die neuesten Modelle
        if ( empty( $items ) ) {
            $fallback = new WP_Query(
                array(
                    'post_type'      => 'robo_robot',
                    'post_status'    => 'publish',
                    'posts_per_page' => 6,
                    'fields'         => 'ids',
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                    'no_found_rows'  => true,
                )
            );
            foreach ( (array) $fallback->posts as $pid ) {
                $items[] = $this->serialize_robot( $pid );
            }
        }
wp_send_json_success(
            array(
                'items' => $items,
            )
        );
    }

    private function serialize_robot( $post_id ) {
        $thumb = get_the_post_thumbnail_url( $post_id, 'medium' );
        if ( ! $thumb ) {
            $thumb = '';
        }

        return array(
            'id'            => $post_id,
            'title'         => get_the_title( $post_id ),
            'permalink'     => get_permalink( $post_id ),
            'thumb'         => $thumb,
            'manufacturer'  => (string) get_post_meta( $post_id, '_rf_manufacturer', true ),
            'm2h'           => (string) get_post_meta( $post_id, '_rf_m2h', true ),
            'battery'       => (string) get_post_meta( $post_id, '_rf_battery_hours', true ),
            'tank'          => (string) get_post_meta( $post_id, '_rf_tank_liters', true ),
            'clean_water'   => (string) get_post_meta( $post_id, '_rf_clean_water', true ),
            'dirty_water'   => (string) get_post_meta( $post_id, '_rf_dirty_water', true ),
            'nav'           => (string) get_post_meta( $post_id, '_rf_nav', true ),
            'has_docking'   => get_post_meta( $post_id, '_rf_has_docking', true ) === '1',
            'dimensions'    => (string) get_post_meta( $post_id, '_rf_dimensions', true ),
            'working_width' => (string) get_post_meta( $post_id, '_rf_working_width', true ),
            'noise'         => (string) get_post_meta( $post_id, '_rf_noise', true ),
            'highlight_1'   => (string) get_post_meta( $post_id, '_rf_highlight_1', true ),
            'highlight_2'   => (string) get_post_meta( $post_id, '_rf_highlight_2', true ),
            'highlight_3'   => (string) get_post_meta( $post_id, '_rf_highlight_3', true ),
        );
    }

    public function handle_lead() {
        check_ajax_referer( 'robo_finder_nonce', 'nonce' );

        $payload = array(
            'name'    => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
            'company' => isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) ) : '',
            'email'   => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
            'phone'   => isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '',
            'note'    => isset( $_POST['note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['note'] ) ) : '',
            'data'    => isset( $_POST['data'] ) ? wp_unslash( $_POST['data'] ) : '',
        );

        if ( empty( $payload['email'] ) ) {
            wp_send_json_error( array( 'message' => __( 'Bitte eine gültige E-Mail angeben.', 'robo-finder-pro' ) ) );
        }

        global $wpdb;
        $wpdb->insert(
            $this->leads_table,
            array(
                'created_at' => current_time( 'mysql' ),
                'name'       => $payload['name'],
                'company'    => $payload['company'],
                'email'      => $payload['email'],
                'phone'      => $payload['phone'],
                'note'       => $payload['note'],
                'data'       => wp_json_encode( $payload['data'] ),
            ),
            array( '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
        );

        $settings = $this->get_settings();
        $to       = $settings['target_email'] ? $settings['target_email'] : get_option( 'admin_email' );

        $subject  = 'Robo Finder Lead – ' . ( $payload['company'] ? $payload['company'] : $payload['email'] );
        $body     = "Neuer Robo Finder Lead:\n\n";
        $body    .= "Name: {$payload['name']}\n";
        $body    .= "Firma: {$payload['company']}\n";
        $body    .= "E-Mail: {$payload['email']}\n";
        $body    .= "Telefon: {$payload['phone']}\n";
        $body    .= "Notiz: {$payload['note']}\n\n";
        $body    .= "Daten: " . print_r( $payload['data'], true ) . "\n";

        @wp_mail( $to, $subject, $body );

        wp_send_json_success( array( 'message' => __( 'Danke! Wir melden uns zeitnah.', 'robo-finder-pro' ) ) );
    }

    public function render_robot_grid( $atts = array() ) {
        $atts = shortcode_atts(
            array(
                'category' => '',
                'env'      => '',
                'limit'    => 12,
            ),
            $atts,
            'robo_robot_grid'
        );

        $tax_query = array( 'relation' => 'AND' );

        if ( $atts['category'] ) {
            $slugs = array_map( 'trim', explode( ',', $atts['category'] ) );
            $tax_query[] = array(
                'taxonomy' => 'robo_category',
                'field'    => 'slug',
                'terms'    => $slugs,
            );
        }
        if ( $atts['env'] ) {
            $slugs = array_map( 'trim', explode( ',', $atts['env'] ) );
            $tax_query[] = array(
                'taxonomy' => 'robo_env',
                'field'    => 'slug',
                'terms'    => $slugs,
            );
        }

        $q = new WP_Query(
            array(
                'post_type'      => 'robo_robot',
                'post_status'    => 'publish',
                'posts_per_page' => max( 1, intval( $atts['limit'] ) ),
                'tax_query'      => count( $tax_query ) > 1 ? $tax_query : array(),
            )
        );

        ob_start();
        echo '<div class="rf-grid-cards">';
        if ( $q->have_posts() ) {
            foreach ( $q->posts as $p ) {
                $r = $this->serialize_robot( $p->ID );
                echo '<article class="rf-robot-card">';
                if ( $r['thumb'] ) {
                    echo '<a href="' . esc_url( $r['permalink'] ) . '"><img class="rf-robot-thumb" src="' . esc_url( $r['thumb'] ) . '" alt="' . esc_attr( $r['title'] ) . '" /></a>';
                }
                echo '<h3 class="rf-robot-title"><a href="' . esc_url( $r['permalink'] ) . '">' . esc_html( $r['title'] ) . '</a></h3>';
                echo '<div class="rf-robot-meta">' . esc_html( $r['manufacturer'] ) . '</div>';
                echo '<ul class="rf-robot-facts">';
                $facts = array(
                    'm2h'           => 'm²/h',
                    'battery'       => 'h Akku',
                    'clean_water'   => 'L Reinwasser',
                    'dirty_water'   => 'L Abwasser',
                    'working_width' => 'Arbeitsbreite',
                    'dimensions'    => 'Abmessungen',
                    'noise'         => 'dB',
                );
                foreach ( $facts as $k => $suffix ) {
                    $v = trim( (string) $r[ $k ] );
                    if ( $v !== '' ) {
                        echo '<li><span>' . esc_html( $suffix ) . '</span><strong>' . esc_html( $v ) . '</strong></li>';
                    }
                }
                echo '</ul>';
                echo '<div class="rf-robot-actions"><a class="button" href="' . esc_url( $r['permalink'] ) . '">' . esc_html__( 'Details', 'robo-finder-pro' ) . '</a></div>';
                echo '</article>';
            }
        } else {
            echo '<p>' . esc_html__( 'Keine Roboter gefunden.', 'robo-finder-pro' ) . '</p>';
        }
        echo '</div>';
        return ob_get_clean();
    }

    public function render_robot_compare( $atts = array() ) {
        $atts = shortcode_atts( array( 'ids' => '' ), $atts, 'robo_robot_compare' );
        $ids  = array_filter( array_map( 'intval', explode( ',', (string) $atts['ids'] ) ) );
        if ( empty( $ids ) ) {
            return '<p>' . esc_html__( 'Bitte IDs angeben: [robo_robot_compare ids="1,2,3"]', 'robo-finder-pro' ) . '</p>';
        }

        $robots = array();
        foreach ( $ids as $id ) {
            if ( get_post_type( $id ) === 'robo_robot' ) {
                $robots[] = $this->serialize_robot( $id );
            }
        }
        if ( empty( $robots ) ) {
            return '<p>' . esc_html__( 'Keine passenden Roboter gefunden.', 'robo-finder-pro' ) . '</p>';
        }

        $rows = array(
            'manufacturer'  => __( 'Hersteller', 'robo-finder-pro' ),
            'm2h'           => __( 'Flächenleistung (m²/h)', 'robo-finder-pro' ),
            'battery'       => __( 'Akkulaufzeit (h)', 'robo-finder-pro' ),
            'tank'          => __( 'Tank gesamt (L)', 'robo-finder-pro' ),
            'clean_water'   => __( 'Reinwasser (L)', 'robo-finder-pro' ),
            'dirty_water'   => __( 'Abwasser (L)', 'robo-finder-pro' ),
            'working_width' => __( 'Arbeitsbreite', 'robo-finder-pro' ),
            'dimensions'    => __( 'Abmessungen', 'robo-finder-pro' ),
            'noise'         => __( 'Geräuschpegel', 'robo-finder-pro' ),
            'nav'           => __( 'Navigation / Sensorik', 'robo-finder-pro' ),
            'has_docking'   => __( 'Dockingstation', 'robo-finder-pro' ),
            'highlight_1'   => __( 'Highlight 1', 'robo-finder-pro' ),
            'highlight_2'   => __( 'Highlight 2', 'robo-finder-pro' ),
            'highlight_3'   => __( 'Highlight 3', 'robo-finder-pro' ),
        );

        ob_start();
        echo '<div class="rf-compare-wrap"><div class="rf-compare-scroll">';
        echo '<table class="rf-compare">';
        echo '<thead><tr><th>' . esc_html__( 'Merkmal', 'robo-finder-pro' ) . '</th>';
        foreach ( $robots as $r ) {
            echo '<th><a href="' . esc_url( $r['permalink'] ) . '">' . esc_html( $r['title'] ) . '</a></th>';
        }
        echo '</tr></thead><tbody>';
        foreach ( $rows as $key => $label ) {
            echo '<tr><td><strong>' . esc_html( $label ) . '</strong></td>';
            foreach ( $robots as $r ) {
                $val = $r[ $key ] ?? '';
                if ( $key === 'has_docking' ) {
                    $val = $val ? '✔' : '—';
                }
                echo '<td>' . esc_html( $val !== '' ? (string) $val : '—' ) . '</td>';
            }
            echo '</tr>';
        }
        echo '</tbody></table></div></div>';
        return ob_get_clean();
    }

    public function register_dashboard_widget() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        wp_add_dashboard_widget(
            'rf_leads_widget',
            __( 'Robo Finder Leads', 'robo-finder-pro' ),
            array( $this, 'render_dashboard_widget' )
        );
    }

    public function render_dashboard_widget() {
        global $wpdb;
        $table = $this->leads_table;
        $count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
        $last  = $wpdb->get_row( "SELECT * FROM {$table} ORDER BY created_at DESC LIMIT 1" );

        echo '<p><strong>' . esc_html__( 'Leads gesamt:', 'robo-finder-pro' ) . '</strong> ' . esc_html( (string) $count ) . '</p>';
        if ( $last ) {
            echo '<p><strong>' . esc_html__( 'Letzter Lead:', 'robo-finder-pro' ) . '</strong><br>';
            echo esc_html( $last->created_at ) . ' – ' . esc_html( $last->company ? $last->company : $last->email ) . '</p>';
        }
        echo '<p><a class="button" href="' . esc_url( admin_url( 'edit.php?post_type=robo_robot&page=robo-finder-pro-leads' ) ) . '">' . esc_html__( 'Leads öffnen', 'robo-finder-pro' ) . '</a></p>';
    }


    public function render_contact_modal() {
        $settings = $this->get_settings();
        $shortcode = $this->get_contact_shortcode_for_robot( get_the_ID() );
        if ( ! $shortcode ) {
            return;
        }

        // Only output once
        static $printed = false;
        if ( $printed ) { return; }
        $printed = true;

        ?>
        <div id="rf-modal-overlay" class="rf-modal-overlay" aria-hidden="true" style="display:none"></div>
        <div id="rf-contact-modal" class="rf-modal" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Kontaktformular', 'robo-finder-pro' ); ?>" style="display:none">
            <div class="rf-modal-header">
                <h3 class="rf-modal-title"><?php echo esc_html( $settings['contact_button_text'] ); ?></h3>
                <button type="button" class="rf-modal-close" aria-label="<?php esc_attr_e( 'Modal schließen', 'robo-finder-pro' ); ?>">✕</button>
            </div>
            <div class="rf-modal-body">
                <?php echo do_shortcode( $shortcode ); ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render the editor content (post_content) into HTML so that the frontend
     * can inject it if the theme does not call the_content() for robo_robot.
     */
    private function get_robot_article_html() {
        if ( ! function_exists( 'is_singular' ) || ! is_singular( 'robo_robot' ) ) {
            return '';
        }

        $robot_id = get_the_ID();
        if ( ! $robot_id ) {
            return '';
        }

        $raw = (string) get_post_field( 'post_content', $robot_id );
        if ( trim( wp_strip_all_tags( $raw ) ) === '' ) {
            return '';
        }

        // Render Gutenberg blocks + shortcodes similarly to the_content(),
        // but avoid infinite recursion by temporarily removing our own filter.
        remove_filter( 'the_content', array( $this, 'inject_robot_content_layout' ), 12 );
        $html = apply_filters( 'the_content', $raw );
        // add_filter( 'the_content', array( $this, 'inject_robot_content_layout' ), 12 ); // disabled: theme controls content


        // Defensive: ensure it's a string and not insanely large.
        $html = (string) $html;
        if ( strlen( $html ) > 300000 ) {
            $html = substr( $html, 0, 300000 );
        }

        return $html;
    }

    /**
     * Ensure the editor content (Artikeltext) is visible on single robot pages.
     * Layout order:
     *   1) Technische Daten (from meta)
     *   2) Artikeltext (normal editor content)
     *   3) Ideal / Nicht ideal (from meta)
     */
    public function inject_robot_content_layout( $content ) {
        if ( ! function_exists( 'is_singular' ) || ! is_singular( 'robo_robot' ) || is_admin() ) {
            return $content;
        }

        // Only touch content when we are inside the loop.
        // NOTE: We intentionally do NOT gate on is_main_query(), because some themes
        // render singular templates through custom queries. Gating too hard was the
        // root cause for "missing" article content on some installations.
        if ( function_exists( 'in_the_loop' ) && ! in_the_loop() ) {
            return $content;
        }

        // Avoid double-processing by checking for our own marker classes.
        // Using a static "done" flag can accidentally suppress the real content
        // when a theme calls the_content() earlier with empty/preview content.
        if ( is_string( $content ) && strpos( $content, 'rf-section rf-fit' ) !== false ) {
            return $content;
        }

        $robot_id = get_the_ID();

        // Build technical data table from meta
        $spec_rows = array();
        $spec_map = array(
            'Flächenleistung' => '_rf_m2h',
            'Akkulaufzeit'    => '_rf_battery_hours',
            'Arbeitsbreite'   => '_rf_working_width',
            'Geräuschpegel'   => '_rf_noise',
            'Tank gesamt'     => '_rf_tank_liters',
            'Reinwasser'      => '_rf_clean_water',
            'Abwasser'        => '_rf_dirty_water',
            'Navigation'      => '_rf_nav',
            'Abmessungen'     => '_rf_dimensions',
            'Dockingstation'  => '_rf_has_docking',
        );

        foreach ( $spec_map as $label => $meta_key ) {
            $val = get_post_meta( $robot_id, $meta_key, true );
            if ( $val === '' || $val === null ) {
                continue;
            }
            if ( $meta_key === '_rf_has_docking' ) {
                $val = (string) $val === '1' ? __( 'Ja', 'robo-finder-pro' ) : __( 'Nein', 'robo-finder-pro' );
            }
            $spec_rows[] = array( $label, $val );
        }

        $tech_html = '';
        if ( ! empty( $spec_rows ) ) {
            ob_start();
            ?>
            <section class="rf-section rf-tech">
                <h2 class="rf-h2"><?php esc_html_e( 'Technische Daten', 'robo-finder-pro' ); ?></h2>
                <div class="rf-specs">
                    <table class="rf-specs-table">
                        <tbody>
                        <?php foreach ( $spec_rows as $row ) : ?>
                            <tr>
                                <th><?php echo esc_html( $row[0] ); ?></th>
                                <td><?php echo esc_html( $row[1] ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
            <?php
            $tech_html = ob_get_clean();
        }

        // Editor content (Artikeltext)
        // IMPORTANT: Do NOT rely solely on the `$content` argument because some themes
        // call the_content() with an empty string for custom layouts.
        $article_html = '';
        $article_inner = $this->get_robot_article_html();
        if ( trim( wp_strip_all_tags( (string) $article_inner ) ) !== '' ) {
            $title = (string) get_the_title( $robot_id );
            $title_upper = function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $title, 'UTF-8' ) : strtoupper( $title );
            $headline    = 'Über den ' . $title_upper;
            $article_html = '<section class="rf-section rf-article">'
                         . '<h3 class="rf-h3">' . esc_html( $headline ) . '</h3>'
                         . '<div class="rf-article-content">' . $article_inner . '</div>'
                         . '</section>';
        }

        // Contact CTA button (opens modal)
        $cta_html = $this->render_contact_button_html();

        // Ideal / Nicht ideal
        $ideal      = get_post_meta( $robot_id, '_rf_ideal_for', true );
        $not_ideal  = get_post_meta( $robot_id, '_rf_not_ideal_for', true );

        $fit_html = '';
        if ( trim( (string) $ideal ) !== '' || trim( (string) $not_ideal ) !== '' ) {
            ob_start();
            ?>
            <section class="rf-section rf-fit">
                <div class="rf-fit-grid">
                    <?php if ( trim( (string) $ideal ) !== '' ) : ?>
                        <div class="rf-fit-box rf-fit-ideal">
                            <h3 class="rf-h3"><?php esc_html_e( 'Ideal für', 'robo-finder-pro' ); ?></h3>
                            <div class="rf-fit-text"><?php echo wp_kses_post( wpautop( $ideal ) ); ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if ( trim( (string) $not_ideal ) !== '' ) : ?>
                        <div class="rf-fit-box rf-fit-notideal">
                            <h3 class="rf-h3"><?php esc_html_e( 'Nicht ideal für', 'robo-finder-pro' ); ?></h3>
                            <div class="rf-fit-text"><?php echo wp_kses_post( wpautop( $not_ideal ) ); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
            <?php
            $fit_html = ob_get_clean();
        }

        // If we have nothing special, return original content
        if ( $tech_html === '' && $fit_html === '' && $cta_html === '' ) {
            return $content;
        }

        // Compose (as requested): Ideal/Nicht ideal -> Artikeltext -> CTA -> Technische Daten
        // Always preserve original editor content when present.
        return $fit_html . $article_html . $cta_html . $tech_html;
    }

    public function append_forum_box_to_robot( $content ) {
        if ( ! function_exists( 'is_singular' ) || ! is_singular( 'robo_robot' ) ) {
            return $content;
        }

        // Forum-Box auf Roboter-Detailseiten wieder immer anzeigen, sobald der Roboter
        // ein Forum-Topic verknüpft und aktiviert hat. Die globale Option steuert
        // weiterhin nur das Widget auf Forum-Seiten.

        global $post;
        if ( ! ( $post instanceof WP_Post ) ) {
            return $content;
        }

        $enabled = (string) get_post_meta( $post->ID, '_rf_forum_enabled', true );
        $url     = (string) get_post_meta( $post->ID, '_rf_forum_topic_url', true );

        if ( $enabled !== '1' || ! $url ) {
            return $content;
        }

        $box  = '<div class="rf-forum-box">';
        $box .= '<h3>' . esc_html__( 'Diskussion im Forum', 'robo-finder-pro' ) . '</h3>';
        $box .= '<p>' . esc_html__( 'Lies Erfahrungen, Fragen & Antworten – oder spring direkt in die Diskussion.', 'robo-finder-pro' ) . '</p>';
        $box .= '<div class="rf-forum-actions">';
        $box .= '<a class="primary" href="' . esc_url( $url ) . '">' . esc_html__( 'Zum Forum-Thema', 'robo-finder-pro' ) . '</a>';
        $box .= '<a href="' . esc_url( $url ) . '#reply">' . esc_html__( 'Beitrag schreiben', 'robo-finder-pro' ) . '</a>';
        $box .= '</div>';
        $box .= '</div>';

        return $content . $box;
    }

    public function maybe_render_forum_widget() {
        $settings = $this->get_settings();
        if ( empty( $settings['enable_forum_link'] ) || $settings['enable_forum_link'] !== '1' ) {
            return;
        }

        $req = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
        $path = parse_url( $req, PHP_URL_PATH );
        if ( ! is_string( $path ) || strpos( $path, '/community/forum/' ) !== 0 ) {
            return;
        }

        // Find matching robot by path
        $robots = get_posts(
            array(
                'post_type'      => 'robo_robot',
                'post_status'    => 'publish',
                'posts_per_page' => 1,
                'meta_query'     => array(
                    array(
                        'key'     => '_rf_forum_topic_url',
                        'value'   => $path,
                        'compare' => 'LIKE',
                    ),
                    array(
                        'key'     => '_rf_forum_enabled',
                        'value'   => '1',
                        'compare' => '=',
                    ),
                ),
            )
        );

        if ( empty( $robots ) ) {
            return;
        }

        $robot_id = $robots[0]->ID;
        $title = get_the_title( $robot_id );
        $url   = get_permalink( $robot_id );
        $img   = get_the_post_thumbnail_url( $robot_id, 'medium_large' );
        if ( ! $img ) {
            $img = plugin_dir_url( __FILE__ ) . 'assets/img/robot-placeholder.svg';
        }
        $forum = (string) get_post_meta( $robot_id, '_rf_forum_topic_url', true );

        $mode = isset( $settings['forum_widget_mode'] ) ? $settings['forum_widget_mode'] : 'floating';

        if ( $mode === 'inline_after_first_post' ) {
            // We mount the card and let JS insert it right under the first post.
            ?>
            <div id="rf-forum-inline-mount" class="rf-forum-inline" style="display:none;">
                <div class="rf-forum-inline-card">
                    <a class="rf-forum-inline-img" href="<?php echo esc_url( $url ); ?>">
                        <img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" />
                    </a>
                    <div class="rf-forum-inline-body">
                        <div class="rf-forum-inline-badge"><?php esc_html_e( 'Passender Roboter', 'robo-finder-pro' ); ?></div>
                        <h3 class="rf-forum-inline-title"><?php echo esc_html( $title ); ?></h3>
                        <p class="rf-forum-inline-sub"><?php esc_html_e( 'Direkt Details ansehen oder in die Diskussion springen.', 'robo-finder-pro' ); ?></p>
                        <div class="rf-forum-inline-actions">
                            <a class="rf-forum-inline-btn primary" href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( 'Zur Detailseite', 'robo-finder-pro' ); ?></a>
                            <a class="rf-forum-inline-btn" href="<?php echo esc_url( $forum ); ?>"><?php esc_html_e( 'Forum öffnen', 'robo-finder-pro' ); ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            return;
        }

        if ( $mode === 'inline_top' || $mode === 'inline_bottom' ) {
            // Inline: print near end of page (simple) – in a real theme you might hook earlier.
            ?>
            <div class="rf-forum-box rf-forum-box-inline" style="max-width:900px;margin-left:auto;margin-right:auto;">
                <div class="rf-forum-box-grid">
                    <a class="rf-forum-box-img" href="<?php echo esc_url( $url ); ?>">
                        <img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" />
                    </a>
                    <div class="rf-forum-box-body">
                        <h3><?php echo esc_html( $title ); ?></h3>
                        <p><?php esc_html_e( 'Passender Roboter zur Diskussion:', 'robo-finder-pro' ); ?></p>
                        <div class="rf-forum-actions">
                            <a class="primary" href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( 'Zur Roboter-Detailseite', 'robo-finder-pro' ); ?></a>
                            <a href="<?php echo esc_url( $forum ); ?>"><?php esc_html_e( 'Forum-Thema öffnen', 'robo-finder-pro' ); ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            return;
        }

        // Floating right widget
        ?>
        <div class="rf-forum-floating" aria-label="<?php esc_attr_e( 'Robo-Guru Roboter Widget', 'robo-finder-pro' ); ?>">
            <div class="rf-forum-card">
                <a class="rf-forum-img" href="<?php echo esc_url( $url ); ?>">
                    <img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" />
                </a>
                <div class="rf-forum-body">
                    <h3 class="rf-forum-title"><?php echo esc_html( $title ); ?></h3>
                    <div class="rf-forum-btns">
                        <a class="rf-forum-btn" href="<?php echo esc_url( $forum ); ?>"><?php esc_html_e( 'Forum', 'robo-finder-pro' ); ?></a>
                        <a class="rf-forum-btn primary" href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( 'Details', 'robo-finder-pro' ); ?></a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }



/**
 * Compatibility shim: prevent fatals if a legacy hook references this method.
 */
public function append_robot_specs_to_content($content) {
    if (is_admin() || !is_singular('robo_robot')) {
        return $content;
    }
    return $content;
}

} // end class

register_activation_hook( __FILE__, array( 'Robo_Finder_Pro_Plugin', 'activate' ) );

add_action( 'plugins_loaded', array( 'Robo_Finder_Pro_Plugin', 'get_instance' ) );









/**
 * Robust shortcode renderer for /robo-finder/.
 * Ensures clickable tiles + step navigation regardless of theme blocks.
 */


/* Legacy shortcode renderer removed in v4.8.0 (class renderer is active). */

// ===== v2.9.39 SureForms handoff (no results) =====
add_action('admin_menu', function(){
    add_options_page('Robo Finder', 'Robo Finder', 'manage_options', 'rfp-settings', 'rfp_render_settings_page');
});

add_action('admin_init', function(){
    register_setting('rfp_settings_group', 'rfp_sureforms_shortcode', array('type'=>'string','sanitize_callback'=>'wp_kses_post','default'=>''));
});

function rfp_render_settings_page(){
    if (!current_user_can('manage_options')) return;
    ?>
    <div class="wrap">
      <h1>Robo Finder – Einstellungen</h1>
      <form method="post" action="options.php">
        <?php settings_fields('rfp_settings_group'); ?>
        <table class="form-table" role="presentation">
          <tr>
            <th scope="row"><label for="rfp_sureforms_shortcode">SureForms Shortcode (Finale Anfrage)</label></th>
            <td>
              <textarea id="rfp_sureforms_shortcode" name="rfp_sureforms_shortcode" rows="4" style="width:100%;max-width:900px;"><?php echo esc_textarea(get_option('rfp_sureforms_shortcode','')); ?></textarea>
              <p class="description">
                Beispiel: <code>[sureforms id="123"]</code><br>
                Lege im Formular Hidden-Felder an (z.B. <code>rf_env</code>, <code>rf_task</code>, <code>rf_floor</code>, <code>rf_area_sqm</code>, <code>rf_tables</code>, <code>rf_barriers</code>) – diese werden automatisch befüllt.
              </p>
            </td>
          </tr>
        </table>
        <?php submit_button(); ?>
      </form>

      <hr>
      <h2>Empfohlene Hidden-Felder in SureForms</h2>
      <ul style="list-style:disc;padding-left:20px;">
        <li><code>rf_domain</code> (reinigung / lieferung / transport)</li>
        <li><code>rf_env</code> (Mehrfachauswahl, kommasepariert)</li>
        <li><code>rf_task</code> (Mehrfachauswahl, kommasepariert)</li>
        <li><code>rf_floor</code> (Mehrfachauswahl, kommasepariert)</li>
        <li><code>rf_area_sqm</code> (Zahl)</li>
        <li><code>rf_tables</code> (Zahl)</li>
        <li><code>rf_barriers</code> (Text – Stufen/Aufzug/Türen)</li>
      </ul>
    </div>
    <?php
}

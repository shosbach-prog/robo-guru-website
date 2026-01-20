<?php
/**
 * Plugin Name: SureForms Starter
 * Plugin URI: https://sureforms.com/
 * Author: SureForms
 * Author URI: https://sureforms.com/
 * Description: Enhance SureForms with new features and blocks, as well as extended functionality for existing blocks.
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * Version: 2.3.0
 * License: GPL v2
 * Text Domain: sureforms-pro
 * Requires Plugins: sureforms
 *
 * @package sureforms-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Set constants
 */
define( 'SRFM_PRO_FILE', __FILE__ );
define( 'SRFM_PRO_BASENAME', plugin_basename( SRFM_PRO_FILE ) );
define( 'SRFM_PRO_DIR', plugin_dir_path( SRFM_PRO_FILE ) );
define( 'SRFM_PRO_URL', plugins_url( '/', SRFM_PRO_FILE ) );
define( 'SRFM_PRO_VER', '2.3.0' );
define( 'SRFM_PRO_SLUG', 'sureforms-pro' );
define( 'SRFM_PRO_PUBLIC_TOKEN', 'pt_ziGV9dzKQRL72PcsC3eQByLq' );
define( 'SRFM_PRO_CORE_RQD_VER', '2.3.0' );
define( 'SRFM_PRO_PRODUCT', 'SureForms Starter' );
define( 'SRFM_PRO_PRODUCT_ID', '6fc4d877-bea9-49bd-b72c-98c751685ef2' );

require_once 'plugin-loader.php';

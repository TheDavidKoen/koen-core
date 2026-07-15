<?php
/**
 * Plugin Name:       Koen Core
 * Plugin URI:        https://github.com/TheDavidKoen/koen-core
 * Description:       Portfolio content types, fields, and admin experience. Theme-independent by design.
 * Version:           0.1.0
 * Requires at least: 6.4
 * Requires PHP:      8.1
 * Author:            David Koen
 * License:           GPL-2.0-or-later
 * Text Domain:       koen-core
 *
 * @package KoenCore
 */

defined( 'ABSPATH' ) || exit;

define( 'KOEN_CORE_VERSION', '0.1.0' );
define( 'KOEN_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'KOEN_CORE_URL', plugin_dir_url( __FILE__ ) );

require KOEN_CORE_DIR . 'includes/post-types.php';
require KOEN_CORE_DIR . 'includes/meta.php';
require KOEN_CORE_DIR . 'includes/admin-columns.php';
require KOEN_CORE_DIR . 'includes/skills.php';

/**
 * Register content types and flush permalinks on activation.
 */
function koen_core_activate(): void {
	koen_core_register_post_types();
	koen_core_register_taxonomies();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'koen_core_activate' );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

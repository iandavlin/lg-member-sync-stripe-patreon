<?php
/**
 * Plugin Name: LG Member Sync
 * Plugin URI:  https://loothgroup.com
 * Description: Polls Stripe + Patreon, writes to lg_membership, arbitrates per-source role opinions, owns the wp_capabilities update path.
 * Version:     0.1.0
 * Author:      Ian Davlin
 * Requires PHP: 8.3
 * License:     GPL v2 or later
 * Text Domain: lg-member-sync
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

define( 'LGMS_VERSION',     '0.1.0' );
define( 'LGMS_PLUGIN_FILE', __FILE__ );
define( 'LGMS_PLUGIN_DIR',  plugin_dir_path( __FILE__ ) );
define( 'LGMS_PLUGIN_URL',  plugin_dir_url( __FILE__ ) );

// Composer autoload (Stripe SDK + project classes under LGMS\*)
if ( file_exists( LGMS_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
    require_once LGMS_PLUGIN_DIR . 'vendor/autoload.php';
}

// Activation: apply schema migrations, schedule cron.
register_activation_hook( __FILE__, [ \LGMS\Plugin::class, 'activate' ] );

// Deactivation: stop cron.
register_deactivation_hook( __FILE__, [ \LGMS\Plugin::class, 'deactivate' ] );

// Boot.
add_action( 'plugins_loaded', [ \LGMS\Plugin::class, 'boot' ] );

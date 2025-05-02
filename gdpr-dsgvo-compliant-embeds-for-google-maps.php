<?php
/**
 * Plugin Name:     GDPR-DSGVO compliant Embeds for Google Maps
 * Plugin URI:      https://lg.m00dy.org/gmaps-wordpress-plugin
 * Description:     Enables GDPR-compliant embedding of multiple Google Maps iframes with user consent, selectable light/dark design, and optional privacy policy notice.
 * Version:         1.0.1
 * Author:          Solution First by M00dy
 * Author URI:      https://profiles.wordpress.org/solutionfirst/
 * Text Domain:     gdpr-dsgvo-compliant-embeds-for-google-maps
 * Domain Path:     /languages
 *
 * @package         GDPR_Google_Maps_Embed_SF
 *
 * License:         GPLv2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 */




 function dsgvo_gm_plugin_action_links( $links ) {
    $settings_label = __( 'Settings', 'gdpr-dsgvo-compliant-embeds-for-google-maps' );
    $buy_label      = __( 'Buy Professional License', 'gdpr-dsgvo-compliant-embeds-for-google-maps' );

    $new_links = array(
        // Link 1: Settings
        '<a href="' . esc_url( admin_url( 'edit.php?post_type=dsgvo_map' ) ) . '">'
            . esc_html( $settings_label ) .
        '</a>',
        // Link 2: Professional License
        '<a href="' . esc_url( 'https://lg.m00dy.org/gmaps-wordpress-plugin' ) . '" target="_blank" class="dsgvo-gm-buy-link" style="color:rgb(198, 44, 44); font-weight: bold;">'
            . esc_html( $buy_label ) .
        '</a>',
    );

    return array_merge( $links, $new_links );
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'dsgvo_gm_plugin_action_links' );




if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Constants
define( 'DSGVO_GM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DSGVO_GM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'DSGVO_GM_MAX_MAPS', 3 );
define( 'DSGVO_GM_VERSION', '1.0.1' );

// Activation & Deactivation
register_activation_hook( __FILE__, 'dsgvo_gm_activate' );
function dsgvo_gm_activate() {
    dsgvo_gm_register_post_type();
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'dsgvo_gm_deactivate' );
function dsgvo_gm_deactivate() {
    flush_rewrite_rules();
}

// Load Textdomain
add_action( 'init', function() {
    load_plugin_textdomain( 'gdpr-dsgvo-compliant-embeds-for-google-maps', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
} );




function dsgvo_gm_enqueue_assets() {
    // CSS: Load CSS
    wp_enqueue_style( 'dsgvo-gm-style', DSGVO_GM_PLUGIN_URL . 'assets/css/dsgvo-gm.css' );

    // JS: Make sure that the JS is loaded
    wp_enqueue_script(
        'dsgvo-gm-script',
        DSGVO_GM_PLUGIN_URL . 'assets/js/dsgvo-gm.js',
        [ 'jquery' ],
        DSGVO_GM_VERSION, 
        true
    );

    wp_localize_script( 'dsgvo-gm-script', 'dsgvoGm', array(
        'buttonText' => __( 'Load Google Maps', 'gdpr-dsgvo-compliant-embeds-for-google-maps' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'dsgvo_gm_enqueue_assets' );

// Includes
require_once DSGVO_GM_PLUGIN_DIR . 'includes/admin.php';
require_once DSGVO_GM_PLUGIN_DIR . 'includes/frontend.php';
require_once DSGVO_GM_PLUGIN_DIR . 'includes/license.php';
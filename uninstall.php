<?php
/**
 * @package         GDPR_Google_Maps_Embed_SF
 * @license         GPLv2 or later
 * @license URI     https://www.gnu.org/licenses/gpl-2.0.html
 */


if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Delete all DSGVO Maps posts and their metadata
$maps = get_posts( array(
    'post_type'   => 'dsgvo_map',
    'numberposts' => -1,
    'post_status' => 'any',
) );
foreach ( $maps as $map ) {
    wp_delete_post( $map->ID, true );
}

// Remove license key option
delete_option( 'dsgvo_gm_license_key' );

// Remove transients for license validation
global $wpdb;
// Delete transient values and timeouts matching our prefix
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_dsgvo_gm_license_%'" );
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_dsgvo_gm_license_%'" );


// Remove complete Backup Directory
$backup_dir = plugin_dir_path( __FILE__ ) . 'backups/';

if ( ! is_dir( $backup_dir ) ) {
    return;
}

// Credentials-Check & WP_Filesystem
if ( ! function_exists( 'WP_Filesystem' ) ) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
}

// If credentials are missing redirect to enter credentials
if ( false === ( $credentials = request_filesystem_credentials( site_url() . '/wp-admin/', '', false, false, null ) ) ) {
    return; // Cancel till credentials are given
}

if ( ! WP_Filesystem( $credentials ) ) {
    return; // Cancel if error
}

global $wp_filesystem;

// Remove files from directory
$files = $wp_filesystem->dirlist( $backup_dir, false, true );
foreach ( $files as $filename => $fileinfo ) {
    if ( 'txt' === pathinfo( $filename, PATHINFO_EXTENSION ) ) {
        $wp_filesystem->delete( trailingslashit( $backup_dir ) . $filename, false );
    }
}

// Remove directory itself
$wp_filesystem->rmdir( $backup_dir, true );



// Flush rewrite rules in case CPT was removed
flush_rewrite_rules();


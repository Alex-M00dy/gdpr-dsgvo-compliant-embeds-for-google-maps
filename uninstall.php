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
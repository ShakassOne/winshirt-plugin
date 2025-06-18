<?php
/**
 * Uninstallation cleanup for the WinShirt plugin.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Delete plugin options
delete_option( 'winshirt_ftp_host' );
delete_option( 'winshirt_ftp_user' );
delete_option( 'winshirt_ftp_pass' );

// Remove custom posts created by the plugin
$post_types = [ 'winshirt_mockup', 'winshirt_visual', 'winshirt_lottery' ];
foreach ( $post_types as $type ) {
    $posts = get_posts([
        'post_type'   => $type,
        'numberposts' => -1,
        'post_status' => 'any',
    ]);
    foreach ( $posts as $p ) {
        wp_delete_post( $p->ID, true );
    }
}

// Clean product meta that references WinShirt data
$meta_keys = [
    '_winshirt_mockups',
    '_winshirt_visuals',
    '_winshirt_default_mockup_front',
    '_winshirt_default_mockup_back',
    '_winshirt_show_button',
    '_winshirt_enabled',
    'linked_lottery',
    'loterie_tickets',
];

$products = get_posts([
    'post_type'   => 'product',
    'numberposts' => -1,
    'post_status' => 'any',
]);

foreach ( $products as $product ) {
    foreach ( $meta_keys as $key ) {
        delete_post_meta( $product->ID, $key );
    }
}

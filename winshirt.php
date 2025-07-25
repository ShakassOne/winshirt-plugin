<?php
/**
 * Plugin Name: WinShirt by Shakass
 * Description: Module de personnalisation produit et loteries pour WooCommerce.
 * Version: 1.0.6
 * Author: Shakass Communication
 */

defined('ABSPATH') || exit;

define('WINSHIRT_PATH', plugin_dir_path(__FILE__));
define('WINSHIRT_URL', plugin_dir_url(__FILE__));

/**
 * Automatically load all PHP files from the given directory.
 *
 * @param string $dir Absolute path to directory.
 */
function winshirt_load_files( $dir ) {
    foreach ( glob( trailingslashit( $dir ) . '*.php' ) as $file ) {
        require_once $file;
    }
    foreach ( glob( trailingslashit( $dir ) . '*', GLOB_ONLYDIR ) as $sub ) {
        winshirt_load_files( $sub );
    }
}

winshirt_load_files( WINSHIRT_PATH . 'includes' );
if ( is_dir( WINSHIRT_PATH . 'admin' ) ) {
    winshirt_load_files( WINSHIRT_PATH . 'admin' );
}

require_once WINSHIRT_PATH . 'winshirt_ia_generate.php';

// Register uninstall hook
register_uninstall_hook(__FILE__, 'winshirt_plugin_uninstall');

/**
 * Callback executed on plugin uninstall.
 *
 * Loads the dedicated uninstall script which cleans up
 * options and custom post data created by the plugin.
 */
function winshirt_plugin_uninstall() {
    require_once WINSHIRT_PATH . 'uninstall.php';
}

// Register WinShirt admin pages
add_action('admin_menu', 'winshirt_register_admin_pages');

/**
 * Add WinShirt menu and submenus in the WordPress admin.
 */
function winshirt_register_admin_pages() {
    $icon_path    = WINSHIRT_PATH . 'assets/logo.svg';
    $icon_data_uri = 'dashicons-tshirt';

    if (file_exists($icon_path)) {
        $icon_data_uri = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($icon_path));
    }

    add_menu_page(
        'WinShirt',
        'WinShirt',
        'manage_options',
        'winshirt-dashboard',
        'winshirt_page_dashboard',
        $icon_data_uri,
        56
    );

    add_submenu_page(
        'winshirt-dashboard',
        'Dashboard',
        'Dashboard',
        'manage_options',
        'winshirt-dashboard',
        'winshirt_page_dashboard'
    );

    add_submenu_page(
        'winshirt-dashboard',
        'Mockups',
        'Mockups',
        'manage_options',
        'winshirt-mockups',
        'winshirt_page_mockups'
    );

    add_submenu_page(
        'winshirt-dashboard',
        'Visuels',
        'Visuels',
        'manage_options',
        'winshirt-designs',
        'winshirt_page_designs'
    );

    add_submenu_page(
        'winshirt-dashboard',
        'Produits',
        'Produits',
        'manage_options',
        'winshirt-products',
        'winshirt_page_products'
    );

    add_submenu_page(
        'winshirt-dashboard',
        'Loteries',
        'Loteries',
        'manage_options',
        'winshirt-lotteries',
        'winshirt_page_lotteries'
    );

    add_submenu_page(
        'winshirt-dashboard',
        'Commandes WinShirt',
        'Commandes',
        'manage_options',
        'winshirt-orders',
        'winshirt_page_orders'
    );

    add_submenu_page(
        'winshirt-dashboard',
        'Configuration',
        'Configuration',
        'manage_options',
        'winshirt-settings',
        'winshirt_page_settings'
    );
}

/**
 * Display the dashboard page.
 */
function winshirt_page_dashboard() {
    if ( isset( $_POST['winshirt_custom_page'] ) && check_admin_referer( 'winshirt_save_custom_page' ) ) {
        update_option( 'winshirt_custom_page', absint( $_POST['winshirt_custom_page'] ) );
        echo '<div class="updated notice"><p>' . esc_html__( 'Page enregistrée.', 'winshirt' ) . '</p></div>';
    }
    // Gather counts based on product metadata
    $products          = wc_get_products(['limit' => -1, 'status' => 'publish']);
    $mockup_ids        = [];
    $visual_ids        = [];
    $lotteries         = [];
    $product_count     = 0;
    $lottery_products  = 0;

    foreach ($products as $product) {
        $pid     = $product->get_id();
        $mockups = get_post_meta($pid, '_winshirt_mockups', true);
        $visuals = get_post_meta($pid, '_winshirt_visuals', true);
        $lottery = get_post_meta($pid, '_winshirt_lottery', true);
        $enabled = get_post_meta($pid, '_winshirt_enabled', true) === 'yes';

        if ($enabled) {
            $product_count++;
        }

        if ($mockups) {
            $ids        = array_filter(array_map('trim', explode(',', $mockups)));
            $mockup_ids = array_merge($mockup_ids, $ids);
        }

        if ($visuals) {
            $ids        = array_filter(array_map('trim', explode(',', $visuals)));
            $visual_ids = array_merge($visual_ids, $ids);
        }

        if ($lottery) {
            $lotteries[] = $lottery;
            $lottery_products++;
        }
    }

    $mockup_count   = count(array_unique($mockup_ids));
    $visual_count   = count(array_unique($visual_ids));
    $lottery_count  = count(array_unique($lotteries));
    $total_products = count($products);
    $lottery_progress = $total_products > 0 ? ($lottery_products / $total_products) * 100 : 0;

    echo '<div class="wrap">';
    include WINSHIRT_PATH . 'templates/admin/partials/dashboard.php';
    echo '</div>';
}

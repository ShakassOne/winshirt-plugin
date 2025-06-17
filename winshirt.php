<?php
/**
 * Plugin Name: WinShirt by Shakass
 * Description: Module de personnalisation produit et loteries pour WooCommerce.
 * Version: 1.0
 * Author: Shakass Communication
 */

defined('ABSPATH') || exit;

define('WINSHIRT_PATH', plugin_dir_path(__FILE__));
define('WINSHIRT_URL', plugin_dir_url(__FILE__));

require_once WINSHIRT_PATH . 'includes/init.php';
require_once WINSHIRT_PATH . 'includes/pages/mockups.php';
require_once WINSHIRT_PATH . 'includes/pages/visuels.php';
require_once WINSHIRT_PATH . 'includes/pages/loteries.php';
require_once WINSHIRT_PATH . 'includes/pages/produits.php';

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
        'Loteries',
        'Loteries',
        'manage_options',
        'winshirt-lotteries',
        'winshirt_page_lotteries'
    );

    add_submenu_page(
        'winshirt-dashboard',
        'Produits',
        'Produits',
        'manage_options',
        'winshirt-products',
        'winshirt_page_products'
    );
}

/**
 * Display the dashboard page.
 */
function winshirt_page_dashboard() {
    echo '<div class="wrap"><h1>Bienvenue sur le tableau de bord WinShirt. Interface à venir.</h1></div>';
}

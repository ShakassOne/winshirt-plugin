<?php
// Hook de démarrage
add_action('admin_menu', function () {
    add_menu_page('WinShirt', 'WinShirt', 'manage_options', 'winshirt', 'winshirt_admin_page', 'dashicons-tshirt', 56);
});

function winshirt_admin_page() {
    echo '<div class="wrap"><h1>WinShirt - Dashboard</h1><p>Bienvenue sur le back-office WinShirt.</p></div>';
}

// Enqueue assets on product pages
add_action('wp_enqueue_scripts', function () {
    if (is_product()) {
        wp_enqueue_style('winshirt-modal', WINSHIRT_URL . 'assets/css/winshirt-modal.css', [], '1.0');
        wp_enqueue_script('winshirt-modal', WINSHIRT_URL . 'assets/js/winshirt-modal.js', ['jquery'], '1.0', true);
    }
});

// Add customize button and modal on product page
add_action('woocommerce_after_add_to_cart_form', function () {
    echo '<button id="winshirt-open-modal" class="button">' . esc_html__('Personnaliser ce produit', 'winshirt') . '</button>';
    include WINSHIRT_PATH . 'templates/modal-personnalisation.php';
});

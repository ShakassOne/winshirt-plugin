<?php


// Enqueue assets on product pages
add_action('wp_enqueue_scripts', function () {
    if (is_product()) {
        wp_enqueue_style('winshirt-modal', WINSHIRT_URL . 'assets/css/winshirt-modal.css', [], '1.0');
        wp_enqueue_script('winshirt-modal', WINSHIRT_URL . 'assets/js/winshirt-modal.js', ['jquery'], '1.0', true);
    }
});

// Enqueue assets on WinShirt admin pages
add_action('admin_enqueue_scripts', function ($hook) {
    if (strpos($hook, 'winshirt') !== false) {
        wp_enqueue_style('winshirt-admin', WINSHIRT_URL . 'assets/css/winshirt-admin.css', [], '1.0');
        wp_enqueue_script('winshirt-admin', WINSHIRT_URL . 'assets/js/winshirt-admin.js', ['wp-element'], '1.0', true);
    }
});

// Add customize button and modal on product page
function winshirt_render_customize_button() {
    echo '<button id="winshirt-open-modal" class="button">' . esc_html__('Personnaliser ce produit', 'winshirt') . '</button>';
    include WINSHIRT_PATH . 'templates/frontend/modal-personnalisation.php';
}
add_action('woocommerce_single_product_summary', 'winshirt_render_customize_button', 35);

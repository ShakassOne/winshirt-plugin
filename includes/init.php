<?php


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

<?php
defined('ABSPATH') || exit;

function winshirt_page_products() {
    if (isset($_POST['winshirt_product_nonce']) && wp_verify_nonce($_POST['winshirt_product_nonce'], 'save_winshirt_product_meta')) {
        $product_id = absint($_POST['product_id']);

        $mockups = isset($_POST['winshirt_mockups']) ? array_map('intval', (array) $_POST['winshirt_mockups']) : [];
        update_post_meta($product_id, '_winshirt_mockups', implode(',', $mockups));

        update_post_meta($product_id, '_winshirt_visuals', sanitize_text_field($_POST['winshirt_visuals'] ?? ''));
        update_post_meta($product_id, '_winshirt_lottery', sanitize_text_field($_POST['winshirt_lottery'] ?? ''));

        update_post_meta($product_id, '_winshirt_default_mockup_front', absint($_POST['winshirt_default_front'] ?? 0));
        update_post_meta($product_id, '_winshirt_default_mockup_back', absint($_POST['winshirt_default_back'] ?? 0));

        update_post_meta($product_id, '_winshirt_show_button', isset($_POST['winshirt_show_button']) ? 'yes' : 'no');
        update_post_meta($product_id, '_winshirt_enabled', isset($_POST['winshirt_enabled']) ? 'yes' : 'no');

        echo '<div class="updated"><p>' . esc_html__('Metadonnees enregistrees.', 'winshirt') . '</p></div>';
    }

    $search   = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
    $products = wc_get_products([
        'search' => $search,
        'limit'  => -1,
        'status' => 'publish',
    ]);

    $all_mockups = get_posts([
        'post_type'   => 'winshirt_mockup',
        'numberposts' => -1,
        'orderby'     => 'title',
    ]);

    echo '<div class="wrap"><h1>' . esc_html__('Produits', 'winshirt') . '</h1>';
    include WINSHIRT_PATH . 'templates/admin/partials/produits-list.php';
    echo '</div>';
}

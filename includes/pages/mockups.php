<?php
defined('ABSPATH') || exit;

function winshirt_page_mockups() {
    $editing = null;

    // Handle deletion
    if (isset($_GET['delete']) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'delete_mockup_' . absint($_GET['delete']))) {
        wp_delete_post(absint($_GET['delete']), true);
        echo '<div class="updated"><p>' . esc_html__('Mockup supprime.', 'winshirt') . '</p></div>';
    }

    // Handle edit request
    if (isset($_GET['edit'])) {
        $editing = get_post(absint($_GET['edit']));
    }

    // Handle form submission
    if (isset($_POST['winshirt_mockup_nonce']) && wp_verify_nonce($_POST['winshirt_mockup_nonce'], 'save_winshirt_mockup')) {
        $mockup_id = isset($_POST['mockup_id']) ? absint($_POST['mockup_id']) : 0;

        $data = [
            'post_type'   => 'winshirt_mockup',
            'post_title'  => sanitize_text_field($_POST['title'] ?? ''),
            'post_status' => 'publish',
        ];

        if ($mockup_id) {
            $data['ID'] = $mockup_id;
            wp_update_post($data);
        } else {
            $mockup_id = wp_insert_post($data);
        }

        update_post_meta($mockup_id, '_winshirt_product_type', sanitize_text_field($_POST['product_type'] ?? ''));
        update_post_meta($mockup_id, '_winshirt_side', in_array($_POST['side'] ?? 'front', ['front', 'back'], true) ? $_POST['side'] : 'front');
        update_post_meta($mockup_id, '_winshirt_format', sanitize_text_field($_POST['format'] ?? ''));

        $area = [
            'x' => floatval($_POST['area_x'] ?? 0),
            'y' => floatval($_POST['area_y'] ?? 0),
            'w' => floatval($_POST['area_w'] ?? 0),
            'h' => floatval($_POST['area_h'] ?? 0),
        ];
        update_post_meta($mockup_id, '_winshirt_area', $area);

        $families = isset($_POST['families']) ? array_map('intval', (array) $_POST['families']) : [];
        update_post_meta($mockup_id, '_winshirt_families', $families);

        if (!empty($_FILES['image']['tmp_name'])) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';
            $attachment_id = media_handle_upload('image', 0);
            if (!is_wp_error($attachment_id)) {
                set_post_thumbnail($mockup_id, $attachment_id);
            }
        }

        echo '<div class="updated"><p>' . esc_html__('Mockup enregistre.', 'winshirt') . '</p></div>';
        $editing = get_post($mockup_id);
    }

    $mockups    = get_posts(['post_type' => 'winshirt_mockup', 'numberposts' => -1, 'orderby' => 'title']);
    $categories = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);

    echo '<div class="wrap"><h1>' . esc_html__('Gestion des mockups', 'winshirt') . '</h1>';
    include WINSHIRT_PATH . 'templates/admin/partials/mockups-list.php';
    echo '</div>';
}

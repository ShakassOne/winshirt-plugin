<?php
defined('ABSPATH') || exit;

function winshirt_page_lotteries() {
    $editing = null;
    $participants = [];

    // Handle deletion
    if (isset($_GET['delete']) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'delete_lottery_' . absint($_GET['delete']))) {
        wp_delete_post(absint($_GET['delete']), true);
        echo '<div class="updated"><p>' . esc_html__('Loterie supprimee.', 'winshirt') . '</p></div>';
    }

    // Handle edit request
    if (isset($_GET['edit'])) {
        $editing = get_post(absint($_GET['edit']));
        $participants = get_post_meta($editing->ID, '_winshirt_lottery_participants', true);
        $participants = is_array($participants) ? $participants : [];
    }

    // Handle form submission
    if (isset($_POST['winshirt_lottery_nonce']) && wp_verify_nonce($_POST['winshirt_lottery_nonce'], 'save_winshirt_lottery')) {
        $lottery_id = isset($_POST['lottery_id']) ? absint($_POST['lottery_id']) : 0;

        $data = [
            'post_type'   => 'winshirt_lottery',
            'post_title'  => sanitize_text_field($_POST['title'] ?? ''),
            'post_content'=> sanitize_textarea_field($_POST['description'] ?? ''),
            'post_status' => 'publish',
        ];

        if ($lottery_id) {
            $data['ID'] = $lottery_id;
            wp_update_post($data);
        } else {
            $lottery_id = wp_insert_post($data);
        }

        update_post_meta($lottery_id, '_winshirt_lottery_start', sanitize_text_field($_POST['start'] ?? ''));
        update_post_meta($lottery_id, '_winshirt_lottery_end', sanitize_text_field($_POST['end'] ?? ''));
        update_post_meta($lottery_id, '_winshirt_lottery_prizes', sanitize_textarea_field($_POST['prizes'] ?? ''));
        // Save related product IDs as a sanitized text string
        update_post_meta($lottery_id, '_winshirt_lottery_product', sanitize_text_field($_POST['product'] ?? ''));
        update_post_meta($lottery_id, '_winshirt_lottery_active', isset($_POST['active']) ? 'yes' : 'no');
        update_post_meta($lottery_id, '_winshirt_lottery_draw', in_array($_POST['draw'] ?? 'manual', ['manual','auto'], true) ? $_POST['draw'] : 'manual');
        update_post_meta($lottery_id, 'max_participants', absint($_POST['max_participants'] ?? 0));

        if (!empty($_FILES['animation']['tmp_name'])) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';
            $attachment_id = media_handle_upload('animation', 0);
            if (!is_wp_error($attachment_id)) {
                update_post_meta($lottery_id, '_winshirt_lottery_animation', $attachment_id);
            }
        }

        echo '<div class="updated"><p>' . esc_html__('Loterie enregistree.', 'winshirt') . '</p></div>';
        $editing = get_post($lottery_id);
        $participants = get_post_meta($lottery_id, '_winshirt_lottery_participants', true);
        $participants = is_array($participants) ? $participants : [];
    }

    $lotteries = get_posts([
        'post_type'   => 'winshirt_lottery',
        'numberposts' => -1,
        'orderby'     => 'date',
    ]);

    echo '<div class="wrap"><h1>' . esc_html__('Gestion des loteries', 'winshirt') . '</h1>';
    include WINSHIRT_PATH . 'templates/admin/partials/loteries-list.php';
    echo '</div>';
}

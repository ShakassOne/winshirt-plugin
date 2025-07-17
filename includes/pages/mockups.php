<?php
defined('ABSPATH') || exit;

function winshirt_page_mockups() {
    $editing = null;

    if (isset($_GET['delete']) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'delete_mockup_' . absint($_GET['delete']))) {
        wp_delete_post(absint($_GET['delete']), true);
        echo '<div class="updated"><p>' . esc_html__('Mockup supprime.', 'winshirt') . '</p></div>';
    }

    if (isset($_GET['edit'])) {
        $editing = get_post(absint($_GET['edit']));
    } elseif (isset($_GET['add'])) {
        $editing = (object) ['ID' => 0];
    }

    if (isset($_POST['winshirt_mockup_nonce']) && wp_verify_nonce($_POST['winshirt_mockup_nonce'], 'save_winshirt_mockup')) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

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

        update_post_meta($mockup_id, '_winshirt_category', sanitize_text_field($_POST['category'] ?? ''));

        $front_id = isset($_POST['front_image_id']) ? absint($_POST['front_image_id']) : 0;
        if ($front_id) {
            update_post_meta($mockup_id, '_winshirt_front_image', $front_id);
        }

        $back_id = isset($_POST['back_image_id']) ? absint($_POST['back_image_id']) : 0;
        if ($back_id) {
            update_post_meta($mockup_id, '_winshirt_back_image', $back_id);
        }

        // Colors
        $colors = [];
        if (!empty($_POST['colors']) && is_array($_POST['colors'])) {
            foreach ((array) $_POST['colors'] as $idx => $cdata) {
                if (isset($cdata['remove']) && $cdata['remove']) {
                    continue;
                }
                $name = sanitize_text_field($cdata['name'] ?? '');
                $code = sanitize_text_field($cdata['code'] ?? '');
                if ($name) {
                    $colors[] = [
                        'name' => $name,
                        'code' => $code,
                    ];
                }
            }
        }
        update_post_meta($mockup_id, '_winshirt_colors', $colors);

        $zones = [];
        if (!empty($_POST['zones']) && is_array($_POST['zones'])) {
            foreach ((array) $_POST['zones'] as $z) {
                if (empty($z['name'])) {
                    continue;
                }
                $zones[] = [
                    'name'   => sanitize_text_field($z['name']),
                    'format' => in_array($z['format'], ['A3','A4','A5','A6','A7']) ? $z['format'] : 'A4',
                    'side'   => $z['side'] === 'back' ? 'back' : 'front',
                    'price'  => floatval(str_replace(',', '.', $z['price'] ?? 0)),
                    'top'    => floatval($z['top'] ?? 0),
                    'left'   => floatval($z['left'] ?? 0),
                    'width'  => floatval($z['width'] ?? 0),
                    'height' => floatval($z['height'] ?? 0),
                ];
            }
        }
        update_post_meta($mockup_id, '_winshirt_print_zones', $zones);

        echo '<div class="updated"><p>' . esc_html__('Mockup enregistre.', 'winshirt') . '</p></div>';
        $editing = get_post($mockup_id);
    }

    $mockups = get_posts([
        'post_type'   => 'winshirt_mockup',
        'numberposts' => -1,
        'orderby'     => 'title',
    ]);

    echo '<div class="wrap"><h1>' . esc_html__('Gestion des mockups', 'winshirt') . '</h1>';
    include WINSHIRT_PATH . 'templates/admin/partials/mockups-list.php';
    echo '</div>';
}

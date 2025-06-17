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

        if (!empty($_FILES['front_image']['tmp_name'])) {
            $front_id = media_handle_upload('front_image', 0);
            if (!is_wp_error($front_id)) {
                update_post_meta($mockup_id, '_winshirt_front_image', $front_id);
            }
        }

        if (!empty($_FILES['back_image']['tmp_name'])) {
            $back_id = media_handle_upload('back_image', 0);
            if (!is_wp_error($back_id)) {
                update_post_meta($mockup_id, '_winshirt_back_image', $back_id);
            }
        }

        // Colors
        $colors = [];
        if (!empty($_POST['colors']) && is_array($_POST['colors'])) {
            foreach ((array) $_POST['colors'] as $idx => $cdata) {
                if (isset($cdata['remove']) && $cdata['remove']) {
                    continue;
                }
                $c = [
                    'name' => sanitize_text_field($cdata['name'] ?? ''),
                    'code' => sanitize_text_field($cdata['code'] ?? ''),
                    'front' => 0,
                    'back'  => 0,
                ];
                if (!empty($_FILES['color_front_' . $idx]['tmp_name'])) {
                    $fid = media_handle_upload('color_front_' . $idx, 0);
                    if (!is_wp_error($fid)) {
                        $c['front'] = $fid;
                    }
                } elseif (!empty($cdata['front'])) {
                    $c['front'] = absint($cdata['front']);
                }
                if (!empty($_FILES['color_back_' . $idx]['tmp_name'])) {
                    $bid = media_handle_upload('color_back_' . $idx, 0);
                    if (!is_wp_error($bid)) {
                        $c['back'] = $bid;
                    }
                } elseif (!empty($cdata['back'])) {
                    $c['back'] = absint($cdata['back']);
                }
                if ($c['name']) {
                    $colors[] = $c;
                }
            }
        }
        update_post_meta($mockup_id, '_winshirt_colors', $colors);

        $areas = [];
        foreach (['A3', 'A4', 'A5', 'A6', 'A7'] as $fmt) {
            $areas[$fmt] = [
                'top'    => floatval($_POST['area_' . $fmt . '_top'] ?? 0),
                'left'   => floatval($_POST['area_' . $fmt . '_left'] ?? 0),
                'width'  => floatval($_POST['area_' . $fmt . '_width'] ?? 0),
                'height' => floatval($_POST['area_' . $fmt . '_height'] ?? 0),
            ];
        }
        update_post_meta($mockup_id, '_winshirt_print_areas', $areas);

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

<?php
defined('ABSPATH') || exit;

/**
 * Upload the given file to the configured FTP server.
 */
function winshirt_send_to_ftp($file_path) {
    $host = get_option('winshirt_ftp_host');
    $user = get_option('winshirt_ftp_user');
    $pass = get_option('winshirt_ftp_pass');

    if (!$host || !$user || !$pass) {
        return;
    }

    $conn = @ftp_connect($host);
    if ($conn && @ftp_login($conn, $user, $pass)) {
        ftp_pasv($conn, true);
        $remote = basename($file_path);
        @ftp_put($conn, $remote, $file_path, FTP_BINARY);
        ftp_close($conn);
    }
}

function winshirt_page_designs() {
    $filters = [
        'date' => sanitize_text_field($_GET['date'] ?? ''),
    ];

    // Handle single deletion
    if (isset($_GET['delete']) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'delete_visual_' . absint($_GET['delete']))) {
        wp_delete_post(absint($_GET['delete']), true);
        echo '<div class="updated"><p>' . esc_html__('Visuel supprimé.', 'winshirt') . '</p></div>';
    }

    // Handle validation
    if (isset($_GET['validate']) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'validate_visual_' . absint($_GET['validate']))) {
        update_post_meta(absint($_GET['validate']), '_winshirt_visual_validated', 'yes');
        echo '<div class="updated"><p>' . esc_html__('Visuel validé.', 'winshirt') . '</p></div>';
    }

    // Handle bulk deletion
    if (isset($_POST['winshirt_bulk_nonce']) && wp_verify_nonce($_POST['winshirt_bulk_nonce'], 'winshirt_bulk_action')) {
        if (!empty($_POST['selected']) && $_POST['bulk_action'] === 'delete') {
            foreach ((array) $_POST['selected'] as $vid) {
                wp_delete_post(absint($vid), true);
            }
            echo '<div class="updated"><p>' . esc_html__('Visuels supprimés.', 'winshirt') . '</p></div>';
        }
    }

    // Handle add form
    if (isset($_POST['winshirt_visual_nonce']) && wp_verify_nonce($_POST['winshirt_visual_nonce'], 'winshirt_add_visual')) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $attachment_id = media_handle_upload('file', 0);
        if (!is_wp_error($attachment_id)) {
            $post_id = wp_insert_post([
                'post_type'   => 'winshirt_visual',
                'post_title'  => sanitize_text_field($_POST['title'] ?? ''),
                'post_status' => 'publish',
            ]);
            if ($post_id) {
                set_post_thumbnail($post_id, $attachment_id);
                update_post_meta($post_id, '_winshirt_category', sanitize_text_field($_POST['category'] ?? ''));
                update_post_meta($post_id, '_winshirt_visual_validated', 'no');
                $path = get_attached_file($attachment_id);
                winshirt_send_to_ftp($path);
                echo '<div class="updated"><p>' . esc_html__('Visuel ajouté.', 'winshirt') . '</p></div>';
            }
        } else {
            echo '<div class="woocommerce-error"><p>' . esc_html__('Erreur lors de l\'upload.', 'winshirt') . '</p></div>';
        }
    }

    $query = [
        'post_type'   => 'winshirt_visual',
        'numberposts' => -1,
        'orderby'     => 'date',
        'order'       => 'DESC',
    ];



    if ($filters['date']) {
        $query['date_query'][] = [
            'after'     => $filters['date'],
            'before'    => $filters['date'] . ' 23:59:59',
            'inclusive' => true,
        ];
    }

    $visuals = get_posts($query);

    echo '<div class="wrap"><h1>Bibliothèque des visuels</h1>';
    include WINSHIRT_PATH . 'templates/admin/partials/visuels-list.php';
    echo '</div>';
}

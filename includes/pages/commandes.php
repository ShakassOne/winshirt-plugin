<?php
defined('ABSPATH') || exit;

/**
 * Display and manage WinShirt orders for production.
 */
function winshirt_page_orders() {
    $filters = [
        'status'    => sanitize_text_field($_GET['status'] ?? ''),
        'date_from' => sanitize_text_field($_GET['date_from'] ?? ''),
        'date_to'   => sanitize_text_field($_GET['date_to'] ?? ''),
    ];

    // Handle production validation
    if (isset($_POST['production_validate'], $_POST['order_id'], $_POST['validate_nonce']) && wp_verify_nonce($_POST['validate_nonce'], 'validate_production_' . absint($_POST['order_id']))) {
        update_post_meta(absint($_POST['order_id']), '_winshirt_production_validated', 'yes');
        if (!get_post_meta(absint($_POST['order_id']), '_winshirt_production_status', true)) {
            update_post_meta(absint($_POST['order_id']), '_winshirt_production_status', 'En cours');
        }
        echo '<div class="updated"><p>' . esc_html__('Production validée.', 'winshirt') . '</p></div>';
    }

    // Handle CSV export
    if (isset($_GET['export']) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'winshirt_export_csv')) {
        $orders = winshirt_get_production_orders($filters);
        winshirt_export_orders_csv($orders);
        exit;
    }

    $orders = winshirt_get_production_orders($filters);

    echo '<div class="wrap"><h1>Commandes WinShirt</h1>';
    include WINSHIRT_PATH . 'templates/admin/partials/commandes-list.php';
    echo '</div>';
}

/**
 * Retrieve orders containing WinShirt enabled products.
 *
 * @param array $filters Optional filters.
 * @return WC_Order[]
 */
function winshirt_get_production_orders(array $filters = []) {
    $args   = [
        'limit'  => -1,
        'orderby'=> 'date',
        'order'  => 'DESC',
    ];
    $orders = wc_get_orders($args);
    $result = [];

    foreach ($orders as $order) {
        $include = false;
        foreach ($order->get_items() as $item) {
            $pid = $item->get_product_id();
            if (get_post_meta($pid, '_winshirt_enabled', true) === 'yes') {
                $include = true;
                break;
            }
        }
        if (!$include) {
            continue;
        }

        $status = $order->get_meta('_winshirt_production_status');
        if ($filters['status'] && $filters['status'] !== $status) {
            continue;
        }

        $created = $order->get_date_created();
        if ($created) {
            $created = $created->format('Y-m-d');
            if ($filters['date_from'] && $created < $filters['date_from']) {
                continue;
            }
            if ($filters['date_to'] && $created > $filters['date_to']) {
                continue;
            }
        }

        $result[] = $order;
    }

    return $result;
}

/**
 * Output a CSV export of orders.
 *
 * @param WC_Order[] $orders Orders to export.
 */
function winshirt_export_orders_csv(array $orders) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="commandes-winshirt.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID', 'Client', 'Produit', 'Fichier recto', 'Fichier verso', 'Statut']);
    foreach ($orders as $order) {
        foreach ($order->get_items() as $item) {
            $pid = $item->get_product_id();
            if (get_post_meta($pid, '_winshirt_enabled', true) !== 'yes') {
                continue;
            }
            $customer = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
            $front    = $item->get_meta('winshirt_front_hd');
            $back     = $item->get_meta('winshirt_back_hd');
            $status   = $order->get_meta('_winshirt_production_status') ?: 'A produire';
            fputcsv($out, [$order->get_id(), $customer, $item->get_name(), $front, $back, $status]);
        }
    }
    fclose($out);
}

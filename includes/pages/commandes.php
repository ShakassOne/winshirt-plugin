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
    $page   = max(1, absint($_GET['paged'] ?? 1));

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
        $orders = winshirt_get_production_orders($filters, -1, 1, false);
        winshirt_export_orders_csv($orders);
        exit;
    }

    $results       = winshirt_get_production_orders($filters, 20, $page);
    $orders        = $results['orders'];
    $max_num_pages = $results['max'];

    echo '<div class="wrap"><h1>Commandes WinShirt</h1>';
    include WINSHIRT_PATH . 'templates/admin/partials/commandes-list.php';
    echo '</div>';
}

/**
 * Retrieve orders containing WinShirt enabled products.
 *
 * @param array $filters  Optional filters.
 * @param int   $limit    Results per page.
 * @param int   $page     Page number.
 * @param bool  $paginate Whether to paginate results.
 * @return array|WC_Order[]
 */
function winshirt_get_production_orders(array $filters = [], int $limit = 20, int $page = 1, bool $paginate = true) {
    $product_ids = get_posts([
        'post_type'   => 'product',
        'numberposts' => -1,
        'fields'      => 'ids',
        'meta_query'  => [
            [
                'key'   => '_winshirt_enabled',
                'value' => 'yes',
            ],
        ],
    ]);

    if (!$product_ids) {
        return $paginate ? ['orders' => [], 'max' => 0] : [];
    }

    $args = [
        'orderby'    => 'date',
        'order'      => 'DESC',
        'limit'      => $limit,
        'page'       => $page,
        'paginate'   => $paginate,
        'product_id' => $product_ids,
        'meta_query' => [],
    ];

    if ($filters['status']) {
        $args['meta_query'][] = [
            'key'   => '_winshirt_production_status',
            'value' => $filters['status'],
        ];
    }

    if ($filters['date_from'] || $filters['date_to']) {
        $range = [];
        if ($filters['date_from']) {
            $range['after'] = $filters['date_from'];
        }
        if ($filters['date_to']) {
            $range['before'] = $filters['date_to'];
        }
        $args['date_created'] = $range;
    }

    $results = wc_get_orders($args);

    if ($paginate) {
        return [
            'orders' => $results->orders,
            'max'    => $results->max_num_pages,
        ];
    }

    return $results;
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

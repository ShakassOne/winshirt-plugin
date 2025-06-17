<?php
/**
 * List of WinShirt orders for production.
 * Variables: $orders, $filters
 */
?>
<form method="get" style="margin-bottom:15px;">
    <input type="hidden" name="page" value="winshirt-orders" />
    <select name="status">
        <option value=""><?php esc_html_e('Tous les statuts', 'winshirt'); ?></option>
        <?php
        $statuses = ['A produire', 'En cours', 'Expédié'];
        foreach ($statuses as $s) {
            echo '<option value="' . esc_attr($s) . '" ' . selected($filters['status'], $s, false) . '>' . esc_html($s) . '</option>';
        }
        ?>
    </select>
    <input type="date" name="date_from" value="<?php echo esc_attr($filters['date_from']); ?>" />
    <input type="date" name="date_to" value="<?php echo esc_attr($filters['date_to']); ?>" />
    <input type="submit" class="button" value="<?php esc_attr_e('Filtrer', 'winshirt'); ?>" />
    <a class="button" href="<?php echo esc_url(wp_nonce_url(add_query_arg(array_merge($_GET, ['export' => 1]), admin_url('admin.php')), 'winshirt_export_csv')); ?>">Export CSV</a>
</form>
<table class="widefat fixed">
    <thead>
        <tr>
            <th><?php esc_html_e('ID', 'winshirt'); ?></th>
            <th><?php esc_html_e('Client', 'winshirt'); ?></th>
            <th><?php esc_html_e('Produit', 'winshirt'); ?></th>
            <th><?php esc_html_e('Recto', 'winshirt'); ?></th>
            <th><?php esc_html_e('Verso', 'winshirt'); ?></th>
            <th><?php esc_html_e('Statut', 'winshirt'); ?></th>
            <th><?php esc_html_e('Valider', 'winshirt'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if ($orders) : ?>
            <?php foreach ($orders as $order) : ?>
                <?php foreach ($order->get_items() as $item) : ?>
                    <?php if (get_post_meta($item->get_product_id(), '_winshirt_enabled', true) !== 'yes') { continue; } ?>
                    <?php
                        $front_prev = $item->get_meta('winshirt_front_preview');
                        $back_prev  = $item->get_meta('winshirt_back_preview');
                        $front_hd   = $item->get_meta('winshirt_front_hd');
                        $back_hd    = $item->get_meta('winshirt_back_hd');
                        $status     = $order->get_meta('_winshirt_production_status') ?: 'A produire';
                        $validated  = $order->get_meta('_winshirt_production_validated') === 'yes';
                    ?>
                    <tr>
                        <td><?php echo esc_html($order->get_id()); ?></td>
                        <td><?php echo esc_html($order->get_billing_first_name() . ' ' . $order->get_billing_last_name()); ?></td>
                        <td><?php echo esc_html($item->get_name()); ?></td>
                        <td>
                            <?php if ($front_prev) : ?>
                                <img src="<?php echo esc_url($front_prev); ?>" style="max-width:80px;height:auto;" alt="front" />
                            <?php endif; ?>
                            <?php if ($front_hd) : ?>
                                <br/><a class="button" href="<?php echo esc_url($front_hd); ?>" download><?php esc_html_e('Télécharger', 'winshirt'); ?></a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($back_prev) : ?>
                                <img src="<?php echo esc_url($back_prev); ?>" style="max-width:80px;height:auto;" alt="back" />
                            <?php endif; ?>
                            <?php if ($back_hd) : ?>
                                <br/><a class="button" href="<?php echo esc_url($back_hd); ?>" download><?php esc_html_e('Télécharger', 'winshirt'); ?></a>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html($status); ?></td>
                        <td>
                            <?php if (!$validated) : ?>
                                <form method="post">
                                    <?php wp_nonce_field('validate_production_' . $order->get_id(), 'validate_nonce'); ?>
                                    <input type="hidden" name="order_id" value="<?php echo esc_attr($order->get_id()); ?>" />
                                    <input type="hidden" name="production_validate" value="1" />
                                    <input type="submit" class="button" value="<?php esc_attr_e('Valider cette production', 'winshirt'); ?>" />
                                </form>
                            <?php else : ?>
                                <?php esc_html_e('Validée', 'winshirt'); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php else : ?>
            <tr><td colspan="7"><?php esc_html_e('Aucune commande', 'winshirt'); ?></td></tr>
        <?php endif; ?>
    </tbody>
</table>

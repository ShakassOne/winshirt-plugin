<?php
/**
 * Admin product list for WinShirt.
 */
?>
<form method="get" style="margin-bottom:15px;">
    <input type="hidden" name="page" value="winshirt-products" />
    <input type="search" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'Rechercher un produit', 'winshirt' ); ?>" />
    <input type="submit" class="button" value="<?php esc_attr_e( 'Rechercher', 'winshirt' ); ?>" />
</form>
<table class="widefat fixed">
    <thead>
        <tr>
            <th><?php esc_html_e( 'Produit', 'winshirt' ); ?></th>
            <th><?php esc_html_e( 'Mockups', 'winshirt' ); ?></th>
            <th><?php esc_html_e( 'Visuels', 'winshirt' ); ?></th>
            <th><?php esc_html_e( 'Loterie', 'winshirt' ); ?></th>
            <th><?php esc_html_e( 'Personnalisation', 'winshirt' ); ?></th>
            <th><?php esc_html_e( 'Action', 'winshirt' ); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php if ( $products ) : ?>
        <?php foreach ( $products as $product ) : ?>
            <?php
                $pid     = $product->get_id();
                $mockups = get_post_meta( $pid, '_winshirt_mockups', true );
                $visuals = get_post_meta( $pid, '_winshirt_visuals', true );
                $lottery = get_post_meta( $pid, '_winshirt_lottery', true );
                $enabled = get_post_meta( $pid, '_winshirt_enabled', true ) === 'yes';
            ?>
            <tr>
                <td><?php echo esc_html( $product->get_name() ); ?></td>
                <td><input type="text" name="winshirt_mockups" value="<?php echo esc_attr( $mockups ); ?>" form="winshirt-form-<?php echo esc_attr( $pid ); ?>" /></td>
                <td><input type="text" name="winshirt_visuals" value="<?php echo esc_attr( $visuals ); ?>" form="winshirt-form-<?php echo esc_attr( $pid ); ?>" /></td>
                <td><input type="text" name="winshirt_lottery" value="<?php echo esc_attr( $lottery ); ?>" form="winshirt-form-<?php echo esc_attr( $pid ); ?>" /></td>
                <td style="text-align:center;"><input type="checkbox" name="winshirt_enabled" value="1" <?php checked( $enabled ); ?> form="winshirt-form-<?php echo esc_attr( $pid ); ?>" /></td>
                <td>
                    <form method="post" id="winshirt-form-<?php echo esc_attr( $pid ); ?>">
                        <?php wp_nonce_field( 'save_winshirt_product_meta', 'winshirt_product_nonce' ); ?>
                        <input type="hidden" name="product_id" value="<?php echo esc_attr( $pid ); ?>" />
                        <input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Enregistrer', 'winshirt' ); ?>" />
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else : ?>
        <tr><td colspan="6"><?php esc_html_e( 'Aucun produit trouve.', 'winshirt' ); ?></td></tr>
    <?php endif; ?>
    </tbody>
</table>

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
            <th style="text-align:center;"><?php esc_html_e( 'Personnalisable', 'winshirt' ); ?></th>
            <th style="text-align:center;"><?php esc_html_e( 'Afficher bouton', 'winshirt' ); ?></th>
            <th><?php esc_html_e( 'Mockups', 'winshirt' ); ?></th>
            <th><?php esc_html_e( 'Loterie', 'winshirt' ); ?></th>
            <th><?php esc_html_e( 'Tickets', 'winshirt' ); ?></th>
            <th><?php esc_html_e( 'Action', 'winshirt' ); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php if ( $products ) : ?>
        <?php foreach ( $products as $product ) : ?>
            <?php
                $pid           = $product->get_id();
                $mockups_raw   = get_post_meta( $pid, '_winshirt_mockups', true );
                $mockups       = $mockups_raw ? array_map( 'intval', explode( ',', $mockups_raw ) ) : [];
                $lottery       = get_post_meta( $pid, 'linked_lottery', true );
                $tickets       = get_post_meta( $pid, 'loterie_tickets', true );
                $enabled       = get_post_meta( $pid, '_winshirt_enabled', true ) === 'yes';
                $show_button   = get_post_meta( $pid, '_winshirt_show_button', true ) === 'yes';
                $default_front = absint( get_post_meta( $pid, '_winshirt_default_mockup_front', true ) );
                $default_back  = absint( get_post_meta( $pid, '_winshirt_default_mockup_back', true ) );
            ?>
            <tr>
                <td><?php echo esc_html( $product->get_name() ); ?></td>
                <td style="text-align:center;"><input type="checkbox" name="winshirt_enabled" value="1" <?php checked( $enabled ); ?> form="winshirt-form-<?php echo esc_attr( $pid ); ?>" /></td>
                <td style="text-align:center;"><input type="checkbox" name="winshirt_show_button" value="1" <?php checked( $show_button ); ?> form="winshirt-form-<?php echo esc_attr( $pid ); ?>" /></td>
                <td>
                    <select name="winshirt_mockups[]" multiple size="3" form="winshirt-form-<?php echo esc_attr( $pid ); ?>">
                        <?php foreach ( $all_mockups as $m ) : ?>
                            <option value="<?php echo esc_attr( $m->ID ); ?>" <?php selected( in_array( $m->ID, $mockups, true ) ); ?>><?php echo esc_html( $m->post_title ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <br />
                    <label><?php esc_html_e( 'Mockup recto', 'winshirt' ); ?>
                        <select name="winshirt_default_front" form="winshirt-form-<?php echo esc_attr( $pid ); ?>">
                            <option value="">-<?php esc_html_e( 'Aucun', 'winshirt' ); ?>-</option>
                            <?php foreach ( $all_mockups as $m ) : ?>
                                <option value="<?php echo esc_attr( $m->ID ); ?>" <?php selected( $default_front, $m->ID ); ?>><?php echo esc_html( $m->post_title ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <br />
                    <label><?php esc_html_e( 'Mockup verso', 'winshirt' ); ?>
                        <select name="winshirt_default_back" form="winshirt-form-<?php echo esc_attr( $pid ); ?>">
                            <option value="">-<?php esc_html_e( 'Aucun', 'winshirt' ); ?>-</option>
                            <?php foreach ( $all_mockups as $m ) : ?>
                                <option value="<?php echo esc_attr( $m->ID ); ?>" <?php selected( $default_back, $m->ID ); ?>><?php echo esc_html( $m->post_title ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </td>
                <td>
                    <button type="button" class="toggle-lottery button" data-target="lottery-<?php echo esc_attr( $pid ); ?>"><?php esc_html_e( 'Afficher les loteries', 'winshirt' ); ?></button>
                    <select id="lottery-<?php echo esc_attr( $pid ); ?>" class="hidden" name="linked_lottery" form="winshirt-form-<?php echo esc_attr( $pid ); ?>">
                        <option value="">-</option>
                        <?php foreach ( $all_lotteries as $l ) : ?>
                            <option value="<?php echo esc_attr( $l->ID ); ?>" <?php selected( $lottery, $l->ID ); ?>><?php echo esc_html( $l->post_title ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td><input type="number" name="loterie_tickets" value="<?php echo esc_attr( $tickets ); ?>" form="winshirt-form-<?php echo esc_attr( $pid ); ?>" /></td>
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
        <tr><td colspan="7"><?php esc_html_e( 'Aucun produit trouve.', 'winshirt' ); ?></td></tr>
    <?php endif; ?>
    </tbody>
</table>
<script>
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.toggle-lottery').forEach(function(btn){
        btn.addEventListener('click', function(){
            var target = btn.getAttribute('data-target');
            var select = document.getElementById(target);
            if(select){
                select.classList.toggle('hidden');
            }
        });
    });
});
</script>

<?php
/**
 * Admin mockup management interface.
 * Variables: $mockups, $editing, $categories
 */
?>
<h2><?php esc_html_e('Liste des mockups', 'winshirt'); ?></h2>
<table class="widefat fixed">
    <thead>
        <tr>
            <th><?php esc_html_e('Titre', 'winshirt'); ?></th>
            <th><?php esc_html_e('Type', 'winshirt'); ?></th>
            <th><?php esc_html_e('Recto/Verso', 'winshirt'); ?></th>
            <th><?php esc_html_e('Format', 'winshirt'); ?></th>
            <th><?php esc_html_e('Aperçu', 'winshirt'); ?></th>
            <th><?php esc_html_e('Actions', 'winshirt'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php if ($mockups) : ?>
        <?php foreach ($mockups as $mockup) : ?>
            <?php
                $type   = get_post_meta($mockup->ID, '_winshirt_product_type', true);
                $side   = get_post_meta($mockup->ID, '_winshirt_side', true);
                $format = get_post_meta($mockup->ID, '_winshirt_format', true);
            ?>
            <tr>
                <td><?php echo esc_html($mockup->post_title); ?></td>
                <td><?php echo esc_html($type); ?></td>
                <td><?php echo esc_html($side === 'back' ? 'Verso' : 'Recto'); ?></td>
                <td><?php echo esc_html($format); ?></td>
                <td><?php echo get_the_post_thumbnail($mockup->ID, 'thumbnail'); ?></td>
                <td>
                    <a class="button" href="<?php echo esc_url(add_query_arg(['page' => 'winshirt-mockups', 'edit' => $mockup->ID], admin_url('admin.php'))); ?>"><?php esc_html_e('Modifier', 'winshirt'); ?></a>
                    <a class="button delete" href="<?php echo esc_url(wp_nonce_url(add_query_arg(['page' => 'winshirt-mockups', 'delete' => $mockup->ID], admin_url('admin.php')), 'delete_mockup_' . $mockup->ID)); ?>"><?php esc_html_e('Supprimer', 'winshirt'); ?></a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else : ?>
        <tr><td colspan="6"><?php esc_html_e('Aucun mockup', 'winshirt'); ?></td></tr>
    <?php endif; ?>
    </tbody>
</table>

<hr/>

<h2><?php echo $editing ? esc_html__('Modifier le mockup', 'winshirt') : esc_html__('Ajouter un mockup', 'winshirt'); ?></h2>
<form method="post" enctype="multipart/form-data">
    <?php wp_nonce_field('save_winshirt_mockup', 'winshirt_mockup_nonce'); ?>
    <input type="hidden" name="mockup_id" value="<?php echo esc_attr($editing->ID ?? 0); ?>" />
    <table class="form-table">
        <tr>
            <th scope="row"><label for="mockup-title">Titre</label></th>
            <td><input name="title" id="mockup-title" type="text" class="regular-text" value="<?php echo esc_attr($editing->post_title ?? ''); ?>" required /></td>
        </tr>
        <tr>
            <th scope="row">Image</th>
            <td>
                <input type="file" name="image" />
                <?php if ($editing && has_post_thumbnail($editing->ID)) { echo get_the_post_thumbnail($editing->ID, 'thumbnail'); } ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="product-type">Type de produit</label></th>
            <td>
                <select name="product_type" id="product-type">
                    <?php $types = ['T-Shirt','Polo','Casquette','Sweat'];
                    $current_type = $editing ? get_post_meta($editing->ID, '_winshirt_product_type', true) : '';
                    foreach ($types as $t) {
                        echo '<option value="' . esc_attr($t) . '" ' . selected($current_type, $t, false) . '>' . esc_html($t) . '</option>';
                    } ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="format">Format impression</label></th>
            <td>
                <select name="format" id="format">
                    <?php $formats = ['A3','A4','A5','A6','A7'];
                    $current_format = $editing ? get_post_meta($editing->ID, '_winshirt_format', true) : '';
                    foreach ($formats as $f) {
                        echo '<option value="' . esc_attr($f) . '" ' . selected($current_format, $f, false) . '>' . esc_html($f) . '</option>';
                    } ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row">Recto/Verso</th>
            <td>
                <?php $current_side = $editing ? get_post_meta($editing->ID, '_winshirt_side', true) : 'front'; ?>
                <label><input type="radio" name="side" value="front" <?php checked($current_side, 'front'); ?> /> <?php esc_html_e('Recto', 'winshirt'); ?></label>
                <label style="margin-left:10px;"><input type="radio" name="side" value="back" <?php checked($current_side, 'back'); ?> /> <?php esc_html_e('Verso', 'winshirt'); ?></label>
            </td>
        </tr>
        <?php $area = $editing ? get_post_meta($editing->ID, '_winshirt_area', true) : ['x'=>'','y'=>'','w'=>'','h'=>''];
              $area = is_array($area) ? $area : ['x'=>'','y'=>'','w'=>'','h'=>'']; ?>
        <tr>
            <th scope="row">Zone d'impression</th>
            <td>
                X <input type="number" step="0.01" name="area_x" value="<?php echo esc_attr($area['x']); ?>" style="width:70px;" />
                Y <input type="number" step="0.01" name="area_y" value="<?php echo esc_attr($area['y']); ?>" style="width:70px;" />
                <?php esc_html_e('Largeur', 'winshirt'); ?> <input type="number" step="0.01" name="area_w" value="<?php echo esc_attr($area['w']); ?>" style="width:70px;" />
                <?php esc_html_e('Hauteur', 'winshirt'); ?> <input type="number" step="0.01" name="area_h" value="<?php echo esc_attr($area['h']); ?>" style="width:70px;" />
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="families">Familles de produits</label></th>
            <td>
                <?php $current_families = $editing ? get_post_meta($editing->ID, '_winshirt_families', true) : [];
                $current_families = is_array($current_families) ? $current_families : []; ?>
                <select name="families[]" id="families" multiple size="5">
                    <?php foreach ($categories as $cat) {
                        echo '<option value="' . esc_attr($cat->term_id) . '" ' . selected(in_array($cat->term_id, $current_families), true, false) . '>' . esc_html($cat->name) . '</option>';
                    } ?>
                </select>
            </td>
        </tr>
    </table>
    <p>
        <input type="submit" class="button button-primary" value="<?php echo $editing ? esc_attr__('Mettre à jour', 'winshirt') : esc_attr__('Ajouter mockup', 'winshirt'); ?>" />
    </p>
</form>

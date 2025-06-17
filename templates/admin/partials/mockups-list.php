<?php
/**
 * Advanced mockup management interface.
 * Variables: $mockups, $editing
 */
?>
<p><a href="<?php echo esc_url(add_query_arg(['page'=>'winshirt-mockups','add'=>1], admin_url('admin.php'))); ?>" class="button button-primary"><?php esc_html_e('Ajouter un mockup', 'winshirt'); ?></a></p>
<table class="widefat fixed">
    <thead>
        <tr>
            <th><?php esc_html_e('Nom du mockup', 'winshirt'); ?></th>
            <th><?php esc_html_e('Catégorie', 'winshirt'); ?></th>
            <th><?php esc_html_e('Couleurs', 'winshirt'); ?></th>
            <th><?php esc_html_e('Actions', 'winshirt'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php if ($mockups) : ?>
        <?php foreach ($mockups as $m) : ?>
            <?php $colors = get_post_meta($m->ID, '_winshirt_colors', true); $colors = is_array($colors) ? $colors : []; ?>
            <tr>
                <td><?php echo esc_html($m->post_title); ?></td>
                <td><?php echo esc_html(get_post_meta($m->ID, '_winshirt_category', true)); ?></td>
                <td><?php echo count($colors); ?></td>
                <td>
                    <a class="button" href="<?php echo esc_url(add_query_arg(['page'=>'winshirt-mockups','edit'=>$m->ID], admin_url('admin.php'))); ?>"><?php esc_html_e('Éditer', 'winshirt'); ?></a>
                    <a class="button delete" href="<?php echo esc_url(wp_nonce_url(add_query_arg(['page'=>'winshirt-mockups','delete'=>$m->ID], admin_url('admin.php')), 'delete_mockup_' . $m->ID)); ?>"><?php esc_html_e('Supprimer', 'winshirt'); ?></a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else : ?>
        <tr><td colspan="4"><?php esc_html_e('Aucun mockup', 'winshirt'); ?></td></tr>
    <?php endif; ?>
    </tbody>
</table>
<?php if ($editing !== null) : ?>
<hr />
<h2><?php echo $editing && $editing->ID ? esc_html__('Modifier le mockup', 'winshirt') : esc_html__('Ajouter un mockup', 'winshirt'); ?></h2>
<form method="post" enctype="multipart/form-data" id="mockup-form">
    <?php wp_nonce_field('save_winshirt_mockup', 'winshirt_mockup_nonce'); ?>
    <input type="hidden" name="mockup_id" value="<?php echo esc_attr($editing->ID); ?>" />
    <h3><?php esc_html_e('Informations générales', 'winshirt'); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="mockup-title"><?php esc_html_e('Nom du mockup', 'winshirt'); ?></label></th>
            <td><input type="text" id="mockup-title" name="title" class="regular-text" value="<?php echo esc_attr($editing->post_title ?? ''); ?>" required /></td>
        </tr>
        <tr>
            <th><label for="category"><?php esc_html_e('Catégorie produit', 'winshirt'); ?></label></th>
            <td><input type="text" id="category" name="category" value="<?php echo esc_attr(get_post_meta($editing->ID, '_winshirt_category', true)); ?>" /></td>
        </tr>
        <tr>
            <th><?php esc_html_e('Image recto', 'winshirt'); ?></th>
            <td>
                <input type="file" name="front_image" />
                <?php $front = $editing->ID ? get_post_meta($editing->ID, '_winshirt_front_image', true) : ''; if ($front) echo wp_get_attachment_image($front, 'thumbnail'); ?>
            </td>
        </tr>
        <tr>
            <th><?php esc_html_e('Image verso', 'winshirt'); ?></th>
            <td>
                <input type="file" name="back_image" />
                <?php $back = $editing->ID ? get_post_meta($editing->ID, '_winshirt_back_image', true) : ''; if ($back) echo wp_get_attachment_image($back, 'thumbnail'); ?>
            </td>
        </tr>
    </table>
    <h3><?php esc_html_e('Variantes de couleurs', 'winshirt'); ?></h3>
    <div id="colors-container">
        <?php
        $colors = $editing->ID ? get_post_meta($editing->ID, '_winshirt_colors', true) : [];
        $colors = is_array($colors) ? $colors : [];
        $index = 0;
        foreach ($colors as $c) : ?>
            <div class="color-row">
                <button class="remove-color button">&times;</button>
                <input type="hidden" name="colors[<?php echo $index; ?>][front]" value="<?php echo esc_attr($c['front']); ?>" />
                <input type="hidden" name="colors[<?php echo $index; ?>][back]" value="<?php echo esc_attr($c['back']); ?>" />
                <label><?php esc_html_e('Nom', 'winshirt'); ?> <input type="text" name="colors[<?php echo $index; ?>][name]" value="<?php echo esc_attr($c['name']); ?>" /></label>
                <label><?php esc_html_e('Code', 'winshirt'); ?> <input type="color" name="colors[<?php echo $index; ?>][code]" value="<?php echo esc_attr($c['code']); ?>" /></label>
                <label><?php esc_html_e('Image recto', 'winshirt'); ?> <input type="file" name="color_front_<?php echo $index; ?>" /><?php if ($c['front']) echo wp_get_attachment_image($c['front'], 'thumbnail'); ?></label>
                <label><?php esc_html_e('Image verso', 'winshirt'); ?> <input type="file" name="color_back_<?php echo $index; ?>" /><?php if ($c['back']) echo wp_get_attachment_image($c['back'], 'thumbnail'); ?></label>
            </div>
        <?php $index++; endforeach; ?>
    </div>
    <p><a href="#" id="add-color" class="button"><?php esc_html_e('Ajouter une couleur', 'winshirt'); ?></a></p>
    <script type="text/template" id="color-template">
        <div class="color-row">
            <button class="remove-color button">&times;</button>
            <input type="hidden" name="colors[%i%][front]" value="" />
            <input type="hidden" name="colors[%i%][back]" value="" />
            <label><?php esc_html_e('Nom', 'winshirt'); ?> <input type="text" name="colors[%i%][name]" value="" /></label>
            <label><?php esc_html_e('Code', 'winshirt'); ?> <input type="color" name="colors[%i%][code]" value="#000000" /></label>
            <label><?php esc_html_e('Image recto', 'winshirt'); ?> <input type="file" name="color_front_%i%" /></label>
            <label><?php esc_html_e('Image verso', 'winshirt'); ?> <input type="file" name="color_back_%i%" /></label>
        </div>
    </script>
    <h3><?php esc_html_e('Zones d\'impression', 'winshirt'); ?></h3>
    <div id="print-zone-wrapper">
        <div id="mockup-canvas">
            <?php if ($front) { echo wp_get_attachment_image($front, 'medium'); } ?>
            <?php
            $areas = $editing->ID ? get_post_meta($editing->ID, '_winshirt_print_areas', true) : [];
            $areas = is_array($areas) ? $areas : [];
            foreach (['A3','A4','A5','A6','A7'] as $fmt) {
                $a = $areas[$fmt] ?? ['top'=>10,'left'=>10,'width'=>20,'height'=>20];
                echo '<div class="print-zone" data-format="'.$fmt.'" style="top:'.$a['top'].'%;left:'.$a['left'].'%;width:'.$a['width'].'%;height:'.$a['height'].'%;">'.$fmt.'</div>';
            }
            ?>
        </div>
    </div>
    <?php foreach (['A3','A4','A5','A6','A7'] as $fmt) : $a = $areas[$fmt] ?? ['top'=>0,'left'=>0,'width'=>0,'height'=>0]; ?>
        <input type="hidden" id="area_<?php echo $fmt; ?>_top" name="area_<?php echo $fmt; ?>_top" value="<?php echo esc_attr($a['top']); ?>" />
        <input type="hidden" id="area_<?php echo $fmt; ?>_left" name="area_<?php echo $fmt; ?>_left" value="<?php echo esc_attr($a['left']); ?>" />
        <input type="hidden" id="area_<?php echo $fmt; ?>_width" name="area_<?php echo $fmt; ?>_width" value="<?php echo esc_attr($a['width']); ?>" />
        <input type="hidden" id="area_<?php echo $fmt; ?>_height" name="area_<?php echo $fmt; ?>_height" value="<?php echo esc_attr($a['height']); ?>" />
    <?php endforeach; ?>
    <p>
        <input type="submit" class="button button-primary" value="<?php echo $editing && $editing->ID ? esc_attr__('Mettre à jour', 'winshirt') : esc_attr__('Enregistrer', 'winshirt'); ?>" />
    </p>
</form>
<?php endif; ?>

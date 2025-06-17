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
    <?php
        $zones = $editing->ID ? get_post_meta($editing->ID, '_winshirt_print_zones', true) : [];
        $zones = is_array($zones) ? $zones : [];
    ?>
    <div id="zone-controls">
        <div id="zones-container">
        <?php $zindex = 0; foreach ($zones as $z) : ?>
            <div class="zone-row" data-index="<?php echo $zindex; ?>">
                <button class="remove-zone button">&times;</button>
                <label><?php esc_html_e('Nom', 'winshirt'); ?> <input type="text" name="zones[<?php echo $zindex; ?>][name]" value="<?php echo esc_attr($z['name']); ?>" /></label>
                <label><?php esc_html_e('Format', 'winshirt'); ?>
                    <select name="zones[<?php echo $zindex; ?>][format]" class="zone-format">
                        <?php foreach(['A3','A4','A5','A6','A7'] as $fmt) echo '<option value="'.$fmt.'" '.selected($z['format'],$fmt,false).'>'.$fmt.'</option>'; ?>
                    </select>
                </label>
                <label><?php esc_html_e('Face', 'winshirt'); ?>
                    <select name="zones[<?php echo $zindex; ?>][side]" class="zone-side">
                        <option value="front" <?php selected($z['side'],'front'); ?>><?php esc_html_e('Recto','winshirt'); ?></option>
                        <option value="back" <?php selected($z['side'],'back'); ?>><?php esc_html_e('Verso','winshirt'); ?></option>
                    </select>
                </label>
                <input type="hidden" name="zones[<?php echo $zindex; ?>][top]" class="zone-top" value="<?php echo esc_attr($z['top']); ?>" />
                <input type="hidden" name="zones[<?php echo $zindex; ?>][left]" class="zone-left" value="<?php echo esc_attr($z['left']); ?>" />
                <input type="hidden" name="zones[<?php echo $zindex; ?>][width]" class="zone-width" value="<?php echo esc_attr($z['width']); ?>" />
                <input type="hidden" name="zones[<?php echo $zindex; ?>][height]" class="zone-height" value="<?php echo esc_attr($z['height']); ?>" />
            </div>
        <?php $zindex++; endforeach; ?>
        </div>
        <p><a href="#" id="add-zone" class="button"><?php esc_html_e('Ajouter une zone', 'winshirt'); ?></a></p>
    </div>
    <script type="text/template" id="zone-template">
        <div class="zone-row" data-index="%i%">
            <button class="remove-zone button">&times;</button>
            <label><?php esc_html_e('Nom', 'winshirt'); ?> <input type="text" name="zones[%i%][name]" /></label>
            <label><?php esc_html_e('Format', 'winshirt'); ?>
                <select name="zones[%i%][format]" class="zone-format">
                    <option value="A3">A3</option>
                    <option value="A4" selected>A4</option>
                    <option value="A5">A5</option>
                    <option value="A6">A6</option>
                    <option value="A7">A7</option>
                </select>
            </label>
            <label><?php esc_html_e('Face', 'winshirt'); ?>
                <select name="zones[%i%][side]" class="zone-side">
                    <option value="front"><?php esc_html_e('Recto','winshirt'); ?></option>
                    <option value="back"><?php esc_html_e('Verso','winshirt'); ?></option>
                </select>
            </label>
            <input type="hidden" name="zones[%i%][top]" class="zone-top" value="10" />
            <input type="hidden" name="zones[%i%][left]" class="zone-left" value="10" />
            <input type="hidden" name="zones[%i%][width]" class="zone-width" value="20" />
            <input type="hidden" name="zones[%i%][height]" class="zone-height" value="20" />
        </div>
    </script>

    <div id="print-zone-wrapper">
        <div id="mockup-canvas-front" class="mockup-canvas">
            <?php if ($front) { echo wp_get_attachment_image($front, 'medium'); } ?>
            <?php foreach ($zones as $i => $z) { if ($z['side'] !== 'front') continue; echo '<div class="print-zone" data-index="'.$i.'" data-side="front" data-format="'.$z['format'].'" style="top:'.$z['top'].'%;left:'.$z['left'].'%;width:'.$z['width'].'%;height:'.$z['height'].'%;">'.$z['format'].'</div>'; } ?>
        </div>
        <div id="mockup-canvas-back" class="mockup-canvas">
            <?php if ($back) { echo wp_get_attachment_image($back, 'medium'); } ?>
            <?php foreach ($zones as $i => $z) { if ($z['side'] !== 'back') continue; echo '<div class="print-zone" data-index="'.$i.'" data-side="back" data-format="'.$z['format'].'" style="top:'.$z['top'].'%;left:'.$z['left'].'%;width:'.$z['width'].'%;height:'.$z['height'].'%;">'.$z['format'].'</div>'; } ?>
        </div>
    </div>
    <p>
        <input type="submit" class="button button-primary" value="<?php echo $editing && $editing->ID ? esc_attr__('Mettre à jour', 'winshirt') : esc_attr__('Enregistrer', 'winshirt'); ?>" />
    </p>
</form>
<?php endif; ?>

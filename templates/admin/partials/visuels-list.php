<?php
/**
 * Admin visual management interface.
 * Variables: $visuals, $filters, $types
 */
?>
<form method="get" style="margin-bottom:15px;">
    <input type="hidden" name="page" value="winshirt-designs" />
    <select name="type">
        <option value=""><?php esc_html_e('Tous les types', 'winshirt'); ?></option>
        <?php foreach ($types as $t) : ?>
            <option value="<?php echo esc_attr($t); ?>" <?php selected($filters['type'], $t); ?>><?php echo esc_html($t); ?></option>
        <?php endforeach; ?>
    </select>
    <input type="date" name="date" value="<?php echo esc_attr($filters['date']); ?>" />
    <input type="submit" class="button" value="<?php esc_attr_e('Filtrer', 'winshirt'); ?>" />
</form>

<form method="post">
    <?php wp_nonce_field('winshirt_bulk_action', 'winshirt_bulk_nonce'); ?>
    <table class="widefat fixed">
        <thead>
            <tr>
                <th style="width:20px;"><input type="checkbox" id="select-all" /></th>
                <th><?php esc_html_e('Miniature', 'winshirt'); ?></th>
                <th><?php esc_html_e('Nom', 'winshirt'); ?></th>
                <th><?php esc_html_e('Type', 'winshirt'); ?></th>
                <th><?php esc_html_e('Date', 'winshirt'); ?></th>
                <th><?php esc_html_e('Actions', 'winshirt'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($visuals) : ?>
                <?php foreach ($visuals as $visual) :
                    $type = get_post_meta($visual->ID, '_winshirt_visual_type', true);
                    $validated = get_post_meta($visual->ID, '_winshirt_visual_validated', true) === 'yes';
                ?>
                <tr>
                    <td><input type="checkbox" name="selected[]" value="<?php echo esc_attr($visual->ID); ?>" /></td>
                    <td><?php echo get_the_post_thumbnail($visual->ID, 'thumbnail'); ?></td>
                    <td><?php echo esc_html($visual->post_title); ?></td>
                    <td><?php echo esc_html($type); ?></td>
                    <td><?php echo esc_html(get_the_date('', $visual)); ?></td>
                    <td>
                        <?php if (!$validated) : ?>
                            <a class="button" href="<?php echo esc_url(wp_nonce_url(add_query_arg(['page' => 'winshirt-designs', 'validate' => $visual->ID], admin_url('admin.php')), 'validate_visual_' . $visual->ID)); ?>"><?php esc_html_e('Valider ce visuel', 'winshirt'); ?></a>
                        <?php endif; ?>
                        <a class="button delete" href="<?php echo esc_url(wp_nonce_url(add_query_arg(['page' => 'winshirt-designs', 'delete' => $visual->ID], admin_url('admin.php')), 'delete_visual_' . $visual->ID)); ?>"><?php esc_html_e('Supprimer', 'winshirt'); ?></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr><td colspan="6"><?php esc_html_e('Aucun visuel', 'winshirt'); ?></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <p>
        <select name="bulk_action">
            <option value=""><?php esc_html_e('Actions groupées', 'winshirt'); ?></option>
            <option value="delete"><?php esc_html_e('Supprimer', 'winshirt'); ?></option>
        </select>
        <input type="submit" class="button" value="<?php esc_attr_e('Appliquer', 'winshirt'); ?>" />
    </p>
</form>

<hr/>

<h2><?php esc_html_e('Ajouter un visuel', 'winshirt'); ?></h2>
<form method="post" enctype="multipart/form-data">
    <?php wp_nonce_field('winshirt_add_visual', 'winshirt_visual_nonce'); ?>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="visual-title"><?php esc_html_e('Nom', 'winshirt'); ?></label></th>
            <td><input name="title" id="visual-title" type="text" class="regular-text" required /></td>
        </tr>
        <tr>
            <th scope="row"><label for="visual-file"><?php esc_html_e('Fichier', 'winshirt'); ?></label></th>
            <td><input type="file" name="file" id="visual-file" required /></td>
        </tr>
        <tr>
            <th scope="row"><label for="visual-type"><?php esc_html_e('Type', 'winshirt'); ?></label></th>
            <td>
                <select name="type" id="visual-type">
                    <?php foreach ($types as $t) { echo '<option value="' . esc_attr($t) . '">' . esc_html($t) . '</option>'; } ?>
                </select>
            </td>
        </tr>
    </table>
    <p><input type="submit" class="button button-primary" value="<?php esc_attr_e('Uploader', 'winshirt'); ?>" /></p>
</form>

<script>
(function(){
    const master = document.getElementById('select-all');
    if(master){
        master.addEventListener('change', function(){
            document.querySelectorAll('input[name="selected[]"]').forEach(function(c){ c.checked = master.checked; });
        });
    }
})();
</script>

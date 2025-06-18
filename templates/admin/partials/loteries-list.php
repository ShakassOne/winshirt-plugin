<?php
/**
 * Admin lottery management interface.
 * Variables: $lotteries, $editing, $participants
 */
?>
<table class="widefat fixed">
    <thead>
        <tr>
            <th><?php esc_html_e('Titre', 'winshirt'); ?></th>
            <th><?php esc_html_e('Produit', 'winshirt'); ?></th>
            <th><?php esc_html_e('Dates', 'winshirt'); ?></th>
            <th style="text-align:center;">Actif</th>
            <th><?php esc_html_e('Participations', 'winshirt'); ?></th>
            <th><?php esc_html_e('Shortcodes', 'winshirt'); ?></th>
            <th><?php esc_html_e('Actions', 'winshirt'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php if ($lotteries) : ?>
        <?php foreach ($lotteries as $lottery) : ?>
            <?php
                $start  = get_post_meta($lottery->ID, '_winshirt_lottery_start', true);
                $end    = get_post_meta($lottery->ID, '_winshirt_lottery_end', true);
                $product= get_post_meta($lottery->ID, '_winshirt_lottery_product', true);
                $active = get_post_meta($lottery->ID, '_winshirt_lottery_active', true) === 'yes';
                $parts  = get_post_meta($lottery->ID, '_winshirt_lottery_participants', true);
                $count  = is_array($parts) ? count($parts) : 0;
            ?>
            <tr>
                <td><?php echo esc_html($lottery->post_title); ?></td>
                <td><?php echo esc_html($product); ?></td>
                <td><?php echo esc_html($start . ' - ' . $end); ?></td>
                <td style="text-align:center;"><input type="checkbox" disabled <?php checked($active); ?> /></td>
                <td><?php echo esc_html($count); ?></td>
                <td>
                    <input type="text" readonly class="regular-text code" value="[loterie_box id=&quot;<?php echo esc_attr($lottery->ID); ?>&quot;]" onclick="this.select();" />
                    <p class="description"><?php esc_html_e('Carte complète', 'winshirt'); ?></p>
                    <input type="text" readonly class="regular-text code" value="[loterie_thumb id=&quot;<?php echo esc_attr($lottery->ID); ?>&quot;]" onclick="this.select();" />
                    <p class="description"><?php esc_html_e('Miniature uniquement', 'winshirt'); ?></p>
                </td>
                <td>
                    <a class="button" href="<?php echo esc_url(add_query_arg(['page' => 'winshirt-lotteries', 'edit' => $lottery->ID], admin_url('admin.php'))); ?>"><?php esc_html_e('Modifier', 'winshirt'); ?></a>
                    <a class="button delete" href="<?php echo esc_url(wp_nonce_url(add_query_arg(['page' => 'winshirt-lotteries', 'delete' => $lottery->ID], admin_url('admin.php')), 'delete_lottery_' . $lottery->ID)); ?>"><?php esc_html_e('Supprimer', 'winshirt'); ?></a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else : ?>
        <tr><td colspan="7"><?php esc_html_e('Aucune loterie', 'winshirt'); ?></td></tr>
    <?php endif; ?>
    </tbody>
</table>
<p class="description">
    <?php esc_html_e('Utilisez [loterie_box id="123"] pour afficher la carte complète ou [loterie_thumb id="123"] pour uniquement la miniature (remplacez 123 par l\'ID de la loterie).', 'winshirt'); ?>
</p>

<?php if ($editing) : ?>
<h2><?php esc_html_e('Participants', 'winshirt'); ?> (<?php echo count($participants); ?>)</h2>
<?php if ($participants) : ?>
    <ul style="list-style:disc;padding-left:20px;">
        <?php foreach ($participants as $p) { echo '<li>' . esc_html($p) . '</li>'; } ?>
    </ul>
<?php else : ?>
    <p><?php esc_html_e('Aucun participant', 'winshirt'); ?></p>
<?php endif; ?>
<?php endif; ?>

<hr/>

<h2><?php echo $editing ? esc_html__('Modifier la loterie', 'winshirt') : esc_html__('Ajouter une loterie', 'winshirt'); ?></h2>
<form method="post" enctype="multipart/form-data">
    <?php wp_nonce_field('save_winshirt_lottery', 'winshirt_lottery_nonce'); ?>
    <input type="hidden" name="lottery_id" value="<?php echo esc_attr($editing->ID ?? 0); ?>" />
    <table class="form-table">
        <tr>
            <th scope="row"><label for="lottery-title">Titre</label></th>
            <td><input name="title" id="lottery-title" type="text" class="regular-text" value="<?php echo esc_attr($editing->post_title ?? ''); ?>" required /></td>
        </tr>
        <tr>
            <th scope="row"><label for="lottery-description">Description</label></th>
            <td><textarea name="description" id="lottery-description" rows="4" class="large-text"><?php echo esc_textarea($editing->post_content ?? ''); ?></textarea></td>
        </tr>
        <tr>
            <th scope="row"><label for="lottery-start">Date de début</label></th>
            <td><input type="date" name="start" id="lottery-start" value="<?php echo esc_attr(get_post_meta($editing->ID ?? 0, '_winshirt_lottery_start', true)); ?>" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="lottery-end">Date de fin</label></th>
            <td><input type="date" name="end" id="lottery-end" value="<?php echo esc_attr(get_post_meta($editing->ID ?? 0, '_winshirt_lottery_end', true)); ?>" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="lottery-prizes">Lots à gagner</label></th>
            <td><textarea name="prizes" id="lottery-prizes" rows="3" class="large-text"><?php echo esc_textarea(get_post_meta($editing->ID ?? 0, '_winshirt_lottery_prizes', true)); ?></textarea></td>
        </tr>
        <tr>
            <th scope="row"><label for="lottery-product">Produit WooCommerce (ID)</label></th>
            <td><input type="number" name="product" id="lottery-product" value="<?php echo esc_attr(get_post_meta($editing->ID ?? 0, '_winshirt_lottery_product', true)); ?>" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="lottery-max">Participants max</label></th>
            <td>
                <input type="number" name="max_participants" id="lottery-max" value="<?php echo esc_attr(get_post_meta($editing->ID ?? 0, 'max_participants', true) ?: 0); ?>" />
                <p class="description">Nombre total de participants autoris&eacute;s (0 = illimit&eacute;)</p>
            </td>
        </tr>
        <tr>
            <th scope="row">Active</th>
            <td><label><input type="checkbox" name="active" value="1" <?php checked(get_post_meta($editing->ID ?? 0, '_winshirt_lottery_active', true), 'yes'); ?> /> <?php esc_html_e('Activer cette loterie', 'winshirt'); ?></label></td>
        </tr>
        <tr>
            <th scope="row"><label for="lottery-draw">Tirage au sort</label></th>
            <td>
                <?php $draw = get_post_meta($editing->ID ?? 0, '_winshirt_lottery_draw', true) ?: 'manual'; ?>
                <select name="draw" id="lottery-draw">
                    <option value="manual" <?php selected($draw, 'manual'); ?>><?php esc_html_e('Manuel', 'winshirt'); ?></option>
                    <option value="auto" <?php selected($draw, 'auto'); ?>><?php esc_html_e('Automatique à la date de fin', 'winshirt'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="lottery-animation">Animation/Image</label></th>
            <td>
                <input type="file" name="animation" id="lottery-animation" />
                <?php $anim = get_post_meta($editing->ID ?? 0, '_winshirt_lottery_animation', true); if ($anim) { echo wp_get_attachment_image($anim, 'thumbnail'); } ?>
            </td>
        </tr>
    </table>
    <p><input type="submit" class="button button-primary" value="<?php echo $editing ? esc_attr__('Mettre à jour', 'winshirt') : esc_attr__('Ajouter la loterie', 'winshirt'); ?>" /></p>
</form>

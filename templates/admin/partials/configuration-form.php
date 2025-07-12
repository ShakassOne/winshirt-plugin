<form method="post" action="options.php">
    <?php
    settings_fields('winshirt_options');
    do_settings_sections('winshirt_options');
    ?>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="winshirt-ftp-host">FTP Host</label></th>
            <td><input name="winshirt_ftp_host" id="winshirt-ftp-host" type="text" value="<?php echo esc_attr(get_option('winshirt_ftp_host')); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="winshirt-ftp-user">FTP User</label></th>
            <td><input name="winshirt_ftp_user" id="winshirt-ftp-user" type="text" value="<?php echo esc_attr(get_option('winshirt_ftp_user')); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="winshirt-ftp-pass">FTP Password</label></th>
            <td><input name="winshirt_ftp_pass" id="winshirt-ftp-pass" type="password" value="<?php echo esc_attr(get_option('winshirt_ftp_pass')); ?>" class="regular-text" /></td>
        </tr>
    </table>

    <h2>Paramètres Supabase</h2>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="winshirt-supabase-url">URL Supabase</label></th>
            <td><input name="winshirt_supabase_url" id="winshirt-supabase-url" type="text" value="<?php echo esc_attr(get_option('winshirt_supabase_url')); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="winshirt-supabase-key">Clé publique</label></th>
            <td><input name="winshirt_supabase_key" id="winshirt-supabase-key" type="text" value="<?php echo esc_attr(get_option('winshirt_supabase_key')); ?>" class="regular-text" /></td>
        </tr>
    </table>

    <h2>Paramètres IA</h2>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="winshirt-ia-api-key">Clé API IA</label></th>
            <td>
                <input name="winshirt_ia_api_key" id="winshirt-ia-api-key" type="password" value="<?php echo esc_attr(get_option('winshirt_ia_api_key')); ?>" class="regular-text" />
                <button id="winshirt-test-ia-key" class="button">Tester la clé</button>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="winshirt-ia-model">Modèle IA</label></th>
            <td><input name="winshirt_ia_model" id="winshirt-ia-model" type="text" value="<?php echo esc_attr(get_option('winshirt_ia_model', 'dall-e-3')); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="winshirt-ia-output-format">Format de sortie</label></th>
            <td><input name="winshirt_ia_output_format" id="winshirt-ia-output-format" type="text" value="<?php echo esc_attr(get_option('winshirt_ia_output_format', '1024x1024')); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="winshirt-ia-generation-limit">Limite de génération</label></th>
            <td><input name="winshirt_ia_generation_limit" id="winshirt-ia-generation-limit" type="number" value="<?php echo esc_attr(get_option('winshirt_ia_generation_limit', '5')); ?>" class="small-text" /></td>
        </tr>
        <tr>
            <th scope="row"><label for="winshirt-ia-image-folder">Dossier de stockage</label></th>
            <td><input name="winshirt_ia_image_folder" id="winshirt-ia-image-folder" type="text" value="<?php echo esc_attr(get_option('winshirt_ia_image_folder', '/wp-content/uploads/winshirt/ia/')); ?>" class="regular-text" /></td>
        </tr>
    </table>
    <?php submit_button(); ?>
</form>

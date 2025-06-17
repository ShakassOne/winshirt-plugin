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
    <?php submit_button(); ?>
</form>

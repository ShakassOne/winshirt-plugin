<?php
defined('ABSPATH') || exit;

function winshirt_page_settings() {
    echo '<div class="wrap"><h1>Configuration WinShirt</h1>';
    include WINSHIRT_PATH . 'templates/admin/partials/configuration-form.php';
    echo '</div>';
}

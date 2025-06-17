<?php
defined('ABSPATH') || exit;

function winshirt_page_mockups() {
    echo '<div class="wrap"><h1>Gestion des mockups</h1>';
    include WINSHIRT_PATH . 'templates/admin/partials/mockups-list.php';
    echo '</div>';
}

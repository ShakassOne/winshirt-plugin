<?php
defined('ABSPATH') || exit;

function winshirt_page_designs() {
    echo '<div class="wrap"><h1>Bibliothèque des visuels</h1>';
    include WINSHIRT_PATH . 'templates/admin/partials/visuels-list.php';
    echo '</div>';
}

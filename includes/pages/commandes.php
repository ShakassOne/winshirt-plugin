<?php
defined('ABSPATH') || exit;

function winshirt_page_orders() {
    echo '<div class="wrap"><h1>Commandes WinShirt</h1>';
    include WINSHIRT_PATH . 'templates/admin/partials/commandes-list.php';
    echo '</div>';
}

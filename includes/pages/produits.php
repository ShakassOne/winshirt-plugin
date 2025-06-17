<?php
defined('ABSPATH') || exit;

function winshirt_page_products() {
    echo '<div class="wrap"><h1>Produits personnalisables</h1>';
    include WINSHIRT_PATH . 'templates/admin/partials/produits-list.php';
    echo '</div>';
}

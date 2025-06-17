<?php
defined('ABSPATH') || exit;

function winshirt_page_lotteries() {
    echo '<div class="wrap"><h1>Gestion des loteries</h1>';
    include WINSHIRT_PATH . 'templates/admin/partials/loteries-list.php';
    echo '</div>';
}

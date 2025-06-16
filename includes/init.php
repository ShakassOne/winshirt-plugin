<?php
// Hook de démarrage
add_action('admin_menu', function () {
    add_menu_page('WinShirt', 'WinShirt', 'manage_options', 'winshirt', 'winshirt_admin_page', 'dashicons-tshirt', 56);
});

function winshirt_admin_page() {
    echo '<div class="wrap"><h1>WinShirt - Dashboard</h1><p>Bienvenue sur le back-office WinShirt.</p></div>';
}

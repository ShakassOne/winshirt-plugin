<?php


// Enqueue assets on product pages
add_action('wp_enqueue_scripts', function () {
    if (is_product()) {
        wp_enqueue_style('winshirt-modal', WINSHIRT_URL . 'assets/css/winshirt-modal.css', [], '1.0');
        wp_enqueue_script('winshirt-modal', WINSHIRT_URL . 'assets/js/winshirt-modal.js', ['jquery', 'jquery-ui-draggable', 'jquery-ui-resizable'], '1.0', true);
    }
});

// Register custom post type for lotteries
add_action('init', function () {
    register_post_type('winshirt_lottery', [
        'label'       => 'Loteries',
        'public'      => false,
        'show_ui'     => false,
        'supports'    => ['title', 'editor', 'thumbnail'],
    ]);
});

// Register custom post type for visuals
add_action('init', function () {
    register_post_type('winshirt_visual', [
        'label'       => 'Visuels',
        'public'      => false,
        'show_ui'     => false,
        'supports'    => ['title', 'thumbnail'],
    ]);
});

// Register FTP options
add_action('admin_init', function () {
    register_setting('winshirt_options', 'winshirt_ftp_host');
    register_setting('winshirt_options', 'winshirt_ftp_user');
    register_setting('winshirt_options', 'winshirt_ftp_pass');
});

// Enqueue assets on WinShirt admin pages
add_action('admin_enqueue_scripts', function ($hook) {
    if (strpos($hook, 'winshirt') !== false) {
        wp_enqueue_style('winshirt-admin', WINSHIRT_URL . 'assets/css/winshirt-admin.css', [], '1.0');
        wp_enqueue_script('winshirt-admin', WINSHIRT_URL . 'assets/js/winshirt-admin.js', ['wp-element'], '1.0', true);

        if (strpos($hook, 'winshirt-mockups') !== false) {
            wp_enqueue_style('winshirt-mockups', WINSHIRT_URL . 'assets/css/winshirt-mockups.css', [], '1.0');
            wp_enqueue_script('winshirt-mockups', WINSHIRT_URL . 'assets/js/winshirt-mockups.js', ['jquery', 'jquery-ui-draggable', 'jquery-ui-resizable'], '1.0', true);
        }
    }
});

// Add customize button and modal on product page
function winshirt_render_customize_button() {
    global $product;
    if ( ! $product instanceof WC_Product ) {
        return;
    }

    $pid            = $product->get_id();
    $enabled        = get_post_meta( $pid, '_winshirt_enabled', true ) === 'yes';
    $show_button    = get_post_meta( $pid, '_winshirt_show_button', true ) === 'yes';
    $front_id       = absint( get_post_meta( $pid, '_winshirt_default_mockup_front', true ) );
    $back_id        = absint( get_post_meta( $pid, '_winshirt_default_mockup_back', true ) );
    $mockups_raw    = get_post_meta( $pid, '_winshirt_mockups', true );
    $has_mockup     = $front_id || $back_id || ! empty( $mockups_raw );

    // Display button only if the product is marked as customizable
    if ( ! $enabled || ! $has_mockup ) {
        return;
    }

    $front_url  = $front_id ? get_the_post_thumbnail_url( $front_id, 'full' ) : '';
    $back_url   = $back_id ? get_the_post_thumbnail_url( $back_id, 'full' ) : '';

    // Retrieve available colors from the default mockup
    $colors_meta = $front_id ? get_post_meta( $front_id, '_winshirt_colors', true ) : [];
    $colors_meta = is_array( $colors_meta ) ? $colors_meta : [];
    $colors      = [];
    foreach ( $colors_meta as $c ) {
        $colors[] = [
            'name'  => $c['name'] ?? '',
            'code'  => $c['code'] ?? '',
            'front' => ! empty( $c['front'] ) ? wp_get_attachment_image_url( $c['front'], 'full' ) : '',
            'back'  => ! empty( $c['back'] ) ? wp_get_attachment_image_url( $c['back'], 'full' ) : '',
        ];
    }

    // Retrieve print zones for front/back sides
    $zones      = [];
    foreach ( [ $front_id, $back_id ] as $mid ) {
        if ( ! $mid ) {
            continue;
        }
        $zmeta = get_post_meta( $mid, '_winshirt_print_zones', true );
        if ( $zmeta && is_array( $zmeta ) ) {
            foreach ( $zmeta as $z ) {
                $zones[] = [
                    'side'   => $z['side'] ?? 'front',
                    'top'    => floatval( $z['top'] ?? 0 ),
                    'left'   => floatval( $z['left'] ?? 0 ),
                    'width'  => floatval( $z['width'] ?? 0 ),
                    'height' => floatval( $z['height'] ?? 0 ),
                ];
            }
        }
    }

    echo '<button id="winshirt-open-modal" class="button">' . esc_html__( 'Personnaliser ce produit', 'winshirt' ) . '</button>';
    $default_front = $front_url;
    $default_back  = $back_url;
    $ws_colors     = wp_json_encode( $colors );
    $ws_zones      = wp_json_encode( $zones );
    include WINSHIRT_PATH . 'templates/personalizer-modal.php';
}
add_action( 'woocommerce_single_product_summary', 'winshirt_render_customize_button', 35 );

// Register custom post type for mockups
add_action('init', function () {
    register_post_type('winshirt_mockup', [
        'label'       => 'Mockups',
        'public'      => false,
        'show_ui'     => false,
        'supports'    => ['title', 'thumbnail'],
    ]);
});

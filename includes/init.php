<?php


// Enqueue assets on product pages
add_action('wp_enqueue_scripts', function () {
    global $post;
    $needs_assets = is_product();
    if ( ! $needs_assets && isset( $post->post_content ) ) {
        $needs_assets = has_shortcode( $post->post_content, 'product_page' );
    }
    if ( $needs_assets ) {
        wp_enqueue_style('winshirt-modal', WINSHIRT_URL . 'assets/css/winshirt-modal.css', [], '1.0');
        wp_enqueue_style('winshirt-lottery', WINSHIRT_URL . 'assets/css/winshirt-lottery.css', [], '1.0');
        wp_enqueue_style('winshirt-theme', WINSHIRT_URL . 'assets/css/winshirt-theme.css', [], '1.0');

        wp_enqueue_script('winshirt-touch', WINSHIRT_URL . 'assets/js/jquery.ui.touch-punch.min.js', ['jquery', 'jquery-ui-mouse'], '0.2.3', true);
        wp_enqueue_script('html2canvas', 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js', [], '1.4.1', true);
        wp_enqueue_script('winshirt-modal', WINSHIRT_URL . 'assets/js/winshirt-modal.js', ['jquery', 'jquery-ui-draggable', 'jquery-ui-resizable', 'winshirt-touch', 'html2canvas'], '1.0', true);
        wp_localize_script('winshirt-modal', 'winshirtAjax', [
            'url'  => admin_url('admin-ajax.php'),
            'rest' => esc_url_raw(rest_url('winshirt/v1/')),
            'nonce'=> wp_create_nonce('wp_rest'),
        ]);

        wp_enqueue_style('winshirt-lottery-selected', WINSHIRT_URL . 'assets/css/winshirt-lottery-selected.css', [], '1.0');
        wp_enqueue_script('winshirt-lottery-selected', WINSHIRT_URL . 'assets/js/winshirt-lottery-selected.js', ['jquery'], '1.0', true);
        wp_enqueue_script('winshirt-lottery-enforce', WINSHIRT_URL . 'assets/js/winshirt-lottery-enforce.js', ['jquery'], '1.0', true);
    }
});

// Enqueue badge style on shop listings
add_action('wp_enqueue_scripts', function () {
    if (is_shop() || is_product_category() || is_product_tag()) {
        wp_enqueue_style('winshirt-badge', WINSHIRT_URL . 'assets/css/winshirt-badge.css', [], '1.0');
    }
});


// Enqueue assets when the lottery shortcode is present
add_action('wp_enqueue_scripts', function(){
    global $post;
    if ( isset( $post->post_content ) && ( has_shortcode( $post->post_content, 'loterie_box' ) || has_shortcode( $post->post_content, 'winshirt_lotteries' ) ) ) {
        wp_enqueue_style( 'winshirt-lottery', WINSHIRT_URL . 'assets/css/winshirt-lottery.css', [], '1.0' );
        wp_enqueue_script( 'vanilla-tilt', WINSHIRT_URL . 'assets/js/vanilla-tilt.min.js', [], '1.0', true );
        wp_enqueue_script( 'winshirt-lottery-card', WINSHIRT_URL . 'assets/js/winshirt-lottery-card.js', [ 'vanilla-tilt' ], '1.0', true );
        wp_enqueue_script( 'winshirt-lottery-cards', WINSHIRT_URL . 'assets/js/winshirt-lottery-cards.js', [ 'jquery', 'winshirt-lottery-card' ], '1.0', true );
    }
});

/**
 * Display a lottery card anywhere with [loterie_box id="123" vedette="true"].
 */
function winshirt_lottery_box_shortcode( $atts ) {
    $atts = shortcode_atts([
        'id'      => 0,
        'vedette' => 'false',
    ], $atts, 'loterie_box');

    $id = absint( $atts['id'] );
    if ( ! $id ) {
        return '';
    }

    $lottery = get_post( $id );
    if ( ! $lottery || $lottery->post_type !== 'winshirt_lottery' ) {
        return '';
    }

    $active      = get_post_meta( $id, '_winshirt_lottery_active', true ) === 'yes';
    $value       = get_post_meta( $id, '_winshirt_lottery_value', true );
    $max         = absint( get_post_meta( $id, 'max_participants', true ) );
    $count       = absint( get_post_meta( $id, 'participants_count', true ) );
    $draw_date   = get_post_meta( $id, '_winshirt_lottery_end', true );
    $img_id      = get_post_meta( $id, '_winshirt_lottery_animation', true );
    $img_url     = $img_id ? wp_get_attachment_image_url( $img_id, 'large' ) : '';
    $url         = get_post_meta( $id, '_winshirt_lottery_url', true );
    $percent     = $max > 0 ? min( 100, ( $count / $max ) * 100 ) : 0;

    ob_start();
    ?>
    <div class="ws-lottery-card" data-end="<?php echo esc_attr( $draw_date ); ?>">
        <?php if ( $active ) : ?>
            <span class="lottery-badge">Active</span>
        <?php endif; ?>
        <?php if ( $atts['vedette'] === 'true' ) : ?>
            <span class="lottery-badge badge-featured">En vedette</span>
        <?php endif; ?>
        <?php if ( $img_url ) : ?>
            <img src="<?php echo esc_url( $img_url ); ?>" alt="" />
        <?php endif; ?>
        <h3 class="lottery-title"><?php echo esc_html( $lottery->post_title ); ?></h3>
        <?php if ( $value ) : ?>
            <p class="lottery-value">Valeur : <?php echo esc_html( $value ); ?>€</p>
        <?php endif; ?>
        <div class="lottery-timer"></div>
        <p class="lottery-count"><?php echo esc_html( $count . ' participants - Objectif : ' . $max ); ?></p>
        <div class="lottery-progress"><div class="lottery-progress-bar" data-progress="<?php echo esc_attr( $percent ); ?>" style="width:<?php echo esc_attr( $percent ); ?>%"></div></div>
        <?php if ( $draw_date ) : ?>
            <p class="lottery-draw"><?php echo esc_html( $draw_date ); ?></p>
        <?php endif; ?>
        <a href="<?php echo $url ? esc_url( $url ) : '#'; ?>" class="lottery-button">Participer</a>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'loterie_box', 'winshirt_lottery_box_shortcode' );

/**
 * Display only the lottery thumbnail image.
 * Usage: [loterie_thumb id="123" size="thumbnail"].
 */
function winshirt_lottery_thumb_shortcode( $atts ) {
    $atts = shortcode_atts([
        'id'   => 0,
        'size' => 'thumbnail',
    ], $atts, 'loterie_thumb');

    $id = absint( $atts['id'] );
    if ( ! $id ) {
        return '';
    }

    $img_id = get_post_meta( $id, '_winshirt_lottery_animation', true );
    if ( ! $img_id ) {
        return '';
    }

    $size = sanitize_key( $atts['size'] );
    if ( ! $size ) {
        $size = 'thumbnail';
    }

    return wp_get_attachment_image( $img_id, $size );
}
add_shortcode( 'loterie_thumb', 'winshirt_lottery_thumb_shortcode' );

/**
 * Display a list of lottery cards with [winshirt_lotteries].
 */
function winshirt_lotteries_shortcode() {
    $lotteries = get_posts([
        'post_type'   => 'winshirt_lottery',
        'numberposts' => -1,
        'orderby'     => 'date',
    ]);

    if ( ! $lotteries ) {
        return '';
    }

    ob_start();
    echo '<div class="ws-lottery-list">';
    foreach ( $lotteries as $lottery ) {
        $id        = $lottery->ID;
        $active    = get_post_meta( $id, '_winshirt_lottery_active', true ) === 'yes';
        $value     = get_post_meta( $id, '_winshirt_lottery_value', true );
        $max       = absint( get_post_meta( $id, 'max_participants', true ) );
        $count     = absint( get_post_meta( $id, 'participants_count', true ) );
        $draw_date = get_post_meta( $id, '_winshirt_lottery_end', true );
        $img_id    = get_post_meta( $id, '_winshirt_lottery_animation', true );
        $img_url   = $img_id ? wp_get_attachment_image_url( $img_id, 'large' ) : '';
        $url       = get_post_meta( $id, '_winshirt_lottery_url', true );
        $featured  = get_post_meta( $id, '_winshirt_lottery_featured', true ) === 'yes';
        $percent   = $max > 0 ? min( 100, ( $count / $max ) * 100 ) : 0;

        ?>
        <div class="ws-lottery-card" data-end="<?php echo esc_attr( $draw_date ); ?>">
            <span class="lottery-badge"><?php echo $active ? 'Active' : 'Terminé'; ?></span>
            <?php if ( $featured ) : ?>
                <span class="lottery-badge badge-featured">En vedette</span>
            <?php endif; ?>
            <?php if ( $img_url ) : ?>
            <div class="lottery-image" data-tilt>
                <img src="<?php echo esc_url( $img_url ); ?>" alt="" />
            </div>
            <?php endif; ?>
            <h3 class="lottery-title"><?php echo esc_html( $lottery->post_title ); ?></h3>
            <?php if ( $value ) : ?>
                <p class="lottery-value">Valeur : <?php echo esc_html( $value ); ?> €</p>
            <?php endif; ?>
            <div class="lottery-timer"></div>
            <div class="lottery-progress"><div class="lottery-progress-bar" data-progress="<?php echo esc_attr( $percent ); ?>" style="width:<?php echo esc_attr( $percent ); ?>%"></div></div>
            <p class="lottery-count"><?php echo esc_html( $count . ' participants / Objectif : ' . $max ); ?></p>
            <?php if ( $draw_date ) : ?>
                <p class="lottery-draw">Tirage le <?php echo esc_html( date_i18n( 'd/m/Y', strtotime( $draw_date ) ) ); ?></p>
            <?php endif; ?>
            <a href="<?php echo $url ? esc_url( $url ) : '#'; ?>" class="lottery-button">Participer</a>
        </div>
        <?php
    }
    echo '</div>';
    return ob_get_clean();
}
add_shortcode( 'winshirt_lotteries', 'winshirt_lotteries_shortcode' );

/**
 * Shortcode to display the customization button and modal.
 * Usage: [winshirt_customizer]
 */
function winshirt_customizer_shortcode() {
    ob_start();
    winshirt_render_customize_button();
    return ob_get_clean();
}
add_shortcode( 'winshirt_customizer', 'winshirt_customizer_shortcode' );

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
    // Register AI settings
    register_setting('winshirt_options', 'winshirt_ia_api_key');
    register_setting('winshirt_options', 'winshirt_ia_model');
    register_setting('winshirt_options', 'winshirt_ia_output_format');
    register_setting('winshirt_options', 'winshirt_ia_generation_limit');
    register_setting('winshirt_options', 'winshirt_ia_image_folder');
    // Register Supabase settings
    register_setting('winshirt_options', 'winshirt_supabase_url');
    register_setting('winshirt_options', 'winshirt_supabase_key');
});

// Enqueue assets on WinShirt admin pages
add_action('admin_enqueue_scripts', function ($hook) {
    if (strpos($hook, 'winshirt') !== false) {
        wp_enqueue_style('winshirt-admin', WINSHIRT_URL . 'assets/css/winshirt-admin.css', [], '1.0');
        wp_enqueue_script('winshirt-admin', WINSHIRT_URL . 'assets/js/winshirt-admin.js', ['wp-element'], '1.0', true);

        if (strpos($hook, 'winshirt-mockups') !== false) {
            wp_enqueue_media();
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
    $show_meta      = get_post_meta( $pid, '_winshirt_show_button', true );
    $show_button    = $show_meta === '' ? true : $show_meta === 'yes';
    $front_id       = absint( get_post_meta( $pid, '_winshirt_default_mockup_front', true ) );
    $back_id        = absint( get_post_meta( $pid, '_winshirt_default_mockup_back', true ) );
    $mockups_raw    = get_post_meta( $pid, '_winshirt_mockups', true );
    $has_mockup     = $front_id || $back_id || ! empty( $mockups_raw );

    // Display button only if the product is marked as customizable
    // and the button should be shown
    if ( ! $enabled || ! $has_mockup || ! $show_button ) {
        return;
    }

    $front_img_id = $front_id ? get_post_meta( $front_id, '_winshirt_front_image', true ) : 0;
    $back_img_id  = $back_id ? get_post_meta( $back_id, '_winshirt_back_image', true ) : 0;

    $front_url  = $front_img_id ? wp_get_attachment_image_url( $front_img_id, 'full' ) : '';
    $back_url   = $back_img_id ? wp_get_attachment_image_url( $back_img_id, 'full' ) : '';

    // Retrieve available colors from the default mockup
    $colors_meta = $front_id ? get_post_meta( $front_id, '_winshirt_colors', true ) : [];
    $colors_meta = is_array( $colors_meta ) ? $colors_meta : [];
    $colors      = [];
    foreach ( $colors_meta as $c ) {
        $colors[] = [
            'name' => $c['name'] ?? '',
            'code' => $c['code'] ?? '',
        ];
    }

    // Retrieve print zones for front/back sides
    $zones      = [];
    foreach ( array_unique( array_filter( [ $front_id, $back_id ] ) ) as $mid ) {
        if ( ! $mid ) {
            continue;
        }
        $zmeta = get_post_meta( $mid, '_winshirt_print_zones', true );
        if ( $zmeta && is_array( $zmeta ) ) {
            foreach ( $zmeta as $z ) {
                $zones[] = [
                    'side'   => $z['side'] ?? 'front',
                    'name'   => $z['name'] ?? '',
                    'format' => $z['format'] ?? 'A4',
                    'price'  => floatval( $z['price'] ?? 0 ),
                    'top'    => floatval( $z['top'] ?? 0 ),
                    'left'   => floatval( $z['left'] ?? 0 ),
                    'width'  => floatval( $z['width'] ?? 0 ),
                    'height' => floatval( $z['height'] ?? 0 ),
                ];
            }
        }
    }

    // Bouton d\xE9clenchant la personnalisation sur la fiche produit
    // Utilise les mêmes classes que le bouton "Ajouter au panier" pour hériter du style du thème
    echo '<div class="winshirt-personnaliser-btn"><button id="btn-personnaliser" class="single_add_to_cart_button button alt glow-on-hover">' . esc_html__( 'Personnaliser ce produit', 'winshirt' ) . '</button></div>';
    $default_front = $front_url;
    $default_back  = $back_url;
    $ws_colors     = wp_json_encode( $colors );
    $ws_zones      = wp_json_encode( $zones );

    // Retrieve validated visuals for the gallery
    $gallery_posts = get_posts([
        'post_type'   => 'winshirt_visual',
        'numberposts' => -1,
        'orderby'     => 'date',
        'order'       => 'DESC',
        'meta_query'  => [
            [
                'key'   => '_winshirt_visual_validated',
                'value' => 'yes',
            ],
        ],
    ]);

    $gallery = [];
    foreach ( $gallery_posts as $g ) {
        $url = get_the_post_thumbnail_url( $g->ID, 'full' );
        if ( $url ) {
            $gallery[] = [
                'id'       => $g->ID,
                'title'    => $g->post_title,
                'url'      => $url,
                'category' => get_post_meta( $g->ID, '_winshirt_category', true ),
            ];
        }
    }
    $ws_gallery = wp_json_encode( $gallery );

// Retrieve validated AI generated visuals
$ai_posts = get_posts([
    'post_type'   => 'winshirt_visual',
    'numberposts' => -1,
    'orderby'     => 'date',
    'order'       => 'DESC',
    'meta_query'  => [
        [ 'key' => '_winshirt_visual_validated', 'value' => 'yes' ],
        [ 'key' => '_winshirt_category', 'value' => 'IA' ],
    ],
]);

    $ai_gallery = [];
    foreach ( $ai_posts as $a ) {
        $url = get_the_post_thumbnail_url( $a->ID, 'full' );
        if ( $url ) {
            $ai_gallery[] = $url;
        }
    }
    $ws_ai_gallery = wp_json_encode( $ai_gallery );

    include WINSHIRT_PATH . 'templates/personalizer-modal.php';
}
// Affiche le bouton juste sous le prix, avant les billets de loterie
add_action( 'woocommerce_single_product_summary', 'winshirt_render_customize_button', 15 );

function winshirt_render_color_picker() {
    global $product;
    if ( ! $product instanceof WC_Product ) {
        return;
    }
    $pid       = $product->get_id();
    $enabled   = get_post_meta( $pid, '_winshirt_enabled', true ) === 'yes';
    if ( ! $enabled ) {
        return;
    }
    $front_id  = absint( get_post_meta( $pid, '_winshirt_default_mockup_front', true ) );
    if ( ! $front_id ) {
        return;
    }
    $img_url = get_the_post_thumbnail_url( $front_id, 'full' );
    if ( ! $img_url ) {
        return;
    }
    ?>
    <script type="text/javascript">
    jQuery(function($){
        var $imgWrap = $('.woocommerce-product-gallery__image').eq(0);
        if(!$imgWrap.length) return;
        var imgSrc = $imgWrap.find('img').attr('src') || <?php echo wp_json_encode( $img_url ); ?>;
        if(!$imgWrap.find('.ws-product-color-overlay').length){
            $imgWrap.css('position','relative');
            $('<div class="ws-product-color-overlay winshirt-theme-inherit"></div>').css({
                '-webkit-mask-image':'url('+imgSrc+')',
                'mask-image':'url('+imgSrc+')',
                '-webkit-mask-size':'contain',
                'mask-size':'contain',
                '-webkit-mask-repeat':'no-repeat',
                'mask-repeat':'no-repeat',
                '-webkit-mask-position':'center',
                'mask-position':'center'
            }).appendTo($imgWrap);
        }
        var stored = localStorage.getItem('winshirt_custom');
        if(stored){
            try{ stored = JSON.parse(stored); }catch(e){ stored = null; }
            if(stored && stored.color){
                $imgWrap.find('.ws-product-color-overlay').css('background-color', stored.color);
            }
        }
    });
    </script>
    <?php
}
add_action( 'woocommerce_single_product_summary', 'winshirt_render_color_picker', 34 );

function winshirt_render_lottery_selector() {
    static $rendered = false;
    if ( $rendered ) {
        return;
    }
    $rendered = true;
    global $product;
    if ( ! $product instanceof WC_Product ) {
        return;
    }

    $pid      = $product->get_id();
    $linked   = absint( get_post_meta( $pid, 'linked_lottery', true ) );
    if ( $linked ) {
        winshirt_render_lottery_info();
        return;
    }
    $tickets  = absint( get_post_meta( $pid, 'loterie_tickets', true ) );
    if ( $tickets < 1 ) {
        return;
    }

    $lotteries = get_posts([
        'post_type'   => 'winshirt_lottery',
        'numberposts' => -1,
        'orderby'     => 'title',
    ]);

    if ( ! $lotteries ) {
        return;
    }

    echo '<div class="winshirt-lottery-selects winshirt-theme-inherit">';
    for ( $i = 1; $i <= $tickets; $i++ ) {
        echo '<div class="form-row form-row-wide winshirt-lottery-select winshirt-theme-inherit">';
        echo '<label class="winshirt-theme-inherit winshirt-lottery-label" for="winshirt-lottery-select-' . $i . '"><strong>' . esc_html__( 'Ticket n°', 'winshirt' ) . $i . ':</strong></label><br />';
        echo '<select id="winshirt-lottery-select-' . $i . '" class="winshirt-lottery-select select winshirt-theme-inherit" name="winshirt_lotteries[]">';
        echo '<option value="">' . esc_html__( '-- Sélectionner --', 'winshirt' ) . '</option>';
        foreach ( $lotteries as $lottery ) {
            $max       = absint( get_post_meta( $lottery->ID, 'max_participants', true ) );
            $count     = absint( get_post_meta( $lottery->ID, 'participants_count', true ) );
            $img_id    = get_post_meta( $lottery->ID, '_winshirt_lottery_animation', true );
            $img_url   = $img_id ? wp_get_attachment_image_url( $img_id, 'large' ) : '';
            $draw_date = get_post_meta( $lottery->ID, '_winshirt_lottery_end', true );
            $active    = get_post_meta( $lottery->ID, '_winshirt_lottery_active', true ) === 'yes';
            $value     = get_post_meta( $lottery->ID, '_winshirt_lottery_value', true );
            $featured  = get_post_meta( $lottery->ID, '_winshirt_lottery_featured', true ) === 'yes';
            $info      = wp_json_encode([
                'goal'         => $max,
                'participants' => $count,
                'image'        => $img_url,
                'name'         => $lottery->post_title,
                'drawDate'     => $draw_date,
                'active'       => $active,
                'value'        => $value,
                'featured'     => $featured,
            ]);
            echo '<option value="' . esc_attr( $lottery->ID ) . '" data-info="' . esc_attr( $info ) . '">' . esc_html( $lottery->post_title ) . '</option>';
        }
        echo '</select>';
        echo '</div>';
    }
    echo '</div>';
    echo '<div class="loteries-container"></div>';
}
add_action( 'woocommerce_single_product_summary', 'winshirt_render_lottery_selector', 28 );

function winshirt_render_lottery_info() {
    global $product;
    if ( ! $product instanceof WC_Product ) {
        return;
    }

    $pid     = $product->get_id();
    $lottery = absint( get_post_meta( $pid, 'linked_lottery', true ) );
    if ( ! $lottery ) {
        return;
    }

    $max      = absint( get_post_meta( $lottery, 'max_participants', true ) );
    $count    = absint( get_post_meta( $lottery, 'participants_count', true ) );
    $img_id   = get_post_meta( $lottery, '_winshirt_lottery_animation', true );
    $img_url  = $img_id ? wp_get_attachment_image_url( $img_id, 'large' ) : '';
    $value    = get_post_meta( $lottery, '_winshirt_lottery_value', true );
    $featured = get_post_meta( $lottery, '_winshirt_lottery_featured', true ) === 'yes';
    $active   = get_post_meta( $lottery, '_winshirt_lottery_active', true ) === 'yes';
    $percent  = $max > 0 ? min( 100, round( ( $count / $max ) * 100 ) ) : 0;

    echo '<p class="lottery-ticket-info">🎟️ Ce T-shirt vous donne 1 ticket pour participer à la loterie <strong>' . esc_html( get_the_title( $lottery ) ) . '</strong>.<br>Tirage déclenché dès que le nombre de participants est atteint.<br><em>Jeu encadré par huissier – <a href="/reglement-du-jeu" target="_blank">Voir le règlement</a></em></p>';

    echo '<div class="loteries-container">';
    echo '<div class="loterie-card winshirt-theme-inherit">';
    if ( $featured ) {
        echo '<span class="loterie-badge">BEST</span>';
    } elseif ( $active ) {
        echo '<span class="loterie-badge">NOUVEAU</span>';
    }
    if ( $img_url ) {
        echo '<img class="loterie-img winshirt-theme-inherit" src="' . esc_url( $img_url ) . '" alt="" />';
    }
    echo '<div class="loterie-info winshirt-theme-inherit">';
    echo '<span class="loterie-title">' . esc_html( get_the_title( $lottery ) ) . '</span>';
    echo '<div class="loterie-meta">';
    if ( $value ) {
        echo '<span class="loterie-price">' . esc_html( $value ) . '€</span>';
    }
    echo '<span class="loterie-participants">' . esc_html( $count . ' / ' . $max . ' participants' ) . '</span>';
    echo '</div>';
    echo '<div class="loterie-bar-bg">';
    echo '<div class="loterie-bar" style="width:' . esc_attr( $percent ) . '%"></div>';
    echo '<div class="loterie-tooltip">' . esc_html( $percent ) . '% rempli (' . esc_html( $count ) . ' sur ' . esc_html( $max ) . ')</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}

function winshirt_lottery_purchasable( $purchasable, $product ) {
    $lottery = absint( get_post_meta( $product->get_id(), 'linked_lottery', true ) );
    if ( $lottery ) {
        $max   = absint( get_post_meta( $lottery, 'max_participants', true ) );
        $count = absint( get_post_meta( $lottery, 'participants_count', true ) );
        if ( $max > 0 && $count >= $max ) {
            return false;
        }
    }
    return $purchasable;
}
add_filter( 'woocommerce_is_purchasable', 'winshirt_lottery_purchasable', 10, 2 );

function winshirt_add_lottery_to_cart_item( $cart_item_data, $product_id ) {
    if ( isset( $_POST['winshirt_lotteries'] ) && is_array( $_POST['winshirt_lotteries'] ) ) {
        $lotteries = array_map( 'absint', (array) $_POST['winshirt_lotteries'] );
        $lotteries = array_filter( $lotteries );
        if ( $lotteries ) {
            $cart_item_data['winshirt_lotteries'] = $lotteries;
        }
    }
    return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'winshirt_add_lottery_to_cart_item', 10, 2 );

function winshirt_display_cart_item_data( $item_data, $cart_item ) {
    if ( ! empty( $cart_item['winshirt_lotteries'] ) ) {
        $names = [];
        foreach ( $cart_item['winshirt_lotteries'] as $lid ) {
            $names[] = get_the_title( $lid );
        }
        if ( $names ) {
            $item_data[] = [
                'key'   => __( 'Loteries', 'winshirt' ),
                'value' => implode( ', ', $names ),
            ];
        }
    }
    return $item_data;
}
add_filter( 'woocommerce_get_item_data', 'winshirt_display_cart_item_data', 10, 2 );

function winshirt_cart_item_preview( $name, $cart_item, $cart_item_key ) {
    $front = $cart_item['winshirt_front_image'] ?? '';
    $back  = $cart_item['winshirt_back_image'] ?? '';
    if ( $front || $back ) {
        $html = '';
        if ( $front ) {
            $html .= '<img src="' . esc_url( $front ) . '" alt="front" />';
        }
        if ( $back ) {
            $html .= '<img src="' . esc_url( $back ) . '" alt="back" />';
        }
        $name .= '<div class="winshirt-cart-custom">' . $html . '</div>';
    }
    return $name;
}
add_filter( 'woocommerce_cart_item_name', 'winshirt_cart_item_preview', 10, 3 );

function winshirt_save_lottery_order_item_meta( $item, $cart_item_key, $values, $order ) {
    if ( ! empty( $values['winshirt_lotteries'] ) ) {
        $item->update_meta_data( '_winshirt_lotteries', $values['winshirt_lotteries'] );
    }
}
add_action( 'woocommerce_checkout_create_order_line_item', 'winshirt_save_lottery_order_item_meta', 10, 4 );

function winshirt_register_lottery_participant( $order_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        return;
    }

    if ( $order->get_meta( '_winshirt_lottery_participants_registered' ) === 'yes' ) {
        return;
    }

    foreach ( $order->get_items() as $item ) {
        $lotteries = $item->get_meta( '_winshirt_lotteries', true );
        if ( $lotteries ) {
            $counts = [];
            foreach ( (array) $lotteries as $lottery_id ) {
                if ( ! isset( $counts[ $lottery_id ] ) ) {
                    $counts[ $lottery_id ] = 0;
                }
                $counts[ $lottery_id ]++;
            }

            foreach ( $counts as $lottery_id => $times ) {
                $current   = absint( get_post_meta( $lottery_id, 'participants_count', true ) );
                $increment = $item->get_quantity() * $times;
                update_post_meta( $lottery_id, 'participants_count', $current + $increment );
            }
            continue;
        }

        $pid     = $item->get_product_id();
        $lottery = absint( get_post_meta( $pid, 'linked_lottery', true ) );
        if ( ! $lottery ) {
            continue;
        }
        $count = absint( get_post_meta( $lottery, 'participants_count', true ) );
        $tickets = absint( get_post_meta( $pid, 'loterie_tickets', true ) );
        $increment = $item->get_quantity();
        if ( $tickets > 0 ) {
            $increment = $item->get_quantity() * $tickets; // participant count per ticket and quantity
        }
        update_post_meta( $lottery, 'participants_count', $count + $increment );
    }

    $order->update_meta_data( '_winshirt_lottery_participants_registered', 'yes' );
    $order->save();
}
// Update lottery participation when a payment is confirmed
add_action( 'woocommerce_payment_complete', 'winshirt_register_lottery_participant' );
add_action( 'woocommerce_order_status_processing', 'winshirt_register_lottery_participant' );
add_action( 'woocommerce_order_status_completed', 'winshirt_register_lottery_participant' );

// Increment participants for lotteries linked directly via product IDs
add_action( 'woocommerce_order_status_completed', 'winshirt_increment_lottery_participants' );

/**
 * Increase lottery participant count based on products defined
 * in the "Produits associés" field of each lottery.
 */
function winshirt_increment_lottery_participants( $order_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        return;
    }

    $loteries = get_posts([
        'post_type'      => 'winshirt_lottery',
        'posts_per_page' => -1,
        'meta_query'     => [
            [ 'key' => '_winshirt_lottery_product', 'compare' => 'EXISTS' ],
        ],
    ]);

    foreach ( $order->get_items() as $item ) {
        $pid = $item->get_product_id();
        $qty = $item->get_quantity();

        foreach ( $loteries as $lottery ) {
            $linked = get_post_meta( $lottery->ID, '_winshirt_lottery_product', true );
            if ( ! $linked ) {
                continue;
            }

            $ids = array_map( 'absint', explode( ',', $linked ) );
            if ( in_array( $pid, $ids, true ) ) {
                $count = absint( get_post_meta( $lottery->ID, 'participants_count', true ) );
                update_post_meta( $lottery->ID, 'participants_count', $count + $qty );
            }
        }
    }
}

// Display related lottery info under each product line in emails and thankyou page
add_filter( 'woocommerce_order_item_meta_end', 'winshirt_display_lottery_in_email', 10, 4 );

function winshirt_order_item_preview( $item_id, $item, $order, $plain_text ) {
    $front = $item->get_meta( 'winshirt_front_preview' );
    $back  = $item->get_meta( 'winshirt_back_preview' );
    if ( ! $front && ! $back ) {
        return;
    }
    if ( $plain_text ) {
        if ( $front ) echo "Recto : $front\n";
        if ( $back )  echo "Verso : $back\n";
    } else {
        $html = '';
        if ( $front ) {
            $html .= '<img src="' . esc_url( $front ) . '" alt="front" />';
        }
        if ( $back ) {
            $html .= '<img src="' . esc_url( $back ) . '" alt="back" />';
        }
        echo '<p class="winshirt-order-preview">' . $html . '</p>';
    }
}
add_action( 'woocommerce_order_item_meta_end', 'winshirt_order_item_preview', 5, 4 );

/**
 * Append a message about the associated lottery below the order item.
 */
function winshirt_display_lottery_in_email( $item_id, $item, $order, $plain_text ) {
    $product_id = $item->get_product_id();

    $loteries = get_posts([
        'post_type'      => 'winshirt_lottery',
        'posts_per_page' => -1,
        'meta_query'     => [
            [ 'key' => '_winshirt_lottery_product', 'compare' => 'EXISTS' ],
        ],
    ]);

    foreach ( $loteries as $lottery ) {
        $ids = array_map( 'absint', explode( ',', get_post_meta( $lottery->ID, '_winshirt_lottery_product', true ) ) );
        if ( in_array( $product_id, $ids, true ) ) {
            $title = get_the_title( $lottery->ID );
            $date  = get_post_meta( $lottery->ID, '_winshirt_lottery_end', true );
            $msg   = "🎁 Ce produit vous inscrit automatiquement à la loterie : *{$title}* – Tirage le {$date}";

            echo $plain_text ? $msg . "\n" : "<p>{$msg}</p>";
        }
    }
}

add_action( 'woocommerce_email_order_meta', 'winshirt_email_custom_images', 15, 4 );
/**
 * Show generated mockup images in order emails.
 */
function winshirt_email_custom_images( $order, $sent_to_admin, $plain_text, $email ) {
    foreach ( $order->get_items() as $item ) {
        $front = $item->get_meta( 'winshirt_front_hd' );
        $back  = $item->get_meta( 'winshirt_back_hd' );
        if ( ! $front && ! $back ) {
            continue;
        }
        if ( $plain_text ) {
            if ( $front ) echo "Recto : $front\n";
            if ( $back )  echo "Verso : $back\n";
        } else {
            echo '<h3>Prévisualisation de votre design</h3>';
            echo '<p>Voici les visuels générés automatiquement à partir de votre personnalisation :</p>';
            if ( $front ) {
                echo '<img src="' . esc_url( $front ) . '" alt="Mockup Recto" style="max-width:100%; height:auto; border:1px solid #ccc;" />';
            }
            if ( $back ) {
                echo '<img src="' . esc_url( $back ) . '" alt="Mockup Verso" style="max-width:100%; height:auto; border:1px solid #ccc;" />';
            }
        }
        break;
    }
}

// Register custom post type for mockups
add_action('init', function () {
    register_post_type('winshirt_mockup', [
        'label'       => 'Mockups',
        'public'      => false,
        'show_ui'     => false,
        'supports'    => ['title', 'thumbnail'],
    ]);
});

// AJAX callback to test AI API key validity
function winshirt_test_ia_key() {
    $key = sanitize_text_field($_POST['key'] ?? '');
    if (!$key) {
        wp_send_json_error(['message' => 'API key missing']);
    }

    $response = wp_remote_get('https://api.openai.com/v1/models', [
        'headers' => [
            'Authorization' => 'Bearer ' . $key,
        ],
        'timeout' => 10,
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => $response->get_error_message()]);
    }

    $code = wp_remote_retrieve_response_code($response);
    if ($code >= 200 && $code < 300) {
        wp_send_json_success(['message' => 'Valid key']);
    }

    wp_send_json_error(['message' => 'Invalid key']);
}
add_action('wp_ajax_winshirt_test_ia_key', 'winshirt_test_ia_key');

/**
 * Hidden fields in add to cart form to store customization and production image URLs.
 */
function winshirt_cart_hidden_fields(){
    echo '<input type="hidden" name="winshirt_custom_data" id="winshirt-custom-data-field" />';
    echo '<input type="hidden" name="winshirt_production_image" id="winshirt-production-image-field" />';
    echo '<input type="hidden" name="winshirt_front_image" id="winshirt-front-image-field" />';
    echo '<input type="hidden" name="winshirt_back_image" id="winshirt-back-image-field" />';
    echo '<input type="hidden" name="winshirt_extra_price" id="winshirt-extra-price-field" />';
}
add_action('woocommerce_before_add_to_cart_button','winshirt_cart_hidden_fields');

/**
 * Add custom data and production image to cart item data.
 */
function winshirt_add_cart_item_custom( $cart_item_data, $product_id ){
    if( isset( $_POST['winshirt_custom_data'] ) ){
        $cart_item_data['winshirt_custom_data'] = wp_unslash( $_POST['winshirt_custom_data'] );
    }
    if( isset( $_POST['winshirt_production_image'] ) ){
        $cart_item_data['winshirt_production_image'] = esc_url_raw( $_POST['winshirt_production_image'] );
    }
    if( isset( $_POST['winshirt_front_image'] ) ){
        $cart_item_data['winshirt_front_image'] = esc_url_raw( $_POST['winshirt_front_image'] );
    }
    if( isset( $_POST['winshirt_back_image'] ) ){
        $cart_item_data['winshirt_back_image'] = esc_url_raw( $_POST['winshirt_back_image'] );
    }
    if( isset( $_POST['winshirt_extra_price'] ) ){
        $cart_item_data['winshirt_extra_price'] = floatval( $_POST['winshirt_extra_price'] );
    }
    return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'winshirt_add_cart_item_custom', 20, 2 );

/**
 * Save production image URL to order item meta and rename file with order ID.
 */
function winshirt_save_order_item_custom( $item, $cart_item_key, $values, $order ){
    if( ! empty( $values['winshirt_custom_data'] ) ){
        $item->add_meta_data( 'winshirt_custom_data', $values['winshirt_custom_data'] );
    }

    if( ! empty( $values['winshirt_production_image'] ) ){
        $url      = $values['winshirt_production_image'];
        $upload   = wp_upload_dir();
        $baseurl  = trailingslashit( $upload['baseurl'] ) . 'winshirt-productions/';
        $basedir  = trailingslashit( $upload['basedir'] ) . 'winshirt-productions/';
        $path     = str_replace( $baseurl, $basedir, $url );
        if( file_exists( $path ) ){
            $ext       = pathinfo( $path, PATHINFO_EXTENSION );
            $new_name  = 'prod_' . $order->get_id() . '_' . time() . '.' . $ext;
            $new_path  = $basedir . $new_name;
            rename( $path, $new_path );
            $url = $baseurl . $new_name;
        }
        $item->add_meta_data( 'winshirt_production_image', $url );
    }

    $upload = wp_upload_dir();
    $tmpurl  = trailingslashit( $upload['baseurl'] ) . 'winshirt-customs/tmp/';
    $tmpdir  = trailingslashit( $upload['basedir'] ) . 'winshirt-customs/tmp/';
    $finaldir = trailingslashit( $upload['basedir'] ) . 'winshirt/customs/' . $order->get_id() . '/';
    $finalurl = trailingslashit( $upload['baseurl'] ) . 'winshirt/customs/' . $order->get_id() . '/';
    if ( ! file_exists( $finaldir ) ) {
        wp_mkdir_p( $finaldir );
    }

    if( ! empty( $values['winshirt_front_image'] ) ){
        $url = $values['winshirt_front_image'];
        $path = str_replace( $tmpurl, $tmpdir, $url );
        if( file_exists( $path ) ){
            $ext = pathinfo( $path, PATHINFO_EXTENSION );
            $new_path = $finaldir . 'recto.' . $ext;
            rename( $path, $new_path );
            $url = $finalurl . 'recto.' . $ext;
        }
        $item->add_meta_data( 'winshirt_front_hd', $url );
        $item->add_meta_data( 'winshirt_front_preview', $url );
    }

    if( ! empty( $values['winshirt_back_image'] ) ){
        $url = $values['winshirt_back_image'];
        $path = str_replace( $tmpurl, $tmpdir, $url );
        if( file_exists( $path ) ){
            $ext = pathinfo( $path, PATHINFO_EXTENSION );
            $new_path = $finaldir . 'verso.' . $ext;
            rename( $path, $new_path );
            $url = $finalurl . 'verso.' . $ext;
        }
        $item->add_meta_data( 'winshirt_back_hd', $url );
        $item->add_meta_data( 'winshirt_back_preview', $url );
    }

    if ( ! empty( $values['winshirt_extra_price'] ) ) {
        $item->add_meta_data( 'winshirt_extra_price', floatval( $values['winshirt_extra_price'] ) );
    }
}
add_action( 'woocommerce_checkout_create_order_line_item', 'winshirt_save_order_item_custom', 20, 4 );

/**
 * Apply extra price from customization zones to cart items.
 */
function winshirt_apply_extra_price( $cart ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
        return;
    }
    if ( did_action( 'winshirt_apply_extra_price' ) ) {
        return;
    }
    foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
        if ( ! empty( $cart_item['winshirt_extra_price'] ) ) {
            $extra = floatval( $cart_item['winshirt_extra_price'] );
            $price = $cart_item['data']->get_price();
            $cart_item['data']->set_price( $price + $extra );
        }
    }
    do_action( 'winshirt_apply_extra_price' );
}
add_action( 'woocommerce_before_calculate_totals', 'winshirt_apply_extra_price', 20, 1 );

/**
 * Push purchase event to Google DataLayer on thank you page.
 */
function winshirt_push_purchase_datalayer( $order_id ){
    $order = wc_get_order( $order_id );
    if( ! $order ) return;
    $items = [];
    foreach( $order->get_items() as $item ){
        $items[] = [
            'id'    => $item->get_product_id(),
            'name'  => $item->get_name(),
            'price' => $order->get_item_total( $item, false ),
            'qty'   => $item->get_quantity(),
        ];
    }
    $data = [
        'event'    => 'purchase',
        'order_id' => $order->get_id(),
        'revenue'  => $order->get_total(),
        'items'    => $items,
    ];
    echo '<script>window.dataLayer=window.dataLayer||[];dataLayer.push(' . wp_json_encode( $data ) . ');</script>';
}
add_action( 'woocommerce_thankyou', 'winshirt_push_purchase_datalayer' );

// Display "Personnalisable" badge on product thumbnails in shop
function winshirt_shop_customizable_badge() {
    global $product;
    if ( ! $product instanceof WC_Product ) {
        return;
    }
    if ( get_post_meta( $product->get_id(), '_winshirt_enabled', true ) === 'yes' ) {
        echo '<span class="ws-customizable-badge">' . esc_html__( 'Personnalisable', 'winshirt' ) . '</span>';
    }
}
add_action( 'woocommerce_before_shop_loop_item_title', 'winshirt_shop_customizable_badge', 5 );

// ----- Account section for saved customizations -----
add_action( 'init', 'winshirt_account_endpoint' );
function winshirt_account_endpoint() {
    add_rewrite_endpoint( 'mes-personnalisations', EP_ROOT | EP_PAGES );
}

add_filter( 'query_vars', function( $vars ) {
    $vars[] = 'mes-personnalisations';
    return $vars;
} );

add_filter( 'woocommerce_account_menu_items', function( $items ) {
    $items['mes-personnalisations'] = __( 'Mes personnalisations', 'winshirt' );
    return $items;
} );

add_action( 'woocommerce_account_mes-personnalisations_endpoint', 'winshirt_account_customs_page' );
function winshirt_account_customs_page() {
    $orders = wc_get_orders( [
        'customer_id' => get_current_user_id(),
        'limit'       => -1,
    ] );
    echo '<h3>' . esc_html__( 'Mes personnalisations', 'winshirt' ) . '</h3>';
    foreach ( $orders as $order ) {
        foreach ( $order->get_items() as $item ) {
            $front = $item->get_meta( 'winshirt_front_hd' );
            $back  = $item->get_meta( 'winshirt_back_hd' );
            if ( ! $front && ! $back ) {
                continue;
            }
            echo '<div style="margin-bottom:1rem;">';
            echo '<p>' . esc_html( $item->get_name() . ' #' . $order->get_id() ) . '</p>';
            if ( $front ) {
                echo '<img src="' . esc_url( $front ) . '" style="max-width:150px;height:auto;border:1px solid #ccc;" />';
                echo '<br/><a href="' . esc_url( $front ) . '" download>' . esc_html__( 'Télécharger', 'winshirt' ) . '</a>';
            }
            if ( $back ) {
                echo '<br/><img src="' . esc_url( $back ) . '" style="max-width:150px;height:auto;border:1px solid #ccc;" />';
                echo '<br/><a href="' . esc_url( $back ) . '" download>' . esc_html__( 'Télécharger', 'winshirt' ) . '</a>';
            }
            echo '</div>';
        }
    }
}

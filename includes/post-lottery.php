<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Add meta boxes to posts for lottery fields
function winshirt_add_lottery_meta_box() {
    add_meta_box(
        'winshirt_lottery_meta',
        __( 'Infos Loterie', 'winshirt' ),
        'winshirt_lottery_meta_box_cb',
        'post',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'winshirt_add_lottery_meta_box' );

function winshirt_lottery_meta_box_cb( $post ) {
    wp_nonce_field( 'winshirt_save_lottery_meta', 'winshirt_lottery_nonce' );
    $participants = get_post_meta( $post->ID, 'winshirt_participants', true );
    $end_date     = get_post_meta( $post->ID, 'winshirt_end_date', true );
    $status       = get_post_meta( $post->ID, 'winshirt_lottery_status', true );
    ?>
    <p>
        <label for="winshirt_participants"><strong><?php esc_html_e( 'Nombre de participants', 'winshirt' ); ?></strong></label><br />
        <input type="number" name="winshirt_participants" id="winshirt_participants" value="<?php echo esc_attr( $participants ); ?>" />
    </p>
    <p>
        <label for="winshirt_end_date"><strong><?php esc_html_e( 'Date de fin', 'winshirt' ); ?></strong></label><br />
        <input type="date" name="winshirt_end_date" id="winshirt_end_date" value="<?php echo esc_attr( $end_date ); ?>" />
    </p>
    <p>
        <label for="winshirt_lottery_status"><strong><?php esc_html_e( 'Statut', 'winshirt' ); ?></strong></label><br />
        <select name="winshirt_lottery_status" id="winshirt_lottery_status">
            <option value="en_cours" <?php selected( $status, 'en_cours' ); ?>><?php esc_html_e( 'En cours', 'winshirt' ); ?></option>
            <option value="terminee" <?php selected( $status, 'terminee' ); ?>><?php esc_html_e( 'Terminée', 'winshirt' ); ?></option>
        </select>
    </p>
    <?php
}

function winshirt_save_lottery_meta_box( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! isset( $_POST['winshirt_lottery_nonce'] ) || ! wp_verify_nonce( $_POST['winshirt_lottery_nonce'], 'winshirt_save_lottery_meta' ) ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    if ( isset( $_POST['winshirt_participants'] ) ) {
        update_post_meta( $post_id, 'winshirt_participants', absint( $_POST['winshirt_participants'] ) );
    }
    if ( isset( $_POST['winshirt_end_date'] ) ) {
        update_post_meta( $post_id, 'winshirt_end_date', sanitize_text_field( $_POST['winshirt_end_date'] ) );
    }
    if ( isset( $_POST['winshirt_lottery_status'] ) ) {
        $allowed = array( 'en_cours', 'terminee' );
        $status  = in_array( $_POST['winshirt_lottery_status'], $allowed, true ) ? $_POST['winshirt_lottery_status'] : 'en_cours';
        update_post_meta( $post_id, 'winshirt_lottery_status', $status );
    }
}
add_action( 'save_post', 'winshirt_save_lottery_meta_box' );

// Shortcode to display active lotteries in a carousel
function winshirt_lottery_carousel_shortcode() {
    $today = current_time( 'Y-m-d' );
    $posts = get_posts( [
        'post_type'      => 'post',
        'posts_per_page' => 10,
        'meta_query'     => [
            [
                'key'     => 'winshirt_lottery_status',
                'value'   => 'en_cours',
                'compare' => '='
            ],
            [
                'key'     => 'winshirt_end_date',
                'value'   => $today,
                'compare' => '>=',
                'type'    => 'DATE'
            ]
        ]
    ] );
    if ( ! $posts ) {
        return '';
    }
    ob_start();
    ?>
    <div class="winshirt-lottery-carousel-wrapper">
        <button type="button" class="carousel-prev" aria-label="<?php esc_attr_e( 'Précédent', 'winshirt' ); ?>">&#10094;</button>
        <div class="winshirt-lottery-carousel">
            <?php foreach ( $posts as $p ) : ?>
                <?php
                $end   = get_post_meta( $p->ID, 'winshirt_end_date', true );
                $count = get_post_meta( $p->ID, 'winshirt_participants', true );
                ?>
                <div class="winshirt-lottery-item ws-lottery-card" data-end="<?php echo esc_attr( $end ); ?>">
                    <?php echo get_the_post_thumbnail( $p->ID, 'medium' ); ?>
                    <h3 class="lottery-title"><?php echo esc_html( get_the_title( $p ) ); ?></h3>
                    <?php if ( $end ) : ?>
                        <p class="lottery-draw"><?php echo esc_html( date_i18n( 'd/m/Y', strtotime( $end ) ) ); ?></p>
                    <?php endif; ?>
                    <?php if ( $count ) : ?>
                        <p class="lottery-count"><?php echo esc_html( $count ); ?> participants</p>
                    <?php endif; ?>
                    <a href="<?php echo esc_url( get_permalink( $p ) ); ?>" class="lottery-button"><?php esc_html_e( 'Voir', 'winshirt' ); ?></a>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="carousel-next" aria-label="<?php esc_attr_e( 'Suivant', 'winshirt' ); ?>">&#10095;</button>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'winshirt_lottery_carousel', 'winshirt_lottery_carousel_shortcode' );

// Optionally display lottery info box at top of post content
function winshirt_lottery_info_box( $content ) {
    if ( is_singular( 'post' ) && in_the_loop() && is_main_query() ) {
        $end   = get_post_meta( get_the_ID(), 'winshirt_end_date', true );
        $count = get_post_meta( get_the_ID(), 'winshirt_participants', true );
        $status= get_post_meta( get_the_ID(), 'winshirt_lottery_status', true );
        if ( $end || $count || $status ) {
            $html  = '<div class="winshirt-lottery-info">';
            if ( $status ) {
                $html .= '<p><strong>' . esc_html__( 'Statut', 'winshirt' ) . ':</strong> ' . esc_html( $status ) . '</p>';
            }
            if ( $end ) {
                $html .= '<p><strong>' . esc_html__( 'Fin', 'winshirt' ) . ':</strong> ' . esc_html( date_i18n( 'd/m/Y', strtotime( $end ) ) ) . '</p>';
            }
            if ( $count ) {
                $html .= '<p><strong>' . esc_html__( 'Participants', 'winshirt' ) . ':</strong> ' . esc_html( $count ) . '</p>';
            }
            $html .= '</div>';
            $content = $html . $content;
        }
    }
    return $content;
}
add_filter( 'the_content', 'winshirt_lottery_info_box' );

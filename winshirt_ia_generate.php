<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Insert an AI generated image into the WordPress media library.
 *
 * @param string $path     Full path to the image file on disk.
 * @param string $filename File name of the image.
 * @param int    $parent   Optional parent post ID.
 * @return int|WP_Error Attachment ID on success.
 */
function winshirt_ai_insert_attachment( $path, $filename, $parent = 0 ) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $filetype = wp_check_filetype( $filename, null );
    $attachment = [
        'post_mime_type' => $filetype['type'],
        'post_title'     => sanitize_file_name( $filename ),
        'post_content'   => '',
        'post_status'    => 'inherit',
    ];

    $attach_id  = wp_insert_attachment( $attachment, $path, $parent );
    if ( ! is_wp_error( $attach_id ) ) {
        $attach_data = wp_generate_attachment_metadata( $attach_id, $path );
        wp_update_attachment_metadata( $attach_id, $attach_data );
    }

    return $attach_id;
}

function winshirt_ia_generate() {
    $prompt = isset( $_POST['prompt'] ) ? sanitize_text_field( wp_unslash( $_POST['prompt'] ) ) : '';
    if ( ! $prompt ) {
        wp_send_json_error( 'missing_prompt' );
    }
    $upload = wp_upload_dir();
    $dir = trailingslashit( $upload['basedir'] ) . 'winshirt/ia/';
    if ( ! file_exists( $dir ) ) {
        wp_mkdir_p( $dir );
    }
    $filename = 'ia_' . md5( $prompt . microtime() ) . '.png';
    $path = $dir . $filename;

    $img = imagecreatetruecolor(512, 512);
    $bg  = imagecolorallocate($img, 240, 240, 240);
    imagefilledrectangle($img, 0, 0, 512, 512, $bg);
    $text = wordwrap($prompt, 20, "\n");
    $color = imagecolorallocate($img, 0, 0, 0);
    $y = 20;
    foreach ( explode("\n", $text) as $line ) {
        imagestring($img, 5, 10, $y, $line, $color);
        $y += 15;
    }
    imagepng($img, $path);
    imagedestroy($img);

    $attach_id = winshirt_ai_insert_attachment( $path, $filename );
    $url       = is_wp_error( $attach_id ) ? trailingslashit( $upload['baseurl'] ) . 'winshirt/ia/' . $filename : wp_get_attachment_url( $attach_id );

    wp_send_json_success( [ 'url' => $url ] );
}
add_action( 'wp_ajax_winshirt_ai_generate', 'winshirt_ia_generate' );
add_action( 'wp_ajax_nopriv_winshirt_ai_generate', 'winshirt_ia_generate' );

function winshirt_rest_generate_image( WP_REST_Request $request ) {
    $prompt = sanitize_text_field( $request->get_param( 'prompt' ) );
    if ( ! $prompt ) {
        return new WP_REST_Response( [ 'message' => 'missing_prompt' ], 400 );
    }

    $user_id = get_current_user_id();
    $limit   = intval( get_option( 'winshirt_ia_generation_limit', 3 ) );
    $count   = intval( get_user_meta( $user_id, '_winshirt_ai_count', true ) );
    if ( $limit > 0 && $count >= $limit ) {
        return new WP_REST_Response( [ 'message' => 'limit_reached' ], 403 );
    }

    $key    = get_option( 'winshirt_ia_api_key' );
    $model  = get_option( 'winshirt_ia_model', 'dall-e-3' );
    $format = get_option( 'winshirt_ia_output_format', '1024x1024' );

    if ( ! $key ) {
        return new WP_REST_Response( [ 'message' => 'missing_api_key' ], 400 );
    }

    $response = wp_remote_post( 'https://api.openai.com/v1/images/generations', [
        'headers' => [
            'Authorization' => 'Bearer ' . $key,
            'Content-Type'  => 'application/json',
        ],
        'body'    => wp_json_encode( [
            'model'  => $model,
            'prompt' => $prompt,
            'n'      => 1,
            'size'   => $format,
        ] ),
        'timeout' => 60,
    ] );

    if ( is_wp_error( $response ) ) {
        return new WP_REST_Response( [ 'message' => $response->get_error_message() ], 500 );
    }

    $body = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( empty( $body['data'][0]['url'] ) ) {
        return new WP_REST_Response( [ 'message' => 'generation_failed' ], 500 );
    }

    $source = $body['data'][0]['url'];
    $upload = wp_upload_dir();
    $dir    = trailingslashit( $upload['basedir'] ) . 'winshirt/ia/';
    if ( ! file_exists( $dir ) ) {
        wp_mkdir_p( $dir );
    }
    $filename = 'ia_' . md5( $prompt . microtime() ) . '.png';
    $path     = $dir . $filename;

    $img_data = wp_remote_get( $source );
    if ( is_wp_error( $img_data ) ) {
        return new WP_REST_Response( [ 'message' => $img_data->get_error_message() ], 500 );
    }

    file_put_contents( $path, wp_remote_retrieve_body( $img_data ) );

    $attach_id = winshirt_ai_insert_attachment( $path, $filename );
    $url       = is_wp_error( $attach_id ) ? trailingslashit( $upload['baseurl'] ) . 'winshirt/ia/' . $filename : wp_get_attachment_url( $attach_id );

    // Insert as visual post for admin validation
    $visual_id = wp_insert_post([
        'post_type'   => 'winshirt_visual',
        'post_title'  => $prompt,
        'post_status' => 'publish',
    ]);
    if ( $visual_id ) {
        // Bypass capability checks when assigning thumbnail
        update_post_meta( $visual_id, '_thumbnail_id', $attach_id );
        wp_update_post( [ 'ID' => $attach_id, 'post_parent' => $visual_id ] );
        update_post_meta( $visual_id, '_winshirt_category', 'IA' );
        update_post_meta( $visual_id, '_winshirt_visual_validated', 'no' );
        update_post_meta( $visual_id, '_winshirt_ai_prompt', $prompt );
        update_user_meta( $user_id, '_winshirt_ai_count', $count + 1 );
    }

    return new WP_REST_Response( [ 'imageUrl' => $url ], 200 );
}

add_action( 'rest_api_init', function() {
    register_rest_route( 'winshirt/v1', '/generate-image', [
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => 'winshirt_rest_generate_image',
        // Allow generation even for non logged-in visitors
        'permission_callback' => '__return_true',
    ] );
} );

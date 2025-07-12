<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Handle upload of production image captured client side.
 */
function winshirt_rest_upload_production_image( WP_REST_Request $request ) {
    if ( ! is_user_logged_in() ) {
        return new WP_REST_Response( [ 'message' => 'forbidden' ], 403 );
    }
    $nonce = $request->get_header( 'X-WP-Nonce' );
    if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
        return new WP_REST_Response( [ 'message' => 'invalid_nonce' ], 403 );
    }

    $file = $request->get_file_params()['image'] ?? null;
    if ( ! $file || empty( $file['tmp_name'] ) ) {
        return new WP_REST_Response( [ 'message' => 'missing_file' ], 400 );
    }

    $ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
    if ( ! in_array( $ext, [ 'png', 'jpg', 'jpeg' ], true ) ) {
        return new WP_REST_Response( [ 'message' => 'invalid_type' ], 400 );
    }

    $upload = wp_upload_dir();
    $dir    = trailingslashit( $upload['basedir'] ) . 'winshirt-productions/';
    if ( ! file_exists( $dir ) ) {
        wp_mkdir_p( $dir );
    }

    $filename = 'prod_tmp_' . time() . '_' . wp_generate_password( 6, false ) . '.' . $ext;
    $path     = $dir . $filename;

    if ( ! move_uploaded_file( $file['tmp_name'], $path ) ) {
        return new WP_REST_Response( [ 'message' => 'upload_failed' ], 500 );
    }

    $url = trailingslashit( $upload['baseurl'] ) . 'winshirt-productions/' . $filename;
    return new WP_REST_Response( [ 'url' => $url ], 200 );
}

/**
 * Upload temporary preview mockup and return URLs of HD and low resolution images.
 */
function winshirt_rest_upload_mockup( WP_REST_Request $request ) {
    if ( ! is_user_logged_in() ) {
        return new WP_REST_Response( [ 'message' => 'forbidden' ], 403 );
    }
    $nonce = $request->get_header( 'X-WP-Nonce' );
    if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
        return new WP_REST_Response( [ 'message' => 'invalid_nonce' ], 403 );
    }

    $file = $request->get_file_params()['image'] ?? null;
    if ( ! $file || empty( $file['tmp_name'] ) ) {
        return new WP_REST_Response( [ 'message' => 'missing_file' ], 400 );
    }

    $ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
    if ( ! in_array( $ext, [ 'png', 'jpg', 'jpeg' ], true ) ) {
        return new WP_REST_Response( [ 'message' => 'invalid_type' ], 400 );
    }

    $upload = wp_upload_dir();
    $dir    = trailingslashit( $upload['basedir'] ) . 'winshirt-mockups/';
    if ( ! file_exists( $dir ) ) {
        wp_mkdir_p( $dir );
    }

    $filename = 'mockup_' . time() . '_' . wp_generate_password( 6, false ) . '.' . $ext;
    $path     = $dir . $filename;

    if ( ! move_uploaded_file( $file['tmp_name'], $path ) ) {
        return new WP_REST_Response( [ 'message' => 'upload_failed' ], 500 );
    }

    $thumb = $dir . 'thumb_' . $filename;
    $editor = wp_get_image_editor( $path );
    if ( ! is_wp_error( $editor ) ) {
        $editor->resize( 400, 0, false );
        $editor->save( $thumb );
    }

    $baseurl = trailingslashit( $upload['baseurl'] ) . 'winshirt-mockups/';
    return new WP_REST_Response( [
        'url'   => $baseurl . $filename,
        'thumb' => $baseurl . 'thumb_' . $filename,
    ], 200 );
}

/**
 * Upload temporary custom side image (front/back).
 */
function winshirt_rest_upload_custom_side( WP_REST_Request $request ) {
    if ( ! is_user_logged_in() ) {
        return new WP_REST_Response( [ 'message' => 'forbidden' ], 403 );
    }
    $nonce = $request->get_header( 'X-WP-Nonce' );
    if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
        return new WP_REST_Response( [ 'message' => 'invalid_nonce' ], 403 );
    }

    $side = sanitize_text_field( $request->get_param( 'side' ) );
    if ( ! in_array( $side, [ 'front', 'back' ], true ) ) {
        $side = 'front';
    }

    $file = $request->get_file_params()['image'] ?? null;
    if ( ! $file || empty( $file['tmp_name'] ) ) {
        return new WP_REST_Response( [ 'message' => 'missing_file' ], 400 );
    }

    $ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
    if ( ! in_array( $ext, [ 'png', 'jpg', 'jpeg' ], true ) ) {
        return new WP_REST_Response( [ 'message' => 'invalid_type' ], 400 );
    }

    $upload = wp_upload_dir();
    $dir    = trailingslashit( $upload['basedir'] ) . 'winshirt-customs/tmp/';
    if ( ! file_exists( $dir ) ) {
        wp_mkdir_p( $dir );
    }

    $filename = 'custom_tmp_' . $side . '_' . time() . '_' . wp_generate_password( 6, false ) . '.' . $ext;
    $path     = $dir . $filename;

    if ( ! move_uploaded_file( $file['tmp_name'], $path ) ) {
        return new WP_REST_Response( [ 'message' => 'upload_failed' ], 500 );
    }

    $url = trailingslashit( $upload['baseurl'] ) . 'winshirt-customs/tmp/' . $filename;
    return new WP_REST_Response( [ 'url' => $url ], 200 );
}

add_action( 'rest_api_init', function() {
    register_rest_route( 'winshirt/v1', '/upload-production-image', [
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => 'winshirt_rest_upload_production_image',
        'permission_callback' => function(){ return is_user_logged_in(); },
    ] );

    register_rest_route( 'winshirt/v1', '/upload-mockup', [
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => 'winshirt_rest_upload_mockup',
        'permission_callback' => function(){ return is_user_logged_in(); },
    ] );

    register_rest_route( 'winshirt/v1', '/upload-custom-side', [
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => 'winshirt_rest_upload_custom_side',
        'permission_callback' => function(){ return is_user_logged_in(); },
    ] );
});

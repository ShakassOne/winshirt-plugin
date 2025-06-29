<?php
if ( ! defined( 'ABSPATH' ) ) exit;

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
    $url = trailingslashit( $upload['baseurl'] ) . 'winshirt/ia/' . $filename;
    wp_send_json_success( [ 'url' => $url ] );
}
add_action( 'wp_ajax_winshirt_ai_generate', 'winshirt_ia_generate' );
add_action( 'wp_ajax_nopriv_winshirt_ai_generate', 'winshirt_ia_generate' );

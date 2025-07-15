<?php
if (!defined('ABSPATH')) exit;

// Allow SVG uploads
add_filter('upload_mimes', function($mimes){
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
});

// Clean uploaded SVGs
add_filter('wp_handle_upload', function($upload){
    $filetype = wp_check_filetype($upload['file']);
    if ($filetype['ext'] !== 'svg') {
        return $upload;
    }

    $svg = file_get_contents($upload['file']);

    // Remove white background rectangles
    $svg = preg_replace('/<rect[^>]+fill="(white|#fff|#ffffff)"[^>]*>.*?<\/rect>/i', '', $svg);

    // Remove inline fills except currentColor
    $svg = preg_replace_callback('/<path[^>]+>/i', function($match){
        $tag = $match[0];
        $tag = preg_replace('/fill="[^"]*"/i', '', $tag);
        $tag = preg_replace('/<path/i', '<path fill="currentColor"', $tag);
        return $tag;
    }, $svg);

    // Remove annoying inline styles
    $svg = preg_replace('/style="[^"]*fill[^"]*"/i', '', $svg);

    // Add viewBox if missing
    if (!preg_match('/viewBox=/i', $svg)) {
        if (preg_match('/width="(\d+)[^"]*"/i', $svg, $w) &&
            preg_match('/height="(\d+)[^"]*"/i', $svg, $h)) {
            $viewBox = 'viewBox="0 0 ' . $w[1] . ' ' . $h[1] . '"';
            $svg = preg_replace('/<svg/i', '<svg ' . $viewBox, $svg);
        }
    }

    file_put_contents($upload['file'], $svg);

    return $upload;
});


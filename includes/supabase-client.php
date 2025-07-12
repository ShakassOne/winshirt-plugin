<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Simple wrapper for Supabase edge function calls.
 */
class Winshirt_Supabase_Client {
    private $url;
    private $key;

    public function __construct( $url = '', $key = '' ) {
        $this->url = $url ?: get_option( 'winshirt_supabase_url' );
        $this->key = $key ?: get_option( 'winshirt_supabase_key' );
    }

    /**
     * Call an edge function with optional JSON body.
     *
     * @param string $function Name of the edge function.
     * @param array  $body     Optional associative array to send as JSON.
     * @return array|WP_Error  Decoded response body or WP_Error on failure.
     */
    public function call_edge_function( $function, array $body = array() ) {
        if ( ! $this->url || ! $this->key ) {
            return new WP_Error( 'missing_keys', __( 'Supabase credentials are missing', 'winshirt' ) );
        }

        $endpoint = trailingslashit( $this->url ) . 'functions/v1/' . ltrim( $function, '/' );
        $args     = array(
            'headers' => array(
                'Content-Type'  => 'application/json',
                'apikey'        => $this->key,
                'Authorization' => 'Bearer ' . $this->key,
            ),
            'body'    => ! empty( $body ) ? wp_json_encode( $body ) : null,
            'timeout' => 20,
        );

        $response = wp_remote_post( $endpoint, $args );
        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $code = wp_remote_retrieve_response_code( $response );
        $data = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( $code < 200 || $code >= 300 ) {
            return new WP_Error( 'supabase_error', __( 'Supabase request failed', 'winshirt' ), array( 'code' => $code, 'data' => $data ) );
        }

        return $data;
    }
}

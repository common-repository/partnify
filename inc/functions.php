<?php
/**
 * Functions.
 * 
 */

function partnify_get_settings() {
    return get_option( 'partnify_settings' );
}

function pratnify_get_connection_status() {
    $settings = partnify_get_settings();
    // print_r( $settings );
    if ( isset( $settings['partnify_status'] ) && 'True' == $settings['partnify_status'] ) {
        return true;
    }
    return false;
}

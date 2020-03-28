<?php
/**
 * Helper functions and definations.
 * 
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get DateTimeZone based one WordPress timezone settings
 *
 * @see wp_timezone
 * 
 * @param string $type
 * @return DateTimeZone
 */
function wpzoom_timezone() {
    if ( function_exists( 'wp_timezone' ) ) {
        return wp_timezone();
    }

    return new DateTimeZone( wpzoom_timezone_string() );
}

/**
 * Retrieves the timezone from site settings as a string.
 *
 * Uses the `timezone_string` option to get a proper timezone if available,
 * otherwise falls back to an offset.
 *
 * Backword compatibility with wp_timezone_string
 * 
 * @see wp_timezone_string
 *
 * @return string PHP timezone string or a ±HH:MM offset.
 */
function wpzoom_timezone_string() {
    if ( function_exists( 'wp_timezone_string' ) ) {
        return wp_timezone_string();
    }

    $timezone_string = get_option( 'timezone_string' );
    if ( $timezone_string ) {
        return $timezone_string;
    }
    
    $offset  = (float) get_option( 'gmt_offset' );
    $hours   = (int) $offset;
    $minutes = ( $offset - $hours );

    $sign      = ( $offset < 0 ) ? '-' : '+';
    $abs_hour  = abs( $hours );
    $abs_mins  = abs( $minutes * 60 );
    $tz_offset = sprintf( '%s%02d:%02d', $sign, $abs_hour, $abs_mins );

    return $tz_offset;
}

/**
 * Check file type is playable.
 *
 * @param string $filetype
 * @return bool
 */
function wpzoom_is_filetype_playable( $filetype = '' ) {
    $filetype = strtolower( $filetype );
    return in_array( $filetype, array( 'mp4', 'm4a' ), true );
}

/**
 * Check a recording array and find if it's playable.
 *
 * @param array $recording
 * @return bool
 */
function wpzoom_is_recording_playable( $recording ) {
    if ( ! is_array( $recording ) ||
        ! isset( $recording['play_url'] ) ||
        ! isset( $recording['file_type'] ) ) {
        return false;
    }

    return wpzoom_is_filetype_playable( $recording['file_type'] );
}

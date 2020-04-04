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

function wpzoom_is_error_response( $response ) {
	return isset( $response['code'], $response['message'] );
}

function wpzoom_get_settings_page_url() {
	return esc_url( admin_url( 'options-general.php?page=' . WPZOOM_Settings::PAGE_SLUG ) );
}

function wpzoom_get_settings_page_anchor( $text = 'click here' ) {
	return sprintf( '<a href="%s" target="_blank">%s</a>', wpzoom_get_settings_page_url(), $text );
}

function wpzoom_is_admin_alert( Exception $e ) {
	return ( $e->getCode() ===  WPZOOM_Shortcode::ALERT_FOR_ADMIN && current_user_can( 'manage_options' ) );
}

function wpzoom_get_alert( $message ) {
	return sprintf( '<div class="wpzoom-alert wpzoom-alert--info">%1$s</div>', $message );
}

function wpzoom_webinar_register_button( $webinar ) {
	if ( isset( $webinar->joinUrl ) ) {
		$anchor = sprintf( '<a class="button" href="%s" target="_blnk">Register</a>', esc_url( $webinar->joinUrl ) );
	} else {
		$url = add_query_arg( array( 'wid' => $webinar->id ), WPZOOM_Settings::getRegistrationPageLink() );
		$anchor = sprintf( '<a class="button" href="%s">Register</a>', esc_url( $url ) );
	}
	echo $anchor;
}

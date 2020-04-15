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
function zoompress_timezone() {
    if ( function_exists( 'wp_timezone' ) ) {
        return wp_timezone();
    }

    return new DateTimeZone( zoompress_timezone_string() );
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
function zoompress_timezone_string() {
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
function zoompress_is_filetype_playable( $filetype = '' ) {
    $filetype = strtolower( $filetype );
    return in_array( $filetype, array( 'mp4', 'm4a' ), true );
}

/**
 * Check a recording array and find if it's playable.
 *
 * @param array $recording
 * @return bool
 */
function zoompress_is_recording_playable( $recording ) {
    if ( ! is_array( $recording ) ||
        ! isset( $recording['play_url'] ) ||
        ! isset( $recording['file_type'] ) ) {
        return false;
    }

    return zoompress_is_filetype_playable( $recording['file_type'] );
}

function zoompress_is_error_response( $response ) {
	return isset( $response['code'], $response['message'] );
}

function zoompress_get_settings_page_url() {
	return esc_url( admin_url( 'options-general.php?page=' . ZOOMPRESS_Settings::PAGE_SLUG ) );
}

function zoompress_get_settings_page_anchor( $text = 'click here' ) {
	return sprintf( '<a href="%s" target="_blank">%s</a>', zoompress_get_settings_page_url(), $text );
}

function zoompress_is_admin_alert( Exception $e ) {
	return ( $e->getCode() ===  ZOOMPRESS_Shortcode::ALERT_FOR_ADMIN && current_user_can( 'manage_options' ) );
}

function zoompress_get_alert( $message ) {
	return sprintf( '<div class="zoompress-alert zoompress-alert--info">%1$s</div>', $message );
}

function zoompress_webinar_register_button( $webinar, $occurrenceID = false ) {
	if ( isset( $webinar->joinUrl ) ) {
		$anchor = sprintf( '<a class="button" href="%s" target="_blnk">Register</a>', esc_url( $webinar->joinUrl ) );
	} else {
		$args = [ 'wid' => $webinar->id ];
		if ( $occurrenceID ) {
			$args['oid'] = $occurrenceID;
		}

		$url = add_query_arg( $args, ZOOMPRESS_Settings::getRegistrationPageLink() );
		$anchor = sprintf( '<a class="button" href="%s">Register</a>', esc_url( $url ) );
	}
	echo $anchor;
}

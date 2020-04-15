<?php
/**
 * Calender shortcode class
 *
 * @package WP_Zoom
 */
defined( 'ABSPATH' ) || exit;

class ZOOMPRESS_ZoomCalendarShortcode extends ZOOMPRESS_Shortcode {

	public function getTag() {
		return 'zoom_calendar';
	}

	public function doShortcode( $atts, $content = null ) {
		$atts = shortcode_atts( array(
			'include' => '',
			'exclude' => '',
			'hide' => '',
			'days' => ''
		), $atts, $this->getTag() );

		try {
			$key    = ZOOMPRESS_Settings::getTokenKey();
			$secret = ZOOMPRESS_Settings::getTokenSecret();

			if ( empty( $key ) || empty( $secret ) ) {
				throw new Exception( sprintf(
					'You did not setup Zoom API keys yet. Please %s and setup API keys first.',
					zoompress_get_settings_page_anchor()
				), ZOOMPRESS_Shortcode::ALERT_FOR_ADMIN );
			}

			$zoomUsers = new Zoom\Endpoint\Users( $key, $secret );
			$userResponse = $zoomUsers->list();

			if ( zoompress_is_error_response( $userResponse ) ) {
				throw new Exception( sprintf(
					'Invalid Zoom API keys. Please %s and add valid API keys.',
					zoompress_get_settings_page_anchor()
				), ZOOMPRESS_Shortcode::ALERT_FOR_ADMIN );
			}

			if ( empty( $userResponse['users'] ) ) {
				throw new Exception( 'No data found.', ZOOMPRESS_Shortcode::ALERT_FOR_ALL );
			}

			$userFirst = current( $userResponse['users'] );

			$zoomWebinar = new Zoom\Endpoint\Webinar( $key, $secret );
			$webinarResponse = $zoomWebinar->list( $userFirst['id'], [] );

			if ( empty( $webinarResponse['webinars'] ) ) {
				throw new Exception( 'No webinars found.', ZOOMPRESS_Shortcode::ALERT_FOR_ALL );
			}

			wp_enqueue_script( 'zoom-calendar-js' );

			wp_localize_script(
				'zoom-calendar-js',
				'wpZoomWebinars',
				$webinarResponse['webinars']
			);

			/* test templating */
			$template = new ZOOMPRESS_Template();
			$template->templatePath = 'src/shortcodes/zoom_calendar/templates/';
			$template->templateName = 'calendar';
			$template->data = array(
				'webinarResponse' => $webinarResponse,
				'webinars' => $webinarResponse['webinars']
			);

			return $template->get();
		} catch ( \Exception $e ) {
			if ( zoompress_is_admin_alert( $e ) ) {
				return zoompress_get_alert( $e->getMessage() );
			} else {
				return zoompress_get_alert( $e->getMessage() );
			}
		}
	}

}

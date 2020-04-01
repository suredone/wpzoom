<?php
/**
 * Calender shortcode class
 *
 * @package WP_Zoom
 */
defined( 'ABSPATH' ) || exit;

class WPZOOM_ZoomCalendarShortcode extends WPZOOM_Shortcode {

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
			$key    = WPZOOM_Settings::getTokenKey();
			$secret = WPZOOM_Settings::getTokenSecret();

			if ( empty( $key ) || empty( $secret ) ) {
				throw new Exception( sprintf(
					'You did not setup Zoom API keys yet. Please %s and setup API keys first.',
					wpzoom_get_settings_page_anchor()
				), WPZOOM_Shortcode::ALERT_FOR_ADMIN );
			}

			$zoomUsers = new Zoom\Endpoint\Users( $key, $secret );
			$userResponse = $zoomUsers->list();

			if ( wpzoom_is_error_response( $userResponse ) ) {
				throw new Exception( sprintf(
					'Invalid Zoom API keys. Please %s and add valid API keys.',
					wpzoom_get_settings_page_anchor()
				), WPZOOM_Shortcode::ALERT_FOR_ADMIN );
			}

			if ( empty( $userResponse['users'] ) ) {
				throw new Exception( 'No data found.', WPZOOM_Shortcode::ALERT_FOR_ALL );
			}

			$userFirst = current( $userResponse['users'] );

			$zoomWebinar = new Zoom\Endpoint\Webinar( $key, $secret );
			$webinarResponse = $zoomWebinar->list( $userFirst['id'], [] );

			if ( empty( $webinarResponse['webinars'] ) ) {
				throw new Exception( 'No webinars found.', WPZOOM_Shortcode::ALERT_FOR_ALL );
			}

			wp_localize_script(
				'zoom-calendar-js',
				'wpZoomWebinars',
				$webinarResponse['webinars']
			);

			/* test templating */
			$template = new WPZOOM_Template();
			$template->templatePath = 'src/shortcodes/zoom_calendar/templates/';
			$template->templateName = 'calendar';
			$template->data = array(
				'webinarResponse' => $webinarResponse,
				'webinars' => $webinarResponse['webinars']
			);

			return $template->get();
		} catch ( \Exception $e ) {
			if ( wpzoom_is_admin_alert( $e ) ) {
				return wpzoom_get_alert( $e->getMessage() );
			} else {
				return wpzoom_get_alert( $e->getMessage() );
			}
		}
	}

}

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
			'days' => '',
			'registration' => 'internal',
		), $atts, $this->getTag() );

		$isExternalRegistration = $atts['registration'] === 'external' ? true : false;

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

			$webinarDates = [];
			foreach( $webinarResponse['webinars'] as $webinarListing ) {

				$webinarDetail = $zoomWebinar->getDetails( $webinarListing['id'] );

				if ( isset($webinarDetail['occurrences']) && is_array($webinarDetail['occurrences']) ) {

					// add each occurence as a webinar date
					foreach( $webinarDetail['occurrences'] as $occurence ) {

						$webinarDate = new stdClass;
						$webinarDate->title = $webinarListing['topic'];
						$webinarDate->start = $this->getFormattedTime( $occurence['start_time'] );
						$webinarDate->duration = $occurence['duration'];

						if( $isExternalRegistration ) {
							$webinarDate->register = $webinarListing['join_url'];
						} else {
							$args = [ 'wid' => $webinarListing['id'] ];
							$args['oid'] = $occurence['occurrence_id'];
							$webinarDate->register = add_query_arg( $args, ZOOMPRESS_Settings::getRegistrationPageLink() );
						}

						$webinarDates[] = $webinarDate;
					}
				} else {
					// add 1 webinar date for the webinar itself (singular)
					$webinarDate = new stdClass;
					$webinarDate->title = $webinarListing['topic'];
					$webinarDate->start = $this->getFormattedTime( $webinarListing['start_time'] );
					$webinarDate->duration = $webinarListing['duration'];

					if( $isExternalRegistration ) {
						$webinarDate->register = $webinarListing['join_url'];
					} else {
						$args = [ 'wid' => $webinarListing['id'] ];
						$webinarDate->register = add_query_arg( $args, ZOOMPRESS_Settings::getRegistrationPageLink() );
					}

					$webinarDates[] = $webinarDate;
				}
			}

			wp_localize_script(
				'zoom-calendar-js',
				'wpZoomWebinars',
				$webinarDates
			);

			/* load calendar template */
			$template = new ZOOMPRESS_Template();
			$template->templatePath = 'src/shortcodes/zoom_calendar/templates/';
			$template->templateName = 'calendar';
			return $template->get();

		} catch ( \Exception $e ) {
			return zoompress_get_alert( $e->getMessage() );
		}
	}

	protected function getFormattedTime( $time ) {
		$dateTime = new DateTime( $time );
		$dateTime->setTimezone( zoompress_timezone() ); // Had to set forcefully
		return $dateTime->format( 'Y-m-d\TG:i:s' );
	}

}

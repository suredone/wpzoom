<?php
/**
 * Webinar shortcode class
 *
 * @package WP_Zoom
 */
defined( 'ABSPATH' ) || exit;

class WPZOOM_ZoomWebinarShortcode extends WPZOOM_Shortcode {

	public function getTag() {
		return 'zoom_webinar';
	}


	public function doShortcode( $atts, $content = null ) {
		$atts = shortcode_atts( array(
			'include' => '',
			'exclude' => '',
			'hide'    => '',
			'days'    => ''
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

			// prepare webinars and do processing
			$webinars = array();
			$webinarsSorted = array_reverse( $webinarResponse['webinars'] );

			foreach ( $webinarsSorted as $webinarData ) {
				$webinar = new stdClass;
				$title = $webinarData['topic'];
				$start = $webinarData['start_time'];

				// check if contains exclude words
				if ( $atts['exclude'] != '' && $this->excludeFilter( $title, $atts['exclude'] ) ) {
					continue;
				}

				// check if contains include words
				if ( $atts['include'] != '' && $this->includeFilter( $title, $atts['include'] ) ) {
					continue;
				}

				$days = 365;

				// check days into future filter
				if ( $atts['days'] != '' ) {
					$days = $atts['days'];
				}

				if ( $this->daysFilter( $start, $days ) ) {
					continue;
				}

				// handle title filtering hide
				if ( $atts['hide'] != '' ) {
					$title = $this->prepareTitle( $title, $atts['hide'] );
				}

				$webinar->title = $title;

				// get the start time
				$startTime = $webinarData['start_time'];
				$dateTime = new DateTime( $startTime );
				$dateTime->setTimezone( wpzoom_timezone() ); // Had to set forcefully
				$webinar->start = $dateTime->format( 'Y-m-d' ) . ' at ' . $dateTime->format( 'g:iA' );
				$webinar->duration = $webinarData['duration'];

				// stash webinar object
				$webinars[] = $webinar;
			}

			/* test templating */
			$template = new WPZOOM_Template();
			$template->templatePath = 'src/shortcodes/zoom_webinar/templates/';
			$template->templateName = 'table';
			$template->data = array(
				'webinarResponse' => $webinarResponse,
				'webinars' => $webinars
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

	public function prepareTitle( $originalTitle, $hide ) {
		return str_replace( $hide, '', $originalTitle );
	}

	public function excludeFilter( $title, $exclude ) {
		return ( strpos( $title, $exclude ) !== false );
	}

	public function includeFilter( $title, $include ) {
		return ( strpos( $title, $include ) === false );
	}

	public function daysFilter( $start, $days ) {
		$dateStart = substr( $start, 0, 10 );

		$datetime1 = new DateTime( $dateStart );
		$datetime1->setTimezone( wpzoom_timezone() );
		$datetime2 = new DateTime( date( 'Y-m-d' ) );
		$datetime2->setTimezone( wpzoom_timezone() );
		$interval = $datetime2->diff( $datetime1 );

		$sign = $interval->format( '%R' );
		if ( $sign == '-' ) {
			return true; // start in the past!
		}

		$daysAway = $interval->format( '%a' );
		if ( $daysAway > $days ) {
			return true;
		}
	}

}

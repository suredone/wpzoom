<?php
/**
 * Webinar shortcode class
 *
 * @package WP_Zoom
 */
defined( 'ABSPATH' ) || exit;

class ZOOMPRESS_ZoomWebinarShortcode extends ZOOMPRESS_Shortcode {

	public function getTag() {
		return 'zoom_webinar';
	}


	public function doShortcode( $atts, $content = null ) {
		$atts = shortcode_atts( array(
			'include' => '',
			'exclude' => '',
			'hide'    => '',
			'days'    => '',
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

			// prepare webinars and do processing
			$webinars = array();
			$webinarsSorted = array_reverse( $webinarResponse['webinars'] );

			foreach ( $webinarsSorted as $webinarData ) {
				$webinar = new stdClass;
				$title = $webinarData['topic'];
				$start = $webinarData['start_time'];

				/**
				 * Recurring webinar has a type flag with value 6 and 9
				 * 6 - Recurring webinar with no fixed time
				 * 9 - Recurring webinar with a fixed time
				 */
				$webinar->isRecurring = in_array( $webinarData['type'], [6,9] );

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
				$dateTime->setTimezone( zoompress_timezone() ); // Had to set forcefully
				$webinar->start = $dateTime->format( 'Y-m-d' ) . ' at ' . $dateTime->format( 'g:iA' );
				$webinar->duration = $webinarData['duration'];
				$webinar->id = $webinarData['id'];

				if ( $isExternalRegistration && isset( $webinarData['join_url'] ) ) {
					$webinar->joinUrl = $webinarData['join_url'];
				}

				// stash webinar object
				$webinars[] = $webinar;
			}

			/* test templating */
			$template = new ZOOMPRESS_Template();
			$template->templatePath = 'src/shortcodes/zoom_webinar/templates/';
			$template->templateName = 'table';
			$template->data = array(
				'zoomWebinar' => $zoomWebinar,
				'webinars' => $webinars,
			);

			return $template->get();

		} catch ( \Exception $e ) {
			return zoompress_get_alert( $e->getMessage() );
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
		$datetime1->setTimezone( zoompress_timezone() );
		$datetime2 = new DateTime( date( 'Y-m-d' ) );
		$datetime2->setTimezone( zoompress_timezone() );
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

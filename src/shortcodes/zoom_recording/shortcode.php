<?php
/**
 * Recording shortcode class
 *
 * @package WP_Zoom
 */
defined( 'ABSPATH' ) || exit;

class ZOOMPRESS_ZoomRecordingShortcode extends ZOOMPRESS_Shortcode {

	public function getTag() {
		return 'zoom_recording';
	}

	public function doShortcode( $atts, $content = null ) {
		$atts = shortcode_atts( array(
			'include' => '',
			'exclude' => '',
			'hide'    => '',
			'days'    => '',
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
			$daysAgo = 30;

			if ( $atts['days'] ) {
				$daysAgo = $atts['days'];
			}

			$fromTime = strtotime( '-' . $daysAgo . ' days' );
			$fromDate = date( 'Y-m-d', $fromTime );

			$args = array(
				'from' => $fromDate
			);

			$zoomRecording = new Zoom\Endpoint\Recording( $key, $secret );
			$response = $zoomRecording->list( $userFirst['id'], $args );

			if ( empty( $response['meetings'] ) ) {
				throw new Exception( 'No recordings found.', ZOOMPRESS_Shortcode::ALERT_FOR_ALL );
			}

			$meetingsResponse = $response['meetings'];

			$meetings = array();
			foreach ( $meetingsResponse as $meetingData ) {

				$meeting = new stdClass;
				$meeting->start = $meetingData['start_time'];
				$meeting->title = $meetingData['topic'];

				// check if contains exclude words
				if ( $atts['exclude'] != '' && $this->excludeFilter( $meeting->title, $atts['exclude'] ) ) {
					continue;
				}

				// check if contains include words
				if ( $atts['include'] != '' && $this->includeFilter( $meeting->title, $atts['include'] ) ) {
					continue;
				}

				$meeting->recording_files = $meetingData['recording_files'];
				$meeting->recording_count = $meetingData['recording_count'];

				$meetings[] = $meeting;
			}

			$template = new ZOOMPRESS_Template();
			$template->templatePath = 'src/shortcodes/zoom_recording/templates/';
			$template->templateName = 'table';
			$template->data = array(
				'response' => $response,
				'meetings' => $meetings
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

	public function prepareTitle( $originalTitle, $hide ) {
		return str_replace( $hide, '', $originalTitle );
	}

	public function includeFilter( $title, $include ) {
		return ( strpos( $title, $include ) === false );
	}

	public function excludeFilter( $title, $exclude ) {
		return ( strpos( $title, $exclude ) !== false );
	}

	public function daysFilter( $start, $days ) {
		$dateStart = substr( $start, 0, 10 );

		$datetime1 = new DateTime( $dateStart );
		$datetime1->setTimezone( zoompress_timezone() );
		$datetime2 = new DateTime( date( 'Y-m-d' ) );
		$datetime2->setTimezone( zoompress_timezone() );
		$interval = $datetime2->diff( $datetime1 );

		$sign = $interval->format( '%R' );
		$daysPast = $interval->format( '%a' );

		if ( $daysPast > $days ) {
			return true;
		}
	}

}

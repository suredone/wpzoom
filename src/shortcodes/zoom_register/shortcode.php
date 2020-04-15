<?php
/**
 * Register shortcode class
 *
 * @package WP_Zoom
 */
defined( 'ABSPATH' ) || exit;

class ZOOMPRESS_ZoomRegisterShortcode extends ZOOMPRESS_Shortcode {

	const ACTION_NONCE_KEY = 'zoompress_register';

	public function __construct() {
		parent::__construct();

		add_action( 'wp_ajax_' . self::ACTION_NONCE_KEY, array( $this, 'processRequest' ) );
		add_action( 'wp_ajax_nopriv_' . self::ACTION_NONCE_KEY, array( $this, 'processRequest' ) );
	}

	public function getTag() {
		return 'zoom_register';
	}

	public function doShortcode( $atts, $content = null ) {
		$atts = shortcode_atts( array(
			'button_text' => 'Register',
		), $atts, $this->getTag() );

		try {
			$webinarId = isset( $_GET['wid'] ) ? absint( $_GET['wid'] ) : 0;

			if ( empty( $webinarId ) ) {
				throw new Exception( 'You must have reached this page by mistake', self::ALERT_FOR_ADMIN );
			}

			$key    = ZOOMPRESS_Settings::getTokenKey();
			$secret = ZOOMPRESS_Settings::getTokenSecret();

			if ( empty( $key ) || empty( $secret ) ) {
				throw new Exception( sprintf(
					'You did not setup Zoom API keys yet. Please %s and setup API keys first.',
					zoompress_get_settings_page_anchor()
				), ZOOMPRESS_Shortcode::ALERT_FOR_ADMIN );
			}

			$zoomWebinar = new Zoom\Endpoint\Webinar( $key, $secret );
			$webinarQuestions = $zoomWebinar->getQuestions($webinarId);

			if ( ! isset($webinarQuestions['code']) || $webinarQuestions['code'] !== 200 ) {
				throw new Exception( 'Webinar not found!',ZOOMPRESS_Shortcode::ALERT_FOR_ALL );
			}

			wp_enqueue_script( 'zoompress-register' );

			$template = new ZOOMPRESS_Template();
			$template->templatePath = 'src/shortcodes/zoom_register/templates/';
			$template->templateName = 'register-form';
			$template->data = array(
				'buttonText' => $atts['button_text'],
				'webinarId' => $webinarId,
				'webinarQuestions' => $webinarQuestions,
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

	public function processRequest() {
		// https://marketplace.zoom.us/docs/api-reference/zoom-api/webinars/webinarregistrantcreate

		try {
			$nonce = isset( $_POST['_wpnonce'] ) ? $_POST['_wpnonce'] : '';
			$webinarId = isset( $_POST['webinar_id'] ) ? absint( $_POST['webinar_id'] ) : 0;

			if ( ! wp_verify_nonce( $nonce, self::ACTION_NONCE_KEY ) ) {
				throw new Exception( 'Nice try, thank you!' );
			}

			if ( empty( $webinarId ) ) {
				throw new Exception( 'You must have reached this page by mistake or you did not select any webinar.' );
			}

			if (!isset($_POST['zoompress_fields']) || empty($_POST['zoompress_fields'])) {
				throw new Exception('There is something wrong with your request!');
			}

			$fields = wp_unslash($_POST['zoompress_fields']);
			$fields = json_decode($fields, true);

			$questions = isset($fields['q']) ? $fields['q'] : [];
			$customQuestions = isset($fields['cq']) ? $fields['cq'] : [];

			$errors = [];

			$mq = [];

			if (! empty($_POST['occurrence_id'])) {
				$mq['occurrence_id'] = $_POST['occurrence_id'];
			}

			foreach ($questions as $qk => $qd ) {
				if (!isset($_POST[$qk]) || trim($_POST[$qk]) === '') {
					$errors[$qk] = sprintf('%s is missing', $qd['title']);
					continue;
				}
				$mq[$qk] = sanitize_text_field( $_POST[$qk] );
			}

			$cq = [];
			foreach ($customQuestions as $qd ) {
				if (!isset($qd['title'])) {
					continue;
				}

				$key = sanitize_key($qd['title']);
				if (!isset($_POST[$key]) || trim($_POST[$key]) === '') {
					$errors[$qk] = sprintf('%s is missing', $qd['title']);
					continue;
				}

				$cq[] = [
					'title' => $qd['title'],
					'value' => sanitize_text_field( $_POST[$key] ),
				];
			}

			if (!empty($errors)) {
				throw new Exception(wp_json_encode($errors));
			}

			$key    = ZOOMPRESS_Settings::getTokenKey();
			$secret = ZOOMPRESS_Settings::getTokenSecret();

			if ( empty( $key ) || empty( $secret ) ) {
				if ( current_user_can( 'manage_options' ) ) {
					throw new Exception( sprintf(
						'You did not setup Zoom API keys yet. Please %s and setup API keys first.',
						zoompress_get_settings_page_anchor()
					), ZOOMPRESS_Shortcode::ALERT_FOR_ADMIN );
				} else {
					throw new Exception( 'There is something wrong, please contact support.' );
				}
			}

			$zoomWebinar = new Zoom\Endpoint\Webinar($key, $secret);
			$registerResponse = $zoomWebinar->register(
				$webinarId,
				array_merge($mq, ['custom_questions' => $cq])
			);

			if ( zoompress_is_error_response( $registerResponse ) ) {
				if ( current_user_can( 'manage_options' ) && $registerResponse['code'] === 124 ) {
					throw new Exception( sprintf(
						'Invalid Zoom API keys. Please %s and add valid API keys.',
						zoompress_get_settings_page_anchor()
					), ZOOMPRESS_Shortcode::ALERT_FOR_ADMIN );
				} else {
					throw new Exception( 'There is something wrong, please contact support.' );
				}
			}

			if ( ! isset( $registerResponse['registrant_id'] ) ) {
				if ( current_user_can( 'manage_options' ) ) {
					throw new Exception( 'There is no registrant id!', ZOOMPRESS_Shortcode::ALERT_FOR_ADMIN );
				} else {
					throw new Exception( 'There is something wrong, please contact support.' );
				}
			}

			wp_send_json_success( 'Congratualtions! You have successfully registered.' );
		} catch ( \Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}
	}
}

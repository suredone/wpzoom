<?php
/**
 * Register shortcode class
 *
 * @package WP_Zoom
 */
defined( 'ABSPATH' ) || exit;

class WPZOOM_ZoomRegisterShortcode extends WPZOOM_Shortcode {

	const ACTION_NONCE_KEY = 'wpzoom_register';

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

			wp_enqueue_script( 'wpzoom-register' );

			$template = new WPZOOM_Template();
			$template->templatePath = 'src/shortcodes/zoom_register/templates/';
			$template->templateName = 'register-form';
			$template->data = array(
				'buttonText' => $atts['button_text'],
				'webinarId' => $webinarId
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

			$firstName = isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
			$lastName = isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
			$email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
			$phone = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';

			$errors = [];

			if ( empty( $firstName ) ) {
				$errors['first_name'] = 'First name cannot be empty.';
			}

			if ( empty( $lastName ) ) {
				$errors['last_name'] = 'Last name cannot be empty.';
			}

			if ( empty( $email ) || ! is_email( $email ) ) {
				$errors['email'] = 'Add a valid email address.';
			}

			if ( count( $errors ) > 0 ) {
				throw new Exception( wp_json_encode( $errors ) );
			}

			$key    = WPZOOM_Settings::getTokenKey();
			$secret = WPZOOM_Settings::getTokenSecret();

			if ( empty( $key ) || empty( $secret ) ) {
				if ( current_user_can( 'manage_options' ) ) {
					throw new Exception( sprintf(
						'You did not setup Zoom API keys yet. Please %s and setup API keys first.',
						wpzoom_get_settings_page_anchor()
					), WPZOOM_Shortcode::ALERT_FOR_ADMIN );
				} else {
					throw new Exception( 'There is something wrong, please contact support.' );
				}
			}

			$zoomWebinar = new Zoom\Endpoint\Webinar( $key, $secret );
			$registerResponse = $zoomWebinar->register(
				$webinarId,
				array(
					'email' => $email,
					'first_name' => $firstName,
					'last_name' => $lastName,
					'phone' => $phone
				)
			);

			if ( wpzoom_is_error_response( $registerResponse ) ) {
				if ( current_user_can( 'manage_options' ) && $registerResponse['code'] === 124 ) {
					throw new Exception( sprintf(
						'Invalid Zoom API keys. Please %s and add valid API keys.',
						wpzoom_get_settings_page_anchor()
					), WPZOOM_Shortcode::ALERT_FOR_ADMIN );
				} else {
					throw new Exception( 'There is something wrong, please contact support.' );
				}
			}

			if ( ! isset( $registerResponse['registrant_id'] ) ) {
				if ( current_user_can( 'manage_options' ) ) {
					throw new Exception( 'There is no registrant id!', WPZOOM_Shortcode::ALERT_FOR_ADMIN );
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

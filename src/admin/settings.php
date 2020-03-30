<?php
/**
 * Settings handler
 */

defined( 'ABSPATH' ) || exit;

class WPZOOM_Settings {

	const PAGE_SLUG = 'wp-zoom-settings';

    const OPTION_JWT_KEYS = 'wp_zoom_keys';

    const OPTION_REGISTRATION_PAGE = 'wp_zoom_registration_page';

    const NONCE_ACTION_KEY = 'zoom_settings_process';

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'adminPage' ) );
        add_filter( 'display_post_states', array( $this, 'addStateLabel' ), 10, 2 );
        add_action( 'wp_ajax_' . self::NONCE_ACTION_KEY, array( $this, 'processSettingRequest' ) );
    }

    public function addStateLabel( $states, $post ) {
        if ( 'page' == get_post_type( $post->ID ) && ( $post->ID == self::getRegistrationPage() ) ) {
            $states['wp-zoom-registration-page'] = 'Webinar Registration Page';
        }

        return $states;
    }

    public function adminPage() {
        add_options_page(
            'WP Zoom Settings',
            'WP Zoom Settings',
            'manage_options',
            'wp-zoom-settings',
            array( $this, 'pageSettings' ),
            90
        );
    }

    public function pageSettings() {
        $template = new WPZOOM_Template();
        $template->templatePath = 'templates/';
        $template->templateName = 'settings-form';
        $template->data = array();
        print $template->get();
    }

    public static function getPageLink() {
        return admin_url( 'options-general.php?page=' . self::PAGE_SLUG );
    }

    public function processSettingRequest() {
		try {
			if ( ! wp_verify_nonce( $_POST['_wpnonce'], self::NONCE_ACTION_KEY ) ) {
				throw new Exception( 'Forbidden request, nice try though!', 403 );
			}

			$registration_page_id = isset( $_POST['field_registration_page'] ) ? absint( $_POST['field_registration_page'] ) : 0;
			update_option( self::OPTION_REGISTRATION_PAGE, $registration_page_id );

			if ( empty( $_POST['field_zoom_key'] ) || empty( $_POST['field_zoom_secret'] ) ) {
				throw new Exception( 'Token Key or Token Secret cannot be empty.' );
			}

			$key = sanitize_text_field( $_POST['field_zoom_key'] );
			$secret = sanitize_text_field( $_POST['field_zoom_secret'] );

			$zoomUsers = new Zoom\Endpoint\Users( $key, $secret );
            $userResponse = $zoomUsers->list();

			if ( wpzoom_is_error_response( $userResponse ) ) {
				throw new Exception( 'Invalid Token Key or Token Secret.' );
			}

			$jwtKeys = array(
				'key' => $key,
				'secret' => $secret
			);

			// update_option( self::OPTION_JWT_KEYS, $jwtKeys );
			wp_send_json_success( 'Congrats, changes have been updated.' );
		} catch ( Exception $e ) {
			wp_send_json_error( $e->getMessage(), $e->getCode() );
		}
    }

    public static function getTokenKey() {
        $jwtKeys = get_option( self::OPTION_JWT_KEYS );
        return isset( $jwtKeys['key'] ) ? $jwtKeys['key'] : '';
    }

    public static function getTokenSecret() {
        $jwtKeys = self::getToken();
        return isset( $jwtKeys['secret'] ) ? $jwtKeys['secret'] : '';
    }

    protected static function getToken() {
        return get_option( self::OPTION_JWT_KEYS, array() );
    }

    public static function getRegistrationPage() {
        return get_option( self::OPTION_REGISTRATION_PAGE, 0 );
    }

    public static function getRegistrationPageLink( $context = 'display' ) {
        $link = get_the_permalink( WPZOOM_Settings::getRegistrationPage() );

        if ( $context === 'edit' ) {
            return $link;
        }

        return esc_url( $link );
    }

    public static function getPages() {
        $out = array(
            0 => 'Select a page'
        );

        $pages = get_pages();

        if ( $pages ) {
            $pages = wp_list_pluck( $pages, 'post_title', 'ID' );
            $out = $out + $pages;
        }

        return $out;
    }
}

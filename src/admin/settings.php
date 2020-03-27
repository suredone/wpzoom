<?php
/**
 * Settings handler
 */

defined( 'ABSPATH' ) || exit;

class WPZOOM_Settings {

    const OPTION_JWT_KEYS = 'wp_zoom_keys';

    const OPTION_REGISTRATION_PAGE = 'wp_zoom_registration_page';

    const NONCE_ACTION_KEY = 'zoom_settings_process';

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'admin_page' ) );
        add_filter( 'display_post_states', array( $this, 'add_state_label' ), 10, 2 );
        add_action( 'admin_post_' . self::NONCE_ACTION_KEY, array( $this, 'pageSettingsProcess' ) );
    }

    public function add_state_label( $states, $post ) { 
        if ( 'page' == get_post_type( $post->ID ) && ( $post->ID == self::getRegistrationPage() ) ) {
            $states['wp-zoom-registration-page'] = 'Webinar Registration Page';
        }

        return $states;
    }   

    public function admin_page() {
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
        return admin_url( 'options-general.php?page=wp-zoom-settings' );
    }

    public function pageSettingsProcess() {
        $verified = wp_verify_nonce( $_POST['_wpnonce'], self::NONCE_ACTION_KEY );

        if ( ! $verified ) {
          admin_notices('Invalid nonce.');
          wp_redirect( self::getPageLink() );
        }
    
        $key = isset( $_POST['field-zoom-key'] ) ? sanitize_text_field( $_POST['field-zoom-key'] ) : '';
        $secret = isset( $_POST['field-zoom-secret'] ) ? sanitize_text_field( $_POST['field-zoom-secret'] ) : '';
        $registration_page = isset( $_POST['field-registration-page'] ) ? absint( $_POST['field-registration-page'] ) : 0;
    
        $jwtKeys = array(
          'key' => $key,
          'secret' => $secret
        );
    
        update_option( self::OPTION_JWT_KEYS, $jwtKeys );
        update_option( self::OPTION_REGISTRATION_PAGE, $registration_page );

        wp_redirect( self::getPageLink() );
        exit;
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

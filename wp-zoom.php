<?php

/**
 *
 * Plugin Name: WP Zoom
 * Plugin URI: https://eatbuildplay.com/plugins/wp-zoom/
 * Description: Integrate Zoom webinars into WordPress
 * Version: 1.0.0
 * Author: Casey Milne, Eat/Build/Play
 * Author URI: https://eatbuildplay.com/
 */

define( 'WP_ZOOM_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'WP_ZOOM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WP_ZOOM_VERSION', '1.0.0' );

class WPZOOM_Plugin {

  public function __construct() {

    require_once( WP_ZOOM_PLUGIN_PATH . 'zoom/vendor/autoload.php' );
    require_once( WP_ZOOM_PLUGIN_PATH . 'src/endpoints/Webinar.php' );
		require_once( WP_ZOOM_PLUGIN_PATH . 'src/endpoints/Recording.php' );

    require_once( WP_ZOOM_PLUGIN_PATH . 'src/shortcodes/zoom_webinar/shortcode.php' );
    require_once( WP_ZOOM_PLUGIN_PATH . 'src/shortcodes/zoom_calendar/shortcode.php' );
		require_once( WP_ZOOM_PLUGIN_PATH . 'src/shortcodes/zoom_recording/shortcode.php' );
    require_once( WP_ZOOM_PLUGIN_PATH . 'src/shortcodes/zoom_register/shortcode.php' );

    require_once( WP_ZOOM_PLUGIN_PATH . 'src/template.php' );

    /* init shortcodes */
    new WPZOOM_ZoomWebinarShortcode();
    new WPZOOM_ZoomCalendarShortcode();
		new WPZOOM_ZoomRecordingShortcode();
    new WPZOOM_ZoomRegisterShortcode();

    add_action('wp_enqueue_scripts', array( $this, 'scripts' ));
    add_action('admin_menu', array( $this, 'admin'));
    add_action('admin_post_zoom_settings_process', array( $this, 'pageSettingsProcess'));

  }

  public function admin() {

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

  public function pageSettingsProcess() {

    $verified = wp_verify_nonce( $_POST['_wpnonce'] );
    if( !$verified ) {
      admin_notices('Invalid nonce.');
      wp_redirect(admin_url('options-general.php?page=wp-zoom-settings'));
    }

    $key = $_POST['field-zoom-key'];
    $secret = $_POST['field-zoom-secret'];

    $jwtKeys = array(
      'key' => $key,
      'secret' => $secret
    );

    update_option( 'wp_zoom_keys', $jwtKeys );
    $redirectUrl = admin_url('options-general.php?page=wp-zoom-settings');
    wp_redirect( $redirectUrl );

  }

  public static function getTokenKey() {
    $jwtKeys = get_option( 'wp_zoom_keys' );
    return $jwtKeys['key'];
  }

  public static function getTokenSecret() {
    $jwtKeys = get_option( 'wp_zoom_keys' );
    return $jwtKeys['secret'];
  }

  public function scripts() {

    wp_enqueue_script(
      'wp-zoom-register-js',
      WP_ZOOM_PLUGIN_URL . 'src/shortcodes/zoom_register/assets/zoom_register.js',
      array( 'jquery' ),
      '1.0.0',
      true
    );

    wp_enqueue_script(
      'full-calendar-core-js',
      'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/core/main.min.js',
      array(),
      '4.2.0',
      true
    );

    wp_enqueue_script(
      'full-calendar-daygrid',
      'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/daygrid/main.min.js',
      array('full-calendar-core-js'),
      '4.2.0',
      true
    );

    wp_enqueue_script(
      'zoom-calendar-js',
      WP_ZOOM_PLUGIN_URL . 'src/shortcodes/zoom_calendar/assets/zoom-calendar.js',
      array('full-calendar-core-js'),
      '1.0.0',
      true
    );

    wp_enqueue_style(
      'full-calendar-core-style',
      'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/core/main.min.css',
      array(),
      '4.2.0',
      true
    );

    wp_enqueue_style(
      'full-calendar-daygrid-style',
      'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/daygrid/main.min.css',
      array('full-calendar-core-style'),
      '4.2.0',
      true
    );

  }

}

new WPZOOM_Plugin();

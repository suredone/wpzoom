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

  }

  public static function getTokenKey() {
    return 'MzjAyttgT76CM79z47S1kA';
  }

  public static function getTokenSecret() {
    return 'TZLZVlQnoo0APtFaaTYfFb4UudC4EgYL3AoR';
  }

  public function scripts() {

    wp_enqueue_script(
      'wp-zoom-register-js', 
      WP_ZOOM_PLUGIN_URL . 'src/shortcodes/zoom_register/assets/zoom_register.js',
      array( 'jquery' ),
      '1.0.0',
      true
    );

  }

}

new WPZOOM_Plugin();

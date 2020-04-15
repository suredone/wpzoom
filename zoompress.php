<?php

/**
 *
 * Plugin Name: ZoomPress
 * Plugin URI: https://eatbuildplay.com/plugins/zoompress
 * Description: Integrates Zoom API features for WordPress
 * Version: 1.0.0
 * Author: Casey Milne, Eat/Build/Play
 * Author URI: https://eatbuildplay.com/
 */

define( 'ZOOMPRESS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'ZOOMPRESS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ZOOMPRESS_VERSION', '1.0.0' );

class ZOOMPRESS_Plugin {

	public function __construct() {
		require_once( ZOOMPRESS_PLUGIN_PATH . 'zoom/vendor/autoload.php' );
		require_once( ZOOMPRESS_PLUGIN_PATH . 'src/endpoints/Webinar.php' );
		require_once( ZOOMPRESS_PLUGIN_PATH . 'src/endpoints/Recording.php' );

		require_once( ZOOMPRESS_PLUGIN_PATH . 'src/class/shortcode-abstract.php' );
		require_once( ZOOMPRESS_PLUGIN_PATH . 'src/shortcodes/zoom_webinar/shortcode.php' );
		require_once( ZOOMPRESS_PLUGIN_PATH . 'src/shortcodes/zoom_calendar/shortcode.php' );
		require_once( ZOOMPRESS_PLUGIN_PATH . 'src/shortcodes/zoom_recording/shortcode.php' );
		require_once( ZOOMPRESS_PLUGIN_PATH . 'src/shortcodes/zoom_register/shortcode.php' );
		require_once( ZOOMPRESS_PLUGIN_PATH . 'src/shortcodes/zoom_webinar/recurring.php' );
		require_once( ZOOMPRESS_PLUGIN_PATH . 'src/shortcodes/zoom_register/form.php' );

		require_once( ZOOMPRESS_PLUGIN_PATH . 'src/template.php' );
		require_once( ZOOMPRESS_PLUGIN_PATH . 'src/admin/settings.php' );
		require_once( ZOOMPRESS_PLUGIN_PATH . 'src/functions.php' );

		/* init shortcodes */
		new ZOOMPRESS_ZoomWebinarShortcode();
		new ZOOMPRESS_ZoomCalendarShortcode();
		new ZOOMPRESS_ZoomRecordingShortcode();
		new ZOOMPRESS_ZoomRegisterShortcode();
		new ZOOMPRESS_Settings();

		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueAdminScripts' ) );
	}

	public function enqueueAdminScripts( $hook ) {
		if ( ! isset( $_GET['page'] ) || $_GET['page'] !== ZOOMPRESS_Settings::PAGE_SLUG ) {
			return;
		}

		wp_enqueue_script(
			'wp-zoom-admin',
			ZOOMPRESS_PLUGIN_URL . 'assets/admin-script.js',
			array( 'jquery' ),
			ZOOMPRESS_VERSION,
			true
		);
	}

	public function scripts() {
		wp_enqueue_style(
			'full-calendar-core-style',
			'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/core/main.min.css',
			array(),
			'4.2.0'
		);

		wp_enqueue_style(
			'full-calendar-daygrid-style',
			'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/daygrid/main.min.css',
			array('full-calendar-core-style'),
			'4.2.0'
		);

		wp_enqueue_style(
			'wp-zoom-style',
			ZOOMPRESS_PLUGIN_URL . 'assets/zoompress-styles.css',
			array('full-calendar-daygrid-style'),
			ZOOMPRESS_VERSION
		);

		wp_register_script(
			'full-calendar-core-js',
			'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/core/main.min.js',
			array(),
			'4.2.0',
			true
		);

		wp_register_script(
			'full-calendar-daygrid',
			'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/daygrid/main.min.js',
			array( 'full-calendar-core-js' ),
			'4.2.0',
			true
		);

		wp_register_script(
			'zoom-calendar-js',
			ZOOMPRESS_PLUGIN_URL . 'src/shortcodes/zoom_calendar/assets/zoom-calendar.js',
			array( 'full-calendar-daygrid' ),
			'1.0.0',
			true
		);

		wp_register_script(
			'zoompress-register',
			ZOOMPRESS_PLUGIN_URL . 'src/shortcodes/zoom_register/assets/zoom_register.js',
			array( 'jquery' ),
			ZOOMPRESS_VERSION,
			true
		);
	}
}

new ZOOMPRESS_Plugin();

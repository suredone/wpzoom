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

    require_once( WP_ZOOM_PLUGIN_PATH . '/zoom/vendor/autoload.php' );

    $key = 'BLZk_6UkTF6nRpSicuVpaw';
    $secret = 's5rL5vY3Hz7BsM2yHtifV2HFnVlNRwnSTMjF';

    $zoomApi = new \Zoom\ZoomAPI( $key, $secret );

    print '<pre>';
    var_dump( $zoomApi );
    print '</pre>';
    die();

  }

}

new WPZOOM_Plugin();

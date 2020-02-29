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
    require_once( WP_ZOOM_PLUGIN_PATH . 'src/shortcodes/zoom_webinar/shortcode.php' );
    require_once( WP_ZOOM_PLUGIN_PATH . 'src/template.php' );


    /* E/B/P */
    //$key = 'BLZk_6UkTF6nRpSicuVpaw';
    //$secret = 's5rL5vY3Hz7BsM2yHtifV2HFnVlNRwnSTMjF';

    /* SureDone */
    $key    = 'MzjAyttgT76CM79z47S1kA';
    $secret = 'TZLZVlQnoo0APtFaaTYfFb4UudC4EgYL3AoR';

    $zoomUsers = new Zoom\Endpoint\Users( $key, $secret );
    $userResponse = $zoomUsers->list();
    $userFirst = $userResponse['users'][0];

    /*
    print '<pre>';
    var_dump( $users );
    print '</pre>';
    */

    $zoomWebinar = new Zoom\Endpoint\Webinar( $key, $secret );
    $webinarResponse = $zoomWebinar->list( $userFirst['id'], [] );

/*
    print '<pre>';
    var_dump( $webinarResponse );
    print '</pre>';

    die();

    */

    /* init shortcodes */
    new WPZOOM_ZoomWebinarShortcode();


    /* test templating */
    $template = new WPZOOM_Template();
    $template->templatePath = 'src/shortcodes/zoom_webinar/templates/';
    $template->templateName = 'table';
    $template->data = array(
      'varsity' => 'kim'
    );
    $template->render();

  }

}

new WPZOOM_Plugin();

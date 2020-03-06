<?php

class WPZOOM_ZoomCalendarShortcode {

  public $tag = 'zoom_calendar';

  public function __construct() {
    add_action('init', array( $this, 'init'));
  }

  public function init() {
    add_shortcode($this->tag, array($this, 'doShortcode'));
  }

  public function doShortcode( $atts ) {

    $atts = shortcode_atts( array(
      'include' => 'include',
      'exclude' => 'exclude',
      'hide' => 'hide',
      'days' => 'days'
    ), $atts, 'zoom_webinar' );

    $key    = 'MzjAyttgT76CM79z47S1kA';
    $secret = 'TZLZVlQnoo0APtFaaTYfFb4UudC4EgYL3AoR';
    $zoomUsers = new Zoom\Endpoint\Users( $key, $secret );
    $userResponse = $zoomUsers->list();
    $userFirst = $userResponse['users'][0];


    $zoomWebinar = new Zoom\Endpoint\Webinar( $key, $secret );
    $webinarResponse = $zoomWebinar->list( $userFirst['id'], [] );

    /* test templating */
    $template = new WPZOOM_Template();
    $template->templatePath = 'src/shortcodes/zoom_calendar/templates/';
    $template->templateName = 'table';
    $template->data = array(
      'webinarResponse' => $webinarResponse,
      'webinars' => $webinarResponse['webinars']
    );
    return $template->get();

  }

}

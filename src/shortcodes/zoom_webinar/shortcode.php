<?php

class WPZOOM_ZoomWebinarShortcode {

  public $tag = 'zoom_webinar';

  public function __construct() {
    add_action('init', array( $this, 'init'));
  }

  public function init() {
    add_shortcode($this->tag, array($this, 'doShortcode'));
  }

  public function doShortcode( $atts ) {

    $atts = shortcode_atts( array(
      'include' => '',
      'exclude' => '',
      'hide'    => '',
      'days'    => ''
    ), $atts, 'zoom_webinar' );

    $key    = WPZOOM_Settings::getTokenKey();
    $secret = WPZOOM_Settings::getTokenSecret();
    $zoomUsers = new Zoom\Endpoint\Users( $key, $secret );
    $userResponse = $zoomUsers->list();

    $userFirst = $userResponse['users'][0];

    $zoomWebinar = new Zoom\Endpoint\Webinar( $key, $secret );
    $webinarResponse = $zoomWebinar->list( $userFirst['id'], [] );

    // prepare webinars and do processing
    $webinars = array();
    if( !empty( $webinarResponse['webinars'] )) {

      $webinarsSorted = array_reverse( $webinarResponse['webinars'] );

      foreach( $webinarsSorted as $webinarData ) :

        $webinar = new stdClass;
        $title = $webinarData['topic'];
        $start = $webinarData['start_time'];

        // check if contains exclude words
        if( $atts['exclude'] != '' ) {
          if( $this->excludeFilter( $title, $atts['exclude'] )) {
            continue;
          }
        }

        // check if contains include words
        if( $atts['include'] != '' ) {
          if( $this->includeFilter( $title, $atts['include'] )) {
            continue;
          }
        }

        // check days into future filter
        if( $atts['days'] != '' ) {
          $days = $atts['days'];
        } else {
          $days = 365;
        }
        if( $this->daysFilter( $start, $days )) {
          continue;
        }

        // handle title filtering hide
        if( $atts['hide'] != '' ) {
          $title = $this->prepareTitle( $title, $atts['hide'] );
        }
        $webinar->title = $title;

        // get the start time
        $startTime = $webinarData['start_time'];
        $dateTime = DateTime::createFromFormat( 'Y-m-d?H:i:s?', $startTime );
        $webinar->start = $dateTime->format( 'Y-m-d' ) . ' at ' . $dateTime->format( 'g:iA' );
        $webinar->duration = $webinarData['duration'];

        // stash webinar object
        $webinars[] = $webinar;

      endforeach;

    } else {
      return 'No webinars to show';
    }

    /* test templating */
    $template = new WPZOOM_Template();
    $template->templatePath = 'src/shortcodes/zoom_webinar/templates/';
    $template->templateName = 'table';
    $template->data = array(
      'webinarResponse' => $webinarResponse,
      'webinars' => $webinars
    );
    return $template->get();

  }

  public function prepareTitle( $originalTitle, $hide ) {
    return str_replace( $hide, '', $originalTitle );
  }

  public function excludeFilter( $title, $exclude ) {
    if( strpos( $title, $exclude ) !== false ) {
      return true;
    }
  }

  public function includeFilter( $title, $include ) {
    $result = strpos( $title, $include );
    if( strpos( $title, $include ) === false ) {
      return true;
    }
  }

  public function daysFilter( $start, $days ) {

    $dateStart = substr( $start, 0, 10 );

    $datetime1 = new DateTime( $dateStart );
    $datetime2 = new DateTime( date('Y-m-d') );
    $interval = $datetime2->diff($datetime1);

    $sign = $interval->format('%R');
    if( $sign == '-' ) {
      return true; // start in the past!
    }

    $daysAway = $interval->format('%a');
    if( $daysAway > $days ) {
      return true;
    }

  }

}

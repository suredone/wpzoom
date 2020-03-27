<?php

class WPZOOM_ZoomRecordingShortcode {

  public $tag = 'zoom_recording';

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
    ), $atts, $this->tag );

    $key    = WPZOOM_Settings::getTokenKey();
    $secret = WPZOOM_Settings::getTokenSecret();
    $zoomUsers = new Zoom\Endpoint\Users( $key, $secret );
    $userResponse = $zoomUsers->list();
    $userFirst = $userResponse['users'][0];


    $zoomRecording = new Zoom\Endpoint\Recording( $key, $secret );

    if( $atts['days'] ) {
      $daysAgo = $atts['days'];
    } else {
      $daysAgo = 30;
    }
    $fromTime = strtotime("-" . $daysAgo . " days");
		$fromDate = date('Y-m-d', $fromTime);

    /*
    print '<pre>';
    var_dump( $fromDate );
    print '</pre>';
    */

		$args = array(
			'from' => $fromDate
		);
    $response = $zoomRecording->list( $userFirst['id'], $args );

    /*
    print '<pre>';
    var_dump( $response );
    print '</pre>';
    */

    if( empty( $response['meetings'] )) {
      return 'No recordings found.';
    }

    $meetingsResponse = $response['meetings'];

    /*
    print '<pre>';
    var_dump($meetingsResponse);
    print '</pre>';
    */

    $meetings = array();
    foreach( $meetingsResponse as $meetingData ) {

      $meeting = new stdClass;
      $meeting->start = $meetingData['start_time'];
      $meeting->title = $meetingData['topic'];

      // check if contains exclude words
      if( $atts['exclude'] != '' ) {
        if( $this->excludeFilter( $meeting->title, $atts['exclude'] )) {
          continue;
        }
      }

      // check if contains include words
      if( $atts['include'] != '' ) {
        if( $this->includeFilter( $meeting->title, $atts['include'] )) {
          continue;
        }
      }

      $meeting->recording_files = $meetingData['recording_files'];
      $meeting->recording_count = $meetingData['recording_count'];

      $meetings[] = $meeting;
    }

    /*
    print '<pre>';
    var_dump($meetings);
    print '</pre>';
    */

    $template = new WPZOOM_Template();
    $template->templatePath = 'src/shortcodes/zoom_recording/templates/';
    $template->templateName = 'table';
    $template->data = array(
      'response' => $response,
      'meetings' => $meetings
    );
    return $template->get();

  }

  public function prepareTitle( $originalTitle, $hide ) {
    return str_replace( $hide, '', $originalTitle );
  }

  public function includeFilter( $title, $include ) {
    $result = strpos( $title, $include );
    if( strpos( $title, $include ) === false ) {
      return true;
    }
  }

  public function excludeFilter( $title, $exclude ) {
    if( strpos( $title, $exclude ) !== false ) {
      return true;
    }
  }

  public function daysFilter( $start, $days ) {

    $dateStart = substr( $start, 0, 10 );

    $datetime1 = new DateTime( $dateStart );
    $datetime2 = new DateTime( date('Y-m-d') );
    $interval = $datetime2->diff($datetime1);

    $sign = $interval->format('%R');

    $daysPast = $interval->format('%a');
    if( $daysPast > $days ) {
      return true;
    }

  }

}

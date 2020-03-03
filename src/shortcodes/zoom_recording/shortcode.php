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
      'foo' => 'no foo',
      'baz' => 'default baz'
    ), $atts, 'bartag' );

    $key    = 'MzjAyttgT76CM79z47S1kA';
    $secret = 'TZLZVlQnoo0APtFaaTYfFb4UudC4EgYL3AoR';
    $zoomUsers = new Zoom\Endpoint\Users( $key, $secret );
    $userResponse = $zoomUsers->list();
    $userFirst = $userResponse['users'][0];


    $zoomRecording = new Zoom\Endpoint\Recording( $key, $secret );
		$args = array(
			'to' => date('yyyy-MM-dd T HH:mm:ssZ')
		);
    $recordingResponse = $zoomRecording->list( $userFirst['id'], [] );

		print '<pre>';
		var_dump($recordingResponse);
		print '</pre>';

    $template = new WPZOOM_Template();
    $template->templatePath = 'src/shortcodes/zoom_recording/templates/';
    $template->templateName = 'table';
    $template->data = array(
      'response' => $recordingResponse,
      'recordings' => $recordingResponse['recordings']
    );
    return $template->get();

  }

}

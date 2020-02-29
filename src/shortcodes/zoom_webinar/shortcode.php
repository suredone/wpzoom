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
      'foo' => 'no foo',
      'baz' => 'default baz'
    ), $atts, 'bartag' );

    $content = 'WEBINAR TABLE!';
    return $content;

  }

}

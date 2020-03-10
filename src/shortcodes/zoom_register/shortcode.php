<?php

class WPZOOM_ZoomRegisterShortcode {

  public $tag = 'zoom_register';

  public function __construct() {
    add_action('init', array( $this, 'init'));
  }

  public function init() {
    add_shortcode($this->tag, array($this, 'doShortcode'));
  }

  public function doShortcode( $atts ) {

    $atts = shortcode_atts( array(
      'foo' => 'no foo',
    ), $atts, $this->tag );

    $template = new WPZOOM_Template();
    $template->templatePath = 'src/shortcodes/zoom_register/templates/';
    $template->templateName = 'register-form';
    $template->data = array();
    return $template->get();

  }

  public function processForm() {

    $prefix = 'zoom-field-';
    $post = $_POST;

    // set field data vars
    $name = $post[ $prefix . 'name' ];
    $email = $post[ $prefix . 'email' ];

    // make call to zoom api to do registration


    // handle failure (show notices)


    // on success return confirmation


  }

}

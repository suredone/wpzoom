<?php

class WPZOOM_Template {

  public $data = [];
  public $templatePath;
  public $templateName;

  public function __construct() {

    $this->templatePath = 'templates/';
    $this->templateName = 'tester';

  }

  public function get() {
    if( is_array( $this->data ) && !empty( $this->data )) {
      extract( $this->data );
    }
    require( WP_ZOOM_PLUGIN_PATH . $this->templatePath . $this->templateName . '.php' );
  }

  public function render() {
    print $this->get();
  }


}

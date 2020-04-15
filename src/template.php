<?php

class ZOOMPRESS_Template {

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
    ob_start();
    require( ZOOMPRESS_PLUGIN_PATH . $this->templatePath . $this->templateName . '.php' );
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
  }

  public function render() {
    print $this->get();
  }


}

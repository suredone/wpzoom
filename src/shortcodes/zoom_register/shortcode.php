<?php
/**
 * Register shortcode class
 *
 * @package WP_Zoom
 */
defined( 'ABSPATH' ) || exit;

class WPZOOM_ZoomRegisterShortcode extends WPZOOM_Shortcode {

	public function getTag() {
		return 'zoom_register';
	}

	public function doShortcode( $atts, $content = null ) {
		$atts = shortcode_atts( array(
			'foo' => 'no foo',
		), $atts, $this->getTag() );

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

<?php
/**
 * Shortcode abstract class.
 *
 * @package WP_Zoom
 */

defined( 'ABSPATH' ) || exit;

abstract class ZOOMPRESS_Shortcode {

	/**
	 * Admin only alert
	 */
	const ALERT_FOR_ADMIN = 555;

	/**
	 * For all
	 */
	const ALERT_FOR_ALL = 444;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Add shortcode on init
	 *
	 * @return void
	 */
	public function init() {
		add_shortcode( $this->getTag(), array( $this, 'doShortcode' ) );
	}

	/**
	 * Process shortcode output
	 *
	 * @return string
	 */
	abstract public function doShortcode( $atts, $content = null );

	/**
	 * Get shortcode tag
	 *
	 * @return string
	 */
	abstract public function getTag();
}

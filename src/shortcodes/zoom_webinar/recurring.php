<?php
/**
 * Recurring webinar class
 *
 * @package WP_Zoom
 */
defined( 'ABSPATH' ) || exit;

class WPZOOM_Recurring_Webinar {

	protected $webinars = null;

	public function __construct($requestInstance, $webinar) {
		$this->parentWebinar = $webinar;
		$this->webinars = $requestInstance->getDetails($this->parentWebinar->id);
	}

	protected function has() {
		if ( is_null($this->webinars) ) {
			return false;
		}

		if ( ! isset($this->webinars['occurrences']) || ! is_array($this->webinars['occurrences']) ) {
			return false;
		}

		return true;
	}

	public function render() {
		if (!$this->has()) {
			return;
		}

		foreach ($this->webinars['occurrences'] as $webinar ) :
			?>
			<tr>
				<td><?php echo 'Re: ' . $this->parentWebinar->title; ?></td>
				<td><?php echo $this->getFormattedTime($webinar['start_time']); ?></td>
				<td><?php echo $webinar['duration']; ?> Minutes</td>
				<td><?php wpzoom_webinar_register_button($this->parentWebinar, $webinar['occurrence_id']); ?></td>
			</tr>
			<?php
		endforeach;
	}

	protected function getFormattedTime( $time ) {
		$dateTime = new DateTime( $time );
		$dateTime->setTimezone( wpzoom_timezone() ); // Had to set forcefully
		return $dateTime->format( 'Y-m-d' ) . ' at ' . $dateTime->format( 'g:iA' );
	}
}

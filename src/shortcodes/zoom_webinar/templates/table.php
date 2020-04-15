<?php
/**
 * Webinar template
 *
 * @package WP_Zoom
 */
defined( 'ABSPATH' ) || exit;

?>
<div class="zoompress-shortcode-webinar">
	<table class="zoompress-table">
		<thead>
			<tr>
				<th>Title</th>
				<th>Start</th>
				<th>Duration</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach( $webinars as $webinar ) : ?>
			<tr>
				<td><?php print $webinar->title; ?></td>
				<td><?php print $webinar->start; ?></td>
				<td><?php print $webinar->duration; ?> Minutes</td>
				<td><?php zoompress_webinar_register_button($webinar); ?></td>
			</tr>
			<?php
			if ( $webinar->isRecurring ) {
				$recurringWebinars = new ZOOMPRESS_Recurring_Webinar($zoomWebinar,$webinar);
				$recurringWebinars->render();
			}
			?>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>

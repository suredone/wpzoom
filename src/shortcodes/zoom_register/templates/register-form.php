<div class="zoompress-registration">
	<h2>Webinar Registration</h2>

	<form id="zoompress-registration-form" method="POST" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
		<input type="hidden" name="action" value="<?php echo ZOOMPRESS_ZoomRegisterShortcode::ACTION_NONCE_KEY; ?>">
		<input type="hidden" name="webinar_id" value="<?php echo esc_attr( $webinarId ); ?>">
		<input type="hidden" name="occurrence_id" value="<?php echo ( isset( $_GET['oid'] ) ? esc_attr( $_GET['oid'] ) : 0 ); ?>">
		<?php wp_nonce_field( ZOOMPRESS_ZoomRegisterShortcode::ACTION_NONCE_KEY ); ?>

		<?php
		$form = new ZOOMPRESS_Registration_Form($webinarQuestions);
		$form->printFields();
		?>

		<div class="zoompress-form-field">
			<input disabled class="zoompress-register-submit" value="<?php echo esc_attr( $buttonText ); ?>" type="submit" />
		</div>

		<div class="zoompress-form-spinner" role="presentation">
			<img src="<?php echo esc_url( get_admin_url() . 'images/spinner.gif' ); ?>" />
		</div>
	</form>
</div>

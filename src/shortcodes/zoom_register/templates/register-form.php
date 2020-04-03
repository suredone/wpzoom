<div class="wpzoom-registration">
	<h2>Webinar Registration</h2>

	<form id="wpzoom-registration-form" method="POST" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
		<input type="hidden" name="action" value="<?php echo WPZOOM_ZoomRegisterShortcode::ACTION_NONCE_KEY; ?>">
		<input type="hidden" name="webinar_id" value="<?php echo esc_attr( $webinarId ); ?>">
		<?php wp_nonce_field( WPZOOM_ZoomRegisterShortcode::ACTION_NONCE_KEY ); ?>

		<div class="wpzoom-form-field">
			<label for="wpzoom-field-first-name">First Name (required)</label>
			<input class="wpzoom-input-field wpzoom-field-first-name" name="first_name" id="wpzoom-field-first-name" type="text" />
		</div>

		<div class="wpzoom-form-field">
			<label for="wpzoom-field-last-name">Last Name (required)</label>
			<input class="wpzoom-input-field wpzoom-field-last-name" name="last_name" id="wpzoom-field-last-name" type="text" />
		</div>

		<div class="wpzoom-form-field">
			<label for="wpzoom-field-email">Email (required)</label>
			<input class="wpzoom-input-field wpzoom-field-email" name="email" id="wpzoom-field-email" type="email" />
		</div>

		<div class="wpzoom-form-field">
			<label for="wpzoom-field-phone">Phone</label>
			<input class="wpzoom-input-field wpzoom-field-phone" name="phone" id="wpzoom-field-phone" type="text" />
		</div>

		<div class="wpzoom-form-field">
			<input class="wpzoom-register-submit" value="<?php echo esc_attr( $buttonText ); ?>" type="submit" />
		</div>
	</form>
</div>

;(function($) {

	$(function() {
		$zoomForm = $('#wp-zoom-settings'),
		$spinner = $zoomForm.find('.spinner'),
		$submitButton = $zoomForm.find('input[type="submit"]');

		function showMessage(msg, type) {
			type = type || 'success';
			var msg = '<div class="notice notice-'+type+'"><p>'+msg+'</p></div>';
			$zoomForm.find('.notice').remove();
			$zoomForm.find('table').before($(msg));
		}

		$zoomForm.on('submit', function(event) {
			event.preventDefault();

			$.ajax({
                url: $zoomForm.attr('action'),
                data: new FormData(this),
                processData: false,
                contentType: false,
				type: 'POST',
				beforeSend: function() {
					$zoomForm.find('#field-zoom-key, #field-zoom-secret').css('border-color', '');
					$spinner.addClass('is-active');
					$submitButton.attr('disabled', true).addClass('disabled');
				},
            }).done(function(response) {
				if (response.success) {
					showMessage(response.data);
				} else {
					$zoomForm.find('#field-zoom-key, #field-zoom-secret').css('border-color', '#dc3232');
					showMessage(response.data, 'error');
				}

				$spinner.removeClass('is-active');
				$submitButton.attr('disabled', false).removeClass('disabled');
            });
		});
	});

}(jQuery));

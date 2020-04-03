;(function($) {

	$(function() {
		$zoomForm = $('#wpzoom-registration-form'),
		$zoomParent = $zoomForm.parent('.wpzoom-registration'),
		$spinner = $zoomForm.find('.spinner'),
		$submitButton = $zoomForm.find('input[type="submit"]');

		function showMessage(msg, type) {
			var msgArr = msg.split('\n');
			type = type || 'info';

			if ( msgArr.length > 1 ) {
				msgArr = msgArr.map(function(msg) {
					return '<li>' + msg + '</li>';
				});
				msg = '<ul>' + msgArr.join('\n') + '</ul>';
			}

			msg = '<div class="wpzoom-alert wpzoom-alert--'+type+'">'+msg+'</div>';

			$zoomParent.find('.wpzoom-alert').remove();
			$zoomForm.before($(msg));
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
					$submitButton.attr('disabled', true).addClass('disabled');
				},
            }).done(function(response) {
				if (response.success) {
					showMessage(response.data, 'success');
				} else {
					showMessage(response.data, 'error');
				}
				$submitButton.attr('disabled', false).removeClass('disabled');
            });
		});
	});

}(jQuery));

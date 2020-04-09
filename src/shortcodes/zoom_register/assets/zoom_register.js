;(function($) {

	$(function() {
		$zoomForm = $('#wpzoom-registration-form'),
		$zoomParent = $zoomForm.parent('.wpzoom-registration'),
		$spinner = $zoomForm.find('.spinner'),
		$submitButton = $zoomForm.find('input[type="submit"]');

		$submitButton.attr('disabled', false);

		$zoomForm.on('keyup', '.wpzoom-input-field', function() {
			$(this).removeClass('wpzoom-input-field--error');
		});

		function showMessage(msg, type) {
			type = type || 'info';
			msg = (msg.indexOf('{') === 0) ? JSON.parse(msg) : msg;

			if (typeof msg === 'object') {
				var ma = [], mks = [];
				for (var mk in msg) {
					ma.push('<li>' + msg[mk] + '</li>');
					mks.push('[name="'+mk+'"]');
				}
				msg = '<ul>' + ma.join('\n') + '</ul>';
				$zoomForm.find($(mks.join(','))).addClass('wpzoom-input-field--error');
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
					$zoomForm.addClass('wpzoom-registration-form--processing');
					$zoomForm.find('.wpzoom-input-field--error').removeClass('.wpzoom-input-field--error');
					$submitButton.attr('disabled', true).addClass('disabled');
				},
            }).done(function(response) {
				if (response.success) {
					showMessage(response.data, 'success');
				} else {
					showMessage(response.data, 'error');
				}
				$zoomForm.removeClass('wpzoom-registration-form--processing');
				$submitButton.attr('disabled', false).removeClass('disabled');
            });
		});
	});

}(jQuery));

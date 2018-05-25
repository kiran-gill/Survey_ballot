jQuery(document).ready(function($) {
	var inbound_setup_is_error = false;

	$('.image-picker').imagepicker({
		hide_select: true,
		show_label: true
	});

	$('.has-tip').frosty();

	$('#setup-console-toggle').click(function(e) {
		e.preventDefault();
		$('#setup-console').toggle();
	});

	$('#start-template-setup').click(function(e) {
		e.preventDefault();
		$('#start-template-setup').prop("disabled", true);

		$('#setup-template').loadingOverlay({
			loadingClass: 'loading',          // Class added to target while loading
			overlayClass: 'loading-overlay',  // Class added to overlay (style with CSS)
			spinnerClass: 'loading-spinner',  // Class added to loading overlay spinner
			iconClass: 'loading-icon',        // Class added to loading overlay spinner
			textClass: 'loading-text',        // Class added to loading overlay spinner
			loadingText: 'Magic in progress'  // Text within loading overlay
		});

		var folder = $('#theme-demo-template').val();
		inbound_setup_ajax ( 'init', folder, 'Preparing import', 0 );
	});


	function inbound_setup_error( message ) {
		inbound_setup_is_error = true;
		$('#setup-template').loadingOverlay('remove', {
			loadingClass: 'loading',
			overlayClass: 'loading-overlay'
		});

		$('.setup-block').hide();
		$('#setup-error-message').html ( message );
		$('#setup-error').show();

		$('#start-template-setup').prop("disabled", false);

		document.body.scrollTop = document.documentElement.scrollTop = 0;

	}

	function inbound_setup_ajax( action, folder, status_message, last ) {
		ajax_nonce = $('#_wpnonce').val();

		$('.loading-text').html( status_message );

		var
				make_front   = $("#make_front").is(':checked'),
				make_default = $("#make_default").is(':checked'),
				skip_images  = $("#skip_images").is(':checked');

		$.ajax({
			type: "POST",
			dataType: "json",
			url: ajaxurl,
			data: {
				// Send JSON back to PHP for eval
				action : "inbound_setup_" + action,
				last: last,
				folder: folder,
				make_front: make_front,
				make_default: make_default,
				_ajax_nonce: ajax_nonce
			},
			success: function(response) {
				// Process JSON sent from PHP
				if(response.type == "success") {
					// Set new nonce
					_ajax_nonce = response.newNonce;
					$('#_wpnonce').val( _ajax_nonce );

					if (!action.length) {
						inbound_setup_error( 'Unexpected response from server.' );
					}

					if ( action == 'images' && response.run_again ) {
						action = 'init';
					}

					if (action == 'init' )
						if ( ! skip_images ) {
							// we haven't opted to skip images, so let's import them
							if ( response.run_again > 0 ) {
								last = response.run_again;
							}
							inbound_setup_ajax ( 'images', folder, 'Importing attached images (' + last + ')', last );

						} else {
							// we don't want images imported, so skip to the next step
							inbound_setup_ajax ( 'profiles', folder, 'Skipping Images. Importing profiles', 0 );
						}
					if (action == 'images' )
						inbound_setup_ajax ('profiles', folder, 'Importing profiles', 0 );
					if (action == 'profiles' )
						inbound_setup_ajax ('modals', folder, 'Importing modals', 0 );
					if (action == 'modals' )
						inbound_setup_ajax ('banners', folder, 'Importing banners', 0 );
					if (action == 'banners')
						inbound_setup_ajax ('pages', folder, 'Importing pages', 0 );
					if (action == 'pages')
						inbound_setup_ajax ('finalize', folder, 'Finalizing import', 0 );
					if (action == 'finalize') {
						if ( response.redirect.length ) {
							// Simulate an HTTP redirect to final page in setup process
							window.location.replace( response.redirect );
						}
					}

					$('#setup-console').append ( response.logmessage + "<br>");

				} else {
					_ajax_nonce = response.newNonce;
					$('#_wpnonce').val( _ajax_nonce );
					inbound_setup_error( response.message );
				}
			},
			error: function(e, x, settings, exception) {
				// Generic debugging
				var errorMessage;
				var statusErrorMap = {
					'400' : "Server understood request but request content was invalid.",
					'401' : "Unauthorized access.",
					'403' : "Forbidden resource can't be accessed.",
					'500' : "Internal Server Error",
					'503' : "Service Unavailable"
				};
				if (x.status) {
					errorMessage = statusErrorMap[x.status];
					if (!errorMessage) {
						errorMessage = "Unknown Error.";
					} else if (exception == 'parsererror') {
						errorMessage = "Error. Parsing JSON request failed.";
					} else if (exception == 'timeout') {
						errorMessage = "Request timed out.";
					} else if (exception == 'abort') {
						errorMessage = "Request was aborted by server.";
					} else {
						errorMessage = "Unknown Error.";
					}
					inbound_setup_error( errorMessage );
					console.log("Error message is: " + errorMessage);
				} else {
					inbound_setup_error( 'Unexpected error. ' + e.responseText );
					console.log("Undefined error.");
					console.log(e);
				}
			}
		}); // Close $.ajax
	}
}); //end ready



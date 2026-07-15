/**
 * Media Library picker for custom image meta boxes.
 * Attach to any element with [data-koen-media-picker] containing a hidden
 * input, a [data-koen-media-preview] container, and select/remove buttons.
 */
( function () {
	'use strict';

	document.querySelectorAll( '[data-koen-media-picker]' ).forEach( function ( picker ) {
		var input = picker.querySelector( 'input[type="hidden"]' );
		var preview = picker.querySelector( '[data-koen-media-preview]' );
		var selectBtn = picker.querySelector( '[data-koen-media-select]' );
		var removeBtn = picker.querySelector( '[data-koen-media-remove]' );
		var frame = null;

		selectBtn.addEventListener( 'click', function () {
			if ( ! frame ) {
				frame = window.wp.media( {
					title: 'Select image',
					library: { type: 'image' },
					multiple: false,
				} );

				frame.on( 'select', function () {
					var attachment = frame.state().get( 'selection' ).first().toJSON();
					var url = attachment.sizes && attachment.sizes.medium
						? attachment.sizes.medium.url
						: attachment.url;

					input.value = attachment.id;
					preview.innerHTML =
						'<img src="' + url + '" alt="" style="max-width:100%;height:auto;">';
					removeBtn.style.display = '';
				} );
			}

			frame.open();
		} );

		removeBtn.addEventListener( 'click', function () {
			input.value = '';
			preview.innerHTML = '';
			removeBtn.style.display = 'none';
		} );
	} );
} )();
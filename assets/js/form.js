/**
 * Form related functions.
 */
document.addEventListener( 'DOMContentLoaded', () => {
	window.formBlockAllowSubmit = {};
	const forms = document.querySelectorAll( '.wp-block-form-block-form' );

	for ( const form of forms ) {
		formBlockAllowSubmit[ form ] = true;
		getNonce( form );
		form.addEventListener( 'submit', submitForm );
	}

	/**
	 * Get a nonce via Ajax.
	 *
	 * @since	1.0.2
	 * @param {HTMLElement} form
	 */
	function getNonce( form ) {
		const formData = new FormData();
		const xhr = new XMLHttpRequest();

		formData.set( 'action', 'form-block-create-nonce' );
		formData.set(
			'form_id',
			form.querySelector( '[name="_form_id"]' ).value
		);

		xhr.open( 'POST', formBlockData.ajaxUrl, true );
		xhr.send( formData );
		xhr.onreadystatechange = () => {
			if ( xhr.readyState !== 4 ) {
				return;
			}

			if ( xhr.status === 200 || xhr.status === 201 ) {
				try {
					const response = JSON.parse( xhr.responseText );

					if ( response.success ) {
						let nonceField =
							form.querySelector( '[name="_wpnonce"]' );

						if ( ! nonceField ) {
							nonceField = document.createElement( 'input' );
							nonceField.name = '_wpnonce';
							nonceField.type = 'hidden';

							form.appendChild( nonceField );
						}

						nonceField.value = response?.data?.nonce;
					} else if ( response?.data?.message ) {
						// server-side error message
						setSubmitMessage(
							form,
							'error',
							response?.data?.message
						);

						// disable submit button if nonce creation was not successful
						const submitButton =
							form.querySelector( '[type="submit"]' );

						if ( submitButton ) {
							submitButton.disabled = true;
						}
					} else {
						// generic error message
						setSubmitMessage(
							form,
							'error',
							formBlockData.i18n.backendError
						);
					}
				} catch ( error ) {
					// invalid data from server
					setSubmitMessage(
						form,
						'error',
						formBlockData.i18n.backendError
					);
					console.error( error );
				}
			} else {
				// request completely failed
				setSubmitMessage(
					form,
					'error',
					formBlockData.i18n.requestError
				);
				console.error( xhr.responseText );
			}
		};
	}

	/**
	 * Submit the form.
	 *
	 * @param	{Event}	event The submit event
	 */
	function submitForm( event ) {
		const form = event.currentTarget;

		if (
			form.hasAttribute( 'data-no-ajax' ) &&
			form.getAttribute( 'data-no-ajax' )
		) {
			return;
		}

		event.preventDefault();

		if ( ! formBlockAllowSubmit[ form ] ) {
			return;
		}

		formBlockAllowSubmit[ form ] = false;

		const messageContainer = form.querySelector(
			'.form-block__message-container'
		);

		if ( messageContainer ) {
			messageContainer.remove();
		}

		let intervalCount = 0;
		const interval = setInterval( () => {
			intervalCount++;

			if ( intervalCount > 10 ) {
				clearInterval( interval );
			}

			if ( ! formBlockIsValidated ) {
				formBlockAllowSubmit[ form ] = false;

				return;
			}

			setSubmitMessage( form, 'loading', formBlockData.i18n.isLoading );
			clearInterval( interval );

			const formData = new FormData( form );
			const url =
				formBlockData.requestUrl !== form.action
					? form.action
					: formBlockData.ajaxUrl;
			const xhr = new XMLHttpRequest();

			formData.set( 'action', 'form-block-submit' );

			xhr.open( 'POST', url, true );
			xhr.send( formData );
			xhr.onreadystatechange = () => {
				if ( xhr.readyState !== 4 ) {
					return;
				}

				formBlockAllowSubmit[ form ] = false;

				if ( xhr.status === 200 ) {
					try {
						const response = JSON.parse( xhr.responseText );

						if ( response.success ) {
							form.reset();
							const fieldsToReset = form.querySelectorAll(
								'.form-block__element.form-error, .form-block__element.is-enabled, .form-block__element.is-invalid, .form-block__element.is-valid'
							);

							for ( const field of fieldsToReset ) {
								field.classList.remove(
									'is-enabled',
									'is-invalid',
									'is-valid',
									'form-error'
								);
								form.validator.unmark( field );
							}

							const dropzoneFiles = form.querySelectorAll(
								'.form-block-pro-dropzone__files'
							);

							if ( dropzoneFiles ) {
								for ( const dropzoneFile of dropzoneFiles ) {
									dropzoneFile.innerHTML = '';
								}
							}

							const customSuccessMessage =
								response?.data?.successMessage;

							if ( form.hasAttribute( 'data-redirect' ) ) {
								setSubmitMessage(
									form,
									'success',
									customSuccessMessage ||
										formBlockData.i18n
											.requestSuccessRedirect,
									!! customSuccessMessage
								);

								window.location.href =
									form.getAttribute( 'data-redirect' );
							} else {
								setSubmitMessage(
									form,
									'success',
									customSuccessMessage ||
										formBlockData.i18n.requestSuccess,
									!! customSuccessMessage
								);
							}
						} else if ( response?.data?.message ) {
							// server-side error message
							setSubmitMessage(
								form,
								'error',
								response?.data?.message
							);
						} else {
							// generic error message
							setSubmitMessage(
								form,
								'error',
								formBlockData.i18n.backendError
							);
						}
					} catch ( error ) {
						// invalid data from server
						setSubmitMessage(
							form,
							'error',
							formBlockData.i18n.backendError
						);
						console.error( error );
					}

					// get a new nonce for another request
					getNonce( form );
				} else {
					// request completely failed
					setSubmitMessage(
						form,
						'error',
						formBlockData.i18n.requestError
					);
					console.error( xhr.responseText );
				}
			};
		}, 50 );
	}

	/**
	 * Set a submit message.
	 *
	 * @param	{HTMLElement}	form Form element
	 * @param	{String}		messageType 'error', 'loading' or 'success'
	 * @param	{String}		message Message
	 * @param	{Boolean}		isHtml Whether the message is raw HTML
	 */
	function setSubmitMessage( form, messageType, message, isHtml ) {
		const ariaLiveType = messageType === 'error' ? 'assertive' : 'polite';
		let messageContainer = form.querySelector(
			'.form-block__message-container'
		);

		if ( ! messageContainer ) {
			messageContainer = document.createElement( 'div' );
			messageContainer.classList.add( 'form-block__message-container' );
			form.appendChild( messageContainer );
		} else {
			messageContainer.classList.remove(
				'is-type-error',
				'is-type-loading',
				'is-type-success'
			);
		}

		messageContainer.classList.add( 'is-type-' + messageType );
		// first add only the text content to make sure no unwanted HTML is added
		messageContainer.textContent = message;
		// then replace all newlines with <br />
		messageContainer.innerHTML = nl2br( messageContainer.innerHTML );
		messageContainer.setAttribute( 'aria-live', ariaLiveType );

		if ( isHtml ) {
			messageContainer.innerHTML = message;
		}

		if ( messageType === 'loading' ) {
			const loadingIndicator = document.createElement( 'span' );

			loadingIndicator.classList.add( 'form-block__loading-indicator' );
			messageContainer.prepend( loadingIndicator );
		}

		// scroll error message into viewport
		if ( ! isElementInViewport( messageContainer ) ) {
			const rect = messageContainer.getBoundingClientRect();

			window.scrollTo(
				0,
				window.scrollY +
					rect.top +
					messageContainer.offsetHeight -
					document.documentElement.clientHeight
			);
		}
	}
} );

/**
 * Replace all newlines with <br />.
 *
 * @see		https://stackoverflow.com/a/784547
 *
 * @param	{String}	string Any string
 * @returns	string The string with replaced newlines
 */
function nl2br( string ) {
	return string.replace( /(?:\r\n|\r|\n)/g, '<br />' );
}

/**
 * Check if an element is in the viewport.
 *
 * @see		https://stackoverflow.com/a/7557433
 *
 * @param	{HTMLElement}	element The element to check
 * @returns	Whether the element is in the viewport
 */
function isElementInViewport( element ) {
	const rect = element.getBoundingClientRect();

	return (
		rect.top >= 0 &&
		rect.left >= 0 &&
		rect.bottom <=
			( window.innerHeight || document.documentElement.clientHeight ) &&
		rect.right <=
			( window.innerWidth || document.documentElement.clientWidth )
	);
}

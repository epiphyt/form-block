/**
 * Form validation related functions.
 */

// add checkbox check
FormValidator.prototype.tests.checkbox = function ( field, data ) {
	if ( field.checked ) {
		return true;
	}

	return this.texts.checked;
};

// add radio check
FormValidator.prototype.tests.radio = function ( field, data ) {
	if ( field.checked ) {
		return true;
	}

	const radioFields = document.querySelectorAll(
		'[name="' + field.name + '"]'
	);

	for ( const radioField of radioFields ) {
		if ( radioField.checked ) {
			return true;
		}
	}

	return this.texts.radio;
};

// add file check
FormValidator.prototype.tests.file = function ( field, data ) {
	const form = field.closest( '.wp-block-form-block-form' );
	const files = field.files;
	let tooBig = false;
	let fileSizeCombined = 0;
	let maxFilesize = formBlockValidationData.validatorMaxFilesize;
	let maxFilesizePerFile =
		formBlockValidationData.validatorMaxFilesizePerFile;
	let tooBigCombined = false;

	if ( form.hasAttribute( 'data-max-upload' ) ) {
		maxFilesize = form.getAttribute( 'data-max-upload' );
	}

	if ( form.hasAttribute( 'data-max-upload-file' ) ) {
		maxFilesizePerFile = form.getAttribute( 'data-max-upload-file' );
	}

	// test each file
	for ( var j = 0; j < files.length; j++ ) {
		fileSizeCombined += files[ j ].size;

		if ( fileSizeCombined > maxFilesize ) {
			tooBigCombined = true;
			break;
		}

		if ( files[ j ].size > maxFilesizePerFile ) {
			tooBig = true;
			break;
		}
	}

	let errorMessage = formBlockValidationData.validatorFileTooBig;

	if ( field.multiple ) {
		errorMessage = formBlockValidationData.validatorOneFileTooBig;

		if ( tooBigCombined ) {
			errorMessage = formBlockValidationData.validatorAllFilesTooBig;
		}
	}

	if ( tooBig || tooBigCombined ) {
		return { valid: false, error: errorMessage };
	}

	return { valid: true, error: '' };
};

// add proper URL check
FormValidator.prototype.tests.url = function ( field, data ) {
	if ( data.pattern ) {
		data.pattern = data.pattern
			.replace( /\\\\/g, '\\' )
			.replace( /^\//, '' )
			.replace( /\/$/, '' );
		const regex = new RegExp( data.pattern );

		if ( ! regex.test( data.value ) ) {
			return this.texts.url;
		}
	}

	if ( FormValidator.prototype.defaults.regex.url.test( data.value ) ) {
		return true;
	}

	return this.texts.url;
};

/**
 * Update the error message of a field that is part of a group.
 *
 * @param	{Object}	data Validation result, extended by the field itself
 * @param	{boolean}	[announce] Whether to announce the message to screen readers
 */
const adjustMultiFieldErrors = ( data, announce ) => {
	const parentField = data.field.closest( '.form-block__element' );
	let innerError = document.getElementById(
		data.field.id + '__inline-error'
	);

	if ( innerError ) {
		innerError.remove();
	}

	if ( data.valid ) {
		data.field.removeAttribute( 'aria-invalid' );

		return;
	}

	const adjacentField = parentField.closest(
		'.form-block__element:not(.is-sub-element)'
	);
	innerError = document.createElement( 'div' );
	const labelContent = parentField.querySelector(
		'.form-block__label-content'
	).textContent;
	innerError.id = data.field.id + '__inline-error';

	// set the role before the text so the message is announced once it is added
	if ( announce ) {
		innerError.setAttribute( 'role', 'alert' );
	}

	innerError.textContent = labelContent + ': ' + data.error;
	innerError.classList.add( 'inline-error' );
	parentField.classList.add( 'form-error' );
	adjacentField.classList.add( 'form-error' );
	adjacentField.appendChild( innerError );
	setAriaDescribedBy( data.field, adjacentField );
	data.field
		.closest( '.form-block__element' )
		.querySelector( '.inline-error' )
		?.remove();
	data.field.ariaInvalid = true;
};

/**
 * Get the visible label text of a form field.
 *
 * For a field that is part of a group (eg. a date with separate fields), the
 * label of the group is used, since the group is what the user perceives as one
 * field.
 *
 * @param	{HTMLElement}	field The form field
 * @returns	{string} The label text, or an empty string if there is none
 */
const getFieldLabel = ( field ) => {
	const container =
		field.closest( '.form-block__input-group' ) ||
		field.closest( '.form-block__element' );
	const label = container?.querySelector( '.form-block__label-content' );

	return label ? label.textContent.trim() : '';
};

/**
 * Get the inline error element belonging to a form field.
 *
 * @param	{HTMLElement}	field The form field
 * @param	{HTMLElement}	[parentField] Element containing the error message
 * @returns	{?HTMLElement} The inline error element, or null if there is none
 */
const getInlineError = ( field, parentField ) => {
	const fieldToExtend = parentField || field;

	return (
		document.getElementById( field.id + '__inline-error' ) ||
		fieldToExtend.parentNode.querySelector( '.inline-error:not([id])' )
	);
};

/**
 * Announce the inline error message of a field to screen readers.
 *
 * Only used when a field is validated on its own. On submit, the summary of all
 * invalid fields is announced instead, so the individual messages have to stay
 * silent to not talk over it.
 *
 * The validator injects the message together with its container, which means the
 * container is not yet a live region by the time the text arrives. Setting the
 * role and re-inserting the existing content afterwards turns the message into a
 * change inside a live region, which is what actually gets announced.
 *
 * @param	{HTMLElement}	field The form field
 * @param	{HTMLElement}	[parentField] Element containing the error message
 */
const announceInlineError = ( field, parentField ) => {
	const innerError = getInlineError( field, parentField );

	if ( ! innerError || innerError.getAttribute( 'role' ) === 'alert' ) {
		return;
	}

	innerError.setAttribute( 'role', 'alert' );
	innerError.replaceChildren( ...innerError.childNodes );
};

const setAriaDescribedBy = ( field, parentField ) => {
	const errorId = field.id + '__inline-error';
	const innerError = getInlineError( field, parentField );

	// there is no error element if the field has not been marked as invalid
	if ( ! innerError ) {
		return;
	}

	innerError.id = errorId;

	if (
		! field.hasAttribute( 'aria-describedby' ) ||
		! field.getAttribute( 'aria-describedby' ).includes( errorId )
	) {
		field.setAttribute(
			'aria-describedby',
			(
				( field.getAttribute( 'aria-describedby' ) || '' ) +
				' ' +
				errorId
			).trim()
		);
	}
};

const validator = new FormValidator( {
	classes: {
		alert: 'inline-error',
		bad: 'form-error',
		item: 'form-block__element',
	},
	texts: {
		checked: formBlockValidationData.validatorChecked,
		date: formBlockValidationData.validatorDate,
		email: formBlockValidationData.validatorEmail,
		empty: formBlockValidationData.validatorEmpty,
		invalid: formBlockValidationData.validatorInvalid,
		long: formBlockValidationData.validatorLong,
		number: formBlockValidationData.validatorNumber,
		number_min: formBlockValidationData.validatorNumberMin,
		number_max: formBlockValidationData.validatorNumberMax,
		radio: formBlockValidationData.validatorRadio,
		short: formBlockValidationData.validatorShort,
		select: formBlockValidationData.validatorSelect,
		time: formBlockValidationData.validatorTime,
		url: formBlockValidationData.validatorUrl,
	},
} );

document.addEventListener( 'DOMContentLoaded', function () {
	const events = [ 'blur', 'change', 'input' ];
	const forms = document.querySelectorAll( '.wp-block-form-block-form' );
	let typingTimeout;

	for ( const form of forms ) {
		form.validator = validator;

		for ( const changeEvent of events ) {
			form.addEventListener( changeEvent, function ( event ) {
				clearTimeout( typingTimeout );

				const check = function () {
					let result = validator.checkField( event.target );

					// files are handled differently
					if ( event.target.type === 'file' ) {
						result = validator.tests.file.call(
							validator,
							event.target,
							validator.prepareFieldData( event.target )
						);

						if ( ! result.valid ) {
							validator.mark( event.target, result.error );
							setAriaDescribedBy( event.target );
							announceInlineError( event.target );
						} else {
							validator.unmark( event.target );
						}
					}

					if ( event.target.closest( '.form-block__input-group' ) ) {
						let data = validator.checkField( event.target );
						data.field = event.target;
						adjustMultiFieldErrors( data, true );

						return;
					}

					// checkField() marks the field but leaves the error message
					// undescribed and silent, so wire it up here as well
					if ( ! result.valid && event.target.type !== 'file' ) {
						setAriaDescribedBy( event.target );
						announceInlineError( event.target );
					}

					const container = event.target.closest(
						'[class^="wp-block-form-block-"]'
					);

					if ( container && result.valid ) {
						container.classList.add( 'is-valid' );
						container.classList.remove( 'is-invalid' );
					} else if ( container ) {
						container.classList.remove( 'is-valid' );
						container.classList.add( 'is-invalid' );
					}
				};

				// input events already have an input type
				// if not, they're either selectable by click (eg. checkboxes, selects)
				// or auto-filled and thus should be checked immediately
				if ( ! event.inputType ) {
					check();
				} else {
					typingTimeout = setTimeout( check, 500 );
				}
			} );
		}

		form.addEventListener( 'submit', function ( event ) {
			const form = event.currentTarget;
			const fileFields = form.querySelectorAll( '[type="file"]' );
			let invalidFields = [];
			const invalidGroups = new Set();

			// on submit, the summary is what gets announced, so stop any message
			// left over from validating a single field from talking over it
			// (has to happen before validating, since re-using a message that is
			// still a live region would announce it right away)
			for ( const inlineError of form.querySelectorAll(
				'.inline-error[role="alert"]'
			) ) {
				inlineError.removeAttribute( 'role' );
			}

			const validatorResult = validator.checkAll( this );

			validatorResult.fields
				.reverse()
				.forEach( function ( field, index, array ) {
					if ( field.field.closest( '.form-block__input-group' ) ) {
						adjustMultiFieldErrors( field );

						// count each invalid group once so it is focused/announced
						if ( ! field.valid ) {
							const group = field.field.closest(
								'.form-block__input-group'
							);

							if ( ! invalidGroups.has( group ) ) {
								invalidGroups.add( group );
								invalidFields.push( field );
							}
						}

						return;
					} else if ( field.field.type !== 'file' ) {
						if ( ! field.valid ) {
							setAriaDescribedBy( field.field );
							invalidFields.push( field );
						}

						return;
					}

					const result = validator.tests.file.call(
						validator,
						field.field,
						validator.prepareFieldData( field.field )
					);

					field.error = result.error;
					field.valid = result.valid;
					array[ index ] = field;

					if ( ! field.valid ) {
						validatorResult.valid = false;
						validator.mark( field.field, field.error );
						setAriaDescribedBy( field.field );
						invalidFields.push( field );
					}
				} );

			for ( const field of fileFields ) {
				// required fields already been processed above
				if ( field.required ) {
					continue;
				}

				const result = validator.tests.file.call(
					validator,
					field,
					validator.prepareFieldData( field )
				);
				const validatorField = {
					field: field,
					error: result.error,
					valid: result.valid,
				};

				validatorResult.fields.push( validatorField );

				if ( ! validatorField.valid ) {
					validatorResult.valid = false;
					validator.mark(
						validatorField.field,
						validatorField.error
					);
					setAriaDescribedBy( validatorField.field );
				}
			}

			if ( ! validatorResult.valid ) {
				event.preventDefault();

				if ( invalidFields.length > 1 ) {
					let invalidFieldNotice = form.querySelector(
						'.form-block__invalid-field-notice'
					);

					if ( ! invalidFieldNotice ) {
						invalidFieldNotice = document.createElement( 'p' );
						invalidFieldNotice.classList.add(
							'form-block__invalid-field-notice',
							'is-error-notice',
							'screen-reader-text'
						);
						invalidFieldNotice.role = 'alert';
						form.appendChild( invalidFieldNotice );
					}

					// list the affected fields, so it is clear what needs
					// fixing without tabbing through the whole form
					const invalidFieldLabels = [
						...new Set(
							invalidFields
								.map( ( invalidField ) =>
									getFieldLabel( invalidField.field )
								)
								.filter( ( label ) => label )
						),
					];
					let notice =
						formBlockValidationData.validationInvalidFieldNotice.replace(
							'%d',
							invalidFields.length
						);

					if ( invalidFieldLabels.length ) {
						notice +=
							' ' +
							formBlockValidationData.validationInvalidFieldList.replace(
								'%s',
								invalidFieldLabels.join( ', ' )
							);
					}

					invalidFieldNotice.textContent = notice;
				} else if ( invalidFields.length === 1 ) {
					invalidFields[ 0 ].field.focus();
				}

				for ( const invalidField of invalidFields ) {
					if (
						! invalidField.field.hasAttribute(
							'data-validate-text-invalid'
						)
					) {
						continue;
					}

					const block = invalidField.field.closest(
						'.form-block__element'
					);
					const label = block.querySelector(
						'.form-block__label-content'
					);
					const inlineError = block.querySelector( '.inline-error' );

					if ( ! inlineError || ! label ) {
						continue;
					}

					inlineError.textContent = invalidField.field
						.getAttribute( 'data-validate-text-invalid' )
						.replace( '{field}', label.textContent );
				}

				// let extensions react to a failed submit once the fields are fully
				// marked up, instead of having to guess when that has happened
				form.dispatchEvent(
					new CustomEvent( 'formBlock:validationFailed', {
						bubbles: true,
						detail: {
							fields: invalidFields.map(
								( invalidField ) => invalidField.field
							),
						},
					} )
				);
			} else if (
				! form.hasAttribute( 'data-no-ajax' ) ||
				! form.getAttribute( 'data-no-ajax' )
			) {
				formBlockAllowSubmit.set( form, true );
				const invalidFieldNotice = form.querySelector(
					'.form-block__invalid-field-notice'
				);

				if ( invalidFieldNotice ) {
					invalidFieldNotice.remove();
				}
			}

			// scroll to first invalid field
			setTimeout( () => {
				const firstInvalidField = form.querySelector( '.form-error' );

				if ( firstInvalidField ) {
					firstInvalidField.scrollIntoView( {
						behavior: 'auto',
						block: 'center',
					} );
				} else {
					formBlockIsValidated = true;
				}
			}, 100 );
		} );

		// special case: radio buttons
		const radioButtons = form.querySelectorAll( 'input[type="radio"]' );
		let radioName = '';

		for ( const radioButton of radioButtons ) {
			if ( ! radioName || radioName !== radioButton.name ) {
				radioName = radioButton.name;
			}

			const radioButtonsByName = form.querySelectorAll(
				'[name="' + radioName + '"]'
			);

			for ( const radioButtonByName of radioButtonsByName ) {
				radioButtonByName.addEventListener(
					'click',
					function ( event ) {
						// explicit no support for multiple forms with identical radio names
						const radios = document.querySelectorAll(
							'[name="' + event.currentTarget.name + '"]'
						);
						let isValid = true;

						for ( const radio of radios ) {
							if ( ! validator.checkField( radio ).valid ) {
								isValid = false;
								break;
							}
						}

						if ( isValid ) {
							for ( const radio of radios ) {
								validator.unmark( radio );
							}
						}
					}
				);
			}
		}

		const newFormErrorObserver = new MutationObserver( function (
			mutations,
			observer
		) {
			const formErrors = form.querySelectorAll( '.form-error' );
			const oldFormErrors = form.querySelectorAll(
				'.form-block__element:not(.form-error) [aria-invalid="true"]'
			);

			if ( formErrors ) {
				for ( const formError of formErrors ) {
					if ( formError.classList.contains( 'has-sub-elements' ) ) {
						if ( ! formError.querySelector( '.form-error' ) ) {
							formError.classList.remove( 'form-error' );
						}

						continue;
					}

					if (
						formError.classList.contains(
							'wp-block-form-block-input'
						)
					) {
						formError.querySelector( 'input' ).ariaInvalid = true;
					} else if (
						formError.classList.contains(
							'wp-block-form-block-select'
						)
					) {
						formError.querySelector( 'select' ).ariaInvalid = true;
					} else if (
						formError.classList.contains(
							'wp-block-form-block-textarea'
						)
					) {
						formError.querySelector(
							'textarea'
						).ariaInvalid = true;
					}
				}
			}

			if ( oldFormErrors ) {
				for ( const oldFormError of oldFormErrors ) {
					oldFormError.ariaInvalid = false;
				}
			}
		} );

		newFormErrorObserver.observe( form, {
			attributeFilter: [ 'class' ],
			subtree: true,
		} );
	}
} );

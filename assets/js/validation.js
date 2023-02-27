/**
 * Form validation related functions.
 */
let formBlockIsValidated = false;

// add checkbox check
FormValidator.prototype.tests.checkbox = function( field, data ) {
	if ( field.checked ) {
		return true;
	}
	
	return this.texts.checked;
};

// add radio check
FormValidator.prototype.tests.radio = function( field, data ) {
	if ( field.checked ) {
		return true;
	}
	
	const radioFields = document.querySelectorAll( '[name="' + field.name + '"]' );
	
	for ( const radioField of radioFields ) {
		if ( radioField.checked ) {
			return true;
		}
	}
	
	return this.texts.radio;
};

// add proper URL check
FormValidator.prototype.tests.url = function( field, data ) {
	if ( data.pattern ) {
		data.pattern = data.pattern.replace( /\\\\/g, '\\' ).replace( /^\//, '' ).replace( /\/$/, '' );
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
		number_min: formBlockValidationData.validatorNumberMax,
		number_max: formBlockValidationData.validatorNumberMin,
		radio: formBlockValidationData.validatorRadio,
		short: formBlockValidationData.validatorShort,
		select: formBlockValidationData.validatorSelect,
		time: formBlockValidationData.validatorTime,
		url: formBlockValidationData.validatorUrl,
	},
} );

document.addEventListener( 'DOMContentLoaded', function() {
	const events = [ 'blur', 'change', 'input' ];
	const forms = document.querySelectorAll( '.wp-block-form-block-form' );
	let typingTimeout;
	
	for ( const form of forms ) {
		for ( const changeEvent of events ) {
			form.addEventListener( changeEvent, function( event ) {
				clearTimeout( typingTimeout );
				
				const check = function() {
					let result = validator.checkField( event.target );
					
					// files are handled differently
					if ( event.target.type === 'file' ) {
						result = validator.tests.file.call( validator, event.target, validator.prepareFieldData( event.target ) );
						
						if ( ! result.valid ) {
							validator.mark( event.target, result.error );
						}
						else {
							validator.unmark( event.target );
						}
					}
					
					const container = event.target.closest( '[class^="wp-block-form-block-"]' );
					
					if ( container && result.valid ) {
						container.classList.add( 'is-valid' );
						container.classList.remove( 'is-invalid' );
					}
					else if ( container ) {
						container.classList.remove( 'is-valid' );
						container.classList.add( 'is-invalid' );
					}
				};
				
				// input events already have an input type
				// if not, they're either selectable by click (eg. checkboxes, selects)
				// or autofilled and thus should be checked immediately
				if ( ! event.inputType ) {
					check();
				}
				else {
					typingTimeout = setTimeout( check, 500 );
				}
			} );
		};
		
		form.addEventListener( 'submit', function( event ) {
			const form = event.currentTarget;
			const fileFields = form.querySelectorAll( '[type="file"]' );
			const validatorResult = validator.checkAll( this );
			
			validatorResult.fields.forEach( function( field, index, array ) {
				if ( field.field.type !== 'file' ) {
					return;
				}
				
				const result = validator.tests.file.call( validator, field.field, validator.prepareFieldData( field.field ) );
				
				field.error = result.error;
				field.valid = result.valid;
				array[ index ] = field;
				
				if ( ! field.valid ) {
					validatorResult.valid = false;
					validator.mark( field.field, field.error );
				}
			} );
			
			for ( const field of fileFields ) {
				// required fields already been processed above
				if ( field.required ) {
					continue;
				}
				
				const result = validator.tests.file.call( validator, field, validator.prepareFieldData( field ) );
				const validatorField = {
					field: field,
					error: result.error,
					valid: result.valid,
				};
				
				validatorResult.fields.push( validatorField );
				
				if ( ! validatorField.valid ) {
					validatorResult.valid = false;
					validator.mark( validatorField.field, validatorField.error );
				}
				
			};
			
			if ( ! ( !! validatorResult.valid ) ) {
				event.preventDefault();
			}
			else if ( form.classList.contains( 'is-ajax-form' ) ) {
				event.preventDefault();
				__formSubmit( event );
			}
			
			// scroll to first invalid field
			setTimeout( () => {
				const firstInvalidField = form.querySelector( '.form-error' );
				
				if ( firstInvalidField ) {
					firstInvalidField.scrollIntoView( { block: 'center' } );
				}
				else {
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
			
			const radioButtonsByName = form.querySelectorAll( '[name="' + radioName + '"]' );
			
			for ( const radioButtonByName of radioButtonsByName ) {
				radioButtonByName.addEventListener( 'click', function( event ) {
					// explicit no support for multiple forms with identical radio names
					const radios = document.querySelectorAll( '[name="' + event.currentTarget.name + '"]' );
					let isValid = true;
					
					for ( const radio of radios ) {
						if ( ! validator.checkField( radio ).valid ) {
							isValid = false;
							break;
						}
					};
					
					if ( isValid ) {
						for ( const radio of radios ) {
							validator.unmark( radio );
						};
					}
				} );
			};
		};
	};
} );

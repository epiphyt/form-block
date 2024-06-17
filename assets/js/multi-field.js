document.addEventListener( 'DOMContentLoaded', () => {
	const multiFieldInputs = document.querySelectorAll( '.form-block__element.is-sub-element input[data-max-length]' );
	
	for ( const multiFieldInput of multiFieldInputs ) {
		multiFieldInput.addEventListener( 'input', onInput );
		multiFieldInput.addEventListener( 'paste', handlePaste );
	}
} );

const onInput = ( event ) => addLeadingZero( event.currentTarget );

/**
 * Add leading zeros to an element.
 * 
 * @see https://stackoverflow.com/a/72864152/3461955
 * 
 * @param {HTMLElement} element The element to add zeros to
 * @param {string} [attribute='data-max-length'] The attribute to check for
 */
function addLeadingZero( element, attribute = 'data-max-length' ) {
	const maxLength = parseInt( element.getAttribute( attribute ) );
	const isNegative = parseInt( element.value ) < 0
	let newValue = ( '0'.repeat( maxLength ) + Math.abs( element.value ).toString() ).slice( -maxLength );
	
	if ( isNegative ) {
		newValue = '-' + newValue;
	}
	
	element.value = newValue;
}

/**
 * Handle pasting into custom date fields.
 * 
 * @param {Event} event Paste event
 */
function handlePaste( event ) {
	const currentTarget = event.currentTarget;
	const isFirstInput = !! currentTarget.closest( '.form-block__element.is-sub-element:first-of-type' );
	
	if ( ! isFirstInput ) {
		return;
	}
	
	const container = currentTarget.closest( '.form-block__element:not(.is-sub-element)' );
	const inputs = container.querySelectorAll( 'input' );
	const format = getFormat( inputs );
	const paste = ( event.clipboardData || event.originalEvent.clipboardData || window.clipboardData ).getData( 'text' ) || '';
	const matches = paste.match( new RegExp( format ) );
	
	if ( matches ) {
		event.preventDefault();
		
		for ( let i = 0; i < inputs.length; i++ ) {
			inputs[ i ].value = matches[ 2 * i + 1 ];
		}
	}
}

/**
 * Get regular expression format from a pasted string.
 * 
 * @param {HTMLCollection} inputs List of inputs
 * @returns {string} Regular expression string
 */
function getFormat( inputs ) {
	let isFirst = true;
	let format = '^';
	
	const escape = ( string, symbol ) => {
		let newString;
		
		for ( let i = 0; i < string.length; i++ ) {
			newString = symbol + string.charAt( i );
		}
		
		return newString;
	}
	
	for ( const input of inputs ) {
		if ( ! isFirst ) {
			if ( input.previousElementSibling ) {
				format += ' ' + input.previousElementSibling.textContent + ' ';
			}
		}
		else {
			isFirst = false;
		}
		
		format += '([0-9]{' + input.getAttribute( 'data-validate-length-range' ) + '})';
		format += '(';
		
		if ( input.nextElementSibling ) {
			format += escape( input.nextElementSibling.textContent, '\\' );
		}
	}
	
	format = format.replace( /\($/, '' );
	format += '?)'.repeat( inputs.length );
	
	return format.replace( /\)$/, '' );
}

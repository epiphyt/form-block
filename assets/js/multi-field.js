document.addEventListener( 'DOMContentLoaded', () => {
	const multiFieldInputs = document.querySelectorAll( '.form-block__element.is-sub-element input[data-max-length]' );
	
	for ( const multiFieldInput of multiFieldInputs ) {
		multiFieldInput.addEventListener( 'input', ( event ) => addLeadingZero( event.currentTarget ) );
	}
} );

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


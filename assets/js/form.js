/**
 * Form related functions.
 */
document.addEventListener( 'DOMContentLoaded', () => {
	const forms = document.querySelectorAll( '.wp-block-form-block-form' );
	
	for ( const form of forms ) {
		form.addEventListener( 'submit', submitForm );
	}
	
	/**
	 * Submit the form.
	 * 
	 * @param	{Event}	event The submit event
	 */
	function submitForm( event ) {
		event.preventDefault();
		
		const form = event.currentTarget;
		const formData = new FormData( form );
		const xhr = new XMLHttpRequest();
		
		formData.set( 'action', 'form-block-submit' ) 
		
		xhr.open( 'POST', formBlockData.ajaxUrl, true );
		xhr.send( formData );
		xhr.onreadystatechange = () => {
			if ( xhr.readyState === 4 ) {
				if ( xhr.status === 200 ) {
					// TODO: success message
					console.log( 'success' );
				}
				else {
					// TODO: error message
					console.error( 'error', xhr.statusText );
				}
			}
			else {
				// TODO: error message
				console.error( 'error', xhr.statusText );
			}
		}
	}
} );

/**
 * Add a message to a snackbar.
 *
 * @param {string} message The message
 * @param {number} timeout Timeout when to hide the message
 */
export default function addSnackbarMessage( message, timeout = 10000 ) {
	const item = document.createElement( 'div' );
	item.textContent = message;
	item.classList.add( 'form-block__snackbar--item' );
	item.classList.add( 'is-init' );
	getSnackbar().appendChild( item );
	setTimeout( () => item.classList.add( 'is-open' ), 10 );

	setTimeout( () => {
		item.classList.remove( 'is-init' );
		item.classList.remove( 'is-open' );
		item.classList.add( 'is-exit' );
		setTimeout( () => item.remove(), 110 );
	}, timeout );
}

/**
 * Get a snackbar.
 *
 * @return {HTMLElement} Snackbar element
 */
export function getSnackbar() {
	let snackbar = document.getElementById( 'form-block__snackbar' );

	if ( snackbar ) {
		return snackbar;
	}

	snackbar = document.createElement( 'div' );
	snackbar.classList.add( 'form-block__snackbar' );
	snackbar.id = 'form-block__snackbar';
	snackbar.ariaLive = 'polite';
	document.getElementById( 'wpcontent' ).appendChild( snackbar );

	return snackbar;
}

window.formBlockSnackbar = {
	addSnackbarMessage,
	getSnackbar,
};

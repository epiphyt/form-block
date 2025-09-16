import addSnackbarMessage from './snackbar.js';

/* global formBlockSubmissions */
document.addEventListener( 'DOMContentLoaded', () => {
	const deleteButtons = document.querySelectorAll( '.form-block__delete' );
	const summaries = document.querySelectorAll(
		'.form-block__data-details > summary'
	);

	const onDelete = async ( event ) => {
		const button = event.currentTarget;
		const row = button.closest( 'tr' );
		const id = button.getAttribute( 'data-id' );

		button.classList.add( 'is-busy' );
		button.setAttribute( 'aria-disabled', true );

		await fetch(
			formBlockSubmissions.restRootUrl +
				'form-block/v1/submission/delete/' +
				id,
			{
				headers: {
					'X-WP-Nonce': formBlockSubmissions.nonce,
				},
				method: 'DELETE',
			}
		)
			.then( async ( response ) => {
				button.classList.remove( 'is-busy' );
				button.setAttribute( 'aria-disabled', false );

				if ( ! response.ok ) {
					const json = await response.json();

					if ( json?.message ) {
						throw new Error( json.message );
					} else {
						throw new Error(
							formBlockSubmissions.submissionRemovedError
						);
					}
				}

				return response;
			} )
			.then( () => {
				row.remove();
				addSnackbarMessage(
					formBlockSubmissions.submissionRemovedSuccess
				);
			} )
			.catch( ( error ) => {
				if ( error?.message ) {
					addSnackbarMessage( error.message );
				}

				console.error( error );
			} );
	};

	const onSummaryClick = ( event ) => {
		event.currentTarget.classList.toggle( 'active' );
	};

	for ( const button of deleteButtons ) {
		button.addEventListener( 'click', onDelete );
	}

	for ( const summary of summaries ) {
		summary.addEventListener( 'click', onSummaryClick );
	}
} );

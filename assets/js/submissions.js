import addSnackbarMessage from './snackbar.js';

/* global formBlockSubmissions */
document.addEventListener( 'DOMContentLoaded', () => {
	const deleteButtons = document.querySelectorAll( '.form-block__delete' );
	const summaries = document.querySelectorAll(
		'.form-block__data-details > summary'
	);

	/**
	 * Move focus to a sensible element before a row is removed.
	 *
	 * The focused button is removed together with its row, so without this focus
	 * would end up on the document body.
	 *
	 * @param {HTMLElement} row The row that is about to be removed
	 */
	const setFocusBeforeRemoval = ( row ) => {
		const nextButton =
			row.nextElementSibling?.querySelector( '.form-block__delete' ) ??
			row.previousElementSibling?.querySelector( '.form-block__delete' );

		if ( nextButton ) {
			nextButton.focus();

			return;
		}

		// there is no submission left, so fall back to the page heading
		const heading = document.querySelector( '.form-block__submissions h2' );

		if ( ! heading ) {
			return;
		}

		heading.tabIndex = -1;
		heading.focus();
	};

	const onDelete = async ( event ) => {
		const button = event.currentTarget;

		// the button stays operable while the request is running, so ignore any
		// further activation instead of deleting the submission twice
		if ( button.getAttribute( 'aria-disabled' ) === 'true' ) {
			return;
		}

		const row = button.closest( 'tr' );
		const id = button.getAttribute( 'data-id' );

		button.classList.add( 'is-busy' );
		button.setAttribute( 'aria-disabled', 'true' );

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
				button.removeAttribute( 'aria-disabled' );

				if ( ! response.ok ) {
					const json = await response.json();

					if ( json?.message ) {
						throw new Error( json.message );
					} else {
						throw new Error(
							formBlockSubmissions.submissionDeletedError
						);
					}
				}

				return response;
			} )
			.then( () => {
				setFocusBeforeRemoval( row );
				row.remove();
				addSnackbarMessage(
					formBlockSubmissions.submissionDeletedSuccess
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

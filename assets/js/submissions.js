import addSnackbarMessage from './snackbar.js';

/* global formBlockSubmissions */
document.addEventListener( 'DOMContentLoaded', () => {
	const deleteButtons = document.querySelectorAll( '.form-block__delete' );
	const typeButtons = document.querySelectorAll(
		'.form-block__submission-type'
	);
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
							formBlockSubmissions.submissionDeletedError
						);
					}
				}

				return response;
			} )
			.then( () => {
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

	const currentView =
		new URLSearchParams( window.location.search ).get( 'type' ) || 'all';

	const updateViewCount = ( view, delta ) => {
		const countElement = document.querySelector(
			'.subsubsub .' + view + ' .count'
		);

		if ( ! countElement ) {
			return;
		}

		const current =
			parseInt( countElement.textContent.replace( /[^0-9]/g, '' ), 10 ) ||
			0;

		countElement.textContent = '(' + Math.max( 0, current + delta ) + ')';
	};

	const onTypeToggle = async ( event ) => {
		const button = event.currentTarget;
		const row = button.closest( 'tr' );
		const id = button.getAttribute( 'data-id' );
		const type = button.getAttribute( 'data-type' );
		const add = button.getAttribute( 'data-add' ) === '1';

		button.classList.add( 'is-busy' );
		button.setAttribute( 'aria-disabled', true );

		await fetch(
			formBlockSubmissions.restRootUrl +
				'form-block/v1/submission/type/' +
				type +
				'/' +
				id,
			{
				body: JSON.stringify( { add } ),
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': formBlockSubmissions.nonce,
				},
				method: 'POST',
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
							formBlockSubmissions.submissionUpdatedError
						);
					}
				}

				return response;
			} )
			.then( () => {
				const typeConfig =
					formBlockSubmissions.submissionTypes?.[ type ] || {};

				updateViewCount( type, add ? 1 : -1 );

				if ( typeConfig.hideFromDefault ) {
					updateViewCount( 'all', add ? -1 : 1 );
				}

				const leavesView =
					currentView === 'all'
						? add && typeConfig.hideFromDefault
						: type === currentView && ! add;

				if ( leavesView ) {
					row.remove();
				} else {
					// keep the row but flip the toggle button
					button.setAttribute( 'data-add', add ? '0' : '1' );
					button.textContent = add
						? typeConfig.actionRemove
						: typeConfig.actionAdd;
				}

				addSnackbarMessage(
					formBlockSubmissions.submissionUpdatedSuccess
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

	for ( const button of typeButtons ) {
		button.addEventListener( 'click', onTypeToggle );
	}

	for ( const summary of summaries ) {
		summary.addEventListener( 'click', onSummaryClick );
	}
} );

document.addEventListener( 'DOMContentLoaded', () => {
	const submissionsInput = document.getElementById(
		'form_block_save_submissions'
	);

	const onSubmissionsInputChange = ( element ) => {
		const row = element.closest( 'tr' );
		const deletionRow = row.nextElementSibling;

		if ( element.checked ) {
			deletionRow.classList.remove( 'is-hidden' );
		} else {
			deletionRow.classList.add( 'is-hidden' );
		}
	};

	onSubmissionsInputChange( submissionsInput );

	submissionsInput.addEventListener( 'click', ( event ) =>
		onSubmissionsInputChange( event.currentTarget )
	);
} );

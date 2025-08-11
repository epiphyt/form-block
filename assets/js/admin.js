document.addEventListener( 'DOMContentLoaded', () => {
	const summaries = document.querySelectorAll(
		'.form-block__data-details > summary'
	);

	const onSummaryClick = ( event ) => {
		event.currentTarget.classList.toggle( 'active' );
	};

	for ( const summary of summaries ) {
		summary.addEventListener( 'click', onSummaryClick );
	}
} );

document.addEventListener( 'DOMContentLoaded', () => {
	const tabs = document.querySelectorAll( '.form-block__settings .nav-tab' );

	for ( const tab of tabs ) {
		initTab( tab, tabs );
	}
} );

/**
 * Initialize a tab.
 *
 * @param {HTMLElement} tab Tab to initialize
 * @param {HTMLCollection} tabs All tabs
 */
export function initTab( tab, tabs ) {
	tab.addEventListener( 'click', onChange );
	tab.addEventListener( 'keydown', ( event ) => {
		const currentTab = event.currentTarget;
		let newActiveTab;

		switch ( event.key ) {
			case 'ArrowLeft':
				newActiveTab = currentTab.previousElementSibling;
				break;
			case 'ArrowRight':
				newActiveTab = currentTab.nextElementSibling;
				break;
			case 'End':
				newActiveTab = tabs[ tabs.length - 1 ];
				break;
			case 'Home':
				newActiveTab = tabs[ 0 ];
				break;
		}

		if ( newActiveTab ) {
			event.preventDefault();
			newActiveTab.focus();
			setActiveTab( newActiveTab.getAttribute( 'data-tab' ), tabs );
		}
	} );

	const currentActiveTab = document.querySelector( '.nav-tab-active' );

	if ( currentActiveTab ) {
		setActiveTab( currentActiveTab.getAttribute( 'data-tab' ), tabs );
	}
}

/**
 * Function to run on tab change.
 *
 * @param {MouseEvent} event The event triggering the change
 */
function onChange( event ) {
	event.preventDefault();

	const currentTarget = event.currentTarget;
	const slug = currentTarget.getAttribute( 'data-slug' );
	const tabs = currentTarget
		.closest( '.nav-tab-wrapper' )
		.querySelectorAll( '.nav-tab' );

	setActiveTab( slug, tabs );
}

/**
 * Set the active tab.
 *
 * @param {string} currentActiveTab Active tab identifier
 * @param {HTMLCollection} tabs All tabs
 */
function setActiveTab( currentActiveTab, tabs ) {
	let tabContents = [];

	for ( const thisTab of tabs ) {
		if ( ! tabContents.length ) {
			tabContents = thisTab
				.closest( '.nav-tab-wrapper' )
				.parentNode.querySelectorAll( '.nav-tab__content' );
		}
		thisTab.ariaSelected = false;
		thisTab.classList.remove( 'nav-tab-active' );
		thisTab.tabIndex = -1;
	}

	for ( const tabContent of tabContents ) {
		tabContent.classList.remove( 'nav-tab-content-active' );
		tabContent.hidden = true;
		tabContent.tabIndex = -1;
	}

	let activeTabElement = document.querySelector(
		'.nav-tab[data-slug="' + currentActiveTab + '"]'
	);
	let activeTabContentElement = document.getElementById(
		'nav-tab__content--' + currentActiveTab
	);

	if ( ! activeTabElement ) {
		activeTabElement = tabs[ 0 ];
	}

	if ( ! activeTabContentElement ) {
		activeTabContentElement = tabContents[ 0 ];
	}

	activeTabElement.ariaSelected = true;
	activeTabElement.classList.add( 'nav-tab-active' );
	activeTabElement.tabIndex = 0;
	activeTabContentElement.classList.add( 'nav-tab-content-active' );
	activeTabContentElement.hidden = false;
	activeTabContentElement.tabIndex = 0;

	updateReferer( currentActiveTab );
}

/**
 * Update the _wp_http_referer input depending on the active tab.
 *
 * @param {string} tab Current active tab identifier
 */
function updateReferer( tab ) {
	const refererInput = document.querySelector( '[name="_wp_http_referer"]' );
	const tabParameter = 'tab';
	const url = refererInput.value;

	if ( ! url.includes( '?' ) ) {
		return;
	}

	const params = url.split( '?' ).pop();
	const urlParameters = new URLSearchParams( params );
	urlParameters.set( tabParameter, tab );

	refererInput.value = refererInput.value.replace(
		params,
		urlParameters.toString()
	);

	// eslint-disable-next-line no-undef
	if ( history.pushState ) {
		try {
			// eslint-disable-next-line no-undef
			history.replaceState( null, null, refererInput.value );
		} catch ( e ) {
			// eslint-disable-next-line no-console
			console.error( e );
		}
	}
}

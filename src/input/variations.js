import { __, sprintf } from '@wordpress/i18n';

import { input } from './icon';
import { types } from './html-data';

const variations = Object.keys( types ).map( ( type ) => {
	const attributes = { type };
	let description = types[ type ]?.description;
	const scope = [ 'inserter', 'transform' ];

	if ( type === 'text' ) {
		scope.push( 'block' );
	} else if ( type === 'reset' ) {
		attributes.value = __( 'Reset', 'form-block' );
	} else if ( type === 'submit' ) {
		attributes.value = __( 'Submit', 'form-block' );
	}

	if ( ! description ) {
		description = sprintf(
			__( 'A single %s field for a form.', 'form-block' ),
			types[ type ].label
		);
	}

	return {
		attributes,
		description,
		icon: input,
		isActive: ( blockAttributes ) => blockAttributes.type === type,
		isDefault: type === 'text',
		name: 'input-' + type,
		scope,
		title: types[ type ].label,
	};
} );

export default variations;

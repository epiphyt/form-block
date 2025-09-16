import { applyFilters } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

import meta from './block.json';

const customAttributes = {
	options: {
		default: [
			{ label: __( '- Please select -', 'form-block' ), value: '' },
		],
		query: {
			label: {
				attribute: 'label',
				source: 'attribute',
				type: 'string',
			},
			value: {
				source: 'text',
				type: 'string',
			},
		},
		selector: 'option',
		source: 'query',
		type: 'array',
	},
};

const attributes = applyFilters( 'formBlock.select.attributes', {
	...meta.attributes,
	...customAttributes,
} );

export default attributes;

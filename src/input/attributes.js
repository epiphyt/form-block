import { applyFilters } from '@wordpress/hooks';
import { getTypes } from './html-data';

import meta from './block.json';

const customAttributes = {
	type: {
		attribute: 'type',
		default: 'text',
		enum: getTypes(),
		selector: 'input',
		source: 'attribute',
		type: 'string',
	},
};

const attributes = applyFilters( 'formBlock.input.attributes', {
	...meta.attributes,
	...customAttributes,
} );

export default attributes;

import { applyFilters } from '@wordpress/hooks';
import { getTypes } from './html-data';

import meta from './block.json';

const attributes = applyFilters(
	'formBlock.input.attributes',
	meta.attributes
);

export default attributes;

import { applyFilters } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

import meta from './block.json';

const attributes = applyFilters(
	'formBlock.textarea.attributes',
	meta.attributes
);

export default attributes;

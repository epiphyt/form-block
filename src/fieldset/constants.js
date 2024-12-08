import { applyFilters } from '@wordpress/hooks';

export const ALLOWED_BLOCKS = applyFilters(
	'formBlock.fieldset.allowedBlocks',
	[
		'core/button',
		'core/buttons',
		'core/column',
		'core/columns',
		'core/group',
		'core/paragraph',
		'form-block/input',
		'form-block/select',
		'form-block/textarea',
	]
);

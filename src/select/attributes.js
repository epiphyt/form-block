import { applyFilters } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

const attributes = applyFilters( 'formBlock.select.attributes', {
	autoComplete: {
		attribute: 'autocomplete',
		selector: 'input',
		source: 'attribute',
		type: 'string',
	},
	autoCompleteSection: {
		type: 'string',
	},
	disabled: {
		attribute: 'disabled',
		selector: 'select',
		source: 'attribute',
		type: 'boolean',
	},
	label: {
		selector: '.form-block__label-content',
		source: 'html',
		type: 'string',
	},
	name: {
		attribute: 'name',
		selector: 'select',
		source: 'attribute',
		type: 'string',
	},
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
	required: {
		attribute: 'required',
		selector: 'select',
		source: 'attribute',
		type: 'boolean',
	},
	spellCheck: {
		attribute: 'spellcheck',
		default: true,
		selector: 'input',
		source: 'attribute',
		type: 'boolean',
	},
	value: {
		attribute: 'value',
		selector: 'select',
		source: 'attribute',
		type: 'string',
	},
} );

export default attributes;

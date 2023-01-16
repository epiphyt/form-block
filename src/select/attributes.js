import { __ } from '@wordpress/i18n';

const attributes = {
	autocomplete: {
		attribute: 'autocomplete',
		selector: 'select',
		source: 'attribute',
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
		source: 'text',
		type: 'string',
	},
	multiple: {
		attribute: 'multiple',
		selector: 'select',
		source: 'attribute',
		type: 'boolean',
	},
	name: {
		attribute: 'name',
		selector: 'select',
		source: 'attribute',
		type: 'string',
	},
	options: {
		default: [
			{ label: __( '- Please select -', 'form-block' ), value: '' }
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
	size: {
		attribute: 'size',
		selector: 'select',
		source: 'attribute',
		type: 'string',
	},
	value: {
		attribute: 'value',
		selector: 'select',
		source: 'attribute',
		type: 'string',
	},
};

export default attributes;

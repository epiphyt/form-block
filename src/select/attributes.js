import { __ } from '@wordpress/i18n';

const attributes = {
	autocomplete: {
		type: 'boolean',
	},
	defaultValue: {
		type: 'string',
	},
	disabled: {
		type: 'boolean',
	},
	label: {
		type: 'string',
	},
	multiple: {
		type: 'boolean',
	},
	name: {
		type: 'string',
	},
	options: {
		default: [
			{ label: __( '- Please select -', 'form-block' ), value: '' }
		],
		type: 'array',
	},
	required: {
		type: 'boolean',
	},
	size: {
		type: 'string',
	},
};

export default attributes;

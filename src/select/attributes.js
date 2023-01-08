import { __ } from '@wordpress/i18n';

const attributes = {
	defaultValue: {
		type: 'string',
	},
	label: {
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
};

export default attributes;

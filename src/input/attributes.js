import { getTypes } from './html-data';

const attributes = {
	autoComplete: {
		attribute: 'autocomplete',
		selector: 'input',
		source: 'attribute',
		type: 'string',
	},
	checked: {
		attribute: 'checked',
		selector: 'input',
		source: 'attribute',
		type: 'boolean',
	},
	customDate: {
		default: {
			showLabel: false,
			showPlaceholder: true,
			value: {},
		},
		type: 'object',
	},
	disabled: {
		attribute: 'disabled',
		selector: 'input',
		source: 'attribute',
		type: 'boolean',
	},
	isReplyTo: {
		type: 'boolean',
	},
	label: {
		selector: '.form-block__label-content',
		source: 'html',
		type: 'string',
	},
	name: {
		attribute: 'name',
		selector: 'input',
		source: 'attribute',
		type: 'string',
	},
	pattern: {
		attribute: 'pattern',
		selector: 'input',
		source: 'attribute',
		type: 'string',
	},
	placeholder: {
		attribute: 'placeholder',
		selector: 'input',
		source: 'attribute',
		type: 'string',
	},
	readOnly: {
		attribute: 'readonly',
		selector: 'input',
		source: 'attribute',
		type: 'boolean',
	},
	required: {
		attribute: 'required',
		selector: 'input',
		source: 'attribute',
		type: 'boolean',
	},
	type: {
		attribute: 'type',
		default: 'text',
		enum: getTypes(),
		selector: 'input',
		source: 'attribute',
		type: 'string',
	},
	value: {
		attribute: 'value',
		selector: 'input',
		source: 'attribute',
		type: 'string',
	},
};

export default attributes;

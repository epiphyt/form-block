import { getTypes } from './html-data';

const attributes = {
	checked: {
		attribute: 'checked',
		selector: 'input',
		source: 'attribute',
		type: 'boolean',
	},
	disabled: {
		attribute: 'disabled',
		selector: 'input',
		source: 'attribute',
		type: 'boolean',
	},
	label: {
		selector: '.form-block__label-content',
		source: 'text',
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

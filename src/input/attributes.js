import { getTypes } from './html-data';

const attributes = {
	accept: {
		attribute: 'accept',
		selector: 'input',
		source: 'attribute',
		type: 'string',
	},
	alt: {
		attribute: 'alt',
		selector: 'input',
		source: 'attribute',
		type: 'string',
	},
	autocomplete: {
		attribute: 'autocomplete',
		selector: 'input',
		source: 'attribute',
		type: 'string',
	},
	capture: {
		attribute: 'capture',
		default: '',
		enum: [
			'',
			'environment',
			'user',
		],
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
	dirname: {
		attribute: 'dirname',
		selector: 'input',
		source: 'attribute',
		type: 'string',
	},
	disabled: {
		attribute: 'disabled',
		selector: 'input',
		source: 'attribute',
		type: 'boolean',
	},
	height: {
		attribute: 'height',
		selector: 'input',
		source: 'attribute',
		type: 'string',
	},
	label: {
		selector: '.form-block__label-content',
		source: 'text',
		type: 'string',
	},
	max: {
		attribute: 'max',
		selector: 'input',
		source: 'attribute',
		type: 'string',
	},
	maxLength: {
		attribute: 'maxlength',
		selector: 'input',
		source: 'attribute',
		type: 'string',
	},
	min: {
		attribute: 'min',
		selector: 'input',
		source: 'attribute',
		type: 'string',
	},
	minLength: {
		attribute: 'minlength',
		selector: 'input',
		source: 'attribute',
		type: 'string',
	},
	multiple: {
		attribute: 'multiple',
		selector: 'input',
		source: 'attribute',
		type: 'boolean',
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
	size: {
		attribute: 'size',
		selector: 'input',
		source: 'attribute',
		type: 'string',
	},
	src: {
		attribute: 'src',
		selector: 'input',
		source: 'attribute',
		type: 'string',
	},
	step: {
		attribute: 'step',
		selector: 'input',
		source: 'attribute',
		type: 'string',
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
	width: {
		attribute: 'width',
		selector: 'input',
		source: 'attribute',
		type: 'number',
	},
};

export default attributes;

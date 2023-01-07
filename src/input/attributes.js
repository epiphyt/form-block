import { getTypes } from './html-data';

const attributes = {
	accept: {
		type: 'string',
	},
	alt: {
		type: 'string',
	},
	autocomplete: {
		type: 'boolean',
	},
	capture: {
		default: '',
		enum: [
			'',
			'environment',
			'user',
		],
		type: 'string',
	},
	checked: {
		type: 'boolean',
	},
	dirname: {
		type: 'string',
	},
	disabled: {
		type: 'boolean',
	},
	height: {
		type: 'number',
	},
	label: {
		type: 'string',
	},
	max: {
		type: 'number',
	},
	maxlength: {
		type: 'number',
	},
	min: {
		type: 'number',
	},
	minlength: {
		type: 'number',
	},
	name: {
		type: 'string',
	},
	pattern: {
		type: 'string',
	},
	placeholder: {
		type: 'string',
	},
	readonly: {
		type: 'boolean',
	},
	required: {
		type: 'boolean',
	},
	size: {
		type: 'string',
	},
	src: {
		type: 'string',
	},
	step: {
		type: 'number',
	},
	type: {
		default: 'text',
		enum: getTypes(),
		type: 'string',
	},
	value: {
		type: 'string',
	},
	width: {
		type: 'number',
	},
};

export default attributes;

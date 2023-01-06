import { getTypes } from './html-data';

const attributes = {
	label: {
		type: 'string',
	},
	required: {
		default: false,
		type: 'boolean',
	},
	type: {
		default: 'text',
		enum: getTypes(),
		type: 'string',
	},
	value: {
		type: 'string',
	},
};

export default attributes;

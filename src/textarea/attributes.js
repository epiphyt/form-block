const attributes = {
	autocomplete: {
		type: 'boolean',
	},
	disabled: {
		type: 'boolean',
	},
	label: {
		type: 'string',
	},
	maxlength: {
		type: 'number',
	},
	minlength: {
		type: 'number',
	},
	name: {
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
	rows: {
		type: 'number',
	},
	spellcheck: {
		enum: [
			'default',
			'false',
			'true',
		],
		type: 'string',
	},
	size: {
		type: 'string',
	},
	value: {
		type: 'string',
	},
};

export default attributes;

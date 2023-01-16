const attributes = {
	autocomplete: {
		attribute: 'autocomplete',
		selector: 'textarea',
		source: 'attribute',
		type: 'string',
	},
	disabled: {
		attribute: 'disabled',
		selector: 'textarea',
		source: 'attribute',
		type: 'boolean',
	},
	label: {
		selector: '.form-block__label-content',
		source: 'text',
		type: 'string',
	},
	maxLength: {
		attribute: 'maxlength',
		selector: 'textarea',
		source: 'attribute',
		type: 'number',
	},
	minLength: {
		attribute: 'minlength',
		selector: 'textarea',
		source: 'attribute',
		type: 'number',
	},
	name: {
		attribute: 'name',
		selector: 'textarea',
		source: 'attribute',
		type: 'string',
	},
	placeholder: {
		attribute: 'placeholder',
		selector: 'textarea',
		source: 'attribute',
		type: 'string',
	},
	readOnly: {
		attribute: 'readonly',
		selector: 'textarea',
		source: 'attribute',
		type: 'boolean',
	},
	required: {
		attribute: 'required',
		selector: 'textarea',
		source: 'attribute',
		type: 'boolean',
	},
	rows: {
		attribute: 'rows',
		selector: 'textarea',
		source: 'attribute',
		type: 'number',
	},
	spellCheck: {
		attribute: 'spellcheck',
		enum: [
			'default',
			'false',
			'true',
		],
		selector: 'textarea',
		source: 'attribute',
		type: 'string',
	},
	size: {
		attribute: 'size',
		selector: 'textarea',
		source: 'attribute',
		type: 'string',
	},
	value: {
		selector: 'textarea',
		source: 'text',
		type: 'string',
	},
};

export default attributes;

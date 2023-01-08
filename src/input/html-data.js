export const getTypes = () => Object.keys( types );

export const isAllowedAttribute = ( type, attribute ) => {
	if ( ! types[ type ] ) {
		return false;
	}
	
	return types[ type ].allowedAttributes.includes( attribute );
}

const types = {
	button: {
		allowedAttributes: [
			'disabled',
			'readonly',
			'required',
		],
	},
	checkbox: {
		allowedAttributes: [
			'checked',
			'disabled',
			'label',
			'required',
		],
	},
	color: {
		allowedAttributes: [
			'autocomplete',
			'disabled',
			'label',
		],
	},
	date: {
		allowedAttributes: [
			'autocomplete',
			'disabled',
			'label',
			'max',
			'min',
			'readonly',
			'required',
			'step',
		],
	},
	'datetime-local': {
		allowedAttributes: [
			'autocomplete',
			'disabled',
			'label',
			'max',
			'min',
			'readonly',
			'required',
			'step',
		],
	},
	email: {
		allowedAttributes: [
			'autocomplete',
			'disabled',
			'label',
			'maxlength',
			'minlength',
			'multiple',
			'pattern',
			'placeholder',
			'readonly',
			'required',
			'size',
		],
	},
	file: {
		allowedAttributes: [
			'accept',
			'autocomplete',
			'capture',
			'disabled',
			'label',
			'multiple',
			'readonly',
			'required',
		],
	},
	hidden: {
		allowedAttributes: [
			'autocomplete',
			'disabled',
			'label',
		],
	},
	image: {
		allowedAttributes: [
			'alt',
			'autocomplete',
			'disabled',
			'height',
			'label',
			'readonly',
			'required',
			'src',
			'width',
		],
	},
	month: {
		allowedAttributes: [
			'autocomplete',
			'disabled',
			'label',
			'max',
			'min',
			'readonly',
			'required',
			'step',
		],
	},
	number: {
		allowedAttributes: [
			'autocomplete',
			'disabled',
			'label',
			'max',
			'min',
			'placeholder',
			'readonly',
			'required',
			'step',
		],
	},
	password: {
		allowedAttributes: [
			'autocomplete',
			'disabled',
			'label',
			'maxlength',
			'minlength',
			'pattern',
			'placeholder',
			'readonly',
			'required',
			'size',
		],
	},
	radio: {
		allowedAttributes: [
			'checked',
			'disabled',
			'label',
			'required',
		],
	},
	range: {
		allowedAttributes: [
			'autocomplete',
			'disabled',
			'label',
			'max',
			'min',
			'step',
		],
	},
	reset: {
		allowedAttributes: [
			'autocomplete',
			'disabled',
			'label',
			'readonly',
			'required',
		],
	},
	search: {
		allowedAttributes: [
			'autocomplete',
			'dirname',
			'disabled',
			'label',
			'maxlength',
			'minlength',
			'pattern',
			'placeholder',
			'readonly',
			'required',
			'size',
		],
	},
	submit: {
		allowedAttributes: [
			'disabled',
		],
	},
	tel: {
		allowedAttributes: [
			'autocomplete',
			'disabled',
			'label',
			'maxlength',
			'minlength',
			'pattern',
			'placeholder',
			'readonly',
			'required',
			'size',
		],
	},
	text: {
		allowedAttributes: [
			'autocomplete',
			'dirname',
			'disabled',
			'label',
			'maxlength',
			'minlength',
			'pattern',
			'placeholder',
			'readonly',
			'required',
			'size',
		],
	},
	time: {
		allowedAttributes: [
			'autocomplete',
			'disabled',
			'label',
			'max',
			'min',
			'readonly',
			'required',
			'step',
		],
	},
	url: {
		allowedAttributes: [
			'autocomplete',
			'disabled',
			'label',
			'maxlength',
			'minlength',
			'pattern',
			'placeholder',
			'readonly',
			'required',
			'size',
		],
	},
	week: {
		allowedAttributes: [
			'autocomplete',
			'disabled',
			'label',
			'max',
			'min',
			'readonly',
			'required',
			'step',
		],
	},
};

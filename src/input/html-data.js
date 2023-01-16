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
			'readOnly',
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
			'readOnly',
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
			'readOnly',
			'required',
			'step',
		],
	},
	email: {
		allowedAttributes: [
			'autocomplete',
			'disabled',
			'label',
			'maxLength',
			'minLength',
			'multiple',
			'pattern',
			'placeholder',
			'readOnly',
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
			'readOnly',
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
			'readOnly',
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
			'readOnly',
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
			'readOnly',
			'required',
			'step',
		],
	},
	password: {
		allowedAttributes: [
			'autocomplete',
			'disabled',
			'label',
			'maxLength',
			'minLength',
			'pattern',
			'placeholder',
			'readOnly',
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
			'readOnly',
			'required',
		],
	},
	search: {
		allowedAttributes: [
			'autocomplete',
			'dirname',
			'disabled',
			'label',
			'maxLength',
			'minLength',
			'pattern',
			'placeholder',
			'readOnly',
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
			'maxLength',
			'minLength',
			'pattern',
			'placeholder',
			'readOnly',
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
			'maxLength',
			'minLength',
			'pattern',
			'placeholder',
			'readOnly',
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
			'readOnly',
			'required',
			'step',
		],
	},
	url: {
		allowedAttributes: [
			'autocomplete',
			'disabled',
			'label',
			'maxLength',
			'minLength',
			'pattern',
			'placeholder',
			'readOnly',
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
			'readOnly',
			'required',
			'step',
		],
	},
};

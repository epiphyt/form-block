export const getAllowedAttributes = ( type ) => {
	return types[ type ].allowedAttributes;
}

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
			'autoComplete',
			'disabled',
			'label',
		],
	},
	date: {
		allowedAttributes: [
			'autoComplete',
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
			'autoComplete',
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
			'autoComplete',
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
			'autoComplete',
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
			'autoComplete',
			'disabled',
			'label',
		],
	},
	image: {
		allowedAttributes: [
			'alt',
			'autoComplete',
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
			'autoComplete',
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
			'autoComplete',
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
			'autoComplete',
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
			'autoComplete',
			'disabled',
			'label',
			'max',
			'min',
			'step',
		],
	},
	reset: {
		allowedAttributes: [
			'autoComplete',
			'disabled',
			'label',
			'readOnly',
			'required',
		],
	},
	search: {
		allowedAttributes: [
			'autoComplete',
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
			'autoComplete',
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
			'autoComplete',
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
			'autoComplete',
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
			'autoComplete',
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
			'autoComplete',
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

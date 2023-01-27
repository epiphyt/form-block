import { applyFilters } from '@wordpress/hooks';

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

const types = applyFilters(
	'formBlock.input.htmlTypes',
	{
		checkbox: {
			allowedAttributes: [
				'checked',
				'disabled',
				'label',
				'required',
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
		radio: {
			allowedAttributes: [
				'checked',
				'disabled',
				'label',
				'required',
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
	},
);

import { __, _x } from '@wordpress/i18n';

export const getTypes = () => Object.keys( types );

export const isAllowedAttribute = ( type, attribute ) => {
	if ( ! types[ type ] ) {
		return false;
	}
	
	return types[ type ].allowedAttributes.includes( attribute );
}

export const inputAttributes = {
	accept: {
		label: __( 'Accept', 'form-block' ),
		description: __( 'A list of file types.', 'form-block' ),
		examples: [
			_x( '.pdf,.doc', 'input attribute example', 'form-block' ),
			_x( 'video/mp4', 'input attribute example', 'form-block' ),
			_x( 'audio/*', 'input attribute example', 'form-block' ),
			_x( 'text/*,.pdf', 'input attribute example', 'form-block' ),
		],
		moreInfoLink: __( 'https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/file#unique_file_type_specifiers', 'form-block' ),
	},
	alt: {
		label: __( 'Alternative Text', 'form-block' ),
		description: __( 'An alternative text that is displayed when the image could not be loaded or if a screen reader is used.', 'form-block' ),
	},
};

const types = {
	button: {
		allowedAttributes: [
			'disabled',
			'name',
			'readonly',
			'required',
		],
	},
	checkbox: {
		allowedAttributes: [
			'checked',
			'disabled',
			'label',
			'name',
			'required',
		],
	},
	color: {
		allowedAttributes: [
			'autocomplete',
			'disabled',
			'label',
			'name',
		],
	},
	date: {
		allowedAttributes: [
			'autocomplete',
			'disabled',
			'label',
			'max',
			'min',
			'name',
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
			'name',
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
			'name',
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
			'name',
			'readonly',
			'required',
		],
	},
	hidden: {
		allowedAttributes: [
			'autocomplete',
			'disabled',
			'label',
			'name',
		],
	},
	image: {
		allowedAttributes: [
			'alt',
			'autocomplete',
			'disabled',
			'height',
			'label',
			'name',
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
			'name',
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
			'name',
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
			'name',
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
			'name',
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
			'name',
			'step',
		],
	},
	reset: {
		allowedAttributes: [
			'autocomplete',
			'disabled',
			'label',
			'name',
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
			'name',
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
			'name',
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
			'name',
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
			'name',
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
			'name',
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
			'name',
			'readonly',
			'required',
			'step',
		],
	},
};

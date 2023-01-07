import { __, _x } from '@wordpress/i18n';

export const getTypes = () => Object.keys( types );

export const isAllowedAttribute = ( type, attribute ) => {
	if ( ! types[ type ] ) {
		return false;
	}
	
	return types[ type ].allowedAttributes.includes( attribute );
}

export const mdnAttributeLinkBase = 'https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input';

export const inputAttributes = {
	accept: {
		controlType: 'text',
		description: __( 'A list of file types.', 'form-block' ),
		examples: [
			_x( '.pdf,.doc', 'input attribute example', 'form-block' ),
			_x( 'video/mp4', 'input attribute example', 'form-block' ),
			_x( 'audio/*', 'input attribute example', 'form-block' ),
			_x( 'text/*,.pdf', 'input attribute example', 'form-block' ),
		],
		label: _x( 'Accept', 'HTML input attribute name', 'form-block' ),
	},
	alt: {
		controlType: 'text',
		description: __( 'An alternative text that is displayed when the image could not be loaded or if a screen reader is used.', 'form-block' ),
		label: _x( 'Alternative Text', 'HTML input attribute name', 'form-block' ),
	},
	autocomplete: {
		controlType: 'toggle',
		description: __( 'Whether the browser\'s autofill functionality should be enabled for this input to suggest values.', 'form-block' ),
		label: _x( 'Autocomplete', 'HTML input attribute name', 'form-block' ),
	},
	capture: {
		controlType: 'select',
		description: __( 'Which camera to use for capturing an image/video.', 'form-block' ),
		label: _x( 'Capture', 'HTML input attribute name', 'form-block' ),
		options: [
			{ label: __( 'No preference', 'form-block' ), value: '' },
			{ label: 'environment', value: 'environment' },
			{ label: 'user', value: 'user' },
		],
	},
	checked: {
		controlType: 'toggle',
		description: __( 'Whether the element is checked by default.', 'form-block' ),
		label: _x( 'Checked', 'HTML input attribute name', 'form-block' ),
	},
	dirname: {
		controlType: 'string',
		description: __( 'Allows to send the text direction of the input sent by the browser.', 'form-block' ),
		label: _x( 'Dirname', 'HTML input attribute name', 'form-block' ),
	},
	disabled: {
		controlType: 'toggle',
		description: __( 'Whether the form element is disabled and will not be submitted by sending the form.', 'form-block' ),
		label: _x( 'Disabled', 'HTML input attribute name', 'form-block' ),
	},
	height: {
		controlType: 'number',
		description: __( 'The height of the input.', 'form-block' ),
		label: _x( 'Height', 'HTML input attribute name', 'form-block' ),
	},
	max: {
		controlType: 'text',
		description: __( 'The maximum value of the input.', 'form-block' ),
		label: _x( 'Max', 'HTML input attribute name', 'form-block' ),
	},
	maxlength: {
		controlType: 'number',
		description: __( 'The maximum length (number of characters) of the value.', 'form-block' ),
		label: _x( 'Maxlength', 'HTML input attribute name', 'form-block' ),
	},
	min: {
		controlType: 'text',
		description: __( 'The minimum value of the input.', 'form-block' ),
		label: _x( 'Min', 'HTML input attribute name', 'form-block' ),
	},
	minlength: {
		controlType: 'number',
		description: __( 'The minimum length (number of characters) of the value.', 'form-block' ),
		label: _x( 'Minlength', 'HTML input attribute name', 'form-block' ),
	},
	multiple: {
		controlType: 'toggle',
		description: __( 'Whether multiple entries can be added to this input.', 'form-block' ),
		label: _x( 'Multiple', 'HTML input attribute name', 'form-block' ),
	},
	pattern: {
		controlType: 'text',
		description: __( 'A pattern the value must match to be valid. Any valid regular expression can be used (without forward slashes in the beginning or the end).', 'form-block' ),
		examples: [
			'value',
			'\d+',
			'(.*){10,20}',
		],
		label: _x( 'Pattern', 'HTML input attribute name', 'form-block' ),
	},
	placeholder: {
		controlType: 'text',
		description: __( 'A placeholder is displayed in the input before any value is added to it.', 'form-block' ),
		label: _x( 'Placeholder', 'HTML input attribute name', 'form-block' ),
	},
	readonly: {
		controlType: 'toggle',
		description: __( 'If the input is set to readonly, it cannot be edited but you can still interact with its value (e.g. mark and copy it).', 'form-block' ),
		label: _x( 'Readonly', 'HTML input attribute name', 'form-block' ),
	},
	size: {
		controlType: 'number',
		description: __( 'The size of the input.', 'form-block' ),
		label: _x( 'Size', 'HTML input attribute name', 'form-block' ),
	},
	src: {
		controlType: 'text',
		description: __( 'The URL of the image being displayed.', 'form-block' ),
		label: _x( 'Src', 'HTML input attribute name', 'form-block' ),
	},
	step: {
		controlType: 'number',
		description: __( 'Specifies the interval between valid numbers.', 'form-block' ),
		label: _x( 'Step', 'HTML input attribute name', 'form-block' ),
	},
	width: {
		controlType: 'number',
		description: __( 'The width of the input.', 'form-block' ),
		label: _x( 'Width', 'HTML input attribute name', 'form-block' ),
	},
};

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

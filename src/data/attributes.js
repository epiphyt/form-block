import { __, _x } from '@wordpress/i18n';

export const mdnAttributeLinkBase = 'https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input';

export const attributes = {
	accept: {
		controlType: 'text',
		description: __( 'A list of file types.', 'form-block' ),
		examples: [
			_x( '.pdf,.doc', 'HTML attribute example', 'form-block' ),
			_x( 'video/mp4', 'HTML attribute example', 'form-block' ),
			_x( 'audio/*', 'HTML attribute example', 'form-block' ),
			_x( 'text/*,.pdf', 'HTML attribute example', 'form-block' ),
		],
		label: _x( 'Accept', 'HTML attribute name', 'form-block' ),
	},
	alt: {
		controlType: 'text',
		description: __( 'An alternative text that is displayed when the image could not be loaded or if a screen reader is used.', 'form-block' ),
		label: _x( 'Alternative Text', 'HTML attribute name', 'form-block' ),
	},
	autocomplete: {
		controlType: 'toggle',
		description: __( 'Whether the browser\'s autofill functionality should be enabled for this form element to suggest values.', 'form-block' ),
		label: _x( 'Autocomplete', 'HTML attribute name', 'form-block' ),
	},
	capture: {
		controlType: 'select',
		description: __( 'Which camera to use for capturing an image/video.', 'form-block' ),
		label: _x( 'Capture', 'HTML attribute name', 'form-block' ),
		options: [
			{ label: __( 'No preference', 'form-block' ), value: '' },
			{ label: 'environment', value: 'environment' },
			{ label: 'user', value: 'user' },
		],
	},
	checked: {
		controlType: 'toggle',
		description: __( 'Whether the element is checked by default.', 'form-block' ),
		label: _x( 'Checked', 'HTML attribute name', 'form-block' ),
	},
	dirname: {
		controlType: 'string',
		description: __( 'Allows to send the text direction of the form element sent by the browser.', 'form-block' ),
		label: _x( 'Dirname', 'HTML attribute name', 'form-block' ),
	},
	disabled: {
		controlType: 'toggle',
		description: __( 'Whether the form element is disabled and will not be submitted by sending the form.', 'form-block' ),
		label: _x( 'Disabled', 'HTML attribute name', 'form-block' ),
	},
	height: {
		controlType: 'number',
		description: __( 'The height of the form element.', 'form-block' ),
		label: _x( 'Height', 'HTML attribute name', 'form-block' ),
	},
	max: {
		controlType: 'text',
		description: __( 'The maximum value of the form element.', 'form-block' ),
		label: _x( 'Max', 'HTML attribute name', 'form-block' ),
	},
	maxlength: {
		controlType: 'number',
		description: __( 'The maximum length (number of characters) of the value.', 'form-block' ),
		label: _x( 'Maxlength', 'HTML attribute name', 'form-block' ),
	},
	min: {
		controlType: 'text',
		description: __( 'The minimum value of the form element.', 'form-block' ),
		label: _x( 'Min', 'HTML attribute name', 'form-block' ),
	},
	minlength: {
		controlType: 'number',
		description: __( 'The minimum length (number of characters) of the value.', 'form-block' ),
		label: _x( 'Minlength', 'HTML attribute name', 'form-block' ),
	},
	multiple: {
		controlType: 'toggle',
		description: __( 'Whether multiple entries can be added to this form element.', 'form-block' ),
		label: _x( 'Multiple', 'HTML attribute name', 'form-block' ),
	},
	pattern: {
		controlType: 'text',
		description: __( 'A pattern the value must match to be valid. Any valid regular expression can be used (without forward slashes in the beginning or the end).', 'form-block' ),
		examples: [
			'value',
			'\d+',
			'(.*){10,20}',
		],
		label: _x( 'Pattern', 'HTML attribute name', 'form-block' ),
	},
	placeholder: {
		controlType: 'text',
		description: __( 'A placeholder is displayed in the form element before any value is added to it.', 'form-block' ),
		label: _x( 'Placeholder', 'HTML attribute name', 'form-block' ),
	},
	readonly: {
		controlType: 'toggle',
		description: __( 'If the form element is set to readonly, it cannot be edited but you can still interact with its value (e.g. mark and copy it).', 'form-block' ),
		label: _x( 'Readonly', 'HTML attribute name', 'form-block' ),
	},
	size: {
		controlType: 'number',
		description: __( 'The size of the form element.', 'form-block' ),
		label: _x( 'Size', 'HTML attribute name', 'form-block' ),
	},
	src: {
		controlType: 'text',
		description: __( 'The URL of the image being displayed.', 'form-block' ),
		label: _x( 'Src', 'HTML attribute name', 'form-block' ),
	},
	step: {
		controlType: 'number',
		description: __( 'Specifies the interval between valid numbers.', 'form-block' ),
		label: _x( 'Step', 'HTML attribute name', 'form-block' ),
	},
	width: {
		controlType: 'number',
		description: __( 'The width of the form element.', 'form-block' ),
		label: _x( 'Width', 'HTML attribute name', 'form-block' ),
	},
};

import { applyFilters } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

export const getAllowedAttributes = ( type ) => {
	return types[ type ].allowedAttributes;
};

export const getTypes = () => Object.keys( types );

export const isAllowedAttribute = ( type, attribute ) => {
	if ( ! types[ type ] ) {
		return false;
	}

	return types[ type ].allowedAttributes.includes( attribute );
};

export const types = applyFilters( 'formBlock.input.htmlTypes', {
	checkbox: {
		allowedAttributes: [
			'checked',
			'disabled',
			'label',
			'required',
			'value',
		],
		description: __( 'A field to enable/disable an option.', 'form-block' ),
		label: __( 'Checkbox', 'form-block' ),
	},
	color: {
		allowedAttributes: [
			'autoComplete',
			'autoCompleteSection',
			'disabled',
			'label',
		],
		description: __( 'A field to select a color.', 'form-block' ),
		label: __( 'Color selection', 'form-block' ),
	},
	date: {
		allowedAttributes: [
			'autoComplete',
			'autoCompleteSection',
			'disabled',
			'label',
			'max',
			'min',
			'readOnly',
			'required',
			'spellCheck',
			'step',
		],
		description: __(
			'A field to enter a date with day, month and year.',
			'form-block'
		),
		label: __( 'Date', 'form-block' ),
	},
	'date-custom': {
		allowedAttributes: [
			'autoComplete',
			'autoCompleteSection',
			'customDate',
			'disabled',
			'label',
			'readOnly',
			'required',
			'spellCheck',
		],
		description: __(
			'A field to enter a date in separate fields for day, month and year for improved usability.',
			'form-block'
		),
		label: __( 'Date with separate fields', 'form-block' ),
	},
	'datetime-local': {
		allowedAttributes: [
			'autoComplete',
			'autoCompleteSection',
			'disabled',
			'label',
			'max',
			'min',
			'readOnly',
			'required',
			'spellCheck',
			'step',
		],
		description: __(
			'A field to add a date with day, month and year, and a time with hour and minute.',
			'form-block'
		),
		label: __( 'Date and time', 'form-block' ),
	},
	'datetime-local-custom': {
		allowedAttributes: [
			'autoComplete',
			'autoCompleteSection',
			'customDate',
			'disabled',
			'label',
			'readOnly',
			'required',
			'spellCheck',
		],
		description: __(
			'A field to add a date in separate fields with day, month and year, and a time with hour and minute for improved usability.',
			'form-block'
		),
		label: __( 'Date and time with separate fields', 'form-block' ),
	},
	email: {
		allowedAttributes: [
			'autoComplete',
			'autoCompleteSection',
			'disabled',
			'isReplyTo',
			'label',
			'maxLength',
			'minLength',
			'multiple',
			'pattern',
			'placeholder',
			'readOnly',
			'required',
			'size',
			'spellCheck',
		],
		description: __( 'A field to enter an e-mail address.', 'form-block' ),
		label: __( 'E-mail', 'form-block' ),
	},
	file: {
		allowedAttributes: [
			'accept',
			'autoComplete',
			'autoCompleteSection',
			'capture',
			'disabled',
			'dropzone',
			'label',
			'localFiles',
			'multiple',
			'readOnly',
			'required',
		],
		description: __(
			'A field to select and upload a file from your local computer.',
			'form-block'
		),
		label: __( 'File', 'form-block' ),
	},
	hidden: {
		allowedAttributes: [ 'value' ],
		description: __(
			'A field that is invisible to the user.',
			'form-block'
		),
		label: __( 'Hidden', 'form-block' ),
	},
	image: {
		allowedAttributes: [
			'alt',
			'autoComplete',
			'autoCompleteSection',
			'disabled',
			'height',
			'readOnly',
			'required',
			'src',
			'width',
		],
		description: __(
			'A field to create an image as a submit button.',
			'form-block'
		),
		label: __( 'Image', 'form-block' ),
	},
	month: {
		allowedAttributes: [
			'autoComplete',
			'autoCompleteSection',
			'disabled',
			'label',
			'max',
			'min',
			'readOnly',
			'required',
			'spellCheck',
			'step',
		],
		description: __( 'A field to enter a month.', 'form-block' ),
		label: __( 'Month', 'form-block' ),
	},
	'month-custom': {
		allowedAttributes: [
			'autoComplete',
			'autoCompleteSection',
			'customDate',
			'disabled',
			'label',
			'readOnly',
			'required',
			'spellCheck',
		],
		description: __(
			'A field to enter a month and a year in separate fields for improved usability.',
			'form-block'
		),
		label: __( 'Month with separate fields', 'form-block' ),
	},
	number: {
		allowedAttributes: [
			'autoComplete',
			'autoCompleteSection',
			'disabled',
			'label',
			'max',
			'min',
			'placeholder',
			'readOnly',
			'required',
			'spellCheck',
			'step',
		],
		description: __( 'A field for numeric values.', 'form-block' ),
		label: __( 'Number', 'form-block' ),
	},
	password: {
		allowedAttributes: [
			'autoComplete',
			'autoCompleteSection',
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
		description: __(
			'A field that is invisible to the user.',
			'form-block'
		),
		label: __( 'Password (input not visible)', 'form-block' ),
	},
	radio: {
		allowedAttributes: [
			'checked',
			'disabled',
			'label',
			'required',
			'value',
		],
		description: __( 'An option for a set of options.', 'form-block' ),
		label: __( 'Radio button', 'form-block' ),
	},
	range: {
		allowedAttributes: [
			'autoComplete',
			'autoCompleteSection',
			'disabled',
			'label',
			'max',
			'min',
			'step',
		],
		description: __(
			'Specify a numeric value with a slider.',
			'form-block'
		),
		label: __( 'Range', 'form-block' ),
	},
	reset: {
		allowedAttributes: [ 'disabled', 'value' ],
		description: __(
			'Resets all entered values in any form elements of the current form.',
			'form-block'
		),
		label: __( 'Reset button', 'form-block' ),
	},
	search: {
		allowedAttributes: [
			'autoComplete',
			'autoCompleteSection',
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
			'spellCheck',
		],
		description: __(
			'A field to enter search queries into.',
			'form-block'
		),
		label: __( 'Search', 'form-block' ),
	},
	submit: {
		allowedAttributes: [ 'disabled', 'value' ],
		description: __( 'A button to submit the form.', 'form-block' ),
		label: __( 'Submit button', 'form-block' ),
	},
	tel: {
		allowedAttributes: [
			'autoComplete',
			'autoCompleteSection',
			'disabled',
			'label',
			'maxLength',
			'minLength',
			'pattern',
			'placeholder',
			'readOnly',
			'required',
			'size',
			'spellCheck',
		],
		description: __( 'A field for phone numbers.', 'form-block' ),
		label: __( 'Phone', 'form-block' ),
	},
	text: {
		allowedAttributes: [
			'autoComplete',
			'autoCompleteSection',
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
			'spellCheck',
		],
		description: __( 'A generic text field.', 'form-block' ),
		label: __( 'Text', 'form-block' ),
	},
	time: {
		allowedAttributes: [
			'autoComplete',
			'autoCompleteSection',
			'disabled',
			'label',
			'max',
			'min',
			'readOnly',
			'required',
			'spellCheck',
			'step',
		],
		description: __( 'A field to enter a time.', 'form-block' ),
		label: __( 'Time', 'form-block' ),
	},
	'time-custom': {
		allowedAttributes: [
			'autoComplete',
			'autoCompleteSection',
			'customDate',
			'disabled',
			'label',
			'readOnly',
			'required',
			'spellCheck',
		],
		description: __(
			'A field to enter a time in separate fields for improved usability.',
			'form-block'
		),
		label: __( 'Time with separate fields', 'form-block' ),
	},
	url: {
		allowedAttributes: [
			'autoComplete',
			'autoCompleteSection',
			'disabled',
			'label',
			'maxLength',
			'minLength',
			'pattern',
			'placeholder',
			'readOnly',
			'required',
			'size',
			'spellCheck',
		],
		description: __(
			'A field to enter a URL, usually starting with HTTPS.',
			'form-block'
		),
		label: __( 'URL', 'form-block' ),
	},
	week: {
		allowedAttributes: [
			'autoComplete',
			'autoCompleteSection',
			'disabled',
			'label',
			'max',
			'min',
			'readOnly',
			'required',
			'spellCheck',
			'step',
		],
		description: __( 'A field to enter a week.', 'form-block' ),
		label: __( 'Week', 'form-block' ),
	},
	'week-custom': {
		allowedAttributes: [
			'autoComplete',
			'autoCompleteSection',
			'customDate',
			'disabled',
			'label',
			'readOnly',
			'required',
			'spellCheck',
		],
		description: __(
			'A field to enter a week and a year in separate fields for improved usability.',
			'form-block'
		),
		label: __( 'Week with separate fields', 'form-block' ),
	},
} );

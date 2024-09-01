import { applyFilters } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

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

export const types = applyFilters(
	'formBlock.input.htmlTypes',
	{
		checkbox: {
			allowedAttributes: [
				'checked',
				'disabled',
				'label',
				'required',
				'value',
			],
			label: __( 'Checkbox', 'form-block' ),
		},
		color: {
			allowedAttributes: [
				'ariaDescription',
				'autoComplete',
				'disabled',
				'label',
			],
			label: __( 'Color selection', 'form-block' ),
		},
		date: {
			allowedAttributes: [
				'ariaDescription',
				'autoComplete',
				'disabled',
				'label',
				'max',
				'min',
				'readOnly',
				'required',
				'step',
			],
			label: __( 'Date', 'form-block' ),
		},
		'date-custom': {
			allowedAttributes: [
				'ariaDescription',
				'autoComplete',
				'customDate',
				'disabled',
				'label',
				'readOnly',
				'required',
			],
			label: __( 'Date with separate fields', 'form-block' ),
		},
		'datetime-local': {
			allowedAttributes: [
				'ariaDescription',
				'autoComplete',
				'disabled',
				'label',
				'max',
				'min',
				'readOnly',
				'required',
				'step',
			],
			label: __( 'Date and time', 'form-block' ),
		},
		'datetime-local-custom': {
			allowedAttributes: [
				'ariaDescription',
				'autoComplete',
				'customDate',
				'disabled',
				'label',
				'readOnly',
				'required',
			],
			label: __( 'Date and time with separate fields', 'form-block' ),
		},
		email: {
			allowedAttributes: [
				'autoComplete',
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
			],
			label: __( 'E-mail', 'form-block' ),
		},
		file: {
			allowedAttributes: [
				'accept',
				'autoComplete',
				'capture',
				'disabled',
				'dropzone',
				'label',
				'localFiles',
				'multiple',
				'readOnly',
				'required',
			],
			label: __( 'File', 'form-block' ),
		},
		hidden: {
			allowedAttributes: [],
			label: __( 'Hidden', 'form-block' ),
		},
		image: {
			allowedAttributes: [
				'ariaDescription',
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
			label: __( 'Image', 'form-block' ),
		},
		month: {
			allowedAttributes: [
				'ariaDescription',
				'autoComplete',
				'disabled',
				'label',
				'max',
				'min',
				'readOnly',
				'required',
				'step',
			],
			label: __( 'Month', 'form-block' ),
		},
		'month-custom': {
			allowedAttributes: [
				'ariaDescription',
				'autoComplete',
				'customDate',
				'disabled',
				'label',
				'readOnly',
				'required',
			],
			label: __( 'Month with separate fields', 'form-block' ),
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
			label: __( 'Number', 'form-block' ),
		},
		password: {
			allowedAttributes: [
				'ariaDescription',
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
			label: __( 'Radio button', 'form-block' ),
		},
		range: {
			allowedAttributes: [
				'ariaDescription',
				'autoComplete',
				'disabled',
				'label',
				'max',
				'min',
				'step',
			],
			label: __( 'Range', 'form-block' ),
		},
		reset: {
			allowedAttributes: [
				'disabled',
				'value',
			],
			label: __( 'Reset button', 'form-block' ),
		},
		search: {
			allowedAttributes: [
				'ariaDescription',
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
			label: __( 'Search', 'form-block' ),
		},
		submit: {
			allowedAttributes: [
				'disabled',
				'value',
			],
			label: __( 'Submit button', 'form-block' ),
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
			label: __( 'Telephone', 'form-block' ),
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
			label: __( 'Text', 'form-block' ),
		},
		time: {
			allowedAttributes: [
				'ariaDescription',
				'autoComplete',
				'disabled',
				'label',
				'max',
				'min',
				'readOnly',
				'required',
				'step',
			],
			label: __( 'Time', 'form-block' ),
		},
		'time-custom': {
			allowedAttributes: [
				'ariaDescription',
				'autoComplete',
				'customDate',
				'disabled',
				'label',
				'readOnly',
				'required',
			],
			label: __( 'Time with separate fields', 'form-block' ),
		},
		url: {
			allowedAttributes: [
				'ariaDescription',
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
			label: __( 'URL', 'form-block' ),
		},
		week: {
			allowedAttributes: [
				'ariaDescription',
				'autoComplete',
				'disabled',
				'label',
				'max',
				'min',
				'readOnly',
				'required',
				'step',
			],
			label: __( 'Week', 'form-block' ),
		},
		'week-custom': {
			allowedAttributes: [
				'ariaDescription',
				'autoComplete',
				'customDate',
				'disabled',
				'label',
				'readOnly',
				'required',
			],
			label: __( 'Week with separate fields', 'form-block' ),
		},
	},
);

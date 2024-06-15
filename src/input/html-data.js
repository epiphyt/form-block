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
				'value',
			],
		},
		color: {
			allowedAttributes: [
				'ariaDescription',
				'autoComplete',
				'disabled',
				'label',
			],
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
			allowedAttributes: [],
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
		},
		radio: {
			allowedAttributes: [
				'checked',
				'disabled',
				'label',
				'required',
				'value',
			],
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
		},
		reset: {
			allowedAttributes: [
				'disabled',
				'value',
			],
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
		},
		submit: {
			allowedAttributes: [
				'disabled',
				'value',
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
		},
	},
);

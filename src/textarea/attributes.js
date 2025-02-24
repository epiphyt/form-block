import { applyFilters } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

const attributes = applyFilters( 'formBlock.textarea.attributes', {
	autoComplete: {
		attribute: 'autocomplete',
		selector: 'input',
		source: 'attribute',
		type: 'string',
	},
	autoCompleteSection: {
		type: 'string',
	},
	disabled: {
		attribute: 'disabled',
		selector: 'textarea',
		source: 'attribute',
		type: 'boolean',
	},
	label: {
		selector: '.form-block__label-content',
		source: 'html',
		type: 'string',
	},
	name: {
		attribute: 'name',
		selector: 'textarea',
		source: 'attribute',
		type: 'string',
	},
	placeholder: {
		attribute: 'placeholder',
		selector: 'textarea',
		source: 'attribute',
		type: 'string',
	},
	readOnly: {
		attribute: 'readonly',
		selector: 'textarea',
		source: 'attribute',
		type: 'boolean',
	},
	required: {
		attribute: 'required',
		selector: 'textarea',
		source: 'attribute',
		type: 'boolean',
	},
	spellCheck: {
		attribute: 'spellcheck',
		default: true,
		selector: 'input',
		source: 'attribute',
		type: 'boolean',
	},
	value: {
		selector: 'textarea',
		source: 'text',
		type: 'string',
	},
} );

export default attributes;

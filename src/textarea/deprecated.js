import { RichText, useBlockProps } from '@wordpress/block-editor';

const v1 = {
	attributes: {
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
	},
	save( props ) {
		const {
			attributes: {
				autoComplete,
				disabled,
				label,
				maxLength,
				minLength,
				name,
				placeholder,
				readOnly,
				required,
				rows,
				spellCheck,
				size,
				value,
			},
		} = props;
		const blockProps = useBlockProps.save( {
			className: 'form-block__element',
		} );
		const elementProps = {
			autoComplete,
			disabled,
			maxLength,
			minLength,
			name,
			placeholder,
			readOnly,
			required,
			rows,
			spellCheck,
			size,
			value,
		};

		return (
			<div { ...blockProps }>
				<textarea { ...elementProps } />
				<label className="form-block__label is-textarea-label">
					<RichText.Content
						className="form-block__label-content"
						tagName="span"
						value={ label }
					/>
					{ required ? <span className="is-required">*</span> : '' }
				</label>
			</div>
		);
	},
};

const v2 = {
	attributes: {
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
			selector: 'textarea',
			source: 'attribute',
			type: 'boolean',
		},
		value: {
			selector: 'textarea',
			source: 'text',
			type: 'string',
		},
	},
	migrate( attributes ) {
		attributes.spellCheck = true;

		return attributes;
	},
	save( props ) {
		const {
			attributes: {
				autoComplete,
				disabled,
				label,
				maxLength,
				minLength,
				name,
				placeholder,
				readOnly,
				required,
				rows,
				size,
				value,
			},
		} = props;
		const blockProps = useBlockProps.save( {
			className: 'form-block__element',
		} );
		const elementProps = {
			autoComplete,
			disabled,
			maxLength,
			minLength,
			name,
			placeholder,
			readOnly,
			required,
			rows,
			size,
			value,
		};

		return (
			<div { ...blockProps }>
				<textarea { ...elementProps } />
				<label className="form-block__label is-textarea-label">
					<RichText.Content
						className="form-block__label-content"
						tagName="span"
						value={ label }
					/>
					{ required ? (
						<span className="is-required" aria-hidden="true">
							*
						</span>
					) : (
						''
					) }
				</label>
			</div>
		);
	},
};

const v3 = {
	attributes: {
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
			selector: 'textarea',
			source: 'attribute',
			type: 'boolean',
		},
		value: {
			selector: 'textarea',
			source: 'text',
			type: 'string',
		},
	},
	migrate( attributes ) {
		attributes.spellCheck = true;

		return attributes;
	},
	save( props ) {
		const {
			attributes: {
				autoComplete,
				disabled,
				label,
				maxLength,
				minLength,
				name,
				placeholder,
				readOnly,
				required,
				rows,
				spellCheck,
				size,
				value,
			},
		} = props;
		const blockProps = useBlockProps.save( {
			className: 'form-block__element',
		} );
		const elementProps = {
			autoComplete,
			disabled,
			maxLength,
			minLength,
			name,
			placeholder,
			readOnly,
			required,
			rows,
			spellCheck,
			size,
			value,
		};

		return (
			<div { ...blockProps }>
				<textarea { ...elementProps } />
				<label className="form-block__label is-textarea-label">
					<RichText.Content
						className="form-block__label-content"
						tagName="span"
						value={ label }
					/>
					{ required ? (
						<span className="is-required" aria-hidden="true">
							*
						</span>
					) : (
						''
					) }
				</label>
			</div>
		);
	},
};

export default [ v3, v2, v1 ];

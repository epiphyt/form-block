import { RichText, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

const v1 = {
	attributes: {
		disabled: {
			attribute: 'disabled',
			selector: 'select',
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
			selector: 'select',
			source: 'attribute',
			type: 'string',
		},
		options: {
			default: [
				{ label: __( '- Please select -', 'form-block' ), value: '' }
			],
			query: {
				label: {
					attribute: 'label',
					source: 'attribute',
					type: 'string',
				},
				value: {
					source: 'text',
					type: 'string',
				},
			},
			selector: 'option',
			source: 'query',
			type: 'array',
		},
		required: {
			attribute: 'required',
			selector: 'select',
			source: 'attribute',
			type: 'boolean',
		},
		value: {
			attribute: 'value',
			selector: 'select',
			source: 'attribute',
			type: 'string',
		},
	},
	save( props ) {
		const {
			attributes: {
				autoComplete,
				disabled,
				label,
				multiple,
				name,
				options,
				required,
				size,
			}
		} = props;
		const blockProps = useBlockProps.save( { className: 'form-block__element' } );
		const elementProps = {
			autoComplete,
			disabled,
			multiple,
			name,
			required,
			size,
		}
		
		return (
			<div { ...blockProps }>
				<select { ...elementProps }>
					{ options.map( ( option, index ) => (
						<option
							key={ index }
							label={ option.label }
						>
							{ option.value }
						</option>
					) ) }
				</select>
				<label
					className="form-block__label is-textarea-label"
				>
					<RichText.Content
						className="form-block__label-content"
						tagName="span"
						value={ label }
					/>
					{ required ? <span className="is-required">*</span> : '' }
				</label>
			</div>
		);
	}
};

export default [ v1 ];

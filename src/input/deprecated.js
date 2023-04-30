import { useBlockProps } from '@wordpress/block-editor';

import { getAllowedAttributes, getTypes } from './html-data';

const v1 = {
	attributes: {
		checked: {
			attribute: 'checked',
			selector: 'input',
			source: 'attribute',
			type: 'boolean',
		},
		disabled: {
			attribute: 'disabled',
			selector: 'input',
			source: 'attribute',
			type: 'boolean',
		},
		isReplyTo: {
			type: 'boolean',
		},
		label: {
			selector: '.form-block__label-content',
			source: 'html',
			type: 'string',
		},
		name: {
			attribute: 'name',
			selector: 'input',
			source: 'attribute',
			type: 'string',
		},
		pattern: {
			attribute: 'pattern',
			selector: 'input',
			source: 'attribute',
			type: 'string',
		},
		placeholder: {
			attribute: 'placeholder',
			selector: 'input',
			source: 'attribute',
			type: 'string',
		},
		readOnly: {
			attribute: 'readonly',
			selector: 'input',
			source: 'attribute',
			type: 'boolean',
		},
		required: {
			attribute: 'required',
			selector: 'input',
			source: 'attribute',
			type: 'boolean',
		},
		type: {
			attribute: 'type',
			default: 'text',
			enum: getTypes(),
			selector: 'input',
			source: 'attribute',
			type: 'string',
		},
		value: {
			attribute: 'value',
			selector: 'input',
			source: 'attribute',
			type: 'string',
		},
	},
	save( props ) {
		const {
			attributes: {
				label,
				name,
				required,
				type,
			}
		} = props;
		const blockProps = useBlockProps.save( { className: 'form-block__element' } );
		const allowedAttributes = getAllowedAttributes( type );
		let elementProps = {
			name,
			type,
		};
		
		for ( const allowedAttribute of allowedAttributes ) {
			if ( allowedAttribute === 'label' ) {
				continue;
			}
			
			elementProps[ allowedAttribute ] = props.attributes[ allowedAttribute ];
		}
		
		blockProps.className += ' is-type-' + type;
		
		return (
			<div { ...blockProps }>
				<input { ...elementProps } />
				{ type !== 'hidden' && type !== 'reset' && type !== 'submit'
					? <label
						className="form-block__label is-input-label"
					>
						<span className="form-block__label-content">{ label }</span>
						{ required ? <span className="is-required">*</span> : '' }
					</label>
					: null
				}
			</div>
		);
	}
};

export default [ v1 ];

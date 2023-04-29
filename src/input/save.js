import { RichText, useBlockProps } from '@wordpress/block-editor';
import { getAllowedAttributes } from './html-data';

export default function InputSave( props ) {
	const {
		attributes: {
			label,
			name,
			required,
			type,
		}
	} = props;
	const allowedAttributes = getAllowedAttributes( type );
	const blockProps = useBlockProps.save( { className: 'form-block__element' } );
	let elementProps = {
		name,
		type,
	};
	const isButton = type === 'reset' || type === 'submit';
	
	for ( const allowedAttribute of allowedAttributes ) {
		if ( allowedAttribute === 'label' ) {
			continue;
		}
		
		elementProps[ allowedAttribute ] = props.attributes[ allowedAttribute ];
	}
	
	blockProps.className += ' is-type-' + type;
	
	if ( isButton ) {
		blockProps.className += ' wp-block-button';
		elementProps.className += ' wp-block-button__link wp-element-button';
	}
	
	return (
		<div { ...blockProps }>
			<input { ...elementProps } />
			{ type !== 'hidden' && type !== 'reset' && type !== 'submit'
				? <label
					className="form-block__label is-input-label"
				>
					<RichText.Content
						value={ label }
					/>
					{ required ? <span className="is-required">*</span> : '' }
				</label>
				: null
			}
		</div>
	);
}

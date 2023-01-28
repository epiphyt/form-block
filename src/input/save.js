import { useBlockProps } from '@wordpress/block-editor';
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

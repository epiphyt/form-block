import { useBlockProps } from '@wordpress/block-editor';
import { getAllowedAttributes } from './html-data';

export default function InputSave( props ) {
	const {
		attributes: {
			label,
			required,
			type,
		}
	} = props;
	const blockProps = useBlockProps.save( { className: 'form-block__element' } );
	const allowedAttributes = getAllowedAttributes( type );
	let elementProps = {
		type,
	};
	
	for ( const allowedAttribute of allowedAttributes ) {
		if ( allowedAttribute === 'label' ) {
			continue;
		}
		
		elementProps[ allowedAttribute ] = props.attributes[ allowedAttribute ];
	}
	
	return (
		<div { ...blockProps }>
			<input { ...elementProps } />
			<label
				className="form-block__label is-input-label"
			>
				<span className="form-block__label-content">{ label }</span>
				{ required ? <span className="is-required">*</span> : '' }
			</label>
		</div>
	);
}

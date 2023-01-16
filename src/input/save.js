import { useBlockProps } from '@wordpress/block-editor';
import { applyFilters } from '@wordpress/hooks';

export default function InputSave( props ) {
	const {
		attributes: {
			label,
			required,
			type,
		}
	} = props;
	const blockProps = useBlockProps.save( { className: 'form-block__element' } );
	const elementProps = applyFilters(
		'formBlock.input.elementProps',
		{
			required,
			type,
		},
		props.attributes,
	);
	
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

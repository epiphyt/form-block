import { useBlockProps } from '@wordpress/block-editor';

export default function InputSave( props ) {
	const {
		attributes: {
			accept,
			alt,
			autocomplete,
			capture,
			checked,
			dirname,
			disabled,
			height,
			label,
			max,
			maxLength,
			min,
			minLength,
			multiple,
			name,
			pattern,
			placeholder,
			readOnly,
			required,
			size,
			src,
			step,
			type,
			value,
			width,
		}
	} = props;
	const blockProps = useBlockProps.save( { className: 'form-block__element' } );
	const elementProps = {
		accept,
		alt,
		autocomplete,
		capture,
		checked,
		dirname,
		disabled,
		height,
		max,
		maxLength,
		min,
		minLength,
		multiple,
		name,
		pattern,
		placeholder,
		readOnly,
		required,
		size,
		src,
		step,
		type,
		value,
		width,
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

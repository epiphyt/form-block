import { useBlockProps } from '@wordpress/block-editor';

export default function TextareaSave( props ) {
	const {
		attributes: {
			autocomplete,
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
		}
	} = props;
	const blockProps = useBlockProps.save( { className: 'form-block__element' } );
	const elementProps = {
		autocomplete,
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
	}
	
	return (
		<div { ...blockProps }>
			<textarea { ...elementProps } />
			<label
				className="form-block__label is-textarea-label"
			>
				<span className="form-block__label-content">{ label }</span>
				{ required ? <span className="is-required">*</span> : '' }
			</label>
		</div>
	);
}

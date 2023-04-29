import { RichText, useBlockProps } from '@wordpress/block-editor';

export default function TextareaSave( props ) {
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
		}
	} = props;
	const blockProps = useBlockProps.save( { className: 'form-block__element' } );
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
	}
	
	return (
		<div { ...blockProps }>
			<textarea { ...elementProps } />
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

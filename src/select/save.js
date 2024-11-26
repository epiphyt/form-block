import { RichText, useBlockProps } from '@wordpress/block-editor';

export default function SelectSave( props ) {
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
		},
	} = props;
	const blockProps = useBlockProps.save( {
		className: 'form-block__element',
	} );
	const elementProps = {
		autoComplete,
		disabled,
		multiple,
		name,
		required,
		size,
	};

	return (
		<div { ...blockProps }>
			<select { ...elementProps }>
				{ options.map( ( option, index ) => (
					<option key={ index } label={ option.label || false }>
						{ option.value }
					</option>
				) ) }
			</select>
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
}

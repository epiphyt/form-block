import { useBlockProps } from '@wordpress/block-editor';

export default function SelectSave( props ) {
	const {
		attributes: {
			autocomplete,
			disabled,
			label,
			multiple,
			name,
			options,
			required,
			size,
		}
	} = props;
	const blockProps = useBlockProps.save( { className: 'form-element' } );
	const elementProps = {
		autocomplete,
		disabled,
		multiple,
		name,
		required,
		size,
	}
	console.log( {options} );
	
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
				className="form-block__label is-select-label"
			><span className="form-block__label-content">{ label }</span>{ required ? <span className="is-required">*</span> : '' }</label>
		</div>
	);
}

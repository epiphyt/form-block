import {
	RichText,
	useBlockProps,
	__experimentalGetBorderClassesAndStyles as getBorderClassesAndStyles,
	__experimentalGetColorClassesAndStyles as getColorClassesAndStyles,
	__experimentalGetShadowClassesAndStyles as getShadowClassesAndStyles,
} from '@wordpress/block-editor';
import clsx from 'clsx';

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
	const borderProps = getBorderClassesAndStyles( props.attributes );
	const colorProps = getColorClassesAndStyles( props.attributes );
	const shadowProps = getShadowClassesAndStyles( props.attributes );
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
			<select
				className={ clsx(
					borderProps.className,
					shadowProps.className,
					colorProps.className
				) }
				style={ {
					...borderProps.style,
					...shadowProps.style,
					...colorProps.style,
				} }
				{ ...elementProps }
			>
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

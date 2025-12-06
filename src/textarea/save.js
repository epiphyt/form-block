import {
	RichText,
	useBlockProps,
	__experimentalGetBorderClassesAndStyles as getBorderClassesAndStyles,
	__experimentalGetColorClassesAndStyles as getColorClassesAndStyles,
	__experimentalGetShadowClassesAndStyles as getShadowClassesAndStyles,
} from '@wordpress/block-editor';
import clsx from 'clsx';

export default function TextareaSave( props ) {
	const {
		attributes: {
			autoComplete,
			cols,
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
			wrap,
		},
	} = props;
	const borderProps = getBorderClassesAndStyles( props.attributes );
	const colorProps = getColorClassesAndStyles( props.attributes );
	const shadowProps = getShadowClassesAndStyles( props.attributes );
	const blockProps = useBlockProps.save( {
		className: 'form-block__element',
	} );
	const elementProps = {
		autoComplete,
		cols,
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
		wrap,
	};
	const filteredProps = Object.keys( elementProps ).reduce(
		( r, key ) => (
			elementProps[ key ] && ( r[ key ] = elementProps[ key ] ), r
		),
		{}
	);

	return (
		<div { ...blockProps }>
			<textarea
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
				{ ...filteredProps }
			/>
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

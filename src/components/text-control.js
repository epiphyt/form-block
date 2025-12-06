import {
	__experimentalUseBorderProps as useBorderProps,
	__experimentalGetShadowClassesAndStyles as useShadowProps,
	__experimentalUseColorProps as useColorProps,
} from '@wordpress/block-editor';
import { BaseControl } from '@wordpress/components';
import { useInstanceId } from '@wordpress/compose';
import { __ } from '@wordpress/i18n';
import clsx from 'clsx';

/**
 * Custom text control styling options for the input.
 *
 * @param {object} props Block props
 * @returns {JSX.Element}
 */
export default function FormBlockTextControl( {
	elementProps,
	hideLabelFromVision,
	label,
	onFieldUpdate,
	placeholder,
	type,
	...props
} ) {
	const { attributes, setAttributes } = props;
	const { value } = attributes;
	const borderProps = useBorderProps( attributes );
	const colorProps = useColorProps( attributes );
	const shadowProps = useShadowProps( attributes );
	const fieldValue = value || '';
	const id = useInstanceId( BaseControl, 'inspector-text-control', '' );
	const classNameBorder = borderProps.className || '';
	const classNameColor = colorProps.className || '';
	const classNameShadow = shadowProps.className || '';
	const customStyle = {
		...borderProps.style,
		...colorProps.style,
		...shadowProps.style,
	};

	if ( ! label ) {
		label = attributes.label;
	}

	if ( ! type ) {
		type = attributes.type;
	}

	const isButton = type === 'reset' || type === 'submit';

	return (
		<div
			className={ clsx( {
				'wp-block-button__link wp-element-button': isButton,
				[ classNameBorder ]: isButton,
				[ classNameColor ]: isButton,
				[ classNameShadow ]: isButton,
			} ) }
			style={ isButton ? customStyle : null }
		>
			<BaseControl
				hideLabelFromVision={
					hideLabelFromVision !== undefined
						? hideLabelFromVision
						: true
				}
				id={ id }
				label={ label }
			>
				<input
					{ ...elementProps }
					className={ clsx( 'components-text-control__input', {
						[ classNameBorder ]: ! isButton,
						[ classNameColor ]: ! isButton,
						[ classNameShadow ]: ! isButton,
					} ) }
					id={ id }
					onChange={ ( event ) =>
						onFieldUpdate
							? onFieldUpdate.onUpdate(
									onFieldUpdate.field,
									event.target.value
							  )
							: setAttributes( {
									value: event.target.value,
							  } )
					}
					placeholder={ placeholder }
					style={ ! isButton ? customStyle : null }
					type={ type }
					value={ fieldValue }
				/>
			</BaseControl>
		</div>
	);
}

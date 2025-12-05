import {
	RichText,
	useBlockProps,
	__experimentalGetBorderClassesAndStyles as getBorderClassesAndStyles,
	__experimentalGetColorClassesAndStyles as getColorClassesAndStyles,
	__experimentalGetShadowClassesAndStyles as getShadowClassesAndStyles,
} from '@wordpress/block-editor';
import clsx from 'clsx';

import { getAllowedAttributes } from './html-data';

export default function InputSave( props ) {
	const {
		attributes: { label, name, required, type },
	} = props;
	const allowedAttributes = getAllowedAttributes( type );
	const blockProps = useBlockProps.save( {
		className: 'form-block__element',
	} );
	const borderProps = getBorderClassesAndStyles( props.attributes );
	const colorProps = getColorClassesAndStyles( props.attributes );
	const shadowProps = getShadowClassesAndStyles( props.attributes );
	let elementProps = {
		name,
		type,
	};
	const isButton = type === 'reset' || type === 'submit';

	for ( const allowedAttribute of allowedAttributes ) {
		if ( allowedAttribute === 'label' ) {
			continue;
		}

		elementProps[ allowedAttribute ] = props.attributes[ allowedAttribute ];
	}

	blockProps.className += ' is-type-' + type;

	if ( isButton ) {
		blockProps.className += ' wp-block-button';

		if ( elementProps.className ) {
			elementProps.className +=
				' wp-block-button__link wp-element-button';
		} else {
			elementProps.className = 'wp-block-button__link wp-element-button';
		}
	}

	return (
		<div { ...blockProps }>
			<input
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
			/>
			{ type !== 'hidden' && type !== 'reset' && type !== 'submit' ? (
				<label className="form-block__label is-input-label">
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
			) : null }
		</div>
	);
}

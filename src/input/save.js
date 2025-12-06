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

	blockProps.className = clsx( blockProps.className, 'is-type-' + type, {
		'wp-block-button': isButton,
	} );

	elementProps.className = clsx(
		borderProps.className,
		colorProps.className,
		elementProps.className,
		shadowProps.className,
		{
			'wp-block-button__link wp-element-button': isButton,
		}
	);

	return (
		<div { ...blockProps }>
			<input
				style={ {
					...borderProps.style,
					...colorProps.style,
					...shadowProps.style,
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

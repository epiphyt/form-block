import {
	RichText,
	useBlockProps,
	__experimentalUseBorderProps as useBorderProps,
	__experimentalGetShadowClassesAndStyles as useShadowProps,
	__experimentalUseColorProps as useColorProps,
} from '@wordpress/block-editor';
import {
	BaseControl,
	Button,
	Flex,
	FlexBlock,
	FlexItem,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import { useRef } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { error } from '@wordpress/icons';
import clsx from 'clsx';

import Controls from './controls';
import { isAllowedAttribute } from './html-data';
import { CustomDate, isCustomDate } from './modules/custom-date';
import { stripSpecialChars } from '../data/util';
import { useInstanceId } from '@wordpress/compose';

export default function InputEdit( props ) {
	const { attributes, setAttributes } = props;
	const {
		accept,
		alt,
		autoComplete,
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
		spellCheck,
		src,
		step,
		type,
		value,
		width,
	} = attributes;
	const blockProps = useBlockProps();
	const borderProps = useBorderProps( attributes );
	const colorProps = useColorProps( attributes );
	const shadowProps = useShadowProps( attributes );
	const elementProps = {
		accept,
		alt,
		autoComplete,
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
		spellCheck,
		src,
		step,
		type,
		value,
		width,
	};
	const isButton = type === 'reset' || type === 'submit';
	const nameControlRef = useRef( null );

	blockProps.className += ' is-type-' + type;

	if ( type === 'hidden' ) {
		elementProps.help = __(
			'This input is hidden in the frontend.',
			'form-block'
		);
		elementProps.type = 'text';
	}

	if ( isButton ) {
		blockProps.className += ' wp-block-button';
	}

	const fieldValue = value || '';
	const id = useInstanceId( BaseControl, 'inspector-text-control', '' );
	const nameAttribute = name
		? stripSpecialChars( name, false )
		: stripSpecialChars( label );

	return (
		<div { ...blockProps }>
			<Controls nameControlRef={ nameControlRef } { ...props } />

			{ type === 'checkbox' || type === 'radio' ? (
				<Flex>
					<FlexItem>
						<TextControl
							onChange={ ( value ) => setAttributes( { value } ) }
							{ ...elementProps }
						/>
					</FlexItem>

					{ isAllowedAttribute( type, 'label' ) ? (
						<FlexBlock>
							<RichText
								className="form-block__label"
								onChange={ ( newLabel ) => {
									const oldLabel = label;

									setAttributes( { label: newLabel } );

									if ( ! value || value === oldLabel ) {
										setAttributes( { value: newLabel } );
									}
								} }
								placeholder={ __( 'Label', 'form-block' ) }
								tagName="label"
								value={ label || '' }
							/>
						</FlexBlock>
					) : null }

					{ isAllowedAttribute( type, 'required' ) ? (
						<FlexItem>
							<ToggleControl
								checked={ !! required }
								label={ __( 'Required', 'form-block' ) }
								onChange={ ( required ) =>
									setAttributes( { required } )
								}
								value={ required || false }
							/>
						</FlexItem>
					) : null }
				</Flex>
			) : (
				<>
					{ isAllowedAttribute( type, 'label' ) ||
					isAllowedAttribute( type, 'required' ) ? (
						<Flex align="center">
							{ isAllowedAttribute( type, 'label' ) ? (
								<FlexBlock>
									<RichText
										className="form-block__label"
										onChange={ ( label ) =>
											setAttributes( { label } )
										}
										placeholder={ __(
											'Label',
											'form-block'
										) }
										tagName="label"
										value={ label || '' }
									/>
								</FlexBlock>
							) : null }
							{ nameAttribute &&
							! nameAttribute.startsWith(
								stripSpecialChars( label )
							) ? (
								<FlexItem className="form-block__no-line-height">
									<Button
										aria-label={ __(
											'The label does not match the name of the field.',
											'form-block'
										) }
										className="form-block__is-warning"
										icon={ error }
										label={ __(
											'The label does not match the name of the field.',
											'form-block'
										) }
										onClick={ () => {
											if ( nameControlRef.current ) {
												nameControlRef.current.focus();
											}
										} }
										showTooltip={ true }
									/>
								</FlexItem>
							) : null }
							{ isAllowedAttribute( type, 'required' ) ? (
								<FlexItem>
									<ToggleControl
										checked={ !! required }
										label={ __( 'Required', 'form-block' ) }
										onChange={ ( required ) =>
											setAttributes( { required } )
										}
										value={ required || false }
									/>
								</FlexItem>
							) : null }
						</Flex>
					) : null }

					{ isCustomDate( type ) ? (
						<CustomDate
							elementProps={ elementProps }
							props={ props }
						/>
					) : (
						<BaseControl
							className={ clsx( {
								'wp-block-button__link wp-element-button':
									isButton,
							} ) }
							hideLabelFromVision={ true }
							id={ id }
							label={ label }
						>
							<input
								className={ clsx(
									'components-text-control__input',
									borderProps.className,
									shadowProps.className,
									colorProps.className
								) }
								id={ id }
								onChange={ ( event ) =>
									setAttributes( {
										value: event.target.value,
									} )
								}
								style={ {
									...borderProps.style,
									...shadowProps.style,
									...colorProps.style,
								} }
								type={ type }
								value={ fieldValue }
								{ ...elementProps }
							/>
						</BaseControl>
					) }
				</>
			) }
		</div>
	);
}

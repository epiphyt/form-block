import { RichText, useBlockProps } from '@wordpress/block-editor';
import {
	Button,
	Flex,
	FlexBlock,
	FlexItem,
	TextareaControl,
	ToggleControl,
} from '@wordpress/components';
import { useRef } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { error } from '@wordpress/icons';

import Controls from './controls';
import { stripSpecialChars } from '../data/util';

export default function TextareaEdit( props ) {
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
		setAttributes,
	} = props;
	const blockProps = useBlockProps();
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
	const nameControlRef = useRef( null );
	const nameAttribute = name
		? stripSpecialChars( name, false )
		: stripSpecialChars( label );

	return (
		<div { ...blockProps }>
			<Controls nameControlRef={ nameControlRef } { ...props } />

			<Flex align="center">
				<FlexBlock>
					<RichText
						className="form-block__label"
						onChange={ ( label ) => setAttributes( { label } ) }
						placeholder={ __( 'Label', 'form-block' ) }
						tagName="label"
						value={ label || '' }
					/>
				</FlexBlock>
				{ nameAttribute &&
				! nameAttribute.startsWith( stripSpecialChars( label ) ) ? (
					<FlexItem className="form-block__no-line-height">
						<Button
							aria-label={ __(
								'The label does not match the name of the field.',
								'form-block'
							) }
							className="form-block__is-warning"
							icon={ error }
							onClick={ () => {
								if ( nameControlRef.current ) {
									nameControlRef.current.focus();
								}
							} }
							showTooltip={ true }
						/>
					</FlexItem>
				) : null }
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
			</Flex>

			<TextareaControl
				onChange={ ( value ) => setAttributes( { value } ) }
				{ ...filteredProps }
			/>
		</div>
	);
}

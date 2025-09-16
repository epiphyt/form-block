import { RichText, useBlockProps } from '@wordpress/block-editor';
import {
	Flex,
	FlexBlock,
	FlexItem,
	TextareaControl,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import Controls from './controls';

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

	return (
		<div { ...blockProps }>
			<Controls { ...props } />

			<Flex>
				<FlexBlock>
					<RichText
						className="form-block__label"
						onChange={ ( label ) => setAttributes( { label } ) }
						placeholder={ __( 'Label', 'form-block' ) }
						tagName="label"
						value={ label || '' }
					/>
				</FlexBlock>

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

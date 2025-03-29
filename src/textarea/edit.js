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
		},
		setAttributes,
	} = props;
	console.log( { spellCheck } );
	const blockProps = useBlockProps();
	const elementProps = {
		autoComplete,
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
	};

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
						value={ required }
					/>
				</FlexItem>
			</Flex>

			<TextareaControl
				onChange={ ( value ) => setAttributes( { value } ) }
				{ ...elementProps }
			/>
		</div>
	);
}

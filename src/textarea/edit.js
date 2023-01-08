import { useBlockProps } from '@wordpress/block-editor';
import {
	Flex,
	FlexBlock,
	FlexItem,
	TextareaControl,
	TextControl,
	ToggleControl
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import Controls from './controls';

export default function TextareaEdit( props ) {
	const {
		attributes: {
			label,
			required,
			value,
		},
		setAttributes,
	} = props;
	const blockProps = useBlockProps();
	
	return (
		<div { ...blockProps }>
			<Controls { ...props } />
			
			<Flex>
				<FlexBlock>
					<TextControl
						className="form-block__label-control"
						onChange={ ( label ) => setAttributes( { label } ) }
						placeholder={ __( 'Label', 'form-block' ) }
						value={ label }
					/>
				</FlexBlock>
				
				<FlexItem>
					<ToggleControl
						checked={ !! required }
						label={ __( 'Required', 'form-block' ) }
						onChange={ ( required ) => setAttributes( { required } ) }
						value={ required }
					/>
				</FlexItem>
			</Flex>
			
			<TextareaControl
				onChange={ ( defaultValue ) => setAttributes( { defaultValue } ) }
				value={ value }
			/>
		</div>
	);
}
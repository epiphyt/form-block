import { useBlockProps } from '@wordpress/block-editor';
import {
	Flex, FlexBlock,
	FlexItem,
	TextControl,
	ToggleControl
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import Controls from './controls';
import { isAllowedAttribute } from './html-data';

export default function InputEdit( props ) {
	const {
		attributes: {
			label,
			required,
			type,
			value,
		},
		setAttributes,
	} = props;
	const blockProps = useBlockProps();
	
	return (
		<div { ...blockProps }>
			<Controls { ...props } />
			
			{ isAllowedAttribute( type, 'label' ) || isAllowedAttribute( type, 'required' )
				? <Flex>
					{ isAllowedAttribute( type, 'label' )
						? <FlexBlock>
							<TextControl
								className="form-block__label-control"
								onChange={ ( label ) => setAttributes( { label } ) }
								placeholder={ __( 'Label', 'form-block' ) }
								value={ label }
							/>
						</FlexBlock>
						: null
					}
					{ isAllowedAttribute( type, 'required' )
						? <FlexItem>
							<ToggleControl
								checked={ !! required }
								label={ __( 'Required', 'form-block' ) }
								onChange={ ( required ) => setAttributes( { required } ) }
								value={ required }
							/>
						</FlexItem>
						: null
					}
				</Flex>
				: null
			}
			
			<TextControl
				onChange={ ( value ) => setAttributes( { value } ) }
				type={ type }
				value={ value }
			/>
		</div>
	);
}

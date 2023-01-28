import { useBlockProps } from '@wordpress/block-editor';
import {
	Flex,
	FlexBlock,
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
			src,
			step,
			type,
			value,
			width,
		},
		setAttributes,
	} = props;
	const blockProps = useBlockProps();
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
		src,
		step,
		type,
		value,
		width,
	};
	
	if ( type === 'hidden' ) {
		elementProps.help = __( 'This input is hidden in the frontend.', 'form-block' );
		elementProps.type = 'text';
		blockProps.className += ' is-type-hidden';
	}
	
	return (
		<div { ...blockProps }>
			<Controls { ...props } />
			
			{ type === 'checkbox' || type === 'radio'
				? <Flex>
					<FlexItem>
						<TextControl
							onChange={ ( value ) => setAttributes( { value } ) }
							{ ...elementProps }
						/>
					</FlexItem>
					
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
				: <>
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
						{ ...elementProps }
					/>
				</>
			}
		</div>
	);
}

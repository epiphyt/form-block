import { RichText, useBlockProps } from '@wordpress/block-editor';
import {
	Button,
	Flex,
	FlexBlock,
	FlexItem,
	Modal,
	SelectControl,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import { useRef, useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { error, reset } from '@wordpress/icons';

import Controls from './controls';
import { stripSpecialChars } from '../data/util';

export default function SelectEdit( props ) {
	const {
		attributes: {
			autoComplete,
			disabled,
			label,
			multiple,
			name,
			options,
			required,
			size,
			value,
		},
		setAttributes,
	} = props;
	const blockProps = useBlockProps();
	const elementProps = {
		autoComplete,
		disabled,
		multiple,
		name,
		required,
		size,
		value,
	};
	const [ isOptionModalOpen, setIsOptionModalOpen ] = useState( false );
	const nameControlRef = useRef( null );
	const nameAttribute = name
		? stripSpecialChars( name, false )
		: stripSpecialChars( label );
	// make sure label is identical to value if no label is defined
	const selectOptions = JSON.parse( JSON.stringify( options ) ).map(
		( item ) => {
			if ( ! item.label && item.value ) {
				item.label = item.value;
			}

			return item;
		}
	);

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

			<Flex align="center">
				<FlexBlock>
					<SelectControl
						onChange={ ( value ) => setAttributes( { value } ) }
						options={ selectOptions }
						{ ...elementProps }
					/>
				</FlexBlock>

				<FlexItem className="form-block__flexible-flex-item">
					<Button
						onClick={ () => setIsOptionModalOpen( true ) }
						size="small"
						text={ __( 'Manage options', 'form-block' ) }
						variant="secondary"
					/>
					{ isOptionModalOpen ? (
						<Modal
							onRequestClose={ () =>
								setIsOptionModalOpen( false )
							}
							title={ __( 'Manage options', 'form-block' ) }
						>
							{ options.map( ( option, index ) => (
								<OptionEdit
									key={ index }
									index={ index }
									option={ option }
									options={ options }
									setAttributes={ setAttributes }
								/>
							) ) }

							<div className="form-block__inline-block-container">
								<Button
									onClick={ () =>
										setIsOptionModalOpen( false )
									}
									text={ __( 'Save options', 'form-block' ) }
									variant="primary"
								/>
								<Button
									onClick={ () => {
										let newOptions = JSON.parse(
											JSON.stringify( options )
										);
										newOptions.push( {
											label: '',
											value: '',
										} );

										setAttributes( {
											options: newOptions,
										} );
									} }
									text={ __( 'Add option', 'form-block' ) }
									variant="tertiary"
								/>
							</div>
						</Modal>
					) : null }
				</FlexItem>
			</Flex>
		</div>
	);
}

function OptionEdit( { index, option, options, setAttributes } ) {
	return (
		<>
			<Flex align="center">
				<FlexItem>
					<h2>
						{
							/* translators: option index */
							sprintf(
								__( 'Option %d', 'form-block' ),
								index + 1
							)
						}
					</h2>
				</FlexItem>

				<FlexItem>
					<Button
						className="form-block__select-option--remove"
						icon={ reset }
						label={
							/* translators: option index */
							sprintf(
								__( 'Remove option %d', 'form-block' ),
								index + 1
							)
						}
						onClick={ () => {
							let newOptions = JSON.parse(
								JSON.stringify( options )
							);
							newOptions.splice( index, 1 );

							setAttributes( { options: newOptions } );
						} }
						showTooltip={ true }
						size="small"
						variant="secondary"
					/>
				</FlexItem>
			</Flex>

			<div className="form-block__select-option">
				<Flex>
					<FlexBlock>
						<TextControl
							label={ __( 'Label', 'form-block' ) }
							onChange={ ( label ) => {
								let newOptions = JSON.parse(
									JSON.stringify( options )
								);
								newOptions[ index ].label = label;

								setAttributes( { options: newOptions } );
							} }
							value={ option?.label }
						/>
					</FlexBlock>

					<FlexBlock>
						<TextControl
							label={ __( 'Value', 'form-block' ) }
							onChange={ ( value ) => {
								let newOptions = JSON.parse(
									JSON.stringify( options )
								);
								newOptions[ index ].value = value;

								setAttributes( { options: newOptions } );
							} }
							value={ option?.value }
						/>
					</FlexBlock>
				</Flex>
			</div>
		</>
	);
}

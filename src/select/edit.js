import { speak } from '@wordpress/a11y';
import {
	RichText,
	useBlockProps,
	__experimentalUseBorderProps as useBorderProps,
	__experimentalGetShadowClassesAndStyles as useShadowProps,
	__experimentalUseColorProps as useColorProps,
} from '@wordpress/block-editor';
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
import { useEffect, useRef, useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { chevronDown, chevronUp, error, reset } from '@wordpress/icons';

import Controls from './controls';
import { stripSpecialChars } from '../data/util';

export default function SelectEdit( props ) {
	const { attributes, setAttributes } = props;
	const {
		autoComplete,
		disabled,
		label,
		multiple,
		name,
		options,
		required,
		size,
		value,
	} = attributes;
	const blockProps = useBlockProps();
	const borderProps = useBorderProps( attributes );
	const colorProps = useColorProps( attributes );
	const shadowProps = useShadowProps( attributes );
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
	const moveButtonsRef = useRef( {} );
	const nameControlRef = useRef( null );
	const pendingFocusRef = useRef( null );

	// move an option from one position to another and announce the change
	const moveOption = ( from, to ) => {
		if ( to < 0 || to >= options.length || from === to ) {
			return;
		}

		const newOptions = JSON.parse( JSON.stringify( options ) );
		const [ moved ] = newOptions.splice( from, 1 );
		newOptions.splice( to, 0, moved );

		setAttributes( { options: newOptions } );

		// keep the focus on the moved option's button (now at its new index)
		pendingFocusRef.current = {
			index: to,
			direction: to < from ? 'up' : 'down',
		};

		speak(
			sprintf(
				/* translators: 1: option label or number, 2: new position, 3: total number of options */
				__(
					'Option "%1$s" moved to position %2$d of %3$d.',
					'form-block'
				),
				moved.label || moved.value || to + 1,
				to + 1,
				newOptions.length
			),
			'assertive'
		);
	};

	useEffect( () => {
		const pending = pendingFocusRef.current;

		if ( ! pending ) {
			return;
		}

		pendingFocusRef.current = null;

		const buttons = moveButtonsRef.current;
		const preferred =
			buttons[ `${ pending.index }-${ pending.direction }` ];
		const fallback =
			buttons[
				`${ pending.index }-${
					pending.direction === 'up' ? 'down' : 'up'
				}`
			];
		const target = preferred && ! preferred.disabled ? preferred : fallback;

		target?.focus();
	}, [ options ] );

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

	if ( Object.keys( borderProps.style ).length ) {
		borderProps.style.borderStyle = 'solid';
	}

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
						style={ {
							...borderProps.style,
							...colorProps.style,
							...shadowProps.style,
						} }
						ref={ ( node ) => {
							// overwrite default select styles by making
							// box-shadow important
							if (
								node &&
								Object.keys( shadowProps.style ).length
							) {
								node.style.setProperty(
									'box-shadow',
									shadowProps.style.boxShadow,
									'important'
								);
							}
						} }
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
							isDismissible={ false }
							onRequestClose={ () =>
								setIsOptionModalOpen( false )
							}
							title={ __( 'Manage options', 'form-block' ) }
						>
							{ options.map( ( option, index ) => (
								<OptionEdit
									key={ index }
									index={ index }
									moveButtonsRef={ moveButtonsRef }
									moveOption={ moveOption }
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

function OptionEdit( {
	index,
	moveButtonsRef,
	moveOption,
	option,
	options,
	setAttributes,
} ) {
	const optionName = option?.label || option?.value || index + 1;

	// register the move buttons so focus can follow an option after it moves
	const setMoveButtonRef = ( direction ) => ( node ) => {
		moveButtonsRef.current[ `${ index }-${ direction }` ] = node;
	};

	return (
		<div className="form-block__select-option-wrapper">
			<Flex align="center">
				<FlexBlock>
					<h2>
						{
							/* translators: option index */
							sprintf(
								__( 'Option %d', 'form-block' ),
								index + 1
							)
						}
					</h2>
				</FlexBlock>

				<FlexItem>
					<Button
						disabled={ index === 0 }
						icon={ chevronUp }
						label={ sprintf(
							/* translators: option name or number */
							__( 'Move option "%s" up', 'form-block' ),
							optionName
						) }
						onClick={ () => moveOption( index, index - 1 ) }
						ref={ setMoveButtonRef( 'up' ) }
						showTooltip={ true }
						size="small"
						variant="secondary"
					/>
				</FlexItem>

				<FlexItem>
					<Button
						disabled={ index === options.length - 1 }
						icon={ chevronDown }
						label={ sprintf(
							/* translators: option name or number */
							__( 'Move option "%s" down', 'form-block' ),
							optionName
						) }
						onClick={ () => moveOption( index, index + 1 ) }
						ref={ setMoveButtonRef( 'down' ) }
						showTooltip={ true }
						size="small"
						variant="secondary"
					/>
				</FlexItem>

				<FlexItem>
					<Button
						className="form-block__select-option--remove"
						icon={ reset }
						label={
							/* translators: option index */
							sprintf(
								__( 'Remove option "%d"', 'form-block' ),
								optionName
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
		</div>
	);
}

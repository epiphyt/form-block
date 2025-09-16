import { InspectorControls } from '@wordpress/block-editor';
import {
	Button,
	Modal,
	PanelBody,
	SelectControl,
	TextControl,
	ToggleControl,
	Tooltip,
} from '@wordpress/components';
import { useState } from '@wordpress/element';
import { applyFilters } from '@wordpress/hooks';
import { __, _x, sprintf } from '@wordpress/i18n';
import { help } from '@wordpress/icons';

import {
	attributes as textareaAttributes,
	getAttributeHelp,
} from '../data/attributes';
import Autocomplete from '../data/autocomplete/control';
import { getSanitizedAttributeValue, stripSpecialChars } from '../data/util';
import { autoAssign } from '../data/autocomplete/auto-assign';

export default function Controls( props ) {
	const {
		attributes: { autoComplete, label, name },
		nameControlRef,
		setAttributes,
	} = props;
	const [ isHelpOpen, setIsHelpOpen ] = useState( [] );
	const controls = applyFilters(
		'formBlock.textarea.controlTypes',
		[
			{
				attributeName: 'autoComplete',
				attributes: {
					type: 'autocomplete',
				},
			},
			{
				attributeName: 'cols',
				attributes: {
					inputType: 'number',
					type: 'text',
				},
			},
			{
				attributeName: 'disabled',
				attributes: {
					type: 'toggle',
				},
			},
			{
				attributeName: 'placeholder',
				attributes: {
					type: 'text',
				},
			},
			{
				attributeName: 'rows',
				attributes: {
					inputType: 'number',
					type: 'text',
				},
			},
			{
				attributeName: 'spellCheck',
				attributes: {
					type: 'toggle',
				},
			},
			{
				attributeName: 'wrap',
				attributes: {
					type: 'select',
				},
			},
		],
		props.attributes
	);

	const getControl = ( control, key ) => {
		const {
			attributeName,
			attributes: { help, label, inputType, type },
		} = control;

		switch ( type ) {
			case 'autocomplete':
				const autoAssignedValue = autoAssign(
					autoComplete,
					label,
					name
				);

				return (
					<Autocomplete
						autoAssignedValue={ autoAssignedValue }
						isHelpOpen={ isHelpOpen }
						key={ key }
						setIsHelpOpen={ setIsHelpOpen }
						{ ...props }
					/>
				);
			case 'select':
				return (
					<SelectControl
						className="form-block__block-control"
						key={ key }
						label={ getLabel( attributeName ) }
						onChange={ ( newValue ) =>
							updateValue(
								getSanitizedAttributeValue(
									newValue,
									control.attributes
								),
								attributeName
							)
						}
						options={ getOptions( attributeName ) }
						value={ getSanitizedAttributeValue(
							props.attributes[ attributeName ],
							control.attributes
						) }
					/>
				);
			case 'toggle':
				return (
					<ToggleControl
						checked={ !! props.attributes[ attributeName ] }
						className="form-block__block-control"
						help={ help || null }
						key={ key }
						label={ label || getLabel( attributeName ) }
						onChange={ ( newValue ) =>
							updateValue( newValue, attributeName )
						}
					/>
				);
			case 'text':
			default:
				return (
					<TextControl
						className="form-block__block-control"
						help={ help || null }
						key={ key }
						label={ label || getLabel( attributeName ) }
						onChange={ ( newValue ) =>
							updateValue(
								getSanitizedAttributeValue(
									newValue,
									control.attributes
								),
								attributeName
							)
						}
						type={ inputType || 'text' }
						value={
							getSanitizedAttributeValue(
								props.attributes[ attributeName ],
								control.attributes
							) || ''
						}
					/>
				);
		}
	};

	const getLabel = ( attribute ) => {
		if ( ! textareaAttributes[ attribute ]?.label ) {
			return null;
		}

		return (
			<>
				{ textareaAttributes[ attribute ].label }
				{ textareaAttributes[ attribute ].description ||
				textareaAttributes[ attribute ].examples ? (
					<>
						<Tooltip
							text={ __(
								'Help/Examples for this attribute',
								'form-block'
							) }
						>
							<Button
								icon={ help }
								onClick={ () => {
									let newState = {};
									newState[ attribute ] = true;
									setIsHelpOpen( ( prevState ) => ( {
										...prevState,
										...newState,
									} ) );
								} }
								variant="tertiary"
							/>
						</Tooltip>
						{ isHelpOpen[ attribute ] ? (
							<Modal
								className="form-block__help-modal"
								onRequestClose={ () => {
									let newState = {};
									newState[ attribute ] = false;
									setIsHelpOpen( ( prevState ) => ( {
										...prevState,
										...newState,
									} ) );
								} }
								title={
									/* translators: attribute name */
									sprintf(
										__(
											'Help for attribute %s',
											'form-block'
										),
										textareaAttributes[ attribute ].label
									)
								}
							>
								{ getAttributeHelp( attribute ) }
							</Modal>
						) : null }
					</>
				) : null }
			</>
		);
	};

	const getOptions = ( attribute ) =>
		textareaAttributes[ attribute ].options || [];
	const nameAttribute = name
		? stripSpecialChars( name, false )
		: stripSpecialChars( label );

	const updateValue = ( newValue, attribute ) => {
		let value = {};
		value[ attribute ] = newValue;

		return setAttributes( value );
	};
	const formBlockControls = applyFilters( 'formBlock.textarea.controls', {
		props,
		nameAttribute,
	} );

	return (
		<InspectorControls>
			<PanelBody>
				<TextControl
					help={
						! name
							? __(
									'The name is auto-generated from the label.',
									'form-block'
							  )
							: __(
									'The name has been set manually.',
									'form-block'
							  )
					}
					label={ _x( 'Name', 'HTML attribute name', 'form-block' ) }
					onChange={ ( name ) =>
						setAttributes( {
							name: stripSpecialChars( name, false ),
						} )
					}
					ref={ nameControlRef }
					value={ nameAttribute }
				/>
				{ controls.map( ( control, index ) =>
					getControl( control, index )
				) }
				{ React.isValidElement( formBlockControls )
					? formBlockControls
					: null }
			</PanelBody>
		</InspectorControls>
	);
}

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
import { __, sprintf } from '@wordpress/i18n';
import { help } from '@wordpress/icons';

import {
	attributes as inputAttributes,
	getAttributeHelp,
} from '../data/attributes';
import { getSanitizedAttributeValue, stripSpecialChars } from '../data/util';
import { getTypes, isAllowedAttribute } from './html-data';

export default function Controls( props ) {
	const {
		attributes: {
			name,
			label,
			type,
		},
		setAttributes,
	} = props;
	const [ isHelpOpen, setIsHelpOpen ] = useState( [] );
	
	const getControl = ( attribute, type, settings = {} ) => {
		if ( ! inputAttributes[ attribute ] ) {
			return null;
		}
		
		if ( ! isAllowedAttribute( type, attribute ) ) {
			return null;
		}
		
		switch ( inputAttributes[ attribute ].controlType ) {
			case 'number':
				return (
					<TextControl
						className="form-block__block-control"
						label={ getLabel( attribute ) }
						onChange={ ( newValue ) => updateValue( getSanitizedAttributeValue( newValue, settings ), attribute ) }
						type="number"
						value={ getSanitizedAttributeValue( props.attributes[ attribute ], settings ) }
					/>
				);
			case 'select':
				return (
					<SelectControl
						className="form-block__block-control"
						label={ getLabel( attribute ) }
						onChange={ ( newValue ) => updateValue( getSanitizedAttributeValue( newValue, settings ), attribute ) }
						options={ getOptions( attribute ) }
						value={ getSanitizedAttributeValue( props.attributes[ attribute ], settings ) }
					/>
				);
			case 'toggle':
				return (
					<ToggleControl
						checked={ !! props.attributes[ attribute ] }
						className="form-block__block-control"
						label={ getLabel( attribute ) }
						onChange={ ( newValue ) => updateValue( newValue, attribute ) }
					/>
				);
			case 'text':
			default:
				return (
					<TextControl
						className="form-block__block-control"
						label={ getLabel( attribute ) }
						onChange={ ( newValue ) => updateValue( getSanitizedAttributeValue( newValue, settings ), attribute ) }
						value={ getSanitizedAttributeValue( props.attributes[ attribute ], settings ) }
					/>
				);
		}
	}
	
	const getLabel = ( attribute ) => {
		if ( ! inputAttributes[ attribute ].label ) {
			return null;
		}
		
		return (
			<>
				{ inputAttributes[ attribute ].label }
				{ inputAttributes[ attribute ].description || inputAttributes[ attribute ].examples
					? <>
						<Tooltip
							text={ __( 'Help/Examples for this attribute', 'form-block' ) }
						>
							<Button
								icon={ help }
								onClick={ () => {
									let newState = {};
									newState[ attribute ] = true;
									setIsHelpOpen( ( prevState ) => ( { ...prevState, ...newState } ) )
								} }
								variant="tertiary"
							/>
						</Tooltip>
						{ isHelpOpen[ attribute ]
							? <Modal
								className="form-block__help-modal"
								onRequestClose={ () => {
									let newState = {};
									newState[ attribute ] = false;
									setIsHelpOpen( ( prevState ) => ( { ...prevState, ...newState } ) )
								} }
								title={ sprintf( __( 'Help for attribute %s', 'form-block' ), attribute ) }
							>
								{ getAttributeHelp( attribute ) }
							</Modal>
							: null
						}
					</>
					: null
				}
			</>
		);
	}
	
	const getOptions = ( attribute ) => inputAttributes[ attribute ].options || [];
	
	const updateValue = ( newValue, attribute ) => {
		let value = {};
		value[ attribute ] = newValue;
		
		return setAttributes( value );
	}
	
	return (
		<InspectorControls>
			<PanelBody>
				<SelectControl
					label={ _x( 'Type', 'HTML attribute name', 'form-block' ) }
					onChange={ ( type ) => setAttributes( { type } ) }
					options={ getTypes().map( ( type ) => ( { label: type, value: type } ) ) }
					value={ type }
				/>
				<TextControl
					help={ ! name ? __( 'The name is auto-generated from the label.', 'form-block' ) : __( 'The name has been set manually.', 'form-block' ) }
					label={ _x( 'Name', 'HTML attribute name', 'form-block' )  }
					onChange={ ( name ) => setAttributes( { name: stripSpecialChars( name, false ) } ) }
					value={ name ? stripSpecialChars( name, false ) : stripSpecialChars( label ) }
				/>
				{ getControl( 'autocomplete', type ) }
				{ getControl( 'disabled', type ) }
				{ getControl( 'readonly', type ) }
				{ getControl( 'multiple', type ) }
				{ getControl( 'accept', type ) }
				{ getControl( 'placeholder', type ) }
				{ getControl( 'pattern', type ) }
				{ getControl( 'checked', type ) }
				{ getControl( 'minlength', type ) }
				{ getControl( 'maxlength', type ) }
				{ getControl( 'min', type ) }
				{ getControl( 'max', type ) }
				{ getControl( 'step', type ) }
				{ getControl( 'capture', type ) }
				{ getControl( 'alt', type ) }
				{ getControl( 'size', type ) }
				{ getControl( 'src', type ) }
				{ getControl( 'height', type ) }
				{ getControl( 'width', type ) }
				{ getControl(
					'dirname',
					type,
					{
						stripSpecialChars: true,
						toLowerCase: true,
					}
				) }
			</PanelBody>
		</InspectorControls>
	);
}

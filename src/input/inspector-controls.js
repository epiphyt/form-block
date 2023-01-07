import { InspectorControls } from '@wordpress/block-editor';
import {
	Button,
	ExternalLink,
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
	getTypes,
	inputAttributes,
	isAllowedAttribute,
	mdnAttributeLinkBase,
} from './html-data';

export default function Controls( props ) {
	const {
		attributes: {
			name,
			type,
		},
		setAttributes,
	} = props;
	const [ isHelpOpen, setIsHelpOpen ] = useState( [] );
	
	const getAttributeHelp = ( attribute ) => {
		if ( ! inputAttributes[ attribute ].description ) {
			return null;
		}
		
		return (
			<>
				{ inputAttributes[ attribute ].description
					? <p>{ inputAttributes[ attribute ].description }</p>
					: null
				}
				{ inputAttributes[ attribute ].examples
					? <>
						<h2>{ __( 'Examples', 'form-block' ) }</h2>
						<ul>
						{ inputAttributes[ attribute ].examples.map(
							( example, index ) => <li key={ index }>
								<code className="form-block__inline-code">
									{ example }
								</code>
							</li>
						) }
						</ul>
					</>
					: null
				}
				<ExternalLink href={ mdnAttributeLinkBase + '#' + attribute }>
					{ __( 'More information', 'form-block' ) }
				</ExternalLink>
			</>
		);
	}
	
	const getControl = ( attribute, type ) => {
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
						onChange={ ( newValue ) => updateValue( newValue, attribute ) }
						type="number"
						value={ props.attributes[ attribute ] }
					/>
				);
			case 'select':
				return (
					<SelectControl
						className="form-block__block-control"
						label={ getLabel( attribute ) }
						onChange={ ( newValue ) => updateValue( newValue, attribute ) }
						options={ getOptions( attribute ) }
						value={ props.attributes[ attribute ] }
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
						onChange={ ( newValue ) => updateValue( newValue, attribute ) }
						value={ props.attributes[ attribute ] }
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
					label={ __( 'Type', 'form-block' ) }
					onChange={ ( type ) => setAttributes( { type } ) }
					options={ getTypes().map( ( type ) => ( { label: type, value: type } ) ) }
					value={ type }
				/>
				<TextControl
					label={ __( 'Name', 'form-block' ) }
					onChange={ ( name ) => setAttributes( { name } ) /* TODO: only allowed characters */ }
					value={ name }
				/>
				{ getControl( 'accept', type ) }
				{ getControl( 'alt', type ) }
				{ getControl( 'autocomplete', type ) }
				{ getControl( 'capture', type ) }
				{ getControl( 'checked', type ) }
				{ getControl( 'dirname', type ) /* TODO: only allowed characters */ }
				{ getControl( 'disabled', type ) }
				{ getControl( 'height', type ) }
				{ getControl( 'max', type ) }
				{ getControl( 'maxlength', type ) }
				{ getControl( 'min', type ) }
				{ getControl( 'minlength', type ) }
				{ getControl( 'multiple', type ) }
				{ getControl( 'pattern', type ) }
				{ getControl( 'placeholder', type ) }
				{ getControl( 'readonly', type ) }
				{ getControl( 'required', type ) }
				{ getControl( 'size', type ) }
				{ getControl( 'src', type ) }
				{ getControl( 'step', type ) }
				{ getControl( 'width', type ) }
			</PanelBody>
		</InspectorControls>
	);
}

import { InspectorControls } from '@wordpress/block-editor';
import {
	Button, Modal,
	PanelBody,
	TextControl,
	ToggleControl,
	Tooltip
} from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __, _x, sprintf } from '@wordpress/i18n';
import { help } from '@wordpress/icons';

import {
	attributes as selectAttributes,
	getAttributeHelp,
} from '../data/attributes';
import { stripSpecialChars } from '../data/util';

export default function Controls( props ) {
	const {
		attributes: {
			autocomplete,
			disabled,
			label,
			multiple,
			name,
			size,
		},
		setAttributes,
	} = props;
	const [ isHelpOpen, setIsHelpOpen ] = useState( [] );
	
	const getLabel = ( attribute ) => {
		if (  ! selectAttributes[ attribute ].label ) {
			return null;
		}
		
		return (
			<>
				{ selectAttributes[ attribute ].label }
				{ selectAttributes[ attribute ].description || selectAttributes[ attribute ].examples
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
	
	return (
		<InspectorControls>
			<PanelBody>
				<TextControl
					className="form-block__block-control"
					help={ ! name ? __( 'The name is auto-generated from the label.', 'form-block' ) : __( 'The name has been set manually.', 'form-block' ) }
					label={ _x( 'Name', 'HTML attribute name', 'form-block' )  }
					onChange={ ( name ) => setAttributes( { name: stripSpecialChars( name, false ) } ) }
					value={ name ? stripSpecialChars( name, false ) : stripSpecialChars( label ) }
				/>
				<ToggleControl
					checked={ !! autocomplete }
					className="form-block__block-control"
					label={ getLabel( 'autocomplete' ) }
					onChange={ ( autocomplete ) => setAttributes( { autocomplete } ) }
				/>
				<ToggleControl
					checked={ !! disabled }
					className="form-block__block-control"
					label={ getLabel( 'disabled' ) }
					onChange={ ( disabled ) => setAttributes( { disabled } ) }
				/>
				<ToggleControl
					checked={ !! multiple }
					className="form-block__block-control"
					label={ getLabel( 'multiple' ) }
					onChange={ ( multiple ) => setAttributes( { multiple } ) }
				/>
				<TextControl
					className="form-block__block-control"
					label={ getLabel( 'size' ) }
					onChange={ ( size ) => setAttributes( { size } ) }
					type="number"
					value={ size }
				/>
			</PanelBody>
		</InspectorControls>
	);
}

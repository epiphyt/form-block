import { InspectorControls } from '@wordpress/block-editor';
import {
	Button, Modal,
	PanelBody, SelectControl,
	TextControl,
	ToggleControl,
	Tooltip
} from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __, _x, sprintf } from '@wordpress/i18n';
import { help } from '@wordpress/icons';

import {
	attributes as textareaAttributes,
	getAttributeHelp,
} from '../data/attributes';
import { stripSpecialChars } from '../data/util';

export default function Controls( props ) {
	const {
		attributes: {
			autocomplete,
			disabled,
			label,
			maxLength,
			minLength,
			name,
			placeholder,
			readOnly,
			rows,
			spellCheck,
			size,
		},
		setAttributes,
	} = props;
	const [ isHelpOpen, setIsHelpOpen ] = useState( [] );
	
	const getLabel = ( attribute ) => {
		if (  ! textareaAttributes[ attribute ].label ) {
			return null;
		}
		
		return (
			<>
				{ textareaAttributes[ attribute ].label }
				{ textareaAttributes[ attribute ].description || textareaAttributes[ attribute ].examples
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
	
	const getOptions = ( attribute ) => textareaAttributes[ attribute ].options || [];
	
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
					checked={ !! readOnly }
					className="form-block__block-control"
					label={ getLabel( 'readOnly' ) }
					onChange={ ( readOnly ) => setAttributes( { readOnly } ) }
				/>
				<TextControl
					className="form-block__block-control"
					label={ getLabel( 'placeholder' ) }
					onChange={ ( placeholder ) => setAttributes( { placeholder } ) }
					value={ placeholder }
				/>
				<TextControl
					className="form-block__block-control"
					label={ getLabel( 'rows' ) }
					min="1"
					onChange={ ( rows ) => setAttributes( { rows } ) }
					type="number"
					value={ rows }
				/>
				<SelectControl
					className="form-block__block-control"
					label={ getLabel( 'spellCheck' ) }
					onChange={ ( spellCheck ) => setAttributes( { spellCheck } ) }
					options={ getOptions( 'spellCheck' ) }
					value={ spellCheck }
				/>
				<TextControl
					className="form-block__block-control"
					label={ getLabel( 'minLength' ) }
					onChange={ ( minLength ) => setAttributes( { minLength } ) }
					type="number"
					value={ minLength }
				/>
				<TextControl
					className="form-block__block-control"
					label={ getLabel( 'maxLength' ) }
					onChange={ ( maxLength ) => setAttributes( { maxLength } ) }
					type="number"
					value={ maxLength }
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

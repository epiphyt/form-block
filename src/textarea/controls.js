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

export default function Controls( props ) {
	const {
		attributes: {
			autocomplete,
			disabled,
			maxlength,
			minlength,
			name,
			placeholder,
			readonly,
			rows,
			spellcheck,
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
					label={ _x( 'Name', 'HTML attribute name', 'form-block' ) }
					onChange={ ( name ) => setAttributes( { name } ) }
					value={ name }
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
				<TextControl
					className="form-block__block-control"
					label={ getLabel( 'maxlength' ) }
					onChange={ ( maxlength ) => setAttributes( { maxlength } ) }
					type="number"
					value={ maxlength }
				/>
				<TextControl
					className="form-block__block-control"
					label={ getLabel( 'minlength' ) }
					onChange={ ( minlength ) => setAttributes( { minlength } ) }
					type="number"
					value={ minlength }
				/>
				<TextControl
					className="form-block__block-control"
					label={ getLabel( 'placeholder' ) }
					onChange={ ( placeholder ) => setAttributes( { placeholder } ) }
					value={ placeholder }
				/>
				<ToggleControl
					checked={ !! readonly }
					className="form-block__block-control"
					label={ getLabel( 'readonly' ) }
					onChange={ ( readonly ) => setAttributes( { readonly } ) }
				/>
				<TextControl
					className="form-block__block-control"
					label={ getLabel( 'rows' ) }
					min="1"
					onChange={ ( rows ) => setAttributes( { rows } ) }
					type="number"
					value={ rows }
				/>
				<TextControl
					className="form-block__block-control"
					label={ getLabel( 'size' ) }
					onChange={ ( size ) => setAttributes( { size } ) }
					type="number"
					value={ size }
				/>
				<SelectControl
					className="form-block__block-control"
					label={ getLabel( 'spellcheck' ) }
					onChange={ ( spellcheck ) => setAttributes( { spellcheck } ) }
					options={ getOptions( 'spellcheck' ) }
					value={ spellcheck }
				/>
			</PanelBody>
		</InspectorControls>
	);
}

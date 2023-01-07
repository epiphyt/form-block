import { InspectorControls } from '@wordpress/block-editor';
import {
	Button,
	ExternalLink,
	Modal,
	PanelBody,
	SelectControl,
	TextControl,
	Tooltip,
} from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { help } from '@wordpress/icons';

import { getTypes, inputAttributes, isAllowedAttribute } from './html-data';

export default function Controls( props ) {
	const {
		attributes: {
			accept,
			type,
		},
		setAttributes,
	} = props;
	
	const getAttributeHelp = ( attribute ) => {
		if ( ! inputAttributes[ attribute ].description ) {
			return null;
		}
		
		return (
			<>
				{ inputAttributes[ attribute ].description || null }
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
				{ inputAttributes[ attribute ].moreInfoLink
					? <ExternalLink href={ inputAttributes[ attribute ].moreInfoLink }>
						{ __( 'More information', 'form-block' ) }
					</ExternalLink>
					: null
				}
			</>
		);
	}
	
	const getLabel = ( attribute ) => {
		if ( ! inputAttributes[ attribute ].label ) {
			return null;
		}
		
		const [ isHelpOpen, setIsHelpOpen ] = useState( false );
		
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
								onClick={ () => setIsHelpOpen( true ) }
								variant="tertiary"
							/>
						</Tooltip>
						{ isHelpOpen
							? <Modal
								onRequestClose={ () => setIsHelpOpen( false ) }
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
				<SelectControl
					label={ __( 'Type', 'form-block' ) }
					onChange={ ( type ) => setAttributes( { type } ) }
					options={ getTypes().map( ( type ) => ( { label: type, value: type } ) ) }
					value={ type }
				/>
				{ isAllowedAttribute( type, 'accept' )
					? <TextControl
						className="form-block__block-control"
						label={ getLabel( 'accept' ) }
						onChange={ ( accept ) => setAttributes( { accept } ) }
						value={ accept }
					/>
					: null
				}
				{ isAllowedAttribute( type, 'alt' )
					? <TextControl
						className="form-block__block-control"
						label={ getLabel( 'alt' ) }
						onChange={ ( accept ) => setAttributes( { accept } ) }
						value={ accept }
					/>
					: null
				}
			</PanelBody>
		</InspectorControls>
	);
}

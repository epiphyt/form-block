import { Button, Modal, Tooltip } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';
import { help } from '@wordpress/icons';

import {
	attributes as inputAttributes,
	getAttributeHelp,
} from '../data/attributes';

export const getLabel = ( attribute, isHelpOpen, setIsHelpOpen ) => {
	if ( ! inputAttributes[ attribute ].label ) {
		return null;
	}

	return (
		<>
			{ inputAttributes[ attribute ].label }
			{ inputAttributes[ attribute ].description ||
			inputAttributes[ attribute ].examples ? (
				<>
					<Tooltip
						text={ sprintf(
							__(
								'Help/Examples for attribute %s',
								'form-block'
							),
							inputAttributes[ attribute ].label
						) }
					>
						<Button
							icon={ help }
							label={ sprintf(
								__(
									'Help/Examples for attribute %s',
									'form-block'
								),
								inputAttributes[ attribute ].label
							) }
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
									__( 'Help for attribute %s', 'form-block' ),
									inputAttributes[ attribute ].label
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

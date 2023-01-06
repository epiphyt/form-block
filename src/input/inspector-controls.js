import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import { getTypes } from './html-data';

export default function Controls( props ) {
	const {
		attributes: {
			type,
		},
		setAttributes,
	} = props;
	
	return (
		<InspectorControls>
			<PanelBody>
				<SelectControl
					label={ __( 'Type', 'form-block' ) }
					onChange={ ( type ) => setAttributes( { type } ) }
					options={ getTypes().map( ( type ) => ( { label: type, value: type } ) ) }
					value={ type }
				/>
			</PanelBody>
		</InspectorControls>
	);
}

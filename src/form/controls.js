import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { applyFilters } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

export default function Controls( { props } ) {
	const {
		attributes: { subject },
		setAttributes,
	} = props;

	const additionalPrimaryPanelControls = applyFilters(
		'formBlock.controls.additionalPrimaryPanelControls',
		null,
		props
	);

	return (
		<InspectorControls>
			<PanelBody>
				<TextControl
					label={ __( 'Custom subject', 'form-block' ) }
					onChange={ ( subject ) => setAttributes( { subject } ) }
					type="text"
					value={ subject || '' }
				/>

				{ additionalPrimaryPanelControls }
			</PanelBody>
		</InspectorControls>
	);
}

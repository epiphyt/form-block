import { InspectorControls } from '@wordpress/block-editor';
import { ExternalLink, PanelBody, TextControl } from '@wordpress/components';
import { applyFilters } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

/* global formBlockFormData */
export default function Controls( { props } ) {
	const {
		attributes: { formId, label, subject },
		setAttributes,
	} = props;

	const additionalPrimaryPanelControls = applyFilters(
		'formBlock.controls.additionalPrimaryPanelControls',
		null,
		props
	);

	const getSubmissionLink = () => {
		return formBlockFormData.submissionListTableLink + '&form_id=' + formId;
	};

	return (
		<InspectorControls>
			<PanelBody>
				{ formBlockFormData.saveSubmissions && formId ? (
					<p>
						<ExternalLink href={ getSubmissionLink() }>
							{ __(
								'View submissions (opens in new tab)',
								'form-block'
							) }
						</ExternalLink>
					</p>
				) : null }
				<TextControl
					label={ __( 'Custom subject', 'form-block' ) }
					onChange={ ( subject ) => setAttributes( { subject } ) }
					type="text"
					value={ subject || '' }
				/>

				<TextControl
					help={ __(
						'Give your form a label to tell users what type of form this is.',
						'form-block'
					) }
					label={ __( 'Label', 'form-block' ) }
					onChange={ ( label ) => setAttributes( { label } ) }
					type="text"
					value={ label || '' }
				/>

				{ additionalPrimaryPanelControls }
			</PanelBody>
		</InspectorControls>
	);
}

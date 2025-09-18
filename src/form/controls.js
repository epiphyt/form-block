import { InspectorControls } from '@wordpress/block-editor';
import {
	ExternalLink,
	PanelBody,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import { applyFilters } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

/* global formBlockFormData */
export default function Controls( { props } ) {
	const {
		attributes: { methods, formId, label, subject },
		setAttributes,
	} = props;
	let currentMethods = methods;

	if ( typeof currentMethods?.localStorage === 'undefined' ) {
		if ( typeof currentMethods === 'undefined' ) {
			currentMethods = {};
		}

		currentMethods.localStorage = true;
	}

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
				<ToggleControl
					checked={
						typeof currentMethods?.localStorage !== 'undefined'
							? currentMethods?.localStorage
							: true
					}
					label={ __( 'Save submissions locally', 'form-block' ) }
					onChange={ ( value ) => {
						const newValue = {
							localStorage: value,
						};

						setAttributes( {
							methods: { ...currentMethods, ...newValue },
						} );
					} }
				/>
				{ currentMethods?.localStorage && formId ? (
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

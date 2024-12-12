import { SelectControl, TextControl } from '@wordpress/components';
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

import { OPTIONS } from './constants';
import { autoAssign } from './auto-assign';
import { getSanitizedAttributeValue } from '../util';
import { getLabel } from '../../controls/label';

export default function Autocomplete( props ) {
	const {
		attributes: { label, name },
		isHelpOpen,
		setAttributes,
		setIsHelpOpen,
	} = props;
	const autoComplete = getSanitizedAttributeValue(
		props.attributes.autoComplete,
		{}
	);
	const autoCompleteSection = getSanitizedAttributeValue(
		props.attributes.autoCompleteSection,
		{}
	);

	const autoAssignedValue = autoAssign(
		autoComplete,
		label,
		name,
		setAttributes
	);

	return (
		<>
			<SelectControl
				className="form-block__block-control"
				label={ getLabel( 'autoComplete', isHelpOpen, setIsHelpOpen ) }
				onChange={ ( autoComplete ) =>
					setAttributes( { autoComplete } )
				}
				options={ OPTIONS }
				value={ autoComplete || autoAssignedValue }
			/>
			{ ( autoComplete && autoComplete !== 'off' ) ||
			( autoAssignedValue && autoAssignedValue !== 'off' ) ? (
				<TextControl
					className="form-block__block-control"
					help={ __(
						'Either shipping, billing or a custom section starting with section-, e.g. section-primary-contact.',
						'form-block'
					) }
					label={ __( 'Autocomplete Section', 'form-block' ) }
					onChange={ ( autoCompleteSection ) =>
						setAttributes( { autoCompleteSection } )
					}
					value={ autoCompleteSection || '' }
				/>
			) : null }
		</>
	);
}

const addControlTypes = ( controlTypes ) => {
	if (
		controlTypes.some( ( item ) => item.attributeName === 'autoComplete' )
	) {
		return controlTypes;
	}

	controlTypes.push( {
		attributeName: 'autoComplete',
		attributes: {},
	} );

	return controlTypes;
};

addFilter(
	'formBlock.input.controlTypes',
	'form-block/autocomplete/add-control-types',
	addControlTypes
);

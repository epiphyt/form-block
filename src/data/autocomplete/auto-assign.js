import { FIELD_MAPPING } from './constants';
import { getSanitizedAttributeValue } from '../util';

export const autoAssign = ( autoComplete, label, name ) => {
	if ( autoComplete ) {
		return autoComplete;
	}

	if ( ! label && ! name ) {
		return autoComplete;
	}

	const labelLower = label ? label.toLowerCase() : '';
	const nameLower = name ? name.toLowerCase() : '';

	for ( const key of Object.keys( FIELD_MAPPING ) ) {
		for ( const mappingLabel of FIELD_MAPPING[ key ] ) {
			const mappingLabelLower = mappingLabel.toLowerCase();
			const mappingLabelCssName = getSanitizedAttributeValue(
				mappingLabel,
				{ toLowerCase: true, stripSpecialChars: true }
			);

			if (
				labelLower.startsWith( mappingLabelLower ) ||
				nameLower.startsWith( mappingLabelLower ) ||
				nameLower.startsWith( mappingLabelCssName )
			) {
				return key;
			}
		}
	}

	return '';
};

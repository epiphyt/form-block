import { ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default function CustomDateControls( {
	attribute,
	props,
	updateValue,
} ) {
	const updateSettings = ( field, newValue ) => {
		let updatedValue = structuredClone( props.attributes[ attribute ] );
		updatedValue[ field ] = newValue;

		updateValue( updatedValue, attribute );
	};

	return (
		<>
			<ToggleControl
				checked={ !! props.attributes[ attribute ].showPlaceholder }
				className="form-block__block-control"
				label={ __( 'Show placeholders', 'form-block' ) }
				onChange={ ( newValue ) =>
					updateSettings( 'showPlaceholder', newValue )
				}
			/>
			<ToggleControl
				checked={ !! props.attributes[ attribute ].showLabel }
				className="form-block__block-control"
				label={ __( 'Show labels', 'form-block' ) }
				onChange={ ( newValue ) =>
					updateSettings( 'showLabel', newValue )
				}
			/>
		</>
	);
}

import { ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default function CustomDateControls( { attribute, props, updateValue } ) {
	return (
		<>
			<ToggleControl
				checked={ !! props.attributes[ attribute ].showPlaceholder }
				className="form-block__block-control"
				label={ __( 'Show placeholders', 'form-block' ) }
				onChange={ ( newValue ) => {
					let updatedValue = structuredClone( props.attributes[ attribute ] );
					updatedValue.showPlaceholder = newValue;
					
					updateValue( updatedValue, attribute );
				} }
			/>
		</>
	);
}

import {
	Flex,
	FlexBlock,
	FlexItem,
	TextControl,
} from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { addFilter, applyFilters } from '@wordpress/hooks';
import { __, _x } from '@wordpress/i18n';

import './editor.scss';

const getAllowedInputTypes = () => {
	let types = [
		'date-custom',
		'datetime-local-custom',
		'month-custom',
		'time-custom',
		'week-custom',
	];
	
	types = applyFilters(
		'formBlock.module.datePicker.allowedInputTypes',
		types
	);
	
	return types;
}

const addControlTypes = ( controlTypes, props ) => {
	const {
		attributes: {
			type,
		},
	} = props;
	
	if ( ! isCustomDate( type ) ) {
		return controlTypes;
	}
	
	controlTypes.push( {
		attributeName: 'customDate',
		attributes: {},
	} );
	
	return controlTypes;
}

addFilter( 'formBlock.input.controlTypes', 'form-block/custom-date/add-control-types', addControlTypes );

export const isCustomDate = ( type ) => getAllowedInputTypes().includes( type );

export function CustomDate( { props, elementProps } ) {
	const {
		attributes: {
			customDate,
			label,
			type,
		},
		setAttributes,
	} = props;
	const {
		showLabel,
		showPlaceholder,
		value,
	} = customDate;
	const fieldData = {
		day: {
			label: __( 'Day', 'form-block' ),
			placeholder: _x( 'DD', 'date field placeholder', 'form-block' ),
		},
		month: {
			label: __( 'Month', 'form-block' ),
			placeholder: _x( 'MM', 'date field placeholder', 'form-block' ),
		},
		year: {
			label: __( 'Year', 'form-block' ),
			placeholder: _x( 'YYYY', 'date field placeholder', 'form-block' ),
		},
	}
	
	const onFieldUpdate = ( field, fieldValue ) => {
		let newValue = structuredClone( customDate );
		newValue.value[ field ] = fieldValue;
		
		setAttributes( { customDate: newValue } );
	}
	
	switch ( type ) {
		case 'date-custom':
			const fields = _x( 'month, day, year', 'date order in lowercase', 'form-block' ).split( ', ' );
			
			return (
				<fieldset className="form-block__date-custom">
					<legend className="screen-reader-text">{ label }</legend>
					
					<Flex align="flex-end">
						{ fields.map( ( field, index ) => (
							<Fragment key={ index }>
								<FlexBlock className={ 'is-type-' + field }>
									<TextControl
										hideLabelFromVision={ ! showLabel }
										label={ fieldData[ field ].label }
										onChange={ ( value ) => onFieldUpdate( field, value ) }
										{ ...elementProps }
										placeholder={ showPlaceholder ? fieldData[ field ].placeholder : '' }
										value={ value ? ( value[ field ] || '' ) : '' }
									/>
								</FlexBlock>
								
								<FlexItem className="form-block__date-custom--separator">
									{ _x( '/', 'date separator', 'form-block' ) }
								</FlexItem>
							</Fragment>
						) ) }
					</Flex>
				</fieldset>
			);
	}
	
	return null;
}

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
	let fields;
	const fieldData = {
		day: {
			label: __( 'Day', 'form-block' ),
			placeholder: _x( 'DD', 'date field placeholder', 'form-block' ),
			separatorAfter: _x( '/', 'date separator', 'form-block' ),
			separatorBefore: '',
		},
		hour: {
			label: __( 'Hours', 'form-block' ),
			placeholder: _x( 'HH', 'date field placeholder', 'form-block' ),
			separatorAfter: _x( ':', 'time separator', 'form-block' ),
			separatorBefore: _x( 'at', 'date and time separator', 'form-block' ),
		},
		minute: {
			label: __( 'Minutes', 'form-block' ),
			placeholder: _x( 'MM', 'date field placeholder', 'form-block' ),
			separatorAfter: '',
			separatorBefore: '',
		},
		month: {
			label: __( 'Month', 'form-block' ),
			placeholder: _x( 'MM', 'date field placeholder', 'form-block' ),
			separatorAfter: _x( '/', 'date separator', 'form-block' ),
			separatorBefore: '',
		},
		year: {
			label: __( 'Year', 'form-block' ),
			placeholder: _x( 'YYYY', 'date field placeholder', 'form-block' ),
			separatorAfter: '',
			separatorBefore: '',
		},
	}
	
	const onFieldUpdate = ( field, fieldValue ) => {
		let newValue = structuredClone( customDate );
		newValue.value[ field ] = fieldValue;
		
		setAttributes( { customDate: newValue } );
	}
	
	switch ( type ) {
		case 'date-custom':
			fields = _x( 'month, day, year', 'date order in lowercase', 'form-block' ).split( ', ' );
			break;
		case 'datetime-local-custom':
			fields = _x( 'month, day, year, hour, minute', 'date order in lowercase', 'form-block' ).split( ', ' );
			break;
		case 'month-custom':
			fields = _x( 'month, year', 'date order in lowercase', 'form-block' ).split( ', ' );
			break;
		case 'time-custom':
			fields = _x( 'hour, minute', 'date order in lowercase', 'form-block' ).split( ', ' );
			break;
	}
	
	return (
		<fieldset className="form-block__date-custom">
			<legend className="screen-reader-text">{ label }</legend>
			
			<Flex align="flex-end">
				{ fields.map( ( field, index ) => (
					<Fragment key={ index }>
						{ fieldData[ field ].separatorBefore
							? <FlexItem className="form-block__date-custom--separator is-before">
								{ fieldData[ field ].separatorBefore }
							</FlexItem>
							: null
						}
						
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
						
						{ fieldData[ field ].separatorAfter
							? <FlexItem className="form-block__date-custom--separator is-after">
								{ fieldData[ field ].separatorAfter }
							</FlexItem>
							: null
						}
					</Fragment>
				) ) }
			</Flex>
		</fieldset>
	);
}

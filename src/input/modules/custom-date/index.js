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
		case 'week-custom':
			fields = _x( 'week, year', 'date order in lowercase', 'form-block' ).split( ', ' );
			break;
	}
	
	return (
		<fieldset className="form-block__date-custom">
			<legend className="screen-reader-text">{ label }</legend>
			
			<Flex align="flex-end">
				{ fields.map( ( field, index ) => (
					<Fragment key={ index }>
						{ formBlockInputCustomDate[ field ].separator.before
							? <FlexItem className="form-block__date-custom--separator is-before">
								{ formBlockInputCustomDate[ field ].separator.before }
							</FlexItem>
							: null
						}
						
						<FlexBlock className={ 'is-type-' + field }>
							<TextControl
								hideLabelFromVision={ ! showLabel }
								label={ formBlockInputCustomDate[ field ].label }
								onChange={ ( value ) => onFieldUpdate( field, value ) }
								{ ...elementProps }
								type="number"
								placeholder={ showPlaceholder ? formBlockInputCustomDate[ field ].placeholder : '' }
								value={ value ? ( value[ field ] || '' ) : '' }
							/>
						</FlexBlock>
						
						{ formBlockInputCustomDate[ field ].separator.after
							? <FlexItem className="form-block__date-custom--separator is-after">
								{ formBlockInputCustomDate[ field ].separator.after }
							</FlexItem>
							: null
						}
					</Fragment>
				) ) }
			</Flex>
		</fieldset>
	);
}

import { InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import { useState } from '@wordpress/element';
import { addFilter, applyFilters } from '@wordpress/hooks';
import { __, _x } from '@wordpress/i18n';

import { getLabel } from '../controls/label';
import { attributes as inputAttributes } from '../data/attributes';
import Autocomplete from '../data/autocomplete/control';
import { getSanitizedAttributeValue, stripSpecialChars } from '../data/util';
import { getTypes, isAllowedAttribute, types } from './html-data';
import CustomDateControls from './modules/custom-date/controls';
import { autoAssign } from '../data/autocomplete/auto-assign';

export default function Controls( props ) {
	const {
		attributes: { autoComplete, name, label, type },
		nameControlRef,
		setAttributes,
	} = props;
	const defaultControlTypes = [
		{
			attributeName: 'autoComplete',
			attributes: {
				type: 'autocomplete',
			},
		},
		{
			attributeName: 'isReplyTo',
			attributes: {},
		},
		{
			attributeName: 'disabled',
			attributes: {},
		},
		{
			attributeName: 'readOnly',
			attributes: {},
		},
		{
			attributeName: 'placeholder',
			attributes: {},
		},
		{
			attributeName: 'pattern',
			attributes: {},
		},
		{
			attributeName: 'checked',
			attributes: {},
		},
		{
			attributeName: 'spellCheck',
			attributes: {},
		},
	];
	const [ isHelpOpen, setIsHelpOpen ] = useState( {} );

	if (
		type === 'checkbox' ||
		type === 'radio' ||
		type === 'reset' ||
		type === 'submit'
	) {
		defaultControlTypes.push( {
			attributeName: 'value',
			attributes: {},
		} );
	}

	const controls = applyFilters(
		'formBlock.input.controlTypes',
		defaultControlTypes,
		props
	);

	const getControl = ( control, type, key ) => {
		const {
			attributeName,
			attributes: { help },
		} = control;
		const attributes = control.attributes;

		if ( ! inputAttributes[ attributeName ] ) {
			return null;
		}

		if ( ! isAllowedAttribute( type, attributeName ) ) {
			return null;
		}

		addFilter(
			'formBlock.input.elementProps',
			'formBlock/input-controls/element-props',
			( elementProps, blockProps ) => {
				let newProps = { ...elementProps };
				newProps[ attributeName ] = blockProps[ attributeName ];

				return newProps;
			}
		);

		switch ( inputAttributes[ attributeName ].controlType ) {
			case 'autocomplete':
				const autoAssignedValue = autoAssign(
					autoComplete,
					label,
					name
				);

				return (
					<Autocomplete
						autoAssignedValue={ autoAssignedValue }
						isHelpOpen={ isHelpOpen }
						key={ key }
						setIsHelpOpen={ setIsHelpOpen }
						{ ...props }
					/>
				);
			case 'custom-date':
				return (
					<CustomDateControls
						attribute={ attributeName }
						key={ key }
						props={ props }
						updateValue={ updateValue }
					/>
				);
			case 'number':
				return (
					<TextControl
						className="form-block__block-control"
						help={ help || null }
						key={ key }
						label={ getLabel(
							attributeName,
							isHelpOpen,
							setIsHelpOpen
						) }
						onChange={ ( newValue ) =>
							updateValue(
								getSanitizedAttributeValue(
									newValue,
									attributes
								),
								attributeName
							)
						}
						type="number"
						value={
							getSanitizedAttributeValue(
								props.attributes[ attributeName ],
								attributes
							) || ''
						}
					/>
				);
			case 'select':
				return (
					<SelectControl
						className="form-block__block-control"
						help={ help || null }
						key={ key }
						label={ getLabel(
							attributeName,
							isHelpOpen,
							setIsHelpOpen
						) }
						onChange={ ( newValue ) =>
							updateValue(
								getSanitizedAttributeValue(
									newValue,
									attributes
								),
								attributeName
							)
						}
						options={ getOptions( attributeName ) }
						value={ getSanitizedAttributeValue(
							props.attributes[ attributeName ],
							attributes
						) }
					/>
				);
			case 'toggle':
				return (
					<ToggleControl
						checked={ !! props.attributes[ attributeName ] }
						className="form-block__block-control"
						help={ help || null }
						key={ key }
						label={ getLabel(
							attributeName,
							isHelpOpen,
							setIsHelpOpen
						) }
						onChange={ ( newValue ) =>
							updateValue( newValue, attributeName )
						}
					/>
				);
			case 'text':
			default:
				return (
					<TextControl
						className="form-block__block-control"
						help={ help || null }
						key={ key }
						label={ getLabel(
							attributeName,
							isHelpOpen,
							setIsHelpOpen
						) }
						onChange={ ( newValue ) =>
							updateValue(
								getSanitizedAttributeValue(
									newValue,
									attributes
								),
								attributeName
							)
						}
						value={ getSanitizedAttributeValue(
							props.attributes[ attributeName ],
							attributes
						) }
					/>
				);
		}
	};

	const getOptions = ( attribute ) =>
		inputAttributes[ attribute ].options || [];
	const nameAttribute = name
		? stripSpecialChars( name, false )
		: stripSpecialChars( label );

	const updateValue = ( newValue, attribute ) => {
		let value = {};
		value[ attribute ] = newValue;

		return setAttributes( value );
	};
	const formBlockControls = applyFilters( 'formBlock.input.controls', {
		props,
		nameAttribute,
	} );

	return (
		<InspectorControls>
			<PanelBody>
				<SelectControl
					label={ _x( 'Type', 'HTML attribute name', 'form-block' ) }
					onChange={ ( type ) => setAttributes( { type } ) }
					options={ getTypes().map( ( type ) => ( {
						label: types[ type ].label,
						value: type,
					} ) ) }
					value={ type }
				/>
				<TextControl
					help={
						! name
							? __(
									'The name is auto-generated from the label.',
									'form-block'
							  )
							: __(
									'The name has been set manually.',
									'form-block'
							  )
					}
					label={ _x( 'Name', 'HTML attribute name', 'form-block' ) }
					onChange={ ( name ) =>
						setAttributes( {
							name: stripSpecialChars( name, false ),
						} )
					}
					ref={ nameControlRef }
					value={ nameAttribute }
				/>
				{ controls.map( ( control, index ) =>
					getControl( control, type, index )
				) }
				{ React.isValidElement( formBlockControls )
					? formBlockControls
					: null }
			</PanelBody>
		</InspectorControls>
	);
}

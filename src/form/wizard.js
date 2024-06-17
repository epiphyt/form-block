import {
	store as blockEditorStore,
} from '@wordpress/block-editor';
import {
	createBlocksFromInnerBlocksTemplate,
} from '@wordpress/blocks';
import { Button, Flex, FlexItem, Modal, TextControl, ToggleControl } from '@wordpress/components';
import { useDispatch } from '@wordpress/data';
import { useState } from '@wordpress/element';
import { applyFilters } from '@wordpress/hooks';
import { __, _x } from '@wordpress/i18n';

const fieldMatches = applyFilters(
	'formBlock.wizard.fieldMatches',
	{
		checkbox: [
			_x( '?', 'potential form field name in lowercase', 'form-block' ),
			_x( 'consent', 'potential form field name in lowercase', 'form-block' ),
		],
		email: [
			_x( 'e-mail', 'potential form field name in lowercase', 'form-block' ),
			_x( 'email', 'potential form field name in lowercase', 'form-block' ),
			_x( 'mail', 'potential form field name in lowercase', 'form-block' ),
		],
		file: [
			_x( 'file', 'potential form field name in lowercase', 'form-block' ),
			_x( 'upload', 'potential form field name in lowercase', 'form-block' ),
		],
		password: [
			_x( 'password', 'potential form field name in lowercase', 'form-block' ),
		],
		tel: [
			_x( 'tel', 'potential form field name in lowercase', 'form-block' ),
			_x( 'phone', 'potential form field name in lowercase', 'form-block' ),
		],
		text: [
			_x( 'address', 'potential form field name in lowercase', 'form-block' ),
			_x( 'city', 'potential form field name in lowercase', 'form-block' ),
			_x( 'first name', 'potential form field name in lowercase', 'form-block' ),
			_x( 'last name', 'potential form field name in lowercase', 'form-block' ),
			_x( 'name', 'potential form field name in lowercase', 'form-block' ),
			_x( 'zip', 'potential form field name in lowercase', 'form-block' ),
		],
		textarea: [
			_x( 'message', 'potential form field name in lowercase', 'form-block' ),
		],
	}
);

export default function Wizard( props ) {
	const {
		clientId,
		isWizardOpen,
		setIsWizardOpen,
	} = props;
	const [ fields, setFields ] = useState( '' );
	const [ includeConsentCheckbox, setIncludeConsentCheckbox ] = useState( false );
	const { replaceInnerBlocks } = useDispatch( blockEditorStore );
	
	if ( ! isWizardOpen ) {
		return null;
	}
	
	const onInsert = () => {
		let blocks = [];
		const preparedFields = fields.split( ',' ).map( ( field ) => field.trim() );
		
		for ( const preparedField of preparedFields ) {
			let isAdded = false;
			const isRequired = preparedField.includes( '*' );
			const fieldLabel = preparedField.replace( '*', '' );
			
			checkFieldTypeLoop:
			for ( const fieldType of Object.keys( fieldMatches ) ) {
				for ( const potentialMatch of fieldMatches[ fieldType ] ) {
					if ( ! preparedField.toLowerCase().includes( potentialMatch ) ) {
						continue;
					}
					
					switch ( fieldType ) {
						case 'select':
							blocks.push( [
								'form-block/select',
								{
									label: fieldLabel,
									required: isRequired,
								},
							] );
							break;
						case 'textarea':
							blocks.push( [
								'form-block/textarea',
								{
									label: fieldLabel,
									required: isRequired,
								},
							] );
							break;
						default:
							let blockAttributes = {
								label: fieldLabel,
								required: isRequired,
								type: fieldType,
							};
							
							if ( fieldType === 'email' ) {
								blockAttributes.isReplyTo = true;
								blockAttributes.required = true;
							}
							else if ( potentialMatch.includes( _x( 'name', 'potential form field name in lowercase', 'form-block' ) ) ) {
								blockAttributes.required = true;
							}
							
							blocks.push( [
								'form-block/input',
								blockAttributes,
							] );
							break;
					}
					
					isAdded = true;
					break checkFieldTypeLoop;
				}
			}
			
			if ( ! isAdded ) {
				blocks.push( [
					'form-block/input',
					{
						label: fieldLabel,
						required: isRequired,
						type: 'text',
					},
				] );
					}
					break checkFieldTypeLoop;
				}
			}
		}
		
		if ( blocks.length ) {
			if ( includeConsentCheckbox ) {
				blocks.push( [
					'form-block/input',
					{
						label: __( 'I agree that my data will be stored and processed for the purpose of contacting me. You can find more information in our privacy policy.', 'form-block' ),
						name: 'data-processing',
						required: true,
						type: 'checkbox',
					},
				] );
			}
			
			blocks.push( [
				'form-block/input',
				{
					type: 'submit',
					value: __( 'Submit', 'form-block' ),
				},
			] );
			
			replaceInnerBlocks(
				clientId,
				createBlocksFromInnerBlocksTemplate( blocks ),
				true
			);
		}
		
		setIsWizardOpen( false );
	};
	
	return (
		<Modal
			className="form-block__wizard-modal"
			onRequestClose={ () => setIsWizardOpen( false ) }
			title={ __( 'Form creation wizard', 'form-block' ) }
		>
			<TextControl
				help={ __( 'Define the field labels of the fields you need. Separate multiple field labels with a comma. Add an * to a label to automatically mark the field as required.', 'form-block' ) }
				label={ __( 'Which form fields do you need?', 'form-block' ) }
				onChange={ ( fields ) => setFields( fields ) }
			/>
			<ToggleControl
				checked={ includeConsentCheckbox }
				help={ __( 'Add a checkbox the user has to check to give consent to the processing of the data provided in the form.', 'form-block' ) }
				label={ __( 'Consent checkbox', 'form-block' ) }
				onChange={ ( value ) => setIncludeConsentCheckbox( !! value ) }
			/>
			
			<Flex
				gap="1"
				justify="flex-start"
			>
				<FlexItem>
					<Button
						onClick={ () => onInsert() }
						variant="primary"
					>
						{ __( 'Create form', 'form-block' ) }
					</Button>
				</FlexItem>
				
				<FlexItem>
					<Button
						onClick={ () => setIsWizardOpen( false ) }
						variant="secondary"
					>
						{ __( 'Cancel', 'form-block' ) }
					</Button>
				</FlexItem>
			</Flex>
		</Modal>
	);
}

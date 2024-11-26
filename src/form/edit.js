import {
	__experimentalBlockVariationPicker as BlockVariationPicker,
	InnerBlocks,
	store as blockEditorStore,
	useBlockProps,
} from '@wordpress/block-editor';
import {
	createBlocksFromInnerBlocksTemplate,
	store as blocksStore,
} from '@wordpress/blocks';
import { useDispatch, useSelect } from '@wordpress/data';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import Controls from './controls';
import { form } from './icon';
import Wizard from './wizard';

export default function FormEdit( props ) {
	const { clientId, name, setAttributes } = props;
	const blockProps = useBlockProps();
	const hasInnerBlocks = useSelect(
		( select ) =>
			select( blockEditorStore ).getBlocks( clientId ).length > 0,
		[ clientId ]
	);

	if ( ! hasInnerBlocks ) {
		return (
			<Placeholder
				clientId={ clientId }
				name={ name }
				setAttributes={ setAttributes }
			/>
		);
	}

	return (
		<div { ...blockProps }>
			<Controls props={ props } />

			<InnerBlocks />
		</div>
	);
}

function Placeholder( { clientId, name, setAttributes } ) {
	const [ isWizardOpen, setIsWizardOpen ] = useState( false );
	const { defaultVariation, variations } = useSelect(
		( select ) => {
			const { getBlockVariations, getDefaultBlockVariation } =
				select( blocksStore );

			return {
				defaultVariation: getDefaultBlockVariation( name, 'block' ),
				variations: getBlockVariations( name, 'block' ),
			};
		},
		[ name ]
	);
	const { replaceInnerBlocks } = useDispatch( blockEditorStore );
	const blockProps = useBlockProps();

	return (
		<div { ...blockProps }>
			<BlockVariationPicker
				icon={ form }
				instructions={ __(
					'Create a form with the wizard or select one of the variations. You can adjust them at any time.',
					'form-block'
				) }
				label={ __( 'Form Type', 'form-block' ) }
				variations={ variations }
				onSelect={ ( nextVariation = defaultVariation ) => {
					if ( nextVariation.attributes ) {
						setAttributes( nextVariation.attributes );
					}

					if ( nextVariation.innerBlocks ) {
						replaceInnerBlocks(
							clientId,
							createBlocksFromInnerBlocksTemplate(
								nextVariation.innerBlocks
							),
							true
						);
					} else if ( nextVariation.name === 'wizard' ) {
						setIsWizardOpen( true );
					}
				} }
				allowSkip
			/>

			<Wizard
				clientId={ clientId }
				isWizardOpen={ isWizardOpen }
				setIsWizardOpen={ setIsWizardOpen }
			/>
		</div>
	);
}

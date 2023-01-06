import {
	__experimentalBlockVariationPicker as BlockVariationPicker,
	store as blockEditorStore, useBlockProps,
} from '@wordpress/block-editor';
import {
	createBlocksFromInnerBlocksTemplate,
	store as blocksStore,
} from '@wordpress/blocks';
import { useDispatch, useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

import icon from './icon';

export default function FormEdit( props ) {
	const {
		attributes: {
			
		},
		clientId,
		name,
	} = props;
	const hasInnerBlocks = useSelect(
		( select ) =>
			select( blockEditorStore ).getBlocks( clientId ).length > 0,
		[ clientId ]
	);
	
	if ( ! hasInnerBlocks ) {
		return <Placeholder
			name={ name }
		/>;
	}
	
	return null;
}

function Placeholder( { name } ) {
	const { defaultVariation, variations } = useSelect(
		( select ) => {
			const {
				getBlockVariations,
				getDefaultBlockVariation,
			} = select( blocksStore );
			
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
				label={ __( 'Form Type', 'form-block' ) }
				icon={ icon }
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
					}
				} }
				allowSkip
			/>
		</div>
	);
}

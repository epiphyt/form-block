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
import { select } from '@wordpress/data';
import { useDispatch, useSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { form } from './icon';

export default function FormEdit( props ) {
	const {
		attributes: {
			formId,
		},
		clientId,
		name,
		setAttributes,
	} = props;
	const blockProps = useBlockProps();
	const hasInnerBlocks = useSelect(
		( select ) =>
			select( blockEditorStore ).getBlocks( clientId ).length > 0,
		[ clientId ]
	);
	
	const isFormIdInUse = ( clientId, formId, blocks ) => {
		let hasFormId = false;
		
		for ( const block of blocks ) {
			if ( block.clientId === clientId ) {
				continue;
			}
			
			if ( block.innerBlocks ) {
				hasFormId = isFormIdInUse( clientId, formId, block.innerBlocks );
			}
			
			if ( hasFormId ) {
				return true;
			}
			
			hasFormId = block.attributes.formId === formId;
			
			if ( hasFormId ) {
				return true;
			}
		}
		
		return false;
	}
	
	const setFormId = () => {
		if ( ! formId ) {
			setAttributes( { formId: clientId } );
			
			return;
		}
		
		const allBlocks = select( blockEditorStore ).getBlocks();
		
		if ( isFormIdInUse( clientId, formId, allBlocks ) ) {
			setAttributes( { formId: clientId } );
			
			return;
		}
	}
	
	useEffect( () => setFormId, [] );
	
	if ( ! hasInnerBlocks ) {
		return <Placeholder
			clientId={ clientId }
			name={ name }
			setAttributes={ setAttributes }
		/>;
	}
	
	return (
		<div { ...blockProps }>
			<InnerBlocks />
		</div>
	);
}

function Placeholder( { clientId, name, setAttributes } ) {
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
				icon={ form }
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

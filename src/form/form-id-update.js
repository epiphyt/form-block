import {
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { createHigherOrderComponent } from '@wordpress/compose';
import { useEffect } from '@wordpress/element';
import { select } from '@wordpress/data';
import { addFilter } from '@wordpress/hooks';

const setFormId = createHigherOrderComponent( ( BlockEdit ) => ( props ) => {
	const {
		attributes: {
			formId,
		},
		clientId,
		setAttributes,
		name,
	} = props;
	
	if ( name !== 'form-block/form' ) {
		return <BlockEdit { ...props } />;
	}
	
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
	
	useEffect( () => {
		if ( ! formId ) {
			setAttributes( { formId: clientId } );
			
			return;
		}
		
		const allBlocks = select( blockEditorStore ).getBlocks();
		
		if ( isFormIdInUse( clientId, formId, allBlocks ) ) {
			setAttributes( { formId: clientId } );
			
			return;
		}
	}, [] );
	
	return <BlockEdit { ...props } />;
} );

addFilter( 'editor.BlockEdit', 'formBlock/form-id-update/set-form-id', setFormId );

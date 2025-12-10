import { createBlock } from '@wordpress/blocks';

const transforms = {
	to: [
		{
			type: 'block',
			blocks: [ 'form-block/fieldset' ],
			isMultiBlock: true,
			__experimentalConvert( blocks ) {
				const fieldsetInnerBlocks = blocks.map( ( block ) => {
					return createBlock(
						block.name,
						block.attributes,
						block.innerBlocks
					);
				} );

				return createBlock(
					'form-block/fieldset',
					{},
					fieldsetInnerBlocks
				);
			},
		},
	],
	from: [
		{
			type: 'block',
			blocks: [ 'form-block/select', 'form-block/textarea' ],
			transform: ( attributes ) => {
				return createBlock( 'form-block/input', attributes );
			},
		},
	],
};

export default transforms;

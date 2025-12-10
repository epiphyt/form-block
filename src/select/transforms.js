import { createBlock } from '@wordpress/blocks';

const transforms = {
	from: [
		{
			type: 'block',
			blocks: [ 'form-block/input', 'form-block/textarea' ],
			transform: ( attributes ) => {
				return createBlock( 'form-block/select', attributes );
			},
		},
	],
};

export default transforms;

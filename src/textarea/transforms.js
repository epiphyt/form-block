import { createBlock } from '@wordpress/blocks';

const transforms = {
	from: [
		{
			type: 'block',
			blocks: [ 'form-block/input', 'form-block/select' ],
			transform: ( attributes ) => {
				return createBlock( 'form-block/textarea', attributes );
			},
		},
	],
};

export default transforms;

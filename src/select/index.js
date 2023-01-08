/**
 * Form block.
 */
import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

import attributes from './attributes';
import SelectEdit from './edit';
import { select } from './icon';

registerBlockType( {
	apiVersion: 2,
	name: 'form-block/select',
}, {
	title: __( 'Select', 'form-block' ),
	icon: select,
	category: 'formatting',
	attributes,
	edit: SelectEdit,
	keywords: [
		__( 'choice', 'form-block' ),
		__( 'option', 'form-block' ),
	],
	save: () => null,
	supports: {
		html: false,
	},
	ancestor: [
		'form-block/form',
	],
} );

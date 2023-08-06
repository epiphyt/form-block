/**
 * Form block.
 */
import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

import attributes from './attributes';
import deprecated from './deprecated';
import SelectEdit from './edit';
import { select } from './icon';
import SelectSave from './save';

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
	save: SelectSave,
	supports: {
		html: false,
	},
	ancestor: [
		'form-block/form',
	],
	deprecated,
} );

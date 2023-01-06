/**
 * Form block.
 */
import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

import attributes from './attributes';
import FormEdit from './edit';

registerBlockType( {
	apiVersion: 2,
	name: 'form-block/input',
}, {
	title: __( 'Input', 'form-block' ),
	icon: 'editor-textcolor',
	category: 'formatting',
	attributes,
	edit: FormEdit,
	keywords: [
		__( 'text', 'form-block' ),
		__( 'number', 'form-block' ),
	],
	save: () => null,
	supports: {
		html: false,
	},
	ancestor: [
		'form-block/form',
	],
} );

/**
 * Form block.
 */
import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { overlayText } from '@wordpress/icons';

import attributes from './attributes';
import InputEdit from './edit';
import InputSave from './save';

registerBlockType( {
	apiVersion: 2,
	name: 'form-block/input',
}, {
	title: __( 'Input', 'form-block' ),
	icon: overlayText,
	category: 'formatting',
	attributes,
	edit: InputEdit,
	keywords: [
		__( 'text', 'form-block' ),
		__( 'number', 'form-block' ),
	],
	save: InputSave,
	supports: {
		html: false,
	},
	ancestor: [
		'form-block/form',
	],
} );

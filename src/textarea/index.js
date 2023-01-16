/**
 * Form block.
 */
import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

import attributes from './attributes';
import TextareaEdit from './edit';
import { textarea } from './icon';
import TextareaSave from './save';

registerBlockType( {
	apiVersion: 2,
	name: 'form-block/textarea',
}, {
	title: __( 'Textarea', 'form-block' ),
	icon: textarea,
	category: 'formatting',
	attributes,
	edit: TextareaEdit,
	keywords: [
		__( 'input', 'form-block' ),
		__( 'paragraph', 'form-block' ),
	],
	save: TextareaSave,
	supports: {
		html: false,
	},
	ancestor: [
		'form-block/form',
	],
} );

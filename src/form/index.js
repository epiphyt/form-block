/**
 * Form block.
 */
import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

import attributes from './attributes';
import FormEdit from './edit';
import { form } from './icon';
import FormSave from './save';
import variations from './variations';

registerBlockType( {
	apiVersion: 2,
	name: 'form-block/form',
}, {
	title: __( 'Form', 'form-block' ),
	icon: form,
	category: 'formatting',
	attributes,
	edit: FormEdit,
	keywords: [
		__( 'contact', 'form-block' ),
		__( 'mail', 'form-block' ),
		// TODO: add third keywords
	],
	save: FormSave,
	supports: {
		align: [
			'full',
			'wide',
		],
		anchor: true,
		html: false,
	},
	variations,
} );

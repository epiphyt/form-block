/**
 * Form block.
 */
import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

import deprecated from './deprecated';
import TextareaEdit from './edit';
import { textarea } from './icon';
import TextareaSave from './save';
import meta from './block.json';

import './editor.scss';

registerBlockType( meta, {
	icon: textarea,
	edit: TextareaEdit,
	save: TextareaSave,
	deprecated,
} );

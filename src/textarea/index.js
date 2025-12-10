/**
 * Textarea block.
 */
import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

import attributes from './attributes';
import deprecated from './deprecated';
import TextareaEdit from './edit';
import { textarea } from './icon';
import TextareaSave from './save';
import transforms from './transforms';
import meta from './block.json';

import './editor.scss';

registerBlockType( meta, {
	attributes,
	icon: textarea,
	edit: TextareaEdit,
	save: TextareaSave,
	deprecated,
	transforms,
} );

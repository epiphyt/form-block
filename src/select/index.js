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
import meta from './block.json';

import './editor.scss';

registerBlockType( meta, {
	attributes,
	icon: select,
	edit: SelectEdit,
	save: SelectSave,
	deprecated,
} );

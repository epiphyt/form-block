/**
 * Fieldset block.
 */
import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

import FieldsetEdit from './edit';
import { fieldset } from './icon';
import FieldsetSave from './save';
import meta from './block.json';

import './editor.scss';

registerBlockType( meta, {
	icon: fieldset,
	edit: FieldsetEdit,
	save: FieldsetSave,
} );

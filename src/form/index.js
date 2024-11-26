/**
 * Form block.
 */
import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

import FormEdit from './edit';
import { form } from './icon';
import FormSave from './save';
import meta from './block.json';
import variations from './variations';
import './form-id-update';

import './editor.scss';

registerBlockType( meta, {
	icon: form,
	edit: FormEdit,
	save: FormSave,
	variations,
} );

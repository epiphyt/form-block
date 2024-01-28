/**
 * Form block.
 */
import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

import attributes from './attributes';
import deprecated from './deprecated';
import InputEdit from './edit';
import { input } from './icon';
import InputSave from './save';
import meta from './block.json';

import './editor.scss';

registerBlockType(
	meta,
	{
		attributes,
		icon: input,
		edit: InputEdit,
		save: InputSave,
		deprecated,
	}
);

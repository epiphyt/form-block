import { InnerBlocks, RichText, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

import { ALLOWED_BLOCKS } from './constants';

export default function FieldsetEdit( props ) {
	const {
		attributes: { legend },
		setAttributes,
	} = props;
	const blockProps = useBlockProps();

	return (
		<fieldset { ...blockProps }>
			<RichText
				className="form-block__legend"
				onChange={ ( legend ) => setAttributes( { legend } ) }
				placeholder={ __( 'Legend', 'form-block' ) }
				tagName="legend"
				value={ legend || '' }
			/>

			<InnerBlocks
				allowedBlocks={ ALLOWED_BLOCKS }
				template={ [ [ 'form-block/input', { type: 'radio' } ] ] }
			/>
		</fieldset>
	);
}

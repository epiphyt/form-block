import { InnerBlocks, RichText, useBlockProps } from '@wordpress/block-editor';

export default function FieldsetSave( props ) {
	const {
		attributes: { legend },
	} = props;
	const blockProps = useBlockProps.save();

	return (
		<fieldset { ...blockProps }>
			<RichText.Content
				className="form-block__legend-content"
				tagName="legend"
				value={ legend }
			/>

			<InnerBlocks.Content />
		</fieldset>
	);
}

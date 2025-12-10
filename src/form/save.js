import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

export default function FormSave( props ) {
	const blockProps = useBlockProps.save();
	const innerBlocksProps = useInnerBlocksProps.save( blockProps );

	if ( ! props?.innerBlocks?.length ) {
		return null;
	}

	return (
		<form
			{ ...innerBlocksProps }
			enctype="multipart/form-data"
			noValidate={ true }
		/>
	);
}

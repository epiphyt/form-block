import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

export default function FormSave() {
	const blockProps = useBlockProps.save();
	const innerBlocksProps = useInnerBlocksProps.save( blockProps );

	return (
		<form
			{ ...innerBlocksProps }
			enctype="multipart/form-data"
			noValidate={ true }
		/>
	);
}

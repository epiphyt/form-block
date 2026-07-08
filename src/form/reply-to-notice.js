import { store as blockEditorStore } from '@wordpress/block-editor';
import { Button, Flex, FlexItem, Notice } from '@wordpress/components';
import { useReducedMotion } from '@wordpress/compose';
import { useDispatch, useSelect } from '@wordpress/data';
import { useMemo } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';

function getInputBlocks( blocks ) {
	return blocks.reduce( ( inputBlocks, block ) => {
		if ( block.name === 'form-block/input' ) {
			inputBlocks.push( block );
		}

		if ( block.innerBlocks.length ) {
			inputBlocks.push( ...getInputBlocks( block.innerBlocks ) );
		}

		return inputBlocks;
	}, [] );
}

export default function ReplyToNotice( { clientId } ) {
	const formBlock = useSelect(
		( select ) => select( blockEditorStore ).getBlock( clientId ),
		[ clientId ]
	);
	const { emailBlocks, replyToBlocks } = useMemo( () => {
		const inputBlocks = getInputBlocks( formBlock?.innerBlocks ?? [] );

		return {
			emailBlocks: inputBlocks.filter(
				( block ) => block.attributes.type === 'email'
			),
			replyToBlocks: inputBlocks.filter(
				( block ) => block.attributes.isReplyTo
			),
		};
	}, [ formBlock ] );
	const { flashBlock, selectBlock } = useDispatch( blockEditorStore );
	const prefersReducedMotion = useReducedMotion();

	let error = null;
	let linkBlocks = [];
	let linkLabel = '';

	if ( replyToBlocks.length > 1 ) {
		error = __(
			'Multiple fields are marked as reply-to. Only a single field can be used as reply-to for the email.',
			'form-block'
		);
		linkBlocks = replyToBlocks;
		linkLabel = __( 'Fields marked as reply-to:', 'form-block' );
	} else if ( replyToBlocks.length === 0 ) {
		if ( emailBlocks.length === 0 ) {
			error = __(
				'No field is marked as reply-to. Add an email input field and enable "Use as reply-to" for it in the block sidebar to allow answering the user via email.',
				'form-block'
			);
		} else {
			error = __(
				'No field is marked as reply-to. Enable "Use as reply-to" in the block sidebar for your email field to allow answering the user via email.',
				'form-block'
			);
			linkBlocks = emailBlocks;
			linkLabel = __( 'Available email fields:', 'form-block' );
		}
	}

	if ( ! error ) {
		return null;
	}

	const highlightBlock = ( event, highlightClientId ) => {
		selectBlock( highlightClientId );
		flashBlock( highlightClientId );

		event.target.ownerDocument
			.getElementById( `block-${ highlightClientId }` )
			?.scrollIntoView( {
				behavior: prefersReducedMotion ? 'instant' : 'smooth',
			} );
	};
	const getBlockLabel = ( block, index ) =>
		block.attributes.label ||
		block.attributes.name ||
		// translators: %d: number of the field
		sprintf( __( 'Field %d', 'form-block' ), index + 1 );

	return (
		<Notice
			className="form-block__reply-to-notice"
			isDismissible={ false }
			politeness="polite"
			spokenMessage={ error }
			status="error"
		>
			<p>{ error }</p>

			{ linkBlocks.length ? (
				<Flex justify="flex-start" wrap>
					<FlexItem>{ linkLabel }</FlexItem>

					{ linkBlocks.map( ( block, index ) => (
						<FlexItem key={ block.clientId }>
							<Button
								onClick={ ( event ) =>
									highlightBlock( event, block.clientId )
								}
								variant="link"
							>
								{ sprintf(
									// translators: %s: field label
									__( 'Go to input %s', 'form-block' ),
									getBlockLabel( block, index )
								) }
							</Button>
						</FlexItem>
					) ) }
				</Flex>
			) : null }
		</Notice>
	);
}

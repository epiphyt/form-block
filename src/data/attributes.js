import { ExternalLink } from '@wordpress/components';
import { applyFilters } from '@wordpress/hooks';
import { __, _x } from '@wordpress/i18n';

const mdnAttributeLinkBase = 'https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input';

export const attributes = applyFilters(
	'formBlock.data.attributes',
	{
		checked: {
			controlType: 'toggle',
			description: __( 'Whether the element is checked by default.', 'form-block' ),
			label: _x( 'Checked', 'HTML attribute name', 'form-block' ),
		},
		customDate: {
			controlType: 'custom-date',
		},
		disabled: {
			controlType: 'toggle',
			description: __( 'Whether the form element is disabled and will not be submitted by sending the form.', 'form-block' ),
			label: _x( 'Disabled', 'HTML attribute name', 'form-block' ),
		},
		isReplyTo: {
			controlType: 'toggle',
			description: __( 'Whether to use the value of this field as reply-to in the email to allow automatically answer the user via email.', 'form-block' ),
			hideLink: true,
			label: __( 'Use as reply-to', 'form-block' ),
		},
		pattern: {
			controlType: 'text',
			description: __( 'A pattern the value must match to be valid. Any valid regular expression can be used (without forward slashes in the beginning or the end).', 'form-block' ),
			examples: [
				'value',
				'\d+',
				'(.*){10,20}',
			],
			label: _x( 'Pattern', 'HTML attribute name', 'form-block' ),
		},
		placeholder: {
			controlType: 'text',
			description: __( 'A placeholder is displayed in the form element before any value is added to it.', 'form-block' ),
			label: _x( 'Placeholder', 'HTML attribute name', 'form-block' ),
		},
		readOnly: {
			controlType: 'toggle',
			description: __( 'If the form element is set to readonly, it cannot be edited but you can still interact with its value (e.g. mark and copy it).', 'form-block' ),
			label: _x( 'Readonly', 'HTML attribute name', 'form-block' ),
		},
		value: {
			controlType: 'text',
			description: __( 'The value of the input.', 'form-block' ),
			label: _x( 'Value', 'HTML attribute name', 'form-block' ),
		},
	},
);

export const getAttributeHelp = ( attribute ) => {
	if ( ! attributes[ attribute ].description ) {
		return null;
	}
	
	return (
		<>
			{ attributes[ attribute ].description
				? <p>{ attributes[ attribute ].description }</p>
				: null
			}
			{ attributes[ attribute ].examples
				? <>
					<h2>{ __( 'Examples', 'form-block' ) }</h2>
					<ul>
					{ attributes[ attribute ].examples.map(
						( example, index ) => <li key={ index }>
							<code className="form-block__inline-code">
								{ example }
							</code>
						</li>
					) }
					</ul>
				</>
				: null
			}
			{ ! attributes[ attribute ].hideLink
				? <ExternalLink href={ mdnAttributeLinkBase + '#' + attribute }>
					{ __( 'More information', 'form-block' ) }
				</ExternalLink>
				: null
			}
		</>
	);
}

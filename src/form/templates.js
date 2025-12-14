import { applyFilters } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

export const contactForm = applyFilters( 'formBlock.form.template.contact', [
	[
		'form-block/input',
		{
			label: __( 'Name', 'form-block' ),
			required: true,
		},
	],
	[
		'form-block/input',
		{
			isReplyTo: true,
			label: __( 'Email Address', 'form-block' ),
			required: true,
			type: 'email',
		},
	],
	[
		'form-block/input',
		{
			label: __( 'Phone', 'form-block' ),
			type: 'tel',
		},
	],
	[
		'form-block/textarea',
		{
			label: __( 'Message', 'form-block' ),
		},
	],
	[
		'form-block/input',
		{
			type: 'submit',
			value: __( 'Submit', 'form-block' ),
		},
	],
] );

export const defaultForm = applyFilters( 'formBlock.form.template.default', [
	[ 'form-block/input', {} ],
	[
		'form-block/input',
		{
			type: 'submit',
			value: __( 'Submit', 'form-block' ),
		},
	],
] );

export const newsletterForm = applyFilters(
	'formBlock.form.template.newsletter',
	[
		[
			'form-block/input',
			{
				isReplyTo: true,
				label: __( 'Email Address', 'form-block' ),
				required: true,
				type: 'email',
			},
		],
		[
			'form-block/input',
			{
				type: 'submit',
				value: __( 'Submit', 'form-block' ),
			},
		],
	]
);

import { __ } from '@wordpress/i18n';

export const contactForm = [
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
			label: __( 'Email Address', 'form-block' ),
			required: true,
			type: 'email',
		},
	],
	[
		'form-block/input',
		{
			label: __( 'Telephone', 'form-block' ),
			type: 'tel',
		},
	],
	[
		'form-block/input',
		{
			label: __( 'Submit', 'form-block' ),
			type: 'submit',
		},
	],
];

export const defaultForm = [
	[
		'form-block/input',
		{},
	],
	[
		'form-block/input',
		{
			label: __( 'Submit', 'form-block' ),
			type: 'submit',
		},
	],
];

export const newsletterForm = [
	[
		'form-block/input',
		{
			label: __( 'Email Address', 'form-block' ),
			required: true,
			type: 'email',
		},
	],
	[
		'form-block/input',
		{
			label: __( 'Submit', 'form-block' ),
			type: 'submit',
		},
	],
];

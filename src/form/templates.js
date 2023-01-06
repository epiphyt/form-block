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
			label: __( 'Email', 'form-block' ),
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
];

export const defaultForm = [
	[
		'form-block/input',
		{},
	],
];

export const newsletterForm = [
	
];

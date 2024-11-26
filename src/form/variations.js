import { __ } from '@wordpress/i18n';
import { atSymbol } from '@wordpress/icons';

import { empty, contact, wand } from './icon';
import { contactForm, defaultForm, newsletterForm } from './templates';

const variations = [
	{
		name: 'wizard',
		title: __( 'Form Wizard', 'form-block' ),
		description: __(
			'An interactive wizard for easy and fast form creation',
			'form-block'
		),
		icon: wand,
		innerBlocks: null,
		isDefault: true,
		scope: [ 'block' ],
	},
	{
		name: 'empty',
		title: __( 'Empty Form', 'form-block' ),
		description: __( 'A single empty input field', 'form-block' ),
		icon: empty,
		innerBlocks: defaultForm,
		scope: [ 'block' ],
	},
	{
		name: 'contact',
		title: __( 'Contact Form', 'form-block' ),
		description: __(
			'Basic contact form with name, email, phone and message field',
			'form-block'
		),
		icon: contact,
		innerBlocks: contactForm,
		scope: [ 'block' ],
	},
	{
		name: 'newsletter',
		title: __( 'Newsletter Form', 'form-block' ),
		description: __( 'Newsletter form with an email field', 'form-block' ),
		icon: atSymbol,
		innerBlocks: newsletterForm,
		scope: [ 'block' ],
	},
];

export default variations;

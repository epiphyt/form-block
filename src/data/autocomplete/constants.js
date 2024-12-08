import { __ } from '@wordpress/i18n';

export const FIELD_MAPPING = {
	'address-level1': [ __( 'State', 'form-block' ) ],
	'address-level2': [
		__( 'City', 'form-block' ),
		__( 'Location', 'form-block' ),
		__( 'Town', 'form-block' ),
	],
	'address-line1': [
		__( 'Address', 'form-block' ),
		__( 'Street', 'form-block' ),
	],
	'country-name': [ __( 'Country', 'form-block' ) ],
	email: [ __( 'E-mail', 'form-block' ), __( 'Email', 'form-block' ) ],
	'family-name': [ __( 'Last name', 'form-block' ) ],
	'given-name': [ __( 'First name', 'form-block' ) ],
	name: [ __( 'Name', 'form-block' ) ],
	'postal-code': [
		__( 'Post code', 'form-block' ),
		__( 'Postal code', 'form-block' ),
		__( 'ZIP code', 'form-block' ),
	],
	sex: [
		__( 'Gender', 'form-block' ),
		__( 'Greeting', 'form-block' ),
		__( 'Sex', 'form-block' ),
	],
	tel: [ __( 'Phone', 'form-block' ), __( 'Tel', 'form-block' ) ],
	'honorific-prefix': [ __( 'Title', 'form-block' ) ],
};

export const OPTIONS = [
	{ label: __( 'No value set', 'form-block' ), value: '' },
	{ label: __( 'off', 'form-block' ), value: 'off' },
	{ label: __( 'on', 'form-block' ), value: 'on' },
	{ label: __( 'Name', 'form-block' ), value: 'name' },
	{ label: __( 'First name', 'form-block' ), value: 'given-name' },
	{ label: __( 'Middle name', 'form-block' ), value: 'additional-name' },
	{ label: __( 'Last name', 'form-block' ), value: 'family-name' },
	{ label: __( 'Nickname', 'form-block' ), value: 'nickname' },
	{ label: __( 'Username', 'form-block' ), value: 'username' },
	{ label: __( 'Title or prefix', 'form-block' ), value: 'honorific-prefix' },
	{ label: __( 'Email address', 'form-block' ), value: 'email' },
	{ label: __( 'Telephone', 'form-block' ), value: 'tel' },
	{ label: __( 'Suffix', 'form-block' ), value: 'honorific-suffix' },
	{ label: __( 'Organization', 'form-block' ), value: 'organization' },
	{ label: __( 'Job title', 'form-block' ), value: 'organization-title' },
	{ label: __( 'Gender identity', 'form-block' ), value: 'sex' },
	{ label: __( 'Street address', 'form-block' ), value: 'address-line1' },
	{ label: __( 'City', 'form-block' ), value: 'address-level2' },
	{ label: __( 'State', 'form-block' ), value: 'address-level1' },
	{ label: __( 'ZIP code', 'form-block' ), value: 'postal-code' },
	{ label: __( 'Country code', 'form-block' ), value: 'country' },
	{ label: __( 'Country name', 'form-block' ), value: 'country-name' },
	{ label: __( 'New password', 'form-block' ), value: 'new-password' },
	{
		label: __( 'Current password', 'form-block' ),
		value: 'current-password',
	},
];

=== Form Block ===
Contributors: epiphyt, kittmedia
Tags: contact, form, contact form, gutenberg, block editor
Requires at least: 6.3
Stable tag: 1.2.0
Tested up to: 6.4
Requires PHP: 7.4
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

An extensive yet user-friendly form block.

== Description ==

WordPress offers several (contact) form plugins, but most of them are not up-to-date anymore when it comes to creating forms. Form Block tackles this problem by utilizing the block editor's power. It enables you to create a form effortlessly within the block editor itself, which makes the process of creating a form much more enjoyable and similar to creating other types of blocks. This innovative approach to form creation ensures that Form Block stands out from other WordPress form plugins.

**Note: This plugins requires the PHP extension ["Document Object Model" (php-dom)](https://www.php.net/manual/en/book.dom.php).**

= Features =

* Fully support of the block editor
* Create forms with an unlimited number of fields
* Select from a wide variety of field types
* Use a predefined form or start from scratch
* Integrated honeypot for spam protection
* Integrated knowledge base for field types
* Client-side and server-side validation

= Getting started =

1. Add the "Form Block" block anywhere you want to enable a form in your block editor.
1. Select a predefined form or start from scratch.
1. Add/Edit/Remove fields to your form as you like. You can select from input, select and textarea fields.

After submission, the form data will be sent to the email address you have set in the settings.

= Coming soon =

There is much more planned for Form Block. Here is a small selection of features that are coming soon:

* Flood Control
* [Antispam Bee](https://wordpress.org/plugins/antispam-bee/) integration
* Custom date picker
* More customization options

= Pro version =

There is also a Pro version of Form Block available. It offers additional features, such as:

* Extended server-side validation
* Custom recipient(s) for each form
* Custom form action
* Upload via drag and drop
* Global defined consent checkbox
* Field dependencies

You can find more information at [https://formblock.pro/en/](https://formblock.pro/en/).


== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/form-block` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress.
1. Add the 'Form Block' block anywhere you want to enable a form in your block editor.


== Frequently Asked Questions ==

= Where can I find the settings? =

The settings can be found in **Settings > Writing > Form Block**.

= Where will submitted forms be sent to? =

All submitted forms will be sent to the administration email address you have set in the settings (**Settings > General > Administration Email Address**).

If you want to use another email address as recipient, you can use the filter `form_block_recipients` to change the recipient email address.

= Is there a Pro version? =

Yes, there is a Pro version with enhanced functionality. Lean more at [https://formblock.pro/en/](https://formblock.pro/en/).

= How can I contribute? =

The code is open source and hosted on [GitHub](https://github.com/epiphyt/form-block). Read the [contributing guide](https://github.com/epiphyt/form-block/blob/main/CONTRIBUTING.md) for detailed information.

= Who are you, folks? =

We are [Epiphyt](https://epiph.yt/), your friendly neighborhood WordPress plugin shop from southern Germany.


== Changelog ==

= 1.3.0 =
* Added: Support block settings like font size, line height and dimensions
* Added: By selecting an invalid field, the error message will now be announced to screen readers
* Fixed: Improved resetting a form after successful submit
* Notice: This version requires at least WordPress 6.3
* For developers: Each `<form>` element has now its validator object attached to it

= 1.2.0 =
* Added: Form creation wizard for even easier form creation
* Added: Ability to set a custom subject for each form
* Added: Error message summary if multiple form fields are invalid for screen readers
* Added: Checks to prohibit multiple form submissions while clicking multiple times on the submit button
* Added: Check for PHP DOM extension
* Added: A new filter to add custom controls in the primary panel of the form block
* Added: A new class for the notice below the field stating how required fields are marked for easier customization
* Fixed: After re-validating an invalid form, it can now be sent successfully
* Fixed: Line breaks for checkbox labels in Twenty Twenty-Three

= 1.1.4 =
* Fixed: Sending potentially the wrong checkbox/radio button value if multiple fields have the same name. There is now a new "value" field available for these input types. Please review your forms and adjust the value for your inputs to fix this issue.

= 1.1.3 =
* Fixed: Invalid fields are now marked via `aria-invalid` for better accessibility
* Fixed: Added `aria-hidden="true"` to the asterisk, marking a field as required, for better accessibility

= 1.1.2 =
* Fixed: Issue storing form data for forms when they are in another block (e.g. in a column)

= 1.1.1 =
* Fixed: Design issue with labels of checkboxes and radio buttons

= 1.1.0 =
* Added: Rich text editor for labels
* Added: Option to set an email field as "reply-to" in the email notification
* Added: Preparation to fully support Form Block Pro version 1.0.0
* Improved: Displaying of values from checkboxes and radio buttons
* Improved: Design of reset and submit buttons (thanks [@zu](https://github.com/zu) for reporting)
* Fixed: Getting correct values for form field name attributes

= 1.0.2 =
* Fixed: Security issue regarding an CSRF (thanks [@DanielRuf](https://github.com/DanielRuf) for reporting)
* Fixed: Changing value of reset/submit buttons is not possible (thanks [@zu](https://github.com/zu) for reporting)
* Fixed: Allowing multiple radio buttons with identical name
* Fixed: Submitting an empty form with required fields shows the loading indicator

= 1.0.1 =
* Added: Missing loading information/animation after submit
* Fixed: Potential PHP warning on uninstallation
* Fixed: Design for Twenty Twenty-Three
* Fixed: Design for Twenty Twenty-Two

= 1.0.0 =
* Initial release


== Upgrade Notice ==

== Screenshots ==
1. The form in the frontend
2. The form block variation picker
3. The form creation wizard to create form fields without them creating manually
4. The form block in the backend
5. The form block settings

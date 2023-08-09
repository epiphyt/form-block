=== Form Block ===
Contributors: epiphyt, kittmedia
Tags: contact, form, contact form, gutenberg, block editor
Requires at least: 6.0
Stable tag: 1.1.3
Tested up to: 6.3
Requires PHP: 7.4
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

An extensive yet user-friendly form block.

== Description ==

WordPress offers several (contact) form plugins, but most of them are not up-to-date anymore when it comes to creating forms. Form Block tackles this problem by utilizing the block editor's power. It enables you to create a form effortlessly within the block editor itself, which makes the process of creating a form much more enjoyable and similar to creating other types of blocks. This innovative approach to form creation ensures that Form Block stands out from other WordPress form plugins.


== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/form-block` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress.
1. Add the 'Form Block' block anywhere you want to enable a form in your block editor.


== Frequently Asked Questions ==

= Where can I find the settings? =

The settings can be found in **Settings > Writing > Form Block**.

= Is there a Pro version? =

Yes, there is a Pro version with enhanced functionality. Lean more at https://formblock.pro/en/.

= How can I contribute? =

The code is open source and hosted on [GitHub](https://github.com/epiphyt/form-block). Read the [contributing guide](https://github.com/epiphyt/form-block/blob/main/CONTRIBUTING.md) for detailed information.

= Who are you, folks? =

We are [Epiphyt](https://epiph.yt/), your friendly neighborhood WordPress plugin shop from southern Germany.


== Changelog ==

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
2. The form block in the backend
3. The form block settings

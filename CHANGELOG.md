# Changelog

## 1.4.1
* Improved: The notice for required fields is now above the form for enhanced accessibility
* Fixed: Gaps in certain browsers from hidden input fields
* Fixed: The value of hidden input fields is now populated in the frontend
* Fixed: Options without a label now correctly show the value in the select
* Fixed: Potential PHP warning for split date and time fields

## 1.4.0
* Added: Custom separated date fields (read [the announcement for more information](https://epiph.yt/en/blog/2024/form-block-1-4-0-release-and-opinions-on-date-pickers/))
* Added: All supported input types that were previously only part of the Pro version
* Added: Design for Twenty Twenty-Four
* Added: More recognized field names for the form wizard
* Improved: Input type selection is now more descriptive and translatable
* Fixed: `aria-describedby` for error fields is no more added multiple times
* Fixed: Form wizard now returns the proper input fields

## 1.3.0
* Added: Support block settings like font size, line height and dimensions
* Added: By selecting an invalid field, the error message will now be announced to screen readers
* Fixed: Improved resetting a form after successful submit
* Notice: This version requires at least WordPress 6.3
* For developers: Each `<form>` element has now its validator object attached to it

## 1.2.0
* Added: Form creation wizard for even easier form creation
* Added: Ability to set a custom subject for each form
* Added: Error message summary if multiple form fields are invalid for screen readers
* Added: Checks to prohibit multiple form submissions while clicking multiple times on the submit button
* Added: Check for PHP DOM extension
* Added: A new filter to add custom controls in the primary panel of the form block
* Added: A new class for the notice below the field stating how required fields are marked for easier customization
* Fixed: After re-validating an invalid form, it can now be sent successfully
* Fixed: Line breaks for checkbox labels in Twenty Twenty-Three

## 1.1.4
* Fixed: Sending potentially the wrong checkbox/radio button value if multiple fields have the same name. There is now a new "value" field available for these input types. Please review your forms and adjust the value for your inputs to fix this issue.

## 1.1.3
* Fixed: Invalid fields are now marked via `aria-invalid` for better accessibility
* Fixed: Added `aria-hidden="true"` to the asterisk, marking a field as required, for better accessibility

## 1.1.2
* Fixed: Issue storing form data for forms when they are in another block (e.g. in a column)

## 1.1.1
* Fixed: Design issue with labels of checkboxes and radio buttons

## 1.1.0
* Added: Rich text editor for labels
* Added: Option to set an email field as "reply-to" in the email notification
* Added: Preparation to fully support Form Block Pro version 1.0.0
* Improved: Displaying of values from checkboxes and radio buttons
* Improved: Design of reset and submit buttons (thanks [@zu](https://github.com/zu) for reporting)
* Fixed: Getting correct values for form field name attributes

## 1.0.2
* Fixed: Security issue regarding an CSRF (thanks [@DanielRuf](https://github.com/DanielRuf) for reporting)
* Fixed: Changing value of reset/submit buttons is not possible (thanks [@zu](https://github.com/zu) for reporting)
* Fixed: Allowing multiple radio buttons with identical name
* Fixed: Submitting an empty form with required fields shows the loading indicator

## 1.0.1

* Added: Missing loading information/animation after submit
* Fixed: Potential PHP warning on uninstallation
* Fixed: Design for Twenty Twenty-Three
* Fixed: Design for Twenty Twenty-Two

## 1.0.0

* Initial release

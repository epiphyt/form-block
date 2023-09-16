# Changelog

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

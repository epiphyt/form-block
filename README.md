# Form Block

WordPress offers several (contact) form plugins, but most of them are not up-to-date anymore when it comes to creating forms. Form Block tackles this problem by utilizing the block editor's power. It enables you to create a form effortlessly within the block editor itself, which makes the process of creating a form much more enjoyable and similar to creating other types of blocks. This innovative approach to form creation ensures that Form Block stands out from other WordPress form plugins.

## Requirements

PHP: 7.4<br>
WordPress: 6.3

**Note: This plugins requires the PHP extension ["Document Object Model" (php-dom)](https://www.php.net/manual/en/book.dom.php).**


## Installation

1. Upload the plugin files to the `/wp-content/plugins/form-block` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress.
1. Add the 'Form Block' block anywhere you want to enable a form in your block editor.


## Frequently Asked Questions

### Where can I find the settings?

The settings can be found in **Settings > Writing > Form Block**.

### Where will submitted forms be sent to?

All submitted forms will be sent to the administration email address you have set in the settings (**Settings > General > Administration Email Address**).

If you want to use another email address as recipient, you can use the filter `form_block_recipients` to change the recipient email address.

### Is there a Pro version?

Yes, there is a Pro version with enhanced functionality. Lean more at https://formblock.pro/en/.

### How can I contribute?

The code is open source and hosted on [GitHub](https://github.com/epiphyt/form-block). Read the [contributing guide](https://github.com/epiphyt/form-block/blob/main/CONTRIBUTING.md) for detailed information.

### Who are you, folks?

We are [Epiphyt](https://epiph.yt/), your friendly neighborhood WordPress plugin shop from southern Germany.

## Security

For security related information, please consult the [security policy](SECURITY.md).

## License

Form Block is free software, and is released under the terms of the GNU General Public License version 2 or (at your option) any later version. See [LICENSE.md](LICENSE.md) for complete license.

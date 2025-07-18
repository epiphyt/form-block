<?php
namespace epiphyt\Form_Block;

/*
Plugin Name:		Form Block
Plugin URI:			https://formblock.pro/en/
Description:		An extensive yet user-friendly form block.
Version:			1.5.6
Author:				Epiphyt
Author URI:			https://epiph.yt
License:			GPL2
License URI:		https://www.gnu.org/licenses/gpl-2.0.html
Requires at least:	6.3
Requires PHP:		7.4
Tags:				form, blocks, block editor, email, contact form
Tested up to:		6.8
Text Domain:		form-block

Form Block is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Form Block is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Form Block. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/
\defined( 'ABSPATH' ) || exit;

if ( ! \defined( 'EPI_FORM_BLOCK_BASE' ) ) {
	if ( \file_exists( \WP_PLUGIN_DIR . '/form-block/' ) ) {
		\define( 'EPI_FORM_BLOCK_BASE', \WP_PLUGIN_DIR . '/form-block/' );
	}
	else if ( \file_exists( \WPMU_PLUGIN_DIR . '/form-block/' ) ) {
		\define( 'EPI_FORM_BLOCK_BASE', \WPMU_PLUGIN_DIR . '/form-block/' );
	}
	else {
		\define( 'EPI_FORM_BLOCK_BASE', \plugin_dir_path( __FILE__ ) );
	}
}

\define( 'EPI_FORM_BLOCK_FILE', \EPI_FORM_BLOCK_BASE . \basename( __FILE__ ) );
\define( 'EPI_FORM_BLOCK_URL', \plugin_dir_url( \EPI_FORM_BLOCK_FILE ) );
\define( 'FORM_BLOCK_VERSION', '1.5.6' );

if ( ! \extension_loaded( 'dom' ) ) {
	/**
	 * Disable the plugin if the php-dom extension is missing.
	 */
	function disable_plugin(): void {
		?>
		<div class="notice notice-error">
			<p><?php \esc_html_e( 'The PHP extension "Document Object Model" (php-dom) is missing. Form Block requires this extension to be installed and enabled. Please ask your hosting provider to install and enable it. Form Block disables itself now. Please re-enable it again if the extension is installed and enabled.', 'form-block' ); ?></p>
		</div>
		<?php
		\deactivate_plugins( \plugin_basename( __FILE__ ) );
	}
	
	\add_action( 'admin_notices', __NAMESPACE__ . '\disable_plugin' );
}

/**
 * Autoload all necessary classes.
 * 
 * @param	string	$class_name The class name of the auto-loaded class
 */
\spl_autoload_register( static function( string $class_name ): void {
	if ( \strpos( $class_name, __NAMESPACE__ ) !== 0 ) {
		return;
	}
	
	$namespace = \strtolower( __NAMESPACE__ . '\\' );
	$path = \explode( '\\', $class_name );
	$filename = \str_replace( '_', '-', \strtolower( \array_pop( $path ) ) );
	$class_name = \str_replace(
		[ $namespace, '\\', '_' ],
		[ '', '/', '-' ],
		\strtolower( $class_name )
	);
	$string_position = \strrpos( $class_name, $filename );
	
	if ( $string_position !== false ) {
		$class_name = \substr_replace( $class_name, 'class-' . $filename, $string_position, \strlen( $filename ) );
	}
	
	$maybe_file = __DIR__ . '/inc/' . $class_name . '.php';
	
	if ( \file_exists( $maybe_file ) ) {
		require_once $maybe_file;
	}
} );

Form_Block::get_instance()->init();

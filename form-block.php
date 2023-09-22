<?php
namespace epiphyt\Form_Block;
use function array_pop;
use function define;
use function defined;
use function explode;
use function file_exists;
use function plugin_dir_url;
use function spl_autoload_register;
use function str_replace;
use function strlen;
use function strrpos;
use function strtolower;
use function substr_replace;
use const WP_PLUGIN_DIR;

/*
Plugin Name:	Form Block
Plugin URI:		https://formblock.pro/en/
Description:	An extensive yet user-friendly form block.
Version:		1.1.4
Author:			Epiphyt
Author URI:		https://epiph.yt
License:		GPL2
License URI:	https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:	form-block
Domain Path:	/languages

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

// exit if ABSPATH is not defined
defined( 'ABSPATH' ) || exit;

define( 'FORM_BLOCK_VERSION', '1.1.4' );

if ( ! defined( 'EPI_FORM_BLOCK_BASE' ) ) {
	define( 'EPI_FORM_BLOCK_BASE', WP_PLUGIN_DIR . '/form-block/' );
}

if ( ! defined( 'EPI_FORM_BLOCK_FILE' ) ) {
	define( 'EPI_FORM_BLOCK_FILE', __FILE__ );
}

if ( ! defined( 'EPI_FORM_BLOCK_URL' ) ) {
	define( 'EPI_FORM_BLOCK_URL', plugin_dir_url( EPI_FORM_BLOCK_FILE ) );
}

if ( ! \extension_loaded( 'dom' ) ) {
	/**
	 * Disable the plugin if the php-dom extension is missing.
	 */
	function disable_plugin() {
		?>
		<div class="notice notice-error">
			<p><?php \esc_html_e( 'The PHP extension "Document Object Model" (php-dom) is missing. Embed Privacy requires this extension to be installed and enabled. Please ask your hosting provider to install and enable it. Embed Privacy disables itself now. Please re-enable it again if the extension is installed and enabled.', 'embed-privacy' ); ?></p>
		</div>
		<?php
		\deactivate_plugins( \plugin_basename( __FILE__ ) );
	}
	
	\add_action( 'admin_notices', __NAMESPACE__ . '\disable_plugin' );
}

/**
 * Autoload all necessary classes.
 * 
 * @param	string	$class The class name of the autoloaded class
 */
spl_autoload_register( function( string $class ) {
	$namespace = strtolower( __NAMESPACE__ . '\\' );
	$path = explode( '\\', $class );
	$filename = str_replace( '_', '-', strtolower( array_pop( $path ) ) );
	$class = str_replace(
		[ $namespace, '\\', '_' ],
		[ '', '/', '-' ],
		strtolower( $class )
	);
	$string_position = strrpos( $class, $filename );
	
	if ( $string_position !== false ) {
		$class = substr_replace( $class, 'class-' . $filename, $string_position, strlen( $filename ) );
	}
	
	$maybe_file = __DIR__ . '/inc/' . $class . '.php';
	
	if ( file_exists( $maybe_file ) ) {
		require_once $maybe_file;
	}
} );

Form_Block::get_instance()->init();

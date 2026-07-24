<?php
/**
 * PHPUnit bootstrap file.
 *
 * Sets up Composer autoloading, defines the plugin constants the classes
 * under test rely on and registers the same class autoloader the plugin uses
 * at runtime (without booting the plugin itself).
 */
namespace epiphyt\Form_Block;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/TestCase.php';

// constants used by classes under test
\defined( 'ABSPATH' ) || \define( 'ABSPATH', __DIR__ . '/../' );
\defined( 'EPI_FORM_BLOCK_BASE' ) || \define( 'EPI_FORM_BLOCK_BASE', \dirname( __DIR__ ) . '/' );
\defined( 'EPI_FORM_BLOCK_FILE' ) || \define( 'EPI_FORM_BLOCK_FILE', \EPI_FORM_BLOCK_BASE . 'form-block.php' );
\defined( 'EPI_FORM_BLOCK_URL' ) || \define( 'EPI_FORM_BLOCK_URL', 'https://example.org/wp-content/plugins/form-block/' );
\defined( 'FORM_BLOCK_VERSION' ) || \define( 'FORM_BLOCK_VERSION', '1.8.0-dev' );

/**
 * Autoload the plugin classes from inc/ (mirrors the autoloader in form-block.php).
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

	$maybe_file = \dirname( __DIR__ ) . '/inc/' . $class_name . '.php';

	if ( \file_exists( $maybe_file ) ) {
		require_once $maybe_file;
	}
} );

<?php
namespace epiphyt\Form_Block\blocks;

/**
 * Block registry functionality.
 * 
 * @since	1.6.0
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Block_Registry {
	/**
	 * Initialize the class.
	 */
	public function init(): void {
		\add_action( 'init', [ $this, 'register' ], 15 );
	}
	
	/**
	 * Register blocks.
	 * 
	 * @param	string	$base_path Plugin base path
	 */
	public function register( string $base_path = '' ): void {
		if ( empty( $base_path ) ) {
			$base_path = \EPI_FORM_BLOCK_BASE;
		}
		
		if ( \function_exists( 'wp_register_block_types_from_metadata_collection' ) ) {
			// WordPress 6.8+
			\wp_register_block_types_from_metadata_collection(
				$base_path . 'build',
				$base_path . 'build/blocks-manifest.php'
			);
		}
		else {
			// WordPress 6.7
			if ( \function_exists( 'wp_register_block_metadata_collection' ) ) {
				\wp_register_block_metadata_collection( $base_path . 'build', $base_path . 'build/blocks-manifest.php' );
			}
			
			$manifest_data = require $base_path . 'build/blocks-manifest.php';
			
			foreach ( \array_keys( $manifest_data ) as $block_type ) {
				\register_block_type( $base_path. "build/{$block_type}" );
			}
		}
	}
}

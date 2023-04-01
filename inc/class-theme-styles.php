<?php
namespace epiphyt\Form_Block;

/**
 * Form Block theme styles class.
 * 
 * @since	1.0.1
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Theme_Styles {
	/**
	 * @var		\epiphyt\Form_Block\Theme_Styles
	 */
	public static $instance;
	
	/**
	 * Initialize the class.
	 */
	public function init(): void {
		add_action( 'init', [ $this, 'register_styles' ], 20 );
		
		add_filter( 'form_block_form_style', [ $this, 'register_block_styles' ] );
	}
	
	/**
	 * Get a unique instance of the class.
	 * 
	 * @return	\epiphyt\Form_Block\Theme_Styles The single instance of this class
	 */
	public static function get_instance(): Theme_Styles {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * Check if the current theme is matching your name.
	 * 
	 * @param	string	$name The theme name to test
	 * @return	bool True if the current theme is matching, false otherwise
	 */
	public function is_theme( $name ) {
		$name = strtolower( $name );
		
		if ( strtolower( wp_get_theme()->get( 'Name' ) ) === $name || strtolower( wp_get_theme()->get( 'Template' ) ) === $name ) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Register block style.
	 */
	public function register_block_styles( string $style ): string {
		if ( $this->is_theme( 'Twenty Twenty-Three' ) ) {
			$style = 'form-block-twenty-twenty-three';
		}
		
		return $style;
	}
	
	/**
	 * Register frontend styles.
	 */
	public function register_styles(): void {
		$is_debug = defined( 'WP_DEBUG' ) && WP_DEBUG;
		$suffix = ( $is_debug ? '' : '.min' );
		
		if ( $this->is_theme( 'Twenty Twenty-Three' ) ) {
			$file_path = plugin_dir_path( EPI_FORM_BLOCK_FILE ) . 'assets/style/build/twenty-twenty-three' . $suffix . '.css';
			$file_url = plugin_dir_url( EPI_FORM_BLOCK_FILE ) . 'assets/style/build/twenty-twenty-three' . $suffix . '.css';
			
			wp_register_style( 'form-block-twenty-twenty-three', $file_url, [ 'form-block' ], $is_debug ? filemtime( $file_path ) : FORM_BLOCK_VERSION );
		}
	}
}

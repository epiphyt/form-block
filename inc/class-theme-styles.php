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
	public static ?self $instance = null;
	
	/**
	 * Initialize the class.
	 */
	public function init(): void {
		\add_action( 'init', [ $this, 'register_styles' ], 20 );
		
		\add_filter( 'form_block_form_style', [ $this, 'register_block_styles' ] );
	}
	
	/**
	 * Get a unique instance of the class.
	 * 
	 * @return	\epiphyt\Form_Block\Theme_Styles The single instance of this class
	 */
	public static function get_instance(): self {
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
	public function is_theme( string $name ): bool {
		$name = \strtolower( $name );
		
		return \strtolower( \wp_get_theme()->get( 'Name' ) ) === $name
			|| \strtolower( \wp_get_theme()->get( 'Template' ) ) === $name;
	}
	
	/**
	 * Register block styles.
	 * 
	 * @param	array	$styles Registered block styles
	 * @return	array Updated block styles
	 */
	public function register_block_styles( array $styles ): array {
		if ( $this->is_theme( 'Twenty Twenty-Four' ) ) {
			$styles[] = 'form-block-twenty-twenty-four';
		}
		else if ( $this->is_theme( 'Twenty Twenty-Three' ) ) {
			$styles[] = 'form-block-twenty-twenty-three';
		}
		else if ( $this->is_theme( 'Twenty Twenty-Two' ) ) {
			$styles[] = 'form-block-twenty-twenty-two';
		}
		
		return $styles;
	}
	
	/**
	 * Register frontend styles.
	 */
	public function register_styles(): void {
		$is_debug = \defined( 'WP_DEBUG' ) && \WP_DEBUG;
		$suffix = ( $is_debug ? '' : '.min' );
		
		if ( $this->is_theme( 'Twenty Twenty-Four' ) ) {
			$file_path = \plugin_dir_path( \EPI_FORM_BLOCK_FILE ) . 'assets/style/build/twenty-twenty-four' . $suffix . '.css';
			$file_url = \plugin_dir_url( \EPI_FORM_BLOCK_FILE ) . 'assets/style/build/twenty-twenty-four' . $suffix . '.css';
			
			\wp_register_style( 'form-block-twenty-twenty-four', $file_url, [ 'form-block' ], $is_debug ? \filemtime( $file_path ) : \FORM_BLOCK_VERSION );
		}
		else if ( $this->is_theme( 'Twenty Twenty-Three' ) ) {
			$file_path = \plugin_dir_path( \EPI_FORM_BLOCK_FILE ) . 'assets/style/build/twenty-twenty-three' . $suffix . '.css';
			$file_url = \plugin_dir_url( \EPI_FORM_BLOCK_FILE ) . 'assets/style/build/twenty-twenty-three' . $suffix . '.css';
			
			\wp_register_style( 'form-block-twenty-twenty-three', $file_url, [ 'form-block' ], $is_debug ? \filemtime( $file_path ) : \FORM_BLOCK_VERSION );
		}
		else if ( $this->is_theme( 'Twenty Twenty-Two' ) ) {
			$file_path = \plugin_dir_path( \EPI_FORM_BLOCK_FILE ) . 'assets/style/build/twenty-twenty-two' . $suffix . '.css';
			$file_url = \plugin_dir_url( \EPI_FORM_BLOCK_FILE ) . 'assets/style/build/twenty-twenty-two' . $suffix . '.css';
			
			\wp_register_style( 'form-block-twenty-twenty-two', $file_url, [ 'form-block' ], $is_debug ? \filemtime( $file_path ) : \FORM_BLOCK_VERSION );
		}
	}
}

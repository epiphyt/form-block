<?php
namespace epiphyt\Form_Block\blocks;

/**
 * Fieldset block class.
 * 
 * @since	1.5.0
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Fieldset {
	/**
	 * @var		\epiphyt\Form_Block\blocks\Fieldset
	 */
	public static ?self $instance;
	
	/**
	 * Initialize the class.
	 */
	public static function init(): void {
		\add_action( 'init', [ self::class, 'register_block' ] );
	}
	
	/**
	 * Get a unique instance of the class.
	 * 
	 * @return	\epiphyt\Form_Block\blocks\Fieldset The single instance of this class
	 */
	public static function get_instance(): self {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * Register block.
	 */
	public static function register_block(): void {
		\register_block_type( \EPI_FORM_BLOCK_BASE . '/build/fieldset' );
	}
}

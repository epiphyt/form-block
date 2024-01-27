<?php
namespace epiphyt\Form_Block\blocks;

use epiphyt\Form_Block\Form_Block;

/**
 * Textarea block class.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Textarea {
	/**
	 * @var		\epiphyt\Form_Block\blocks\Textarea
	 */
	public static $instance;
	
	/**
	 * Initialize the class.
	 */
	public function init(): void {
		\add_action( 'init', [ $this, 'register_block' ] );
		\add_filter( 'render_block_form-block/textarea', [ Form_Block::get_instance(), 'add_attributes' ], 10, 2 );
	}
	
	/**
	 * Get a unique instance of the class.
	 * 
	 * @return	\epiphyt\Form_Block\blocks\Textarea The single instance of this class
	 */
	public static function get_instance(): Textarea {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * Register block.
	 * 
	 * @since	1.3.0
	 */
	public static function register_block(): void {
		register_block_type( \EPI_FORM_BLOCK_BASE . '/build/textarea' );
	}
}

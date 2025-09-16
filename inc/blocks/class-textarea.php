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
	public static ?self $instance = null;
	
	/**
	 * Initialize the class.
	 */
	public function init(): void {
		\add_filter( 'render_block_form-block/textarea', [ Form_Block::get_instance(), 'add_attributes' ], 10, 2 );
	}
	
	/**
	 * Get a unique instance of the class.
	 * 
	 * @return	\epiphyt\Form_Block\blocks\Textarea The single instance of this class
	 */
	public static function get_instance(): self {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * Register block.
	 * 
	 * @deprecated	1.6.0 Use epiphyt\Form_Block\blocks\Block_Registry::register() instead
	 * @since		1.3.0
	 */
	public static function register_block(): void {
		\_doing_it_wrong(
			__METHOD__,
			\sprintf(
				/* translators: alternative method */
				\esc_html__( 'Use %s instead', 'form-block' ),
				'epiphyt\Form_Block\blocks\Block_Registry::register()'
			),
			'1.6.0'
		);
	}
}

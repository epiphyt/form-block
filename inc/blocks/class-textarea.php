<?php
namespace epiphyt\Form_Block\blocks;

use epiphyt\Form_Block\Form_Block;

/**
 * Textarea block class.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Textarea
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
		add_action( 'render_block_form-block/textarea', [ Form_Block::get_instance(), 'add_attributes' ], 10, 2 );
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
}

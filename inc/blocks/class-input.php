<?php
namespace epiphyt\Form_Block\blocks;

use epiphyt\Form_Block\Form_Block;

/**
 * Input block class.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Input {
	/**
	 * @var		\epiphyt\Form_Block\blocks\Input
	 */
	public static $instance;
	
	/**
	 * Initialize the class.
	 */
	public function init(): void {
		add_action( 'render_block_form-block/input', [ Form_Block::get_instance(), 'add_attributes' ], 10, 2 );
	}
	
	/**
	 * Get a unique instance of the class.
	 * 
	 * @return	\epiphyt\Form_Block\blocks\Input The single instance of this class
	 */
	public static function get_instance(): Input {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
}

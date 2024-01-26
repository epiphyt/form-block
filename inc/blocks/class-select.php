<?php
namespace epiphyt\Form_Block\blocks;

use epiphyt\Form_Block\Form_Block;

/**
 * Select block class.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Select {
	/**
	 * @var		\epiphyt\Form_Block\blocks\Select
	 */
	public static $instance;
	
	/**
	 * Initialize the class.
	 */
	public function init(): void {
		\add_filter( 'render_block_form-block/select', [ Form_Block::get_instance(), 'add_attributes' ], 10, 2 );
	}
	
	/**
	 * Get a unique instance of the class.
	 * 
	 * @return	\epiphyt\Form_Block\blocks\Select The single instance of this class
	 */
	public static function get_instance(): Select {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
}

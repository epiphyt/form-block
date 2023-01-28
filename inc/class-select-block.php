<?php
/** @noinspection PhpMissingFieldTypeInspection */
namespace epiphyt\Form_Block;

/**
 * Select block class.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Select_Block
 */
final class Select_Block {
	/**
	 * @var		\epiphyt\Form_Block\Select_Block
	 */
	public static $instance;
	
	/**
	 * Initialize the class.
	 */
	public function init(): void {
		add_action( 'render_block_form-block/select', [ Form_Block::get_instance(), 'add_attributes' ], 10, 2 );
	}
	
	/**
	 * Get a unique instance of the class.
	 * 
	 * @return	\epiphyt\Form_Block\Select_Block The single instance of this class
	 */
	public static function get_instance(): Select_Block {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
}

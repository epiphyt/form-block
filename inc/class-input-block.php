<?php
/** @noinspection PhpMissingFieldTypeInspection */
namespace epiphyt\Form_Block;

/**
 * Input block class.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Input_Block
 */
final class Input_Block {
	/**
	 * @var		\epiphyt\Form_Block\Input_Block
	 */
	public static $instance;
	
	/**
	 * Initialize the class.
	 */
	public function init() {
		
	}
	
	/**
	 * Get a unique instance of the class.
	 * 
	 * @return	\epiphyt\Form_Block\Input_Block The single instance of this class
	 */
	public static function get_instance(): Input_Block {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
}

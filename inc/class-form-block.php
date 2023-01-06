<?php
/** @noinspection PhpMissingFieldTypeInspection */
namespace epiphyt\Form_Block;

/**
 * Form Block main class.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Form_Block {
	/**
	 * @var		\epiphyt\Form_Block\Form_Block
	 */
	public static $instance;
	
	/**
	 * Initialize the class.
	 */
	public function init() {
		Admin::get_instance()->init();
	}
	
	/**
	 * Get a unique instance of the class.
	 * 
	 * @return	\epiphyt\Form_Block\Form_Block The single instance of this class
	 */
	public static function get_instance(): Form_Block {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
}

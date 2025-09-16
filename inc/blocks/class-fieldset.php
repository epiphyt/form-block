<?php
namespace epiphyt\Form_Block\blocks;

/**
 * Fieldset block class.
 * 
 * @since		1.5.0
 * @deprecated	1.6.0
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Fieldset {
	/**
	 * @var		\epiphyt\Form_Block\blocks\Fieldset
	 */
	public static ?self $instance = null;
	
	/**
	 * Initialize the class.
	 * 
	 * @deprecated	1.6.0
	 */
	public static function init(): void {
		// deprecated
	}
	
	/**
	 * Get a unique instance of the class.
	 * 
	 * @deprecated	1.6.0
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
	 * 
	 * @deprecated	1.6.0 Use epiphyt\Form_Block\blocks\Block_Registry::register() instead
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

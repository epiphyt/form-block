<?php
namespace epiphyt\Form_Block\blocks;

/**
 * Form block class.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form
 */
final class Form {
	/**
	 * @var		\epiphyt\Form_Block\blocks\Form
	 */
	public static $instance;
	
	/**
	 * Initialize the class.
	 */
	public function init(): void {
		add_action( 'render_block_form-block/form', [ $this, 'add_honeypot' ], 10, 2 );
	}
	
	/**
	 * Add the honeypot code.
	 *
	 * @param	string	$block_content The block content
	 * @param	array	$block Block attributes
	 * @return	string Updated block content
	 */
	public function add_honeypot( string $block_content, array $block ): string {
		$honeypot = '<div class="wp-block-form-block-input form-block__element"><input name="_town" id="id-_town" type="text"aria-hidden="true" autocomplete="new-password" style="padding: 0; clip: rect(1px, 1px, 1px, 1px); position: absolute !important; white-space: nowrap; height: 1px; width: 1px; overflow: hidden;" tabindex="-1"/></div>';
		
		/**
		 * Filter the honeypot code.
		 * 
		 * @param	string	$honeypot The honeypot code
		 * @param	string	$block_content The block content
		 * @param	array	$block Block attributes
		 */
		$honeypot = apply_filters( 'form_block_honeypot_code', $honeypot , $block_content, $block );
		
		return str_replace( '</form>', $honeypot . '</form>', $block_content );
	}
	
	/**
	 * Get a unique instance of the class.
	 * 
	 * @return	\epiphyt\Form_Block\blocks\Form The single instance of this class
	 */
	public static function get_instance(): Form {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
}

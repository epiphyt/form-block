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
		add_filter( 'wp_kses_allowed_html', [ $this, 'set_allow_tags' ], 10, 2 );
		
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
	
	/**
	 * Add some used HTML elements to the allowed tags.
	 * 
	 * @param	array[]|string	$tags The allowed HTML tags
	 * @param	string			$context The context
	 * @return	array[]|string The updated allowed tags
	 */
	public function set_allow_tags( $tags, string $context ) {
		if ( $context !== 'post' ) {
			return $tags;
		}
		
		$tags['input'] = [
			'autocomplete' => true,
			'class' => true,
			'data-*' => true,
			'disabled' => true,
			'id' => true,
			'max' => true,
			'maxlength' => true,
			'min' => true,
			'minlength' => true,
			'multiple' => true,
			'name' => true,
			'pattern' => true,
			'placeholder' => true,
			'readonly' => true,
			'required' => true,
			'size' => true,
			'spellcheck' => true,
			'step' => true,
			'type' => true,
			'value' => true,
		];
		$tags['label'] = [
			'class' => true,
			'data-*' => true,
			'for' => true,
		];
		$tags['option'] = [
			'class' => true,
			'data-*' => true,
			'disabled' => true,
			'name' => true,
			'selected' => true,
			'value' => true,
		];
		$tags['select'] = [
			'autocomplete' => true,
			'class' => true,
			'data-*' => true,
			'disabled' => true,
			'id' => true,
			'multiple' => true,
			'name' => true,
			'readonly' => true,
			'required' => true,
			'size' => true,
		];
		$tags['span'] = [
			'class' => true,
			'data-*' => true,
			'id' => true,
		];
		$tags['textarea'] = [
			'autocomplete' => true,
			'class' => true,
			'cols' => true,
			'data-*' => true,
			'disabled' => true,
			'id' => true,
			'label' => true,
			'maxlength' => true,
			'minlength' => true,
			'name' => true,
			'placeholder' => true,
			'readonly' => true,
			'required' => true,
			'rows' => true,
			'spellcheck' => true,
			'type' => true,
			'value' => true,
		];
		
		return $tags;
	}
}

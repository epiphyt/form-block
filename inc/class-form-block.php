<?php
namespace epiphyt\Form_Block;

use DOMDocument;
use epiphyt\Form_Block\block_data\Data as BlockDataData;
use epiphyt\Form_Block\blocks\Form;
use epiphyt\Form_Block\blocks\Input;
use epiphyt\Form_Block\blocks\Select;
use epiphyt\Form_Block\blocks\Textarea;
use epiphyt\Form_Block\form_data\Data as FormDataData;

/**
 * Form Block main class.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Form_Block {
	/**
	 * @var		array List of block name attributes
	 */
	private $block_name_attributes = [
		'_town',
	];
	
	/**
	 * @var		\epiphyt\Form_Block\Form_Block
	 */
	public static $instance;
	
	/**
	 * Initialize the class.
	 */
	public function init(): void {
		add_filter( 'wp_kses_allowed_html', [ $this, 'set_allow_tags' ], 10, 2 );
		
		Admin::get_instance()->init();
		BlockDataData::get_instance()->init();
		Form::get_instance()->init();
		FormDataData::get_instance()->init();
		Input::get_instance()->init();
		Select::get_instance()->init();
		Textarea::get_instance()->init();
	}
	
	/**
	 * Add attributes for name, id and label for.
	 *
	 * @param	string	$block_content The block content
	 * @param	array	$block Block attributes
	 * @return	string Updated block content
	 */
	public function add_attributes( string $block_content, array $block ): string {
		$dom = new DOMDocument();
		$element_type = str_replace( 'form-block/', '', $block['blockName'] );
		$label = '';
		$multiple_regex = '/<input([^>]+)\s+multiple\s*/';
		$name_regex = '/name="(?<attribute>[^"]*)"/';
		
		$dom->loadHTML(
			mb_convert_encoding(
				'<html>' . $block_content . '</html>',
				'HTML-ENTITIES',
				'UTF-8'
			),
			LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
		);
		
		// get label content
		foreach ( $dom->getElementsByTagName( 'span' ) as $span ) {
			if ( $span->getAttribute( 'class' ) !== 'form-block__label-content' ) {
				continue;
			}
			
			$label = $span->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		}
		
		// get name attribute
		preg_match( $name_regex, $block_content, $matches );
		// get multiple attribute
		preg_match( $multiple_regex, $block_content, $multiple_matches );
		
		$block['attrs']['label'] = $label;
		$block['attrs']['name'] = $matches['attribute'] ?? '';
		$name = $this->get_block_name_attribute( $block );
		$attribute_replacement = 'name="' . esc_attr( $name ) . ( ! empty( $multiple_matches ) ? '[]' : '' ) . '" id="id-' . esc_attr( $name ) . '"';
		
		if ( preg_match( $name_regex, $block_content ) ) {
			$block_content = preg_replace( $name_regex, $attribute_replacement, $block_content );
		}
		else {
			$block_content = str_replace( '<' . $element_type, '<' . $element_type . ' ' . $attribute_replacement, $block_content );
		}
		
		$block_content = str_replace( '<label', '<label for="id-' . esc_attr( $name ) . '"', $block_content );
		
		$dom->loadHTML(
			mb_convert_encoding(
				'<html>' . $block_content . '</html>',
				'HTML-ENTITIES',
				'UTF-8'
			),
			LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
		);
		
		$element = $dom->getElementById( 'id-' . esc_attr( $name ) );
		
		if (
			! $element->hasAttribute( 'required' )
			&& ! $element->hasAttribute( 'disabled' )
			&& ! $element->hasAttribute( 'readonly' )
			&& $element->getAttribute( 'type' ) !== 'checkbox'
			&& $element->getAttribute( 'type' ) !== 'radio'
		) {
			$element->setAttribute( 'class', trim( $element->getAttribute( 'class' ) . ' optional' ) );
		}
		
		if ( $element->hasAttribute( 'capture' ) && empty( $element->getAttribute( 'capture' ) ) ) {
			$element->removeAttribute( 'capture' );
		}
		
		// make sure staring and ending slashes are available
		if ( $element->hasAttribute( 'pattern' ) ) {
			$element->setAttribute( 'pattern', '/' . trim( $element->getAttribute( 'pattern' ) ) . '/' );
		}
		
		if ( $element->hasAttribute( 'min' ) && empty( $element->getAttribute( 'min' ) ) ) {
			$element->removeAttribute( 'min' );
		}
		
		if ( $element->hasAttribute( 'nax' ) && empty( $element->getAttribute( 'nax' ) ) ) {
			$element->removeAttribute( 'nax' );
		}
		
		if ( $element->hasAttribute( 'minlength' ) && empty( $element->getAttribute( 'minlength' ) ) ) {
			$element->removeAttribute( 'minlength' );
		}
		
		if ( $element->hasAttribute( 'maxlength' ) && empty( $element->getAttribute( 'maxlength' ) ) ) {
			$element->removeAttribute( 'maxlength' );
		}
		
		if ( $element->hasAttribute( 'min' ) || $element->hasAttribute( 'max' ) ) {
			$value = '';
			
			if ( $element->hasAttribute( 'min' ) && ! empty( $element->getAttribute( 'min' ) ) ) {
				$value .= $element->getAttribute( 'min' );
			}
			
			if ( $element->hasAttribute( 'max' ) && ! empty( $element->getAttribute( 'max' ) ) ) {
				$value .= ',' . $element->getAttribute( 'max' );
			}
			
			if ( $value ) {
				$element->setAttribute( 'data-validate-minmax', $value );
			}
		}
		
		if ( $element->hasAttribute( 'minlength' ) || $element->hasAttribute( 'maxlength' ) ) {
			$value = '';
			
			if ( $element->hasAttribute( 'minlength' ) && ! empty( $element->getAttribute( 'minlength' ) ) ) {
				$value .= $element->getAttribute( 'minlength' );
			}
			
			if ( $element->hasAttribute( 'maxlength' ) && ! empty( $element->getAttribute( 'maxlength' ) ) ) {
				$value .= ',' . $element->getAttribute( 'maxlength' );
			}
			
			if ( $value ) {
				$element->setAttribute( 'data-validate-length-range', $value );
			}
		}
		
		return str_replace( [ '<html>', '</html>' ], '', $dom->saveHTML( $dom->documentElement ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	}
	
	/**
	 * Get a valid name attribute of a form element.
	 *
	 * @param	array	$block Block attributes
	 * @return	string A valid name attribute
	 */
	public function get_block_name_attribute( array $block ): string {
		// if we only have field data, there is no 'attrs' key
		if ( ! isset( $block['attrs'] ) ) {
			$block = [
				'attrs' => $block,
			];
		}
		
		if ( ! empty( $block['attrs']['name'] ) ) {
			return $this->get_unique_block_name_attribute( $block['attrs']['name'] );
		}
		
		if ( ! empty( $block['attrs']['label'] ) ) {
			return $this->get_unique_block_name_attribute( $this->get_name_by_label( $block['attrs']['label'] ) );
		}
		
		return $this->get_unique_block_name_attribute( 'unknown' );
	}
	
	/**
	 * Get the current request URL.
	 *
	 * @return	string The current request URL
	 */
	public function get_current_request_url(): string {
		$request_uri = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) );
		$current_path = $request_uri ?: '/';
		$home_path = home_url( '', 'relative' );
		$home_path_position = strpos( $current_path, $home_path );
		
		if ( $home_path_position !== false ) {
			$current_path = substr_replace( $current_path, '', $home_path_position, strlen( $home_path ) );
		}
		
		return home_url( $current_path );
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
	 * Get a valid name by its label.
	 *
	 * @param	string	$label The original label
	 * @param	bool	$to_lowercase Whether the name should be lowercase
	 * @return	string The valid name
	 */
	private function get_name_by_label( string $label, bool $to_lowercase = true ): string {
		if ( $to_lowercase ) {
			$label = mb_strtolower( $label );
		}
		
		/**
		 * Filter the label before generating a name out of it.
		 * 
		 * @param	string	$label The original label
		 * @param	bool	$to_lowercase Whether the name should be lowercase
		 * @return	string The updated label
		 */
		$label = apply_filters( 'form_block_pre_get_name_by_label', $label, $to_lowercase );
		
		$regex = '/[^A-Za-z0-9\-_\[\]]/';
		$replace = [ 'ae', 'oe', 'ue', 'ss', '-' ];
		$search = [ 'ä', 'ö', 'ü', 'ß', ' ' ];
		$name = preg_replace( $regex, '', str_replace( $search, $replace, $label ) );
		
		/**
		 * Filter the generated name from a label.
		 * 
		 * @param	string	$name The generated name
		 * @param	string	$label The original label
		 * @param	bool	$to_lowercase Whether the name should be lowercase
		 * @return	string The updated name
		 */
		$name = apply_filters( 'form_block_get_name_by_label', $name, $label, $to_lowercase );
		
		return $name;
	}
	
	/**
	 * Get a unique name attribute.
	 * Similar to wp_unique_post(), which has been the inspiration for this. 
	 *
	 * @param	string	$block_name The block name
	 * @return	string A unique name attribute
	 */
	private function get_unique_block_name_attribute( string $block_name ): string {
		$block_name_check = in_array( $block_name, $this->block_name_attributes, true );
		
		if ( ! $block_name_check ) {
			$this->block_name_attributes[] = $block_name;
			
			return $block_name;
		}
		
		$suffix = 2;
		
		do {
			$new_block_name = $block_name . '-' . $suffix;
			$block_name_check = in_array( $new_block_name, $this->block_name_attributes, true );
			$suffix++;
		} while ( $block_name_check );
		
		$this->block_name_attributes[] = $new_block_name;
		
		return $new_block_name;
	}
	
	/**
	 * Reset the block name attributes.
	 */
	public function reset_block_name_attributes(): void {
		$this->block_name_attributes = [
			'_town',
		];
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

		$tags['form'] = [
			'action' => true,
			'accept' => true,
			'accept-charset' => true,
			'class' => true,
			'data-*' => true,
			'enctype' => true,
			'id' => true,
			'method' => true,
			'name' => true,
			'target' => true,
		];
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

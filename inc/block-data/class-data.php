<?php
namespace epiphyt\Form_Block\block_data;

use DOMDocument;
use WP_Post;

/**
 * Block data class.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Data {
	/**
	 * @var		\epiphyt\Form_Block\block_data\Data
	 */
	public static $instance;
	
	/**
	 * Initialize the class.
	 */
	public function init(): void {
		add_action( 'save_post', [ $this, 'set' ], 20, 2 );
	}
	
	/**
	 * Get form data from blocks.
	 *
	 * @param	array	$blocks Blocks from parsed_blocks()
	 * @param	array	$data Current form data
	 * @param	string	$form_id The form ID
	 * @return	array Form data
	 */
	public function get( array $blocks, array $data = [], string $form_id = '' ): array {
		foreach ( $blocks as $block ) {
			// reusable blocks need to be get first
			if ( $block['blockName'] === 'core/block' && ! empty( $block['attrs']['ref'] ) ) {
				$reusable_post = get_post( $block['attrs']['ref'] );
				
				if ( ! $reusable_post instanceof WP_Post ) {
					return $data;
				}
				
				$reusable_blocks = parse_blocks( $reusable_post->post_content );
				$data = $this->get( $reusable_blocks, $data );
			}
			else if ( strpos( $block['blockName'], 'form-block/' ) !== false ) {
				// ignore fields without a form block
				if ( empty( $form_id ) && $block['blockName'] !== 'form-block/form' ) {
					return $data;
				}
				
				$field_data = [];
				
				switch ( $block['blockName'] ) {
					case 'form-block/form':
						if ( empty( $block['attrs']['formId'] ) ) {
							break;
						}
						
						$form_id = $block['attrs']['formId'];
						$data[ $form_id ] = [
							'block_type' => 'form',
							'fields' => [],
						];
						
						/**
						 * Filter form block data.
						 * 
						 * @param	array	$form_data The current block data that is being stored
						 * @param	array	$block The original block data
						 * @param	string	$form_id The form ID
						 */
						$data[ $form_id ] = apply_filters( 'form_block_data_form', $data[ $form_id ], $block, $form_id );
						break;
					case 'form-block/input':
						$field_data = $this->get_attributes( $block['innerHTML'], 'input' );
						break;
					case 'form-block/select':
						$field_data = $this->get_attributes( $block['innerHTML'], 'select' );
						break;
					case 'form-block/textarea':
						$field_data = $this->get_attributes( $block['innerHTML'], 'textarea' );
						break;
				}
				
				/**
				 * Filter the field data.
				 * 
				 * @param	array	$field_data The field data
				 * @param	array	$blocks Blocks from parsed_blocks()
				 * @param	array	$data Current form data
				 * @param	string	$form_id The form ID
				 */
				$field_data = apply_filters( 'form_block_get_form_data', $field_data, $block, $data, $form_id );
				
				if ( ! empty( $field_data ) ) {
					$data[ $form_id ]['fields'][] = $field_data;
					
					unset( $field_data );
				}
				
				/**
				 * Filter the form data.
				 * 
				 * @param	array	$data Current form data
				 * @param	array	$blocks Blocks from parsed_blocks()
				 * @param	string	$form_id The form ID
				 */
				$data = apply_filters( 'form_block_get_data', $data, $block, $form_id );
			}
			
			if ( ! empty( $block['innerBlocks'] ) ) {
				$data = $this->get( $block['innerBlocks'], $data, $form_id );
			}
		}
		
		return $data;
	}
	
	/**
	 * Get all attributes from a HTML element.
	 *
	 * @param	string	$element The HTML element
	 * @param	string	$tag_name The tag name
	 * @return	array List of attributes
	 */
	public function get_attributes( string $element, string $tag_name ): array {
		$dom = new DOMDocument();
		$dom->loadHTML(
			mb_convert_encoding(
				'<html>' . $element . '</html>',
				'HTML-ENTITIES',
				'UTF-8'
			),
			LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
		);
		
		$attributes = $this->get_element_attributes( $dom, $tag_name );
		
		// get label data
		$label_attributes = $this->get_element_attributes(
			$dom,
			'span',
			[
				'class_name' => 'form-block__label-content',
				'get_text_content' => true,
			],
		);
		$attributes['label'] = $label_attributes['textContent'] ?? '';
		
		// get options
		if ( $tag_name === 'select' ) {
			$option_attributes = $this->get_element_attributes(
				$dom,
				'option',
				[
					'get_text_content' => true,
				],
			);
			
			// check for associative keys
			if ( count( array_filter( array_keys( $option_attributes ), 'is_string' ) ) > 0 ) {
				$option_attributes['value'] = $option_attributes['textContent'];
				unset( $option_attributes['textContent'] );
				$option_attributes = [ $option_attributes ];
			}
			else {
				$option_attributes = array_map( function( $option ) {
					$option['value'] = $option['textContent'];
					unset( $option['textContent'] );
					
					return $option;
				}, $option_attributes );
			}
			
			$attributes['options'] = $option_attributes;
		}
		
		return $attributes;
	}
	
	/**
	 * Get all attributes of an element.
	 *
	 * @param	DOMDocument	$dom The DOMDocument instance
	 * @param	string		$tag_name The tag name
	 * @param	array		$arguments {
	 * 	Additional arguments
	 * 	
	 * 	@type	string	$class_name Class name to check the element for
	 * 	@type	bool	$get_text_content Whether to get the text content as well
	 * }
	 * @return	array List of element's attributes
	 */
	private function get_element_attributes( DOMDocument $dom, string $tag_name, array $arguments = [] ): array {
		$attributes = [];
		$arguments = wp_parse_args( $arguments, [
			'class_name' => '',
			'get_text_content' => false,
		] );
		$iteration = 0;
		
		foreach ( $dom->getElementsByTagName( $tag_name ) as $tag ) {
			if ( ! $tag->hasAttributes() ) {
				continue;
			}
			
			if ( ! empty( $arguments['class_name'] ) && strpos( $tag->getAttribute( 'class' ), $arguments['class_name'] ) === false ) {
				continue;
			}
			
			foreach ( $tag->attributes as $attribute ) {
				$attributes[ $iteration ][ $attribute->nodeName ] = $attribute->nodeValue;
			}
			
			if ( $arguments['get_text_content'] ) {
				$attributes[ $iteration ]['textContent'] = $tag->textContent;
			}
			
			$iteration++;
		}
		
		if ( count( $attributes ) === 1 ) {
			$attributes = reset( $attributes );
		}
		
		return $attributes;
	}
	
	/**
	 * Get a unique instance of the class.
	 * 
	 * @return	\epiphyt\Form_Block\block_data\Data The single instance of this class
	 */
	public static function get_instance(): Data {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * Maybe delete old form data.
	 * 
	 * @param	int		$post_id Current post ID
	 * @param	array	$data Form data
	 */
	private function maybe_delete( int $post_id, array $data ): void {
		$data_ids = array_keys( $data );
		$form_ids = get_option( 'form_block_form_ids', [] );
		
		foreach ( $form_ids as $form_id => &$post_ids ) {
			if ( ! in_array( $post_id, $post_ids, true ) ) {
				continue;
			}
			
			$keep = false;
			
			foreach ( $data_ids as $id ) {
				if ( $form_id !== $id ) {
					continue;
				}
				
				$keep = true;
			}
			
			if ( ! $keep ) {
				// delete post ID from array
				$key = array_search( $post_id, $post_ids, true );
				
				if ( $key !== false ) {
					unset( $post_ids[ $key ] );
				}
				
				// completely delete only if it's not used anywhere else
				if ( empty( $post_ids ) ) {
					delete_option( 'form_block_data_' . $form_id );
					unset( $form_ids[ $form_id ] );
				}
			}
		}
		
		update_option( 'form_block_form_ids', $form_ids );
	}
	
	/**
	 * Set form data.
	 * 
	 * @param	int			$post_id Current post ID
	 * @param	\WP_Post	$post Current post object
	 * @return	\WP_Post Current post object
	 */
	public function set( int $post_id, WP_Post $post ): WP_Post {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post;
		}
		
		if ( $post->post_type === 'revision' ) {
			return $post;
		}
		
		if ( ! Util::has_block('form-block/form', $post->ID ) ) {
			return $post;
		}
		
		$data = $this->get( parse_blocks( $post->post_content ) );
		$form_ids = get_option( 'form_block_form_ids', [] );
		$new_form_ids = $form_ids;
		
		foreach ( $data as $form_id => $form ) {
			if ( empty( $new_form_ids[ $form_id ] ) ) {
				$new_form_ids[ $form_id ] = [ $post_id ];
			}
			else if ( ! in_array( $post_id, $new_form_ids[ $form_id ], true ) ) {
				$new_form_ids[ $form_id ][] = $post_id;
			}
			
			// store form data
			update_option( 'form_block_data_' . $form_id, $form );
		}
		
		if ( $form_ids !== $new_form_ids ) {
			update_option( 'form_block_form_ids', $new_form_ids );
		}
		
		if ( $post->post_type !== 'wp_block' ) {
			$this->maybe_delete( $post_id, $data );
		}
		
		return $post;
	}
}

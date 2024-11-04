<?php
namespace epiphyt\Form_Block\block_data;

use DOMDocument;
use WP_Post;
use WP_Widget;

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
		add_action( 'update_option_widget_block', [ $this, 'set_for_widget' ], 20, 2 );
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
			// don't process empty blocks
			if ( $block['blockName'] === null ) {
				continue;
			}
			
			// reusable blocks need to be get first
			if ( $block['blockName'] === 'core/block' && ! empty( $block['attrs']['ref'] ) ) {
				$reusable_post = get_post( $block['attrs']['ref'] );
				
				if ( ! $reusable_post instanceof WP_Post ) {
					return $data;
				}
				
				$reusable_blocks = parse_blocks( $reusable_post->post_content );
				$data = $this->get( $reusable_blocks, $data );
			}
			else if ( ! str_starts_with( $block['blockName'], 'form-block/' ) || ! str_starts_with( $block['blockName'], 'form-block-pro/' ) ) {
				// ignore fields without a form block
				if ( empty( $form_id ) && $block['blockName'] !== 'form-block/form' ) {
					if ( ! empty( $block['innerBlocks'] ) ) {
						$data = array_merge( $data, $this->get( $block['innerBlocks'], $data, $form_id ) );
					}
					
					continue;
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
							'subject' => $block['attrs']['subject'] ?? '',
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
						$field_data['block_type'] = 'input';
						
						if ( isset( $block['attrs']['isReplyTo'] ) ) {
							$field_data['is_reply_to'] = $block['attrs']['isReplyTo'];
						}
						break;
					case 'form-block/select':
						$field_data = $this->get_attributes( $block['innerHTML'], 'select' );
						$field_data['block_type'] = 'select';
						break;
					case 'form-block/textarea':
						$field_data = $this->get_attributes( $block['innerHTML'], 'textarea' );
						$field_data['block_type'] = 'textarea';
						break;
				}
				
				$field_data = \array_merge( $block['attrs'], $field_data );
				
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
			}
				
			if ( ! empty( $block['innerBlocks'] ) ) {
				$data = array_merge( $data, $this->get( $block['innerBlocks'], $data, $form_id ) );
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
			'<html><meta charset="UTF-8">' . $element . '</html>',
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
		
		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		/** @var	\DOMElement $tag */
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
				// textContent removes all line breaks, so get the actual HTML
				// of the element and store them without tags, but with line breaks
				foreach ( $tag->childNodes as $child ) {
					$attributes[ $iteration ]['textContent'] .= $tag->ownerDocument->saveHTML( $child );
				}
				
				$attributes[ $iteration ]['textContent'] = \str_replace( [ '<br>', '<br />' ], \PHP_EOL, $attributes[ $iteration ]['textContent'] );
				$attributes[ $iteration ]['textContent'] = \wp_strip_all_tags( $attributes[ $iteration ]['textContent'] );
			}
			
			$iteration++;
		}
		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		
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
	 * @param	int]string	$id Current object ID
	 * @param	array		$data Form data
	 * @param	string		$type The object type
	 */
	private function maybe_delete( $id, array $data, string $type = 'post' ): void {
		$data_ids = array_keys( $data );
		$form_ids = get_option( 'form_block_form_ids', [] );
		$original_id = $id;
		$widgets = [];
		$widgets_unset = [];
		
		if ( $type !== 'post' ) {
			$id = $type . '-' . $id;
		}
		
		if ( $type === 'widget' ) {
			$widgets = get_option( 'widget_block', [] );
			$widget_content = $widgets[ $original_id ]['content'];
			
			if ( empty( $widget_content ) || ! Util::has_block_in_content( 'form-block/form', $widget_content ) ) {
				$widgets_unset[] = $id;
			}
		}
		
		foreach ( $form_ids as $form_id => &$ids ) {
			if ( ! in_array( $id, $ids, true ) ) {
				continue;
			}
			
			$keep = false;
			
			foreach ( $data_ids as $id ) {
				if ( $form_id !== $id ) {
					continue;
				}
				
				$keep = true;
			}
			
			if ( ! $keep || in_array( $id, $widgets_unset, true ) ) {
				// delete object ID from array
				$key = array_search( $id, $ids, true );
				
				if ( $key !== false ) {
					unset( $ids[ $key ] );
				}
				
				// completely delete only if it's not used anywhere else
				if ( empty( $ids ) ) {
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
		
		if ( ! Util::has_block( 'form-block/form', $post->ID ) ) {
			return $post;
		}
		
		$data = $this->get( parse_blocks( $post->post_content ) );
		
		$this->set_form_block_data( $data, $post_id );
		
		if ( $post->post_type !== 'wp_block' ) {
			$this->maybe_delete( $post_id, $data );
		}
		
		return $post;
	}
	
	/**
	 * Set form data for widgets.
	 * 
	 * @param	mixed	$old_value The old option value
	 * @param	mixed	$new_value The new option value
	 * @return	mixed The new option value
	 */
	public function set_for_widget( $old_value, $new_value ) {
		if ( ! is_array( $new_value ) ) {
			return $new_value;
		}
		
		foreach ( $new_value as $widget_id => $widget_data ) {
			if ( empty( $widget_data['content'] ) ) {
				continue;
			}
			
			$data = $this->get( parse_blocks( $widget_data['content'] ) );
			
			$this->set_form_block_data( $data, $widget_id, 'widget' );
			$this->maybe_delete( $widget_id, $data, 'widget' );
		}
		
		return $new_value;
	}
	
	/**
	 * Set form block data.
	 * 
	 * @param	array	$data Array of form data
	 * @param	int		$id The object ID
	 * @param	string	$type The object type
	 */
	public function set_form_block_data( array $data, int $id, string $type = 'post' ): void {
		$form_ids = get_option( 'form_block_form_ids', [] );
		$new_form_ids = $form_ids;
		
		if ( $type !== 'post' ) {
			$id = $type . '-' . $id;
		}
		
		foreach ( $data as $form_id => $form ) {
			if ( empty( $new_form_ids[ $form_id ] ) ) {
				$new_form_ids[ $form_id ] = [ $id ];
			}
			else if ( ! in_array( $id, $new_form_ids[ $form_id ], true ) ) {
				$new_form_ids[ $form_id ][] = $id;
			}
			
			// store form data
			update_option( 'form_block_data_' . $form_id, $form );
		}
		
		if ( $form_ids !== $new_form_ids ) {
			update_option( 'form_block_form_ids', $new_form_ids );
		}
	}
}

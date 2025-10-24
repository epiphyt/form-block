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
	public static ?self $instance = null;
	
	/**
	 * Initialize the class.
	 */
	public function init(): void {
		\add_action( 'save_post', [ $this, 'set' ], 20, 2 );
		\add_action( 'update_option_widget_block', [ $this, 'set_for_widget' ], 20, 2 );
		\add_filter( 'form_block_get_data', [ $this, 'set_contextual_block_data' ], 5, 5 );
	}
	
	/**
	 * Get form data from blocks.
	 * 
	 * @param	array	$blocks Blocks from parsed_blocks()
	 * @param	array	$data Current form data
	 * @param	string	$form_id The form ID
	 * @param	string	$context Current context
	 * @return	array Form data
	 */
	public function get( array $blocks, array $data = [], string $form_id = '', string $context = '' ): array {
		foreach ( $blocks as $block ) {
			// don't process empty blocks
			if ( $block['blockName'] === null ) {
				continue;
			}
			
			$field_data = [];
			
			// reusable blocks need to be processed first
			if ( $block['blockName'] === 'core/block' && ! empty( $block['attrs']['ref'] ) ) {
				$reusable_post = \get_post( $block['attrs']['ref'] );
				
				if ( ! $reusable_post instanceof WP_Post ) {
					return $data;
				}
				
				$reusable_blocks = \parse_blocks( $reusable_post->post_content );
				$data = $this->get( $reusable_blocks, $data );
			}
			else if (
				\str_starts_with( $block['blockName'], 'form-block/' )
				|| \str_starts_with( $block['blockName'], 'form-block-pro/' )
			) {
				// ignore fields without a form block
				if ( empty( $form_id ) && $block['blockName'] !== 'form-block/form' ) {
					if ( ! empty( $block['innerBlocks'] ) ) {
						$data = \array_merge( $data, $this->get( $block['innerBlocks'], $data, $form_id ) );
					}
					
					continue;
				}
				
				if ( empty( $context ) ) {
					$context = self::get_block_context( $block );
				}
				
				switch ( $block['blockName'] ) {
					case 'form-block/fieldset':
						$field_data = $this->get_attributes( $block['innerHTML'], 'fieldset' );
						$field_data['block_type'] = 'fieldset';
						$field_data['fields'] = $this->get( $block['innerBlocks'], [], $form_id, $context );
						break;
					case 'form-block/form':
						if ( empty( $block['attrs']['formId'] ) ) {
							break;
						}
						
						$form_id = $block['attrs']['formId'];
						$data[ $form_id ] = [
							'block_type' => 'form',
							'fields' => [],
							'label' => $block['attrs']['label'] ?? '',
							'subject' => $block['attrs']['subject'] ?? '',
						];
						
						/**
						 * Filter form block data.
						 * 
						 * @param	array	$form_data The current block data that is being stored
						 * @param	array	$block The original block data
						 * @param	string	$form_id The form ID
						 */
						$data[ $form_id ] = \apply_filters( 'form_block_data_form', $data[ $form_id ], $block, $form_id );
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
				
				if ( $block['blockName'] !== 'form-block/form' ) {
					$field_data = \array_merge( $block['attrs'], $field_data );
				}
				
				/**
				 * Filter the field data.
				 * 
				 * @param	array	$field_data The field data
				 * @param	array	$blocks Blocks from parsed_blocks()
				 * @param	array	$data Current form data
				 * @param	string	$form_id The form ID
				 */
				$field_data = (array) \apply_filters( 'form_block_get_form_data', $field_data, $block, $data, $form_id );
			}
			
			/**
			 * Filter the form data.
			 * 
			 * @since	1.5.2 Added parameters $field_data and $context
			 * 
			 * @param	mixed[]	$data Current form data
			 * @param	mixed[]	$block Current parsed block
			 * @param	string	$form_id The form ID
			 * @param	mixed[]	$field_data Field data
			 * @param	string	$context Block context
			 */
			$data = (array) \apply_filters( 'form_block_get_data', $data, $block, $form_id, $field_data, $context );
			
			if ( $context && self::is_current_context( $block, $context ) ) {
				$context = '';
			}
		}
		
		return \array_filter( $data );
	}
	
	/**
	 * Get all attributes from a HTML element.
	 * 
	 * @1.5.2	Added $context parameter
	 * 
	 * @param	string	$element The HTML element
	 * @param	string	$tag_name The tag name
	 * @param	string	$context Current context
	 * @return	array List of attributes
	 */
	public function get_attributes( string $element, string $tag_name, string $context = '' ): array {
		$dom = new DOMDocument();
		$dom->loadHTML(
			'<html><meta charset="UTF-8">' . $element . '</html>',
			\LIBXML_HTML_NOIMPLIED | \LIBXML_HTML_NODEFDTD
		);
		
		$attributes = $this->get_element_attributes( $dom, $tag_name, [ 'context' => $context ] );
		
		if ( $tag_name === 'fieldset' ) {
			$attributes['legend'] = $this->get_element_attributes(
				$dom,
				'legend',
				[
					'get_text_content' => true,
				]
			);
		}
		else {
			// get label data
			$label_attributes = $this->get_element_attributes(
				$dom,
				'span',
				[
					'class_name' => 'form-block__label-content',
					'context' => $context,
					'get_text_content' => true,
				]
			);
			$attributes['label'] = $label_attributes['textContent'] ?? '';
		}
		
		// get options
		if ( $tag_name === 'select' ) {
			$option_attributes = $this->get_element_attributes(
				$dom,
				'option',
				[
					'context' => $context,
					'get_text_content' => true,
				]
			);
			
			// check for associative keys
			if ( \count( \array_filter( \array_keys( $option_attributes ), 'is_string' ) ) > 0 ) {
				$option_attributes['value'] = $option_attributes['textContent'];
				unset( $option_attributes['textContent'] );
				$option_attributes = [ $option_attributes ];
			}
			else {
				$option_attributes = \array_map( static function( $option ) {
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
	 * @param	\DOMDocument	$dom The DOMDocument instance
	 * @param	string			$tag_name The tag name
	 * @param	array{class_name?: string, context?: string, get_text_content?: bool}|array{}	$arguments Additional arguments
	 * @return	array List of element's attributes
	 */
	private function get_element_attributes( DOMDocument $dom, string $tag_name, array $arguments = [] ): array {
		$attributes = [];
		$arguments = \wp_parse_args( $arguments, [
			'class_name' => '',
			'context' => '',
			'get_text_content' => false,
		] );
		$iteration = 0;
		
		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		/** @var	\DOMElement $tag */
		foreach ( $dom->getElementsByTagName( $tag_name ) as $tag ) {
			if ( ! $tag->hasAttributes() ) {
				continue;
			}
			
			if (
				! empty( $arguments['class_name'] )
				&& \strpos( $tag->getAttribute( 'class' ), $arguments['class_name'] ) === false
			) {
				continue;
			}
			
			if (
				! empty( $arguments['context'] )
				&& \strpos( $tag->getAttribute( 'class' ), $arguments['context'] ) === false
			) {
				continue;
			}
			
			foreach ( $tag->attributes as $attribute ) {
				$attributes[ $iteration ][ $attribute->nodeName ] = $attribute->nodeValue;
			}
			
			if ( $arguments['get_text_content'] ) {
				if ( ! isset( $attributes[ $iteration ]['textContent'] ) ) {
					$attributes[ $iteration ]['textContent'] = '';
				}
				
				// textContent removes all line breaks, so get the actual HTML
				// of the element and store them without tags, but with line breaks
				foreach ( $tag->childNodes as $child ) {
					$attributes[ $iteration ]['textContent'] .= $tag->ownerDocument->saveHTML( $child );
				}
				
				$attributes[ $iteration ]['textContent'] = \str_replace( [ '<br>', '<br />' ], \PHP_EOL, $attributes[ $iteration ]['textContent'] );
				$attributes[ $iteration ]['textContent'] = \wp_strip_all_tags( $attributes[ $iteration ]['textContent'] );
			}
			
			++$iteration;
		}
		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		
		if ( \count( $attributes ) === 1 ) {
			$attributes = \reset( $attributes );
		}
		
		return $attributes;
	}
	
	/**
	 * Get context by a block.
	 * 
	 * @param	mixed[]	$block Block data
	 * @return	string Block context
	 */
	public static function get_block_context( array $block ): string {
		return \str_replace( self::get_block_context_prefixes(), '', $block['blockName'] );
	}
	
	/**
	 * Get block context prefixes.
	 * 
	 * @return	string[] Block context prefixes
	 */
	private static function get_block_context_prefixes(): array {
		$context_prefixes = [
			'form-block/',
			'form-block-pro/',
		];
		
		/**
		 * Filter replacements to get block context.
		 * 
		 * @since	1.5.2
		 * 
		 * @param	string[]	$replacements List of replacements
		 */
		$context_prefixes = (array) \apply_filters( 'form_block_block_context_prefixes', $context_prefixes );
		
		return $context_prefixes;
	}
	
	/**
	 * Get a unique instance of the class.
	 * 
	 * @return	\epiphyt\Form_Block\block_data\Data The single instance of this class
	 */
	public static function get_instance(): self {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * Check, whether the current block is in the current context.
	 * 
	 * @param	mixed[]	$block Block data
	 * @param	string	$context Current context
	 * @return	bool Whether the current block is in the current context
	 */
	public static function is_current_context( array $block, string $context ): bool {
		$prefixes = self::get_block_context_prefixes();
		
		foreach ( $prefixes as $prefix ) {
			if ( $block['blockName'] === $prefix . $context ) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Maybe delete old form data.
	 * 
	 * @param	int|string	$id Current object ID
	 * @param	array		$data Form data
	 * @param	string		$type The object type
	 */
	private function maybe_delete( int|string $id, array $data, string $type = 'post' ): void {
		$data_ids = \array_keys( $data );
		$form_ids = \get_option( 'form_block_form_ids', [] );
		$original_id = $id;
		$widgets = [];
		$widgets_unset = [];
		
		if ( $type !== 'post' ) {
			$id = $type . '-' . $id;
		}
		
		if ( $type === 'widget' ) {
			$widgets = \get_option( 'widget_block', [] );
			$widget_content = $widgets[ $original_id ]['content'];
			
			if ( empty( $widget_content ) || ! Util::has_block_in_content( 'form-block/form', $widget_content ) ) {
				$widgets_unset[] = $id;
			}
		}
		
		foreach ( $form_ids as $form_id => &$ids ) {
			if ( ! \in_array( $id, $ids, true ) ) {
				continue;
			}
			
			$keep = false;
			
			foreach ( $data_ids as $id ) {
				if ( $form_id !== $id ) {
					continue;
				}
				
				$keep = true;
			}
			
			if ( ! $keep || \in_array( $id, $widgets_unset, true ) ) {
				// delete object ID from array
				$key = \array_search( $id, $ids, true );
				
				if ( $key !== false ) {
					unset( $ids[ $key ] );
				}
				
				// completely delete only if it's not used anywhere else
				if ( empty( $ids ) ) {
					\delete_option( 'form_block_data_' . $form_id );
					unset( $form_ids[ $form_id ] );
				}
			}
		}
		
		\update_option( 'form_block_form_ids', $form_ids );
	}
	
	/**
	 * Set form data.
	 * 
	 * @param	int			$post_id Current post ID
	 * @param	\WP_Post	$post Current post object
	 */
	public function set( int $post_id, WP_Post $post ): void {
		if ( \defined( 'DOING_AUTOSAVE' ) && \DOING_AUTOSAVE ) {
			return;
		}
		
		if ( $post->post_type === 'revision' ) {
			return;
		}
		
		if ( ! Util::has_block( 'form-block/form', $post->ID ) ) {
			return;
		}
		
		$data = $this->get( \parse_blocks( $post->post_content ) );
		
		$this->set_form_block_data( $data, $post_id );
		
		if ( $post->post_type !== 'wp_block' ) {
			$this->maybe_delete( $post_id, $data );
		}
	}
	
	/**
	 * Set block data depending on context.
	 * 
	 * @param	mixed[]		$data Current form data
	 * @param	mixed[]		$block Current parsed block
	 * @param	string		$form_id The form ID
	 * @param	mixed[]|array{}	$field_data Field data
	 * @param	string		$context Block context
	 * @return	mixed[] Updated blocks form data
	 */
	public function set_contextual_block_data( array $data, array $block, string $form_id, array $field_data, string $context ): array {
		$ignored_contexts = [ 'fieldset' ];
		
		/**
		 * Filter ignored context.
		 * 
		 * @since	1.5.2
		 * 
		 * @param	string[]	$ignored_contexts Current contexts to ignore
		 * @param	mixed[]		$block Current parsed block
		 */
		$ignored_contexts = (array) \apply_filters( 'form_block_data_ignored_context', $ignored_contexts, $block );
		
		if ( empty( $field_data ) ) {
			if ( ! empty( $block['innerBlocks'] ) && ! \in_array( $context, $ignored_contexts, true ) ) {
				$data = \array_merge( $data, $this->get( $block['innerBlocks'], $data, $form_id ) );
			}
			
			return $data;
		}
		
		if ( ! empty( $field_data ) ) { // @phpstan-ignore empty.variable
			if ( \in_array( $context, $ignored_contexts, true ) && ! self::is_current_context( $block, $context ) ) {
				unset( $data[ $form_id ] );
				
				$data[] = $field_data;
			}
			else {
				$data[ $form_id ]['fields'][] = $field_data;
			}
		}
		
		if ( ! empty( $block['innerBlocks'] ) && ! \in_array( $context, $ignored_contexts, true ) ) {
			$data = \array_merge( $data, $this->get( $block['innerBlocks'], $data, $form_id ) );
		}
		
		return $data;
	}
	
	/**
	 * Set form data for widgets.
	 * 
	 * @param	mixed	$old_value The old option value
	 * @param	mixed	$new_value The new option value
	 */
	public function set_for_widget( mixed $old_value, mixed $new_value ): void { // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
		if ( ! \is_array( $new_value ) ) {
			return;
		}
		
		foreach ( $new_value as $widget_id => $widget_data ) {
			if ( empty( $widget_data['content'] ) ) {
				continue;
			}
			
			$data = $this->get( \parse_blocks( $widget_data['content'] ) );
			
			$this->set_form_block_data( $data, $widget_id, 'widget' );
			$this->maybe_delete( $widget_id, $data, 'widget' );
		}
	}
	
	/**
	 * Set form block data.
	 * 
	 * @param	array	$data Array of form data
	 * @param	int		$id The object ID
	 * @param	string	$type The object type
	 */
	public function set_form_block_data( array $data, int $id, string $type = 'post' ): void {
		$form_ids = \get_option( 'form_block_form_ids', [] );
		$new_form_ids = $form_ids;
		
		if ( $type !== 'post' ) {
			$id = $type . '-' . $id;
		}
		
		foreach ( $data as $form_id => $form ) {
			if ( empty( $new_form_ids[ $form_id ] ) ) {
				$new_form_ids[ $form_id ] = [ $id ];
			}
			else if ( ! \in_array( $id, $new_form_ids[ $form_id ], true ) ) {
				$new_form_ids[ $form_id ][] = $id;
			}
			
			// store form data
			\update_option( 'form_block_data_' . $form_id, $form );
		}
		
		if ( $form_ids !== $new_form_ids ) {
			\update_option( 'form_block_form_ids', $new_form_ids );
		}
	}
}

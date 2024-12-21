<?php
namespace epiphyt\Form_Block\modules;

use DOMDocument;
use DOMElement;
use DOMXPath;
use epiphyt\Form_Block\form_data\Field;

/**
 * Custom date module.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Custom_Date {
	/**
	 * @var		array Supported custom date field types
	 */
	public static array $field_types = [
		'date-custom',
		'datetime-local-custom',
		'month-custom',
		'time-custom',
		'week-custom',
	];
	
	/**
	 * Initialize the class.
	 */
	public function init(): void {
		\add_action( 'enqueue_block_editor_assets', [ self::class, 'enqueue_editor_assets' ] );
		\add_filter( 'form_block_output_field_value', [ self::class, 'set_output_format' ], 10, 3 );
		\add_filter( 'render_block_form-block/input', [ self::class, 'set_markup' ], 15, 2 );
	}
	
	/**
	 * Add single date fields to an input element.
	 * 
	 * @param	array			$fields Fields to add
	 * @param	\DOMDocument	$dom DOM object
	 * @param	\DOMElement		$element DOM element object
	 * @param	array			$field_data Data of the input field
	 * @param	array			$block_data Data of the block
	 */
	private static function add_date_fields( array $fields, DOMDocument $dom, DOMElement $element, array $field_data, array $block_data ): void {
		foreach ( $fields as $type => $field ) {
			$container = $dom->createElement( 'div' );
			$input_container = $dom->createElement( 'div' );
			$input_node = $dom->createElement( 'input' );
			$label_classes = 'form-block__label is-input-label';
			$label_content_node = $dom->createElement( 'span', $field['label'] );
			$label_node = $dom->createElement( 'label' );
			$separators = [];
			
			foreach ( $field['separator'] as $position => $value ) {
				if ( ! empty( $value ) ) {
					$separators[ $position ] = $dom->createElement( 'span' );
					$separators[ $position ]->setAttribute( 'class', 'form-block__date-custom--separator is-' . $position );
					$separators[ $position ]->textContent = $value;
				}
			}
			
			$input_node->setAttribute( 'class', $field_data['class'] );
			$input_node->setAttribute( 'data-max-length', $field['validation']['max-length'] );
			$input_node->setAttribute( 'data-type', $type );
			$input_node->setAttribute( 'data-validate-length-range', $field['validation']['min-length'] . ',' . $field['validation']['max-length'] );
			$input_node->setAttribute( 'data-validate-minmax', $field['validation']['min'] . ',' . $field['validation']['max'] );
			$input_node->setAttribute( 'id', $field_data['id'] . '-' . $type );
			$input_node->setAttribute( 'max', $field['validation']['max'] );
			$input_node->setAttribute( 'min', $field['validation']['min'] );
			$input_node->setAttribute( 'name', $field_data['name'] . '[' . $type . ']' );
			$input_node->setAttribute( 'type', 'number' );
			$input_node->setAttribute( 'value', $block_data['value'][ $type ] ?? '' );
			
			if ( $field_data['is_required'] ) {
				$input_node->setAttribute( 'required', '' );
			}
			
			if ( $block_data['showPlaceholder'] ) {
				$input_node->setAttribute( 'placeholder', $field['placeholder'] );
			}
			
			if ( empty( $block_data['showLabel'] ) ) {
				$label_classes .= ' screen-reader-text';
			}
			
			$container->setAttribute( 'class', 'form-block__element is-sub-element is-type-text is-sub-type-' . $type );
			$input_container->setAttribute( 'class', 'form-block__input-container' );
			$label_content_node->setAttribute( 'class', 'form-block__label-content' );
			$label_node->setAttribute( 'class', $label_classes );
			$label_node->setAttribute( 'for', $field_data['id'] . '-' . $type );
			$label_node->appendChild( $label_content_node );
			
			if ( ! empty( $separators['before'] ) ) {
				$input_container->appendChild( $separators['before'] );
			}
			
			$input_container->appendChild( $input_node );
			
			if ( ! empty( $separators['after'] ) ) {
				$input_container->appendChild( $separators['after'] );
			}
			
			$container->appendChild( $input_container );
			$container->appendChild( $label_node );
			$element->appendChild( $container );
		}
	}
	
	/**
	 * Enqueue editor assets.
	 */
	public static function enqueue_editor_assets(): void {
		\wp_localize_script( 'form-block-input-editor-script', 'formBlockInputCustomDate', self::get_field_data() );
	}
	
	/**
	 * Get field data.
	 * 
	 * @param	array	$order Field data order
	 * @return	array Field data
	 */
	public static function get_field_data( array $order = [] ): array {
		$fields = [
			'day' => [
				'label' => \__( 'Day', 'form-block' ),
				'placeholder' => \_x( 'DD', 'date field placeholder', 'form-block' ),
				'separator' => [
					'after' => \_x( '/', 'date separator', 'form-block' ),
					'before' => '',
				],
				'validation' => [
					'max' => 31,
					'max-length' => 2,
					'min' => 1,
					'min-length' => 2,
					'type' => 'numeric',
				],
			],
			'hour' => [
				'label' => \__( 'Hours', 'form-block' ),
				'placeholder' => \_x( 'HH', 'date field placeholder', 'form-block' ),
				'separator' => [
					'after' => \_x( ':', 'time separator', 'form-block' ),
					'before' => \_x( 'at', 'date and time separator', 'form-block' ),
				],
				'validation' => [
					'max' => 24,
					'max-length' => 2,
					'min' => 0,
					'min-length' => 2,
					'type' => 'numeric',
				],
			],
			'minute' => [
				'label' => \__( 'Minutes', 'form-block' ),
				'placeholder' => \_x( 'MM', 'date field placeholder', 'form-block' ),
				'separator' => [
					'after' => '',
					'before' => '',
				],
				'validation' => [
					'max' => 59,
					'max-length' => 2,
					'min' => 0,
					'min-length' => 2,
					'type' => 'numeric',
				],
			],
			'month' => [
				'label' => \__( 'Month', 'form-block' ),
				'placeholder' => \_x( 'MM', 'date field placeholder', 'form-block' ),
				'separator' => [
					'after' => \_x( '/', 'date separator', 'form-block' ),
					'before' => '',
				],
				'validation' => [
					'max' => 12,
					'max-length' => 2,
					'min' => 1,
					'min-length' => 2,
					'type' => 'numeric',
				],
			],
			'week' => [
				'label' => \__( 'Week', 'form-block' ),
				'placeholder' => \_x( 'WK', 'date field placeholder', 'form-block' ),
				'separator' => [
					'after' => \_x( '/', 'date separator', 'form-block' ),
					'before' => '',
				],
				'validation' => [
					'max' => 53,
					'max-length' => 2,
					'min' => 1,
					'min-length' => 2,
					'type' => 'numeric',
				],
			],
			'year' => [
				'label' => \__( 'Year', 'form-block' ),
				'placeholder' => \_x( 'YYYY', 'date field placeholder', 'form-block' ),
				'separator' => [
					'after' => '',
					'before' => '',
				],
				'validation' => [
					'max' => 99999,
					'max-length' => 4,
					'min' => 0,
					'min-length' => 4,
					'type' => 'numeric',
				],
			],
		];
		
		if ( empty( $order ) ) {
			return $fields;
		}
		
		return \array_merge( \array_flip( $order ), $fields );
	}
	
	/**
	 * Get the field order.
	 * 
	 * @param	string	$type Field type
	 * @return	array Field order
	 */
	public static function get_field_order( string $type ): array {
		switch ( $type ) {
			case 'date-custom':
				$order = \explode( ', ', \_x( 'month, day, year', 'date order in lowercase', 'form-block' ) );
				break;
			case 'datetime-local-custom':
				$order = \explode( ', ', \_x( 'month, day, year, hour, minute', 'date order in lowercase', 'form-block' ) );
				break;
			case 'month-custom':
				$order = \explode( ', ', \_x( 'month, year', 'date order in lowercase', 'form-block' ) );
				break;
			case 'time-custom':
				$order = \explode( ', ', \_x( 'hour, minute', 'date order in lowercase', 'form-block' ) );
				break;
			case 'week-custom':
				$order = \explode( ', ', \_x( 'week, year', 'date order in lowercase', 'form-block' ) );
				break;
			default:
				$order = [];
				break;
		}
		
		return $order;
	}
	
	/**
	 * Set markup for a custom date field.
	 * 
	 * @param	string	$block_content The block content
	 * @param	array	$block Block attributes
	 * @return	string Updated block content
	 */
	public static function set_markup( string $block_content, array $block ): string {
		$dom = new DOMDocument();
		$dom->loadHTML(
			'<html><meta charset="UTF-8">' . $block_content . '</html>',
			\LIBXML_HTML_NOIMPLIED | \LIBXML_HTML_NODEFDTD
		);
		$xpath = new DOMXPath( $dom );
		/** @var	\DOMElement $container_node */
		$container_node = $xpath->query( '//div[contains(@class, "wp-block-form-block-input")]' )->item( 0 );
		/** @var	\DOMElement $input_node */
		$input_node = $xpath->query( '//div[contains(@class, "wp-block-form-block-input")]//input' )->item( 0 );
		$label_node = $xpath->query( '//div[contains(@class, "wp-block-form-block-input")]//label' )->item( 0 );
		$label_content_node = $xpath->query( '//div[contains(@class, "wp-block-form-block-input")]//span[contains(@class, "form-block__label-content")]' )->item( 0 );
		$field_data = [
			'class' => $input_node->getAttribute( 'class' ),
			'id' => $input_node->getAttribute( 'id' ),
			'is_required' => $input_node->hasAttribute( 'required' ) ? true : false,
			'name' => $input_node->getAttribute( 'name' ),
			'type' => $input_node->getAttribute( 'type' ),
		];
		
		if ( ! \str_ends_with( $field_data['type'], '-custom' ) ) {
			return $block_content;
		}
		
		$fieldset = $dom->createElement( 'fieldset' );
		$legend = $dom->createElement( 'legend' );
		$legend_content = $dom->createElement( 'span', $label_content_node->textContent ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$legend_content->setAttribute( 'class', 'form-block__label-content' );
		$legend->appendChild( $legend_content );
		
		if ( $field_data['is_required'] ) {
			$required_symbol = $dom->createElement( 'span' );
			$required_symbol->setAttribute( 'class', 'is-required' );
			$required_symbol->setAttribute( 'aria-hidden', 'true' );
			$required_symbol->textContent = '*'; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$legend->appendChild( $required_symbol );
		}
		
		$fieldset->appendChild( $legend );
		$fieldset->setAttribute( 'class', 'form-block__input-group' );
		$order = self::get_field_order( $field_data['type'] );
		
		if ( empty( $order ) ) {
			return $block_content;
		}
		
		\wp_enqueue_script( 'form-block-multi-field' );
		
		$fields = \array_intersect_key( self::get_field_data( $order ), \array_flip( $order ) );
		$block_data = \wp_parse_args(
			$block['attrs']['customDate'] ?? [],
			[
				'showLabel' => false,
				'showPlaceholder' => true,
				'value' => [],
			]
		);
		
		self::add_date_fields( $fields, $dom, $fieldset, $field_data, $block_data );
		$container_node->setAttribute( 'class', $container_node->getAttribute( 'class' ) . ' has-sub-elements' );
		$input_node->parentNode->appendChild( $fieldset ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$input_node->parentNode->removeChild( $input_node ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$label_node->parentNode->removeChild( $label_node ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		
		return \str_replace( [ '<html><meta charset="UTF-8">', '</html>' ], '', $dom->saveHTML( $dom->documentElement ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	}
	
	/**
	 * Set the output format.
	 * 
	 * @param	mixed	$value Post value
	 * @param	string	$name Field name
	 * @param	array	$form_fields Form fields
	 * @return	mixed Output in proper format
	 */
	public static function set_output_format( mixed $value, string $name, array $form_fields ) { // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
		$field = Field::get_by_name( $name, $form_fields );
		
		if ( ! isset( $field['type'] ) || ! \in_array( $field['type'], self::$field_types, true ) ) {
			return $value;
		}
		
		$field_order = self::get_field_order( $field['type'] );
		$format_data = \array_intersect_key( self::get_field_data( $field_order ), \array_flip( $field_order ) );
		$output = '';
		
		foreach ( $format_data as $format_type => $field_format ) {
			// don't start with a separator
			if ( ! empty( $output ) ) {
				$output .= $field_format['separator']['before'] ?? '';
			}
			
			if ( ! empty( $value[ $format_type ] ) && \is_string( $value[ $format_type ] ) ) {
				$output .= $value[ $format_type ];
				$output .= $field_format['separator']['after'] ?? '';
			}
		}
		
		return $output;
	}
}

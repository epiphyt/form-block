<?php
namespace epiphyt\Form_Block\form_data;

use epiphyt\Form_Block\Form_Block;
use epiphyt\Form_Block\util\Array_Operations;

/**
 * Form field data class.
 * 
 * @since	1.5.0
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Field {
	/**
	 * @var		\epiphyt\Form_Block\form_data\Field
	 */
	public static ?self $instance = null;
	
	/**
	 * Get the field data of a list of fields by its name.
	 * 
	 * @param	string	$name The name to search for
	 * @param	array	$fields The fields to search in
	 * @param	string	$mode Mode which matching fields to get, either 'single' or 'all'
	 * @return	array The field data
	 */
	public static function get_by_name( string $name, array $fields, string $mode = 'single' ): array {
		Form_Block::get_instance()->reset_block_name_attributes();
		$fields_list = [];
		$uniqueness = $mode === 'all' ? 'non-unique' : 'unique';
		
		foreach ( $fields as $field ) {
			if ( ! \is_array( $field ) ) {
				continue;
			}
			
			if ( $name !== Form_Block::get_instance()->get_block_name_attribute( $field, $uniqueness ) ) {
				continue;
			}
			
			if ( $mode === 'single' ) {
				return $field;
			}
			else if ( $mode === 'all' ) {
				$fields_list[] = $field;
			}
		}
		
		return $fields_list;
	}
	
	/**
	 * Get field indentation by a defined level.
	 * 
	 * @param	mixed[]	$field Field data
	 * @param	int		$level Indentation level
	 * @return	string Field output with indentation
	 */
	private static function get_indentation( array $field, int $level ): string {
		$output = '';
		
		foreach ( $field as $key => $item ) {
			$output .= \str_repeat( ' ', $level * 2 );
			/* translators: list item key */
			$output .= \sprintf( \_x( '- %s:', 'list element in plaintext email', 'form-block' ), $key );
			
			if ( \is_array( $item ) ) {
				++$level;
				$output .= \PHP_EOL;
				$output .= self::get_indentation( $item, $level );
				--$level;
			}
			else {
				$output .= ' ' . $item;
			}
		}
		
		return $output;
	}
	
	/**
	 * Get a unique instance of the class.
	 * 
	 * @return	\epiphyt\Form_Block\form_data\Field The single instance of this class
	 */
	public static function get_instance(): self {
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
	public static function get_name_by_label( string $label, bool $to_lowercase = true ): string {
		if ( $to_lowercase ) {
			$label = \mb_strtolower( $label );
		}
		
		/**
		 * Filter the label before generating a name out of it.
		 * 
		 * @param	string	$label The original label
		 * @param	bool	$to_lowercase Whether the name should be lowercase
		 * @return	string The updated label
		 */
		$label = \apply_filters( 'form_block_pre_get_name_by_label', $label, $to_lowercase );
		
		$regex = '/[^A-Za-z0-9\-_\[\]]/';
		$replace = [ 'ae', 'oe', 'ue', 'ss', '-' ];
		$search = [ 'ä', 'ö', 'ü', 'ß', ' ' ];
		$name = \preg_replace( $regex, '', \str_replace( $search, $replace, $label ) );
		
		/**
		 * Filter the generated name from a label.
		 * 
		 * @param	string	$name The generated name
		 * @param	string	$label The original label
		 * @param	bool	$to_lowercase Whether the name should be lowercase
		 * @return	string The updated name
		 */
		$name = \apply_filters( 'form_block_get_name_by_label', $name, $label, $to_lowercase );
		
		return $name;
	}
	
	/**
	 * Check if a field matches the structure in the provided POST data.
	 * 
	 * @param	array<string, mixed>	$post_fields POST fields
	 * @param	mixed[]					$field Field data
	 * @return	mixed The value if the field exists, or false otherwise
	 */
	private static function is_matching_in_post_fields( array $post_fields, array $field ): mixed {
		$keys = self::parse_field_name( $field['name'] );
		$current = $post_fields;
		
		if ( empty( $keys ) ) {
			return false;
		}
		
		foreach ( $keys as $key ) {
			if ( $key === '[]' ) {
				if ( \is_array( $current ) ) {
					if ( isset( $field['value'] ) ) {
						if ( $field['value'] !== Array_Operations::get_last_value_recursive( $current ) ) {
							return false;
						}
					}
					
					return $current;
				}
				
				return false;
			}
			else if ( ! \array_key_exists( $key, $current ) ) {
				return false;
			}
			else if ( $key !== $keys[0] ) {
				$current = [ $key => $current[ $key ] ];
			}
			else {
				$current = $current[ $key ];
			}
		}
		
		if ( isset( $field['value'] ) && $field['value'] !== $current ) {
			return false;
		}
		
		return $current;
	}
	
	/**
	 * Recursively format a field output.
	 * 
	 * @param	mixed	$value Value to format
	 * @param	string	$label Label for the field
	 * @param	int		$level Current indentation level
	 * @return	string Formatted output
	 */
	private static function format_output( mixed $value, string $label, int $level = 0 ): string {
		$output = '';
		$prefix = \str_repeat( ' ', ( $level > 0 ? $level - 1 : $level ) * 2 ) . ( $level > 0 ? '- ' : '' );
		
		if ( \is_array( $value ) ) {
			$output .= "{$prefix}{$label}:" . \PHP_EOL;
			
			foreach ( $value as $key => $sub_value ) {
				if ( $level > 1 && \is_numeric( $key ) ) {
					$key = '';
				}
				
				$output .= self::format_output( $sub_value, $key, $level + 1 );
			}
		}
		else if ( ! empty( $label ) ) {
			// multiline support
			if ( \str_contains( $value, \PHP_EOL ) ) {
				// indent value as well
				if ( $level > 0 ) {
					$lines = \explode( \PHP_EOL, $value );
					$lines = \array_map( static function( string $line ) use ( $level ) {
						return \str_repeat( ' ', $level * 2 ) . $line;
					}, $lines );
					$value = \implode( \PHP_EOL, $lines );
				}
				
				$output .= "{$prefix}{$label}:" . \PHP_EOL . $value . \PHP_EOL;
			}
			else {
				$output .= "{$prefix}{$label}: {$value}" . \PHP_EOL;
			}
		}
		else if ( empty( $value ) ) {
			return $output;
		}
		else {
			$output .= "{$prefix}{$value}" . \PHP_EOL;
		}
		
		return $output;
	}
	
	/**
	 * Check each POST field and output its label and value.
	 * 
	 * @param	array<int, array{name: string, label: string}>	$fields Fields data
	 * @param	array<string, mixed>							$post_fields POST fields
	 * @param	int 											$level Current indentation level
	 * @return	string Aggregated output for all matched fields
	 */
	public static function get_output( array $fields, array $post_fields, int $level = 0 ): string {
		$output = '';
		
		foreach ( $fields as $field ) {
			$current_output = '';
			
			if ( ! isset( $field['name'] ) && isset( $field['label'] ) ) {
				$field['name'] = self::get_name_by_label( $field['label'] );
			}
			
			if ( isset( $field['name'] ) ) {
				/**
				 * Filter whether to omit the field from output.
				 * 
				 * @since	1.0.3
				 * @since	1.5.0	Replaced 4th parameter from form data to fields data
				 * 
				 * @param	bool	$omit_field Whether to omit the field from output
				 * @param	string	$name Field name
				 * @param	mixed	$value Field value
				 * @param	array	$fields Fields data
				 */
				$omit_field = \apply_filters( 'form_block_output_field_omit', false, $field['name'], $field['value'] ?? null, $fields );
				
				if ( $omit_field ) {
					continue;
				}
				
				if ( \preg_match( '/\[(\d*)\]/', $field['name'] ) ) {
					$values = self::is_matching_in_post_fields( $post_fields, $field );
					
					if ( \is_array( $values ) ) {
						/**
						 * Filter the field value in the output.
						 * 
						 * @since	1.0.3
						 * @since	1.5.0	Replaced 3th parameter from form data to fields data
						 * @since	1.5.0	Added 4th parameter $level
						 * 
						 * @param	mixed	$values Field values
						 * @param	string	$name Field name
						 * @param	array	$fields Fields data
						 * @param	int 	$level Current indentation level
						 */
						$values = \apply_filters( 'form_block_output_field_value', $values, $field['name'], $fields, $level );
						
						$current_output .= self::format_output( $values, $field['label'], $level );
					}
				}
				else {
					$value = self::is_matching_in_post_fields( $post_fields, $field );
					
					if (
						empty( $value )
						|| (
							\is_array( $value )
							&& $field['type'] !== 'date-custom'
							&& $field['type'] !== 'datetime-local-custom'
							&& $field['type'] !== 'month-custom'
							&& $field['type'] !== 'time-custom'
							&& $field['type'] !== 'week-custom'
						)
					) {
						continue;
					}
					
					/**
					 * This filter is documented in inc/form-data/class-field.php
					 */
					$value = \apply_filters( 'form_block_output_field_value', $value, $field['name'], $fields, $level );
					
					$current_output .= self::format_output( $value, $field['label'], $level );
				}
				
				/**
				 * Filter the field output.
				 * 
				 * @since	1.1.0
				 * @since	1.5.0	Replaced 4th parameter from form data to fields data
				 * @since	1.5.0	Added 5th parameter $level
				 * 
				 * @param	string	$current_output Field output
				 * @param	string	$name Field name
				 * @param	mixed	$value Field value
				 * @param	array	$fields Fields data
				 */
				$current_output = \apply_filters( 'form_block_output_field_output', $current_output, $field['name'], $field['value'] ?? null, $fields, $level );
			}
			else if ( isset( $field['legend']['textContent'] ) ) {
				/**
				 * Filter the fieldset legend text.
				 * 
				 * @since	1.5.0
				 * 
				 * @param	string		$legend Current legend text
				 * @param	mixed[]		$field Form field data
				 * @param	string[]	$post_fields POST fields
				 */
				$legend = \apply_filters( 'form_block_output_fieldset_legend', $field['legend']['textContent'], $field, $post_fields );
				
				/**
				 * This filter is documented in inc/form-data/class-field.php
				 */
				$omit_field = \apply_filters( 'form_block_output_field_omit', false, $legend, null, $fields );
				
				if ( $omit_field ) {
					continue;
				}
				
				$subfields_output = self::get_output( $field['fields'], $post_fields, $level + 1 );
				
				if ( \trim( $subfields_output ) ) {
					$current_output .= $legend . ':' . \PHP_EOL;
					$current_output .= $subfields_output;
				}
				
				/**
				 * This filter is documented in inc/form-data/class-field.php
				 */
				$current_output = \apply_filters( 'form_block_output_field_output', $current_output, $legend, null, $fields, $level );
			}
			
			$output .= $current_output;
		}
		
		return $output;
	}
	
	/**
	 * Set static output value for checkboxes and radio buttons.
	 * 
	 * @param	string	$output The field output
	 * @param	string	$name The field name
	 * @param	mixed	$value The field value
	 * @param	array	$fields Field data
	 * @param	int 	$level Current indentation level
	 * @return	string The updated field output
	 */
	public static function get_static_value_output( string $output, string $name, mixed $value, array $fields, int $level ): string {
		$clear_output = false;
		$fields_data = self::get_by_name( $name, $fields, 'all' );
		
		foreach ( $fields_data as $field_data ) {
			if (
				empty( $field_data['type'] )
				|| (
					$field_data['type'] !== 'checkbox'
					&& $field_data['type'] !== 'radio'
				)
				|| ! $output
			) {
				continue;
			}
			
			$label = self::get_title_by_field( $field_data, $fields );
			$field_keys = self::parse_field_name( $name );
			
			if ( $field_data['type'] === 'checkbox' ) {
				/* translators: form field title */
				$return_value = \sprintf( \__( 'Checked: %s', 'form-block' ), $label );
			}
			else if ( $field_data['type'] === 'radio' ) {
				list( $output_label, $output_value ) = \array_map( 'trim', \explode( ': ', $output ) );
				$clear_output = true;
				
				if ( $output_label !== $label || $output_value !== $value ) {
					continue;
				}
				
				$clear_output = false;
				
				if ( ! \is_array( $value ) && $value !== 'on' ) {
					/* translators: form field title or value */
					$return_value = \sprintf( \__( 'Selected: %s', 'form-block' ), $value );
				}
				else {
					/* translators: form field title or value */
					$return_value = \sprintf( \__( 'Selected: %s', 'form-block' ), $label );
				}
			}
			
			if ( \count( $field_keys ) > 1 ) {
				$return_value = \preg_replace(
					'/- ' . \preg_quote( \end( $field_keys ) . ': ', '/' ) . '(.*)(\r\n|\r|\n)?/',
					"- {$return_value}",
					$output
				);
			}
			else if ( \str_starts_with( $output, '- ' ) ) {
				$return_value = \str_repeat( ' ', $level * 2 ) . $return_value;
			}
			
			return $return_value . \PHP_EOL;
		}
		
		if ( $clear_output ) {
			return '';
		}
		
		return $output;
	}
	
	/**
	 * Get field title of a list of fields by its data.
	 * 
	 * @param	array	$field Field data
	 * @param	array	$fields List of fields
	 * @return	string Field title
	 */
	public static function get_title_by_field( array $field, array $fields ): string {
		foreach ( $fields as $current_field ) {
			if ( $current_field === $field ) {
				return $field['label'] ?? \__( 'Unknown', 'form-block' );
			}
			
			if ( ! empty( $field['fields'] ) ) {
				$field_title = self::get_title_by_field( $field, $field['fields'] );
				
				if ( ! empty( $field_title ) ) {
					return $field_title;
				}
			}
		}
		
		return '';
	}
	
	/**
	 * Get the field title of a list of fields by its name.
	 * 
	 * @param	string	$name The name to search for
	 * @param	array	$fields The fields to search in
	 * @param	bool	$reset_name_attributes Whether to reset the block name attributes
	 * @return	string The field title or the field name, if title cannot be found
	 */
	public static function get_title_by_name( string $name, array $fields, bool $reset_name_attributes = true ): string {
		if ( $reset_name_attributes ) {
			Form_Block::get_instance()->reset_block_name_attributes();
		}
		
		foreach ( $fields as $field ) {
			$field_name = Form_Block::get_instance()->get_block_name_attribute( $field );
			
			if ( $field_name === $name || \preg_match( '/' . \preg_quote( $field_name, '/' ) . '-\d+/', $name ) ) {
				return $field['label'] ?? $name;
			}
			
			if ( ! empty( $field['fields'] ) ) {
				return self::get_title_by_name( $name, $field['fields'], false );
			}
		}
		
		return $name;
	}
	
	/**
	 * Parse a field name into its keys.
	 * 
	 * @param	string 	$field_name Field name to parse
	 * @return	array<int, string> Parsed keys as an array of strings
	 */
	private static function parse_field_name( string $field_name ): array {
		\preg_match_all( '/([^\[\]]+)|\[\]/', $field_name, $matches );
		
		return $matches[0] ?? [];
	}
}

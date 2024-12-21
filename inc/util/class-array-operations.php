<?php
namespace epiphyt\Form_Block\util;

/**
 * Array operations class.
 * 
 * @since	1.5.0
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Array_Operations {
	/**
	 * Filter an array recursively.
	 * 
	 * @param	mixed[]	$input Input array
	 * @return	mixed[] Filtered array
	 */
	public static function filter_recursive( array $input ): array {
		foreach ( $input as $key => &$value ) {
			if ( \is_array( $value ) ) {
				$value = self::filter_recursive( $value );
				
				if ( empty( $value ) ) {
					unset( $input[ $key ] );
				}
			}
			else if ( empty( $value ) ) {
				unset( $input[ $key ] );
			}
		}
		
		return $input;
	}
	
	/**
	 * Get most nested value inside an array recursively.
	 * 
	 * @param	array	$array Array to get value from
	 * @return	mixed Most nested value
	 */
	public static function get_most_nested_value( array $array ) { // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
		$end = \end( $array );
		
		if ( \is_array( $end ) ) {
			return self::get_most_nested_value( $end );
		}
		
		return $end;
	}
}

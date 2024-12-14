<?php
namespace epiphyt\Form_Block\util;

/**
 * Array operations class.
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
	public static function get_last_value_recursive( array $array ): mixed {
		$end = \end( $array );
		
		if ( \is_array( $end ) ) {
			return self::get_last_value_recursive( $end );
		}
		
		return $end;
	}
}

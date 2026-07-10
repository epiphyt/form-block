<?php
declare( strict_types = 1 );

namespace epiphyt\Form_Block\submissions;

/**
 * Registry of submission types.
 * 
 * A submission type is a marker (e.g. "spam") that can be assigned to a
 * submission. Types are registered through the 'form_block_submission_types'
 * filter so features and third-party plugins can add their own without
 * touching the core submission handling.
 * 
 * @since	1.8.0
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Submission_Type {
	/**
	 * Get the label of a registered type.
	 * 
	 * @param	string	$type Type slug
	 * @return	string Type label, or the slug if the type is unknown
	 */
	public static function get_label( string $type ): string {
		return self::get_registered()[ $type ]['label'] ?? $type;
	}
	
	/**
	 * Get all registered submission types.
	 * 
	 * @return	array<string, array{label?: string, hide_from_default?: bool, action_add?: string, action_remove?: string}> Registered types keyed by slug
	 */
	public static function get_registered(): array {
		/**
		 * Filter the registered submission types.
		 * 
		 * Each type is keyed by its slug and provides a label, whether
		 * submissions of this type are hidden from the default list view and
		 * the labels used for the add/remove toggle action.
		 * 
		 * @since	1.8.0
		 * 
		 * @param	array<string, array{label?: string, hide_from_default?: bool, action_add?: string, action_remove?: string}>	$types Registered submission types
		 */
		return (array) \apply_filters( 'form_block_submission_types', [] );
	}
	
	/**
	 * Get the slugs of all types that are hidden from the default list view.
	 * 
	 * @return	string[] List of type slugs
	 */
	public static function hidden_from_default(): array {
		$hidden = [];
		
		foreach ( self::get_registered() as $type => $data ) {
			if ( ! empty( $data['hide_from_default'] ) ) {
				$hidden[] = $type;
			}
		}
		
		return $hidden;
	}
	
	/**
	 * Check whether a type is registered.
	 * 
	 * @param	string	$type Type slug
	 * @return	bool Whether the type is registered
	 */
	public static function is_registered( string $type ): bool {
		return isset( self::get_registered()[ $type ] );
	}
}

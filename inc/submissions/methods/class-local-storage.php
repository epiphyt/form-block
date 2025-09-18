<?php
namespace epiphyt\Form_Block\submissions\methods;

use epiphyt\Form_Block\form_data\Data;
use epiphyt\Form_Block\submissions\Submission_Handler;
use epiphyt\Form_Block\submissions\Submission_Page;

/**
 * Action to store form submissions locally.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Local_Storage {
	public const IDENTIFIER = 'local_storage';
	
	/**
	 * Initialize functionality.
	 */
	public static function init(): void {
		\add_filter( 'form_block_data_form', [ self::class, 'update_form_data' ], 10, 2 );
		\add_filter( 'form_block_submit_data', [ self::class, 'save' ], 10, 4 );
		
		if ( ! empty( Local_Storage::has_submissions() ) ) {
			Submission_Page::init();
		}
	}
	
	/**
	 * Check, whether at least one local submission is available.
	 * 
	 * @param	string	$form_id Form ID
	 * @return	bool Whether at least one local submission is available
	 */
	public static function has_submissions( string $form_id = '' ): bool {
		foreach ( Data::get_form_ids() as $data_form_id ) {
			if ( ! empty( $form_id ) && $form_id !== $data_form_id ) {
				continue;
			}
			
			$form_submissions = \get_option( Submission_Handler::OPTION_KEY_PREFIX . '_' . $data_form_id, [] );
			
			if ( ! empty( $form_submissions ) ) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Check, whether the current form should be stored locally.
	 * 
	 * @param	string	$form_id The form ID
	 * @return	bool Wether the current form should be stored locally
	 */
	public static function is_savable( string $form_id ): bool {
		$form_data = Data::get_instance()->get( $form_id );
		
		if ( isset( $form_data['localStorage'] ) ) {
			return (bool) $form_data['localStorage'];
		}
		
		return true;
	}
	
	/**
	 * Save submission locally.
	 * 
	 * @param	bool[]	$success A list of successful or failed submission methods
	 * @param	string		$form_id The form ID
	 * @param	array		$fields Validated fields
	 * @param	array		$files Files data
	 * @return	bool[] Whether the submission has been saved successfully
	 */
	public static function save( array $success, string $form_id, array $fields, array $files ): array {
		if ( ! self::is_savable( $form_id ) ) {
			return $success;
		}
		
		$success[ self::IDENTIFIER ] = Submission_Handler::create_submission( $form_id, $fields, $files );
		
		return $success;
	}
	
	/**
	 * Update form data before stored in the database.
	 * 
	 * @param	array	$data The current block data that is being stored
	 * @param	array	$block The original block data
	 * @return	array Updated block data
	 */
	public static function update_form_data( array $data, array $block ): array {
		if ( isset( $block['attrs']['methods']['localStorage'] ) ) {
			$data['localStorage'] = (bool) $block['attrs']['methods']['localStorage'];
		}
		else {
			$data['localStorage'] = true;
		}
		
		return $data;
	}
}

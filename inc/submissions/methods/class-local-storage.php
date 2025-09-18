<?php
namespace epiphyt\Form_Block\submissions\methods;

use epiphyt\Form_Block\submissions\Submission_Handler;

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
		\add_filter( 'form_block_submit_data', [ self::class, 'save' ], 10, 4 );
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
		if ( ! \get_option( 'form_block_save_submissions' ) ) {
			return $success;
		}
		
		$success[ self::IDENTIFIER ] = Submission_Handler::create_submission( $form_id, $fields, $files );
		
		return $success;
	}
}

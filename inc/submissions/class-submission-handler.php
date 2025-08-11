<?php
namespace epiphyt\Form_Block\submissions;

use epiphyt\Form_Block\form_data\Data;
use epiphyt\Form_Block\form_data\File;

/**
 * Form submission handler.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Submission_Handler {
	private const OPTION_KEY_PREFIX = 'form_block_submissions';
	
	/**
	 * Create a submission.
	 * 
	 * @param	string	$form_id Form ID
	 * @param	mixed[]	$fields Submitted fields
	 * @param	array{array{field_name: string, name: string, path: string, size: int}}	$files Uploaded files data
	 */
	public static function create_submission( string $form_id, array $fields, array $files ): void {
		if ( ! \get_option( 'form_block_save_submissions' ) ) {
			return;
		}
		
		\add_filter( 'form_block_file_is_saved_locally', '__return_true' );
		
		$form_data = Data::get_instance()->get( $form_id );
		
		foreach ( $files as &$file ) {
			$attachments = [];
			$file = File::get_output( $file, $form_data, $attachments, 'html' );
		}
		
		$data = [
			'fields' => $fields,
			'files' => $files,
		];
		$submission = new Submission( $form_id, $data );
		$form_submissions = self::get_submissions( $form_id );
		$form_submissions[] = $submission;
		
		\update_option( self::OPTION_KEY_PREFIX . '_' . $form_id, $form_submissions );
		\remove_filter( 'form_block_file_is_saved_locally', '__return_true' );
	}
	
	/**
	 * Delete a submission.
	 * 
	 * @param	string	$form_id Form ID
	 * @param	int		$submission_key Key of the submission
	 * @return	bool Whether submission has been deleted
	 */
	public static function delete_submission( string $form_id, int $submission_key ): bool {
		$form_submissions = self::get_submissions( $form_id );
		
		if ( ! isset( $form_submissions[ $submission_key ] ) ) {
			return false;
		}
		
		unset( $form_submissions[ $submission_key ] );
		
		return \update_option( self::OPTION_KEY_PREFIX . '_' . $form_id, $form_submissions );
	}
	
	/**
	 * Get all submissions.
	 * 
	 * @param	string	$form_id Form ID
	 * @return	\epiphyt\Form_Block\submissions\Submission[] List of submissions
	 */
	public static function get_submissions( string $form_id = '' ): array {
		$submissions = [];
		
		foreach ( Data::get_form_ids() as $data_form_id ) {
			if ( ! empty( $form_id ) && $form_id !== $data_form_id ) {
				continue;
			}
			
			$form_submissions = \get_option( self::OPTION_KEY_PREFIX . '_' . $data_form_id, [] );
			
			if ( ! \is_array( $form_submissions ) ) {
				continue;
			}
			
			$submissions += $form_submissions;
		}
		
		return \array_values( $submissions );
	}
}

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
		$formatted_files = [];
		
		foreach ( $files['validated'] as $file_key => $validated_file ) {
			$attachments = [];
			$file = File::get_data( $validated_file, $file_key, $files );
			$formatted_files[] = File::get_output( $file, $form_data, $attachments, 'html' );
		}
		
		$data = [
			'fields' => $fields,
			'files' => $formatted_files,
			'files_local' => $files['local'],
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
		
		if ( ! empty( $form_submissions[ $submission_key ]->get_data( 'files_local' ) ) ) {
			/** @var	\WP_Filesystem_Direct $wp_filesystem */
			global $wp_filesystem;
			
			// initialize the WP filesystem if not exists
			if ( empty( $wp_filesystem ) ) {
			require_once \ABSPATH . 'wp-admin/includes/file.php';
				\WP_Filesystem();
			}
			
			foreach ( $form_submissions[ $submission_key ]->get_data( 'files_local' ) as $local_file ) {
				$wp_filesystem->delete( $local_file['path'] );
				File::delete_hash( $local_file['hash'] );
			}
		}
		
		unset( $form_submissions[ $submission_key ] );
		
		if ( empty( $form_submissions ) ) {
			return \delete_option( self::OPTION_KEY_PREFIX . '_' . $form_id );
		}
		
		return \update_option( self::OPTION_KEY_PREFIX . '_' . $form_id, \array_values( $form_submissions ) );
	}
	
	/**
	 * Get all submissions.
	 * 
	 * @param	string	$form_id Form ID
	 * @return	array<string, \epiphyt\Form_Block\submissions\Submission[]>|\epiphyt\Form_Block\submissions\Submission[] List of submissions
	 */
	public static function get_submissions( string $form_id = '' ): array {
		$submissions = [];
		
		foreach ( Data::get_form_ids() as $data_form_id ) {
			if ( ! empty( $form_id ) && $form_id !== $data_form_id ) {
				continue;
			}
			
			$form_submissions = \get_option( self::OPTION_KEY_PREFIX . '_' . $data_form_id, [] );
			
			if ( ! \is_array( $form_submissions ) || empty( $form_submissions ) ) {
				continue;
			}
			
			$submissions[ $data_form_id ] = $form_submissions;
		}
		
		if ( $form_id && ! empty( $submissions[ $form_id ] ) ) {
			return $submissions[ $form_id ];
		}
		
		return $submissions;
	}
}

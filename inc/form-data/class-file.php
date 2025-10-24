<?php
namespace epiphyt\Form_Block\form_data;

use DateTime;
use epiphyt\Form_Block\Form_Block;
use epiphyt\Form_Block\submissions\methods\Local_Storage;

/**
 * File data class.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class File {
	/**
	 * Initialize the class.
	 */
	public static function init(): void {
		\add_action( 'form_field_attachment_after_add_to_mail', [ self::class, 'reset_add_to_mail_filter' ] );
		\add_action( 'template_redirect', [ self::class, 'get_obfuscated' ] );
		\add_filter( 'form_block_attachment_file_path', [ self::class, 'set_local_file_path' ], 10, 3 );
		\add_filter( 'form_block_output_file_output', [ self::class, 'set_local_file_output' ], 10, 6 );
	}
	
	/**
	 * Delete a hash from the map.
	 * 
	 * @param	string	$hash Hash to delete
	 * @return	bool Whether hash has been deleted
	 */
	public static function delete_hash( string $hash ): bool {
		$map = self::get_hash_map();
		
		if ( ! isset( $map[ $hash ] ) ) {
			return false;
		}
		
		unset( $map[ $hash ] );
		
		return \update_option( 'form_block_local_file_map', $map );
	}
	
	/**
	 * Get local and validated file data for a specific file.
	 * 
	 * @param	array{error: int, full_path: string, name: string, size: int, tmp_name: string, type: string}	$validated_file Specified file
	 * @param	int		$file_key Key of the file in the files array
	 * @param	array{local: array{array{filename?: string, hash?: string, path?: string, url?: string}}, validated: array{array{error: int, full_path: string, name: string, size: int, tmp_name: string, type: string}}|array{}}	$files All files
	 * @return	array{local: array{filename?: string, hash?: string, path?: string, url?: string}, validated: array{error: int, full_path: string, name: string, size: int, tmp_name: string, type: string}|array{}} Local and validated file data
	 */
	public static function get_data( array $validated_file, int $file_key, array $files ): array {
		return [
			'local' => $files['local'][ $file_key ] ?? [],
			'validated' => $validated_file,
		];
	}
	
	/**
	 * Get the file directory and URL.
	 * 
	 * @return	string[] Thumbnail directory and URL
	 */
	public static function get_directory(): array {
		return Form_Block::get_upload_directory( 'files' );
	}
	
	/**
	 * Get information of a file by its associated hash.
	 * 
	 * @param	string	$hash Given hash
	 * @return	array Filename and path of the file associated with the hash
	 */
	public static function get_hash_data( string $hash ): array {
		$map = self::get_hash_map();
		
		if ( empty( $map[ $hash ] ) || ! \file_exists( $map[ $hash ]['path'] ) ) {
			\wp_die( \esc_html__( 'The requested file does not exist.', 'form-block' ) );
		}
		
		return $map[ $hash ];
	}
	
	/**
	 * Get hash mapping data.
	 * 
	 * @return	array Hash mapping data
	 */
	public static function get_hash_map(): array {
		$map = (array) \get_option( 'form_block_local_file_map', [] );
		
		/**
		 * Filter the local file hash map.
		 * 
		 * @since	1.6.0
		 * 
		 * @param	array	$map Current mapping
		 */
		$map = (array) \apply_filters( 'form_block_local_file_map', $map );
		
		return $map;
	}
	
	/**
	 * Get an obfuscated file by a hash parameter.
	 */
	public static function get_obfuscated(): void {
		if ( empty( $_GET['form_block_file'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}
		
		if ( ! \is_user_logged_in() ) {
			\auth_redirect();
		}
		
		$hash = \sanitize_text_field( \wp_unslash( $_GET['form_block_file'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$data = self::get_hash_data( $hash );
		
		\header( 'Content-Disposition: attachment; filename="' . $data['filename'] . '"' );
		\header( 'Content-Length: ' . \filesize( $data['path'] ) );
		\header( 'Content-Type: application/octet-stream' );
		\header( 'Content-Transfer-Encoding: Binary' );
		\ob_clean();
		\ob_flush();
		\readfile( self::get_hash_data( $hash )['path'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile
		exit;
	}
	
	/**
	 * Get output for a file.
	 * 
	 * @param	array{local: array{filename?: string, hash?: string, path?: string, url?: string}, validated: array{field_name: string, name: string, path: string, size: int, type: string}|array{}}	$file File array
	 * @param	mixed[]	$form_data Form data
	 * @param	mixed[]	$attachments Attachments
	 * @param	string	$format_type 'plain' text or 'html'
	 * @return	string File output for display
	 */
	public static function get_output( array $file, array $form_data, array &$attachments, string $format_type = 'plain' ): string {
		$file_data = Field::get_by_name( $file['validated']['field_name'], $form_data['fields'] );
		$new_path = \sys_get_temp_dir() . $file['validated']['name'];
		
		/**
		 * Filter the new path of an uploaded file.
		 * 
		 * @since	1.4.1
		 * 
		 * @param	string	$new_path New path of the file
		 * @param	array	$file Uploaded file information array
		 * @param	array	$file_data Form field data for this file
		 */
		$new_path = \apply_filters( 'form_block_attachment_file_path', $new_path, $file, $file_data );
		
		/**
		 * Whether the file should be added as attachment.
		 * 
		 * @since	1.4.1
		 * 
		 * @param	bool	$add_to_attachments Whether the field should be added
		 * @param	array	$file Uploaded file information array
		 * @param	array	$file_data Form field data for this file
		 */
		$add_to_attachments = \apply_filters( 'form_block_attachment_add_to_mail', true, $file, $file_data );
		
		if ( $add_to_attachments ) {
			$attachments[] = $new_path;
		}
		
		/**
		 * Fires after the file has been added to the mail.
		 * 
		 * @param	array	$file Uploaded file information array
		 * @param	array	$file_data Form field data for this file
		 */
		\do_action( 'form_field_attachment_after_add_to_mail', $file['validated'], $file_data );
		
		/**
		 * Filter the file output.
		 * 
		 * @since	1.4.1
		 * @since	1.6.0 Added parameters $file and $format_type
		 * 
		 * @param	string	$output The field output
		 * @param	string	$name The field name
		 * @param	mixed	$new_path File path
		 * @param	array	$file data File data array
		 * @param	array	$file data File data array
		 * @param	string	$format_type 'plain' text or 'html'
		 */
		$output = \apply_filters( 'form_block_output_file_output', '', $file['validated']['field_name'], $new_path, $file_data, $file, $format_type );
		
		/**
		 * This filter is documented in inc/form-data/class-data.php
		 * 
		 * @since	1.4.1 Added filter for file inputs
		 */
		$output = \apply_filters( 'form_block_output_field_output', $output, $file['validated']['field_name'], $new_path, $form_data, 0, $format_type );
		
		return $output;
	}
	
	/**
	 * Check, whether a file is saved locally.
	 * 
	 * @param	array	$field_data Current field data
	 * @return	bool Whether a file is saved locally
	 */
	public static function is_saved_locally( array $field_data = [] ): bool {
		$is_saved_locally = isset( $field_data['localFiles'] ) && $field_data['localFiles'];
		
		/**
		 * Filter, whether a file is saved locally.
		 * 
		 * @param	bool	$is_saved_locally Whether a file is saved locally
		 * @param	mixed[]	$field_data Current field data
		 */
		$is_saved_locally = (bool) \apply_filters( 'form_block_file_is_saved_locally', $is_saved_locally, $field_data );
		
		return $is_saved_locally;
	}
	
	/**
	 * Reset the filter to add an attachment to the email.
	 */
	public static function reset_add_to_mail_filter(): void {
		\remove_filter( 'form_block_attachment_add_to_mail', '__return_false' );
	}
	
	/**
	 * Save files locally.
	 * 
	 * @param	string	$form_id Current form ID
	 * @param	array{array{field_name: string, name: string, path: string, size: int, type: string}}|array{}	$files List of files
	 * @return	array{array{field_name: string, filename: string, hash: string, path: string, url: string}} Local files data
	 */
	public static function save_local( string $form_id, array $files ): array {
		/** @var ?\WP_Filesystem_Direct $wp_filesystem */
		global $wp_filesystem;
		
		// initialize the WP filesystem if not exists
		if ( empty( $wp_filesystem ) ) {
			require_once \ABSPATH . 'wp-admin/includes/file.php';
			\WP_Filesystem();
		}
		
		$form_data = \get_option( 'form_block_data_' . $form_id, [] );
		$local_files = [];
		
		if ( Local_Storage::is_savable( $form_id ) ) {
			\add_filter( 'form_block_file_is_saved_locally', '__return_true' );
		}
		
		foreach ( $files as $file_key => $file ) {
			$field_data = Field::get_by_name( $file['field_name'], $form_data['fields'] );
			
			if ( ! self::is_saved_locally( $field_data ) ) {
				continue;
			}
			
			$filename = self::set_hashed_filename( $file['name'] );
			$new_path = self::get_directory()['base_dir'] . '/' . $filename;
			$hash = self::set_hash_map( $new_path, $file['name'] );
			$url = \home_url( '?' . \http_build_query( [ 'form_block_file' => $hash ] ) );
			$local_files[ $file_key ] = [
				'field_name' => $file['field_name'],
				'filename' => $file['name'],
				'hash' => $hash,
				'path' => $new_path,
				'url' => $url,
			];
			
			$wp_filesystem->move( $file['path'], $new_path );
		}
		
		if ( Local_Storage::is_savable( $form_id ) ) {
			\remove_filter( 'form_block_file_is_saved_locally', '__return_true' );
		}
		
		return $local_files;
	}
	
	/**
	 * Set a hashed filename of a file.
	 * Basically create an SHA1 hash of the filename, a random hash and the current microtime.
	 * Also add 'bin' file extension.
	 * 
	 * @param	string	$filename Original filename
	 * @return	string Hashed filename
	 */
	public static function set_hashed_filename( string $filename ): string {
		$hash = \bin2hex( \random_bytes( 10 ) );
		
		return \hash( 'sha1', $filename . $hash . \microtime( true ) ) . '.bin';
	}
	
	/**
	 * Set the output for a local file.
	 * 
	 * @param	string	$output Current output
	 * @param	string	$field_name Field name
	 * @param	string	$path Path to file
	 * @param	array	$field_data Field data
	 * @param	array{local: array{filename?: string, hash?: string, path?: string, url?: string}, validated: array{error: int, full_path: string, name: string, size: int, tmp_name: string, type: string}|array{}}	$file File data
	 * @param	string	$format_type 'plain' text or 'html'
	 * @return	string Updated output
	 */
	public static function set_local_file_output( string $output, string $field_name, string $path, array $field_data, array $file, string $format_type ): string {
		if ( ! self::is_saved_locally( $field_data ) ) {
			return $output;
		}
		
		if ( empty( $file['local'] ) ) {
			return $output;
		}
		
		if ( $format_type === 'html' ) {
			$output = '<dt>' . Field::get_title_by_name( $field_name, [ $field_data ] ) . ':</dt>';
		}
		else {
			$output = Field::get_title_by_name( $field_name, [ $field_data ] ) . ': ';
		}
		
		if ( $format_type === 'html' ) {
			/* translators: Filename */
			$output .= '<dd><a href="' . \esc_url( $file['local']['url'] ?? '' ) . '">' . \sprintf( \esc_html__( 'Download %s', 'form-block' ), \esc_html( $file['validated']['name'] ) ) . '</a></dd>';
		}
		else {
			$output .= $file['local']['url'] ?? '';
		}
		
		return $output;
	}
	
	/**
	 * Set the path for a local file.
	 * 
	 * @param	string	$path Current path
	 * @param	array	$file File array
	 * @param	array	$field_data Field data array
	 * @return	string Updated path
	 */
	public static function set_local_file_path( string $path, array $file, array $field_data ): string {
		if ( ! self::is_saved_locally( $field_data ) ) {
			return $path;
		}
		
		if ( empty( $file['local']['path'] ) ) {
			return $path;
		}
		
		\add_filter( 'form_block_attachment_add_to_mail', '__return_false' );
		
		return $file['local']['path'];
	}
	
	/**
	 * Set a mapping of a file path to a hash.
	 * 
	 * @param	string	$path File path
	 * @param	string	$filename File name
	 * @return	string Associated hash of the file path
	 */
	public static function set_hash_map( string $path, string $filename ): string {
		$map = self::get_hash_map();
		$random = \bin2hex( \random_bytes( 10 ) );
		
		while ( isset( $map[ $random ] ) ) {
			$random = \bin2hex( \random_bytes( 10 ) );
		}
		
		$map[ $random ] = [
			'filename' => $filename,
			'path' => $path,
			'uploaded' => new DateTime( 'now', \wp_timezone() ),
		];
		
		\update_option( 'form_block_local_file_map', $map );
		
		return $random;
	}
}

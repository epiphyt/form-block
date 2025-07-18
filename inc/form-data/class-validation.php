<?php
namespace epiphyt\Form_Block\form_data;

use epiphyt\Form_Block\Form_Block;
use epiphyt\Form_Block\util\Array_Operations;

/**
 * Form data class.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Validation {
	/**
	 * @var		\epiphyt\Form_Block\form_data\Validation
	 */
	public static ?self $instance = null;
	
	/**
	 * @since	1.0.2
	 * @var		array List of field names used by the system
	 */
	private array $system_field_names = [
		'_form_id',
		'_town',
		'_wpnonce',
		'action',
	];
	
	/**
	 * Validate form fields by allowed names.
	 * 
	 * @param	string	$name The field name
	 * @param	array	$form_data The form data
	 */
	private function by_allowed_names( string $name, array $form_data ): void {
		$allowed_names = $this->get_allowed_names( $form_data );
		$name = \preg_replace( '/-\d+$/', '', $name );
		
		if ( \in_array( $name, $allowed_names, true ) ) {
			return;
		}
		
		$post_field = $_POST[ $name ] ?? null; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Missing
		
		if ( \is_array( $post_field ) ) {
			$name_regex = \preg_quote( $name, '/' );
			$name_regex .= self::get_allowed_subfield_name_regex( $post_field );
			
			if ( ! empty( \preg_grep( '/' . $name_regex . '/', $allowed_names ) ) ) {
				return;
			}
		}
		
		\wp_send_json_error( [
			'message' => \sprintf(
				/* translators: field title */
				\esc_html__( 'The following field is not allowed: %s', 'form-block' ),
				\esc_html( Field::get_title_by_name( $name, $form_data['fields'] ) )
			),
		] );
	}
	
	/**
	 * Validate form fields by field data.
	 * 
	 * @param	string	$form_id The form ID
	 * @param	array	$fields Field data from request
	 * @return	array List of validation errors
	 */
	private function by_field_data( string $form_id, array $fields ): array {
		$form_data = Data::get_instance()->get( $form_id );
		$errors = $this->get_errors( $fields, $form_data );
		
		/**
		 * Filter the field data errors.
		 * 
		 * @param	array	$errors Current detected errors
		 * @param	array	$form_data Current form data to validate
		 * @param	array	$fields Field data from request
		 * @param	string	$form_id Current form ID
		 */
		$errors = \apply_filters( 'form_block_field_data_errors', $errors, $form_data, $fields, $form_id );
		
		return $errors;
	}
	
	/**
	 * Validate a field value by its field attributes.
	 * 
	 * @param	mixed	$value The field value
	 * @param	array	$attributes Form field attributes
	 * @return	array List of validation errors
	 */
	private function by_attributes( mixed $value, array $attributes ): array {
		$errors = [];
		
		foreach ( $attributes as $attribute => $attribute_value ) {
			switch ( $attribute ) {
				case 'block_type':
					switch ( $attribute_value ) {
						case 'textarea':
							$validated = \sanitize_textarea_field( $value );
							break;
						default:
							if ( \is_array( $value ) ) {
								$validated = \array_map( 'sanitize_text_field', $value );
							}
							else {
								$validated = \sanitize_text_field( $value );
							}
							break;
					}
					
					if ( $value !== $validated ) {
						$errors[] = [
							'message' => \__( 'The entered value is invalid.', 'form-block' ),
							'type' => $attribute,
						];
					}
					break;
				case 'disabled':
				case 'readonly':
					if (
						( ! empty( $attributes['value'] ) && $attributes['value'] !== $value )
						|| ( empty( $attributes['value'] ) && ! empty( $value ) )
					) {
						$errors[] = [
							'message' => \__( 'The value must not change.', 'form-block' ),
							'type' => $attribute,
						];
					}
					break;
			}
		}
		
		/**
		 * Filter the validation by field attributes.
		 * 
		 * @param	array	$errors Current error list
		 * @param	mixed	$value The field value
		 * @param	array	$attributes Form field attributes
		 */
		$errors = \apply_filters( 'form_block_field_attributes_validation', $errors, $value, $attributes );
		
		return $errors;
	}
	
	/**
	 * Get all allowed field names of a field and its sub fields.
	 * 
	 * @param	mixed[]	$field Field data
	 * @return	string[] List of allowed field names
	 */
	private static function get_allowed_field_names( array $field ): array {
		$field_name = Form_Block::get_instance()->get_block_name_attribute( $field );
		$allowed_names[] = \preg_replace( '/-\d+$/', '', $field_name );
		$allowed_names[] = \preg_replace( '/\[([^\]]*)\]/', '', $field_name );
		
		if ( ! empty( $field['fields'] ) ) {
			foreach ( $field['fields'] as $sub_field ) {
				$allowed_names = \array_merge( $allowed_names, self::get_allowed_field_names( $sub_field ) );
			}
		}
		
		return $allowed_names;
	}
	
	/**
	 * Get all allowed name attributes without their unique -\d+ part.
	 * 
	 * @param	array	$form_data Current form data to validate
	 * @return	array List of allowed name attributes
	 */
	private function get_allowed_names( array $form_data ): array {
		Form_Block::get_instance()->reset_block_name_attributes();
		
		$allowed_names = $this->system_field_names;
		$fields = $form_data['fields'];
		
		foreach ( $fields as $field ) {
			$allowed_names = \array_merge( $allowed_names, self::get_allowed_field_names( $field ) );
		}
		
		return \array_unique( $allowed_names );
	}
	
	/**
	 * Get allowed field name regular expression including subfields.
	 * 
	 * @param	mixed[]	$field Current post field
	 * @return	string Regular expression for current field
	 */
	private static function get_allowed_subfield_name_regex( array $field ): string {
		foreach ( $field as $key => $field_value ) {
			$name = '\[(' . \preg_quote( (string) $key, '/' ) . '*)\]';
			
			if ( \is_array( $field_value ) ) {
				foreach ( $field_value as $subfield_key => $subfield_value ) {
					$name .= self::get_allowed_subfield_name_regex( [ $subfield_key => $subfield_value ] );
				}
			}
		}
		
		return $name;
	}
	
	/**
	 * Recursively sanitize array values.
	 * 
	 * @param	array	$array Array to sanitize
	 * @param	array	$form_data Form field data
	 * @return	array Sanitized array
	 */
	private static function sanitize_array_values( array $array, array $form_data ): array {
		foreach ( $array as $key => &$value ) {
			if ( \is_array( $value ) ) {
				$value = self::sanitize_array_values( $value, $form_data );
			}
			else if ( \is_string( $value ) ) {
				$value = \sanitize_textarea_field( \wp_unslash( $value ) );
			}
			else {
				// unknown format, ignore
				\wp_send_json_error( [
					'message' => \sprintf(
						/* translators: the field name */
						\esc_html__( 'Wrong item format in field %s.', 'form-block' ),
						\esc_html( Field::get_title_by_name( $key, $form_data['fields'] ) )
					),
				] );
			}
		}
		
		return $array;
	}
	
	/**
	 * Validate all POST fields.
	 * 
	 * @return	array The validated fields
	 */
	public function fields(): array {
		$form_data = \get_option( 'form_block_data_' . Data::get_instance()->get_form_id(), [] );
		$validated = [];
		
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		foreach ( $_POST as $key => $value ) {
			// sanitize_key() but with support for uppercase
			$key = \preg_replace( '/[^A-Za-z0-9_\-]/', '', \wp_unslash( $key ) );
			
			$this->by_allowed_names( $key, $form_data );
			
			if ( \is_array( $value ) ) {
				$value = Array_Operations::filter_recursive( $value );
				$value = self::sanitize_array_values( $value, $form_data );
			}
			else {
				// if it's not a string, die with an error message
				if ( ! \is_string( $value ) ) {
					\wp_send_json_error( [
						'message' => \sprintf(
							/* translators: the field name */
							\esc_html__( 'Wrong item format in field %s.', 'form-block' ),
							\esc_html( Field::get_title_by_name( $key, $form_data['fields'] ) )
						),
					] );
				}
				
				$value = \sanitize_textarea_field( \wp_unslash( $value ) );
			}
			
			$validated[ $key ] = $value;
		}
		// phpcs:enable
		
		foreach ( $this->system_field_names as $name ) {
			unset( $validated[ $name ] );
		}
		
		// remove empty fields
		foreach ( $validated as $key => $value ) {
			if ( ! empty( $value ) ) {
				continue;
			}
			
			unset( $validated[ $key ] );
		}
		
		/**
		 * Filter the validated fields.
		 * 
		 * @param	array	$validated The validated fields
		 * @param	string	$form_id The form ID
		 * @param	array	$form_data The form data
		 */
		$validated = (array) \apply_filters( 'form_block_validated_fields', $validated, Data::get_instance()->get_form_id(), $form_data );
		
		$required_fields = Data::get_instance()->get_required_fields( Data::get_instance()->get_form_id(), $validated );
		
		// check all required fields
		$missing_fields = [];
		
		// iterate through all required
		foreach ( $required_fields as $field_name ) {
			// check if a field with this identifier is empty
			// and if it's not a file upload
			// phpcs:disable WordPress.Security.NonceVerification.Missing
			if (
				(
					empty( $_FILES[ $field_name ]['tmp_name'] )
					|| ( \is_array( $_FILES[ $field_name ]['tmp_name'] ) && empty( \array_filter( $_FILES[ $field_name ]['tmp_name'] ) ) ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash
				)
				&& empty( $validated[ $field_name ] )
			) {
				$missing_fields[ $field_name ] = Field::get_title_by_name( $field_name, $form_data['fields'] );
			}
			// phpcs:enable WordPress.Security.NonceVerification.Missing
		}
		
		/**
		 * Filter missing fields.
		 * 
		 * @since	1.5.2
		 * 
		 * @param	string[]	$missing_fields List of missing field names/titles
		 * @param	mixed[]		$form_data Form data
		 * @param	mixed[]		$validated List of validated field names and values
		 * @param	string[]	$required_fields List of required field names
		 */
		$missing_fields = (array) \apply_filters( 'form_block_missing_fields', $missing_fields, $form_data, $validated, $required_fields );
		
		// output error if there are missing fields
		if ( ! empty( $missing_fields ) ) {
			\wp_send_json_error( [
				'message' => \sprintf(
					/* translators: missing fields */
					\esc_html( \_n( 'The following field is missing: %s', 'The following fields are missing: %s', \count( $missing_fields ), 'form-block' ) ),
					\esc_html( \implode( ', ', $missing_fields ) )
				),
			] );
		}
		
		$field_data_errors = $this->by_field_data( Data::get_instance()->get_form_id(), $validated );
		
		if ( ! empty( $field_data_errors ) ) {
			$message = '';
			
			foreach ( $field_data_errors as $field_errors ) {
				$message .= \esc_html( $field_errors['field_title'] ) . ': ';
				
				foreach ( $field_errors['errors'] as $error ) {
					$message .= \esc_html( $error['message'] );
				}
				
				$message .= \PHP_EOL;
			}
			
			\wp_send_json_error( [
				'message' => $message,
			] );
		}
		
		return $validated;
	}
	
	/**
	 * Validate all files.
	 * 
	 * @return	array The validated files
	 */
	public function files(): array {
		$form_data = \get_option( 'form_block_data_' . Data::get_instance()->get_form_id(), [] );
		$validated = [];
		
		if ( empty( $_FILES ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return $validated;
		}
		
		$filesize = 0;
		$maximum_file_size = \wp_convert_hr_to_bytes( \ini_get( 'upload_max_filesize' ) );
		$maximum_post_size = \wp_convert_hr_to_bytes( \ini_get( 'post_max_size' ) );
		$maximum_upload_size = \max( $maximum_file_size, $maximum_post_size );
		
		if ( isset( $_SERVER['CONTENT_LENGTH'] ) || isset( $_SERVER['HTTP_CONTENT_LENGTH'] ) ) {
			$content_length = (int) \sanitize_text_field( \wp_unslash( $_SERVER['CONTENT_LENGTH'] ?? $_SERVER['HTTP_CONTENT_LENGTH'] ?? $maximum_upload_size ) );
			
			if ( $content_length >= $maximum_upload_size ) {
				\wp_send_json_error( [
					'message' => \esc_html__( 'The uploaded file(s) are too big.', 'form-block' ),
				] );
			}
		}
		
		foreach ( $_FILES as $field_name => $files ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$this->by_allowed_names( $field_name, $form_data );
			
			if ( \is_array( $files['name'] ) ) {
				// if multiple files, resort
				$files = Data::get_instance()->unify_files_array( $files );
				
				foreach ( $files as $file ) {
					if ( empty( $file['tmp_name'] ) ) {
						continue;
					}
					
					self::validate_file_type( $file );
					
					$filesize += $file['size'];
					$validated[] = [
						'field_name' => $field_name,
						'name' => $file['name'],
						'path' => $file['tmp_name'],
						'size' => $file['size'],
						'type' => $file['type'],
					];
				}
			}
			else if ( ! empty( $files['tmp_name'] ) ) {
				self::validate_file_type( $files );
				
				$filesize += $files['size'];
				$validated[] = [
					'field_name' => $field_name,
					'name' => $files['name'],
					'path' => $files['tmp_name'],
					'size' => $files['size'],
					'type' => $files['type'],
				];
			}
		}
		
		if ( $filesize > \wp_max_upload_size() ) {
			\wp_send_json_error( [
				'message' => \esc_html__( 'The uploaded file(s) are too big.', 'form-block' ),
			] );
		}
		
		/**
		 * Filter validated files.
		 * 
		 * @since	1.0.3
		 * 
		 * @param	array	$validated Validated files data
		 * @param	array	$form_data Current form data
		 * @param	array	$_FILES PHP files array
		 */
		$validated = \apply_filters( 'form_block_files_validation', $validated, $form_data, $_FILES ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		
		return $validated;
	}
	
	/**
	 * Get validation errors by field data attributes.
	 * 
	 * @param	array	$fields Given fields from request
	 * @param	array	$form_data Form data
	 * @return	array A list of errors
	 */
	public function get_errors( array $fields, array $form_data ): array {
		$errors = [];
		
		if ( empty( $form_data['fields'] ) ) {
			return $errors;
		}
		
		foreach ( $form_data['fields'] as $field ) {
			foreach ( $fields as $name => $value ) {
				$field_title = '';
				
				if ( empty( $field['name'] ) ) {
					$field_title = Field::get_title_by_name( $name, $form_data['fields'] );
					
					if ( empty( $field['label'] ) || $field_title !== $field['label'] ) {
						continue;
					}
				}
				else if ( $field['name'] === $name ) {
					$field_title = $field['label'];
				}
				
				if ( empty( $field_title ) ) {
					continue;
				}
				
				$field_errors = $this->by_attributes( $value, $field );
				
				if ( ! empty( $field_errors ) ) {
					$errors[ $name ] = [
						'errors' => $field_errors,
						'field_title' => $field_title,
					];
				}
			}
		}
		
		return $errors;
	}
	
	/**
	 * Get a unique instance of the class.
	 * 
	 * @return	\epiphyt\Form_Block\form_data\Validation The single instance of this class
	 */
	public static function get_instance(): self {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * Validate a given file type with the actual file.
	 * Either exits with a JSON error code or allows further processing.
	 * 
	 * @param	array{error: int, full_path: string, name: string, size: int, tmp_name: string, type: string}	$file File to validate
	 */
	private static function validate_file_type( array $file ): void {
		if ( $file['size'] > \wp_max_upload_size() ) {
			\wp_send_json_error( [
				'message' => \esc_html__( 'The uploaded file is too big.', 'form-block' ),
			], 413 );
		}
		
		// allow all known mime types
		if ( \is_multisite() ) {
			\remove_filter( 'upload_mimes', 'check_upload_mimes' );
		}
		
		$allowed_mime_types = \wp_get_mime_types();
		
		if ( \is_multisite() ) {
			\add_filter( 'upload_mimes', 'check_upload_mimes' );
		}
		
		/**
		 * Filter allowed mime types to upload.
		 * 
		 * @param	string[]	$allowed_mime_types List of allowed mime types
		 * @param	array{error: int, full_path: string, name: string, size: int, tmp_name: string, type: string} $file Current file to validate
		 */
		$allowed_mime_types = (array) \apply_filters( 'form_block_validate_file_type_mime_types', $allowed_mime_types, $file );
		
		$wp_filetype = \wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], $allowed_mime_types );
		
		if ( $wp_filetype['ext'] === false || $wp_filetype['type'] === false ) {
			\wp_send_json_error( [
				'message' => \esc_html__( 'The uploaded file type is not supported.', 'form-block' ),
			], 415 );
		}
		
		if ( $wp_filetype['type'] !== $file['type'] ) {
			\wp_send_json_error( [
				'message' => \esc_html__( 'The uploaded file has an invalid type.', 'form-block' ),
			], 415 );
		}
	}
}

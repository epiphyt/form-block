<?php
namespace epiphyt\Form_Block\form_data;

use epiphyt\Form_Block\block_data\Data as BlockDataData;
use epiphyt\Form_Block\Form_Block;

/**
 * Form data class.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Data {
	/**
	 * @var		string The form ID
	 */
	private $form_id = '';
	
	/**
	 * @var		\epiphyt\Form_Block\form_data\Data
	 */
	public static $instance;
	
	/**
	 * Initialize the class.
	 */
	public function init(): void {
		add_action( 'wp_ajax_form-block-submit', [ $this, 'get_request' ] );
		add_action( 'wp_ajax_nopriv_form-block-submit', [ $this, 'get_request' ] );
	}
	
	/**
	 * Get form data.
	 *
	 * @param	string	$form_id Current form ID
	 * @return	array Form data
	 */
	public function get( string $form_id = '' ): array {
		if ( ! $form_id ) {
			$form_id = $this->form_id;
		}
		
		if ( ! $form_id ) {
			return [];
		}
		
		return (array) get_option( 'form_block_data_' . $form_id, [] );
	}
	
	/**
	 * Get the field title of a list of fields by its name.
	 * 
	 * @param	string	$name The name to search for
	 * @param	array	$fields The fields to search in
	 * @return	string The field title or the field name, if title cannot be found
	 */
	private function get_field_title_by_name( string $name, array $fields ): string {
		foreach ( $fields as $field ) {
			$field_name = Form_Block::get_instance()->get_block_name_attribute( $field );
			
			if ( $field_name === $name ) {
				return $field['label'] ?? $name;
			}
		}
		
		return $name;
	}
	
	/**
	 * Get a unique instance of the class.
	 * 
	 * @return	\epiphyt\Form_Block\form_data\Data The single instance of this class
	 */
	public static function get_instance(): Data {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * Get the request data.
	 */
	public function get_request(): void {
		if ( ! isset( $_POST['_form_id'] ) || ! isset( $_POST['_town'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			/**
			 * Fires after a request is considered invalid.
			 */
			do_action( 'form_block_invalid_data' );
			
			// explicitly return success so that bots cannot learn
			wp_send_json_success();
		}
		
		if ( $this->is_honeypot_filled() ) {
			/**
			 * Fires after a request is considered invalid due to a filled honeypot.
			 */
			do_action( 'form_block_is_honeypot_filled' );
			
			// explicitly return success so that bots cannot learn
			wp_send_json_success();
		}
		
		$this->form_id = sanitize_text_field( wp_unslash( $_POST['_form_id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		
		/**
		 * Fires before data has been validated.
		 * 
		 * @param	string	$form_id The form ID
		 */
		do_action( 'form_block_pre_validated_data', $this->form_id );
		
		$fields = $this->validate_fields();
		$files = $this->validate_files();
		
		/**
		 * Fires after data has been validated.
		 * 
		 * @param	string	$form_id The form ID
		 * @param	array	$fields Validated fields
		 * @param	array	$files Validated files
		 */
		do_action( 'form_block_validated_data', $this->form_id, $fields, $files );
		
		// TODO: send data
		die( var_dump( $_POST ) );
	}
	
	/**
	 * Get all required fields of a form.
	 *
	 * @param	string	$form_id Current form ID
	 * @return	array List of required field names
	 */
	public function get_required_fields( string $form_id = '' ): array {
		if ( ! $form_id ) {
			$form_id = $this->form_id;
		}
		
		if ( ! $form_id ) {
			return [];
		}
		
		$data = $this->get( $form_id );
		$required = [];
		
		foreach ( $data['fields'] as $field ) {
			if ( ! isset( $field['required'] ) ) {
				continue;
			}
			
			$required[] = Form_Block::get_instance()->get_block_name_attribute( $field );
		}
		
		return $required;
	}
	
	/**
	 * Check whether the honeypot is filled.
	 *
	 * @return	boolean Wether the honeypot is filled
	 */
	private function is_honeypot_filled(): bool {
		$honeypot_key = '_town';
		$is_filled = false;
		
		/**
		 * Filter the honeypot key.
		 * 
		 * @param	string	$honeypot_key The default key '_town'
		 */
		$honeypot_key = apply_filters( 'form_block_honeypot_key', $honeypot_key );
		
		$is_filled = ! empty( $_POST[ $honeypot_key ] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		
		/**
		 * Filter whether the honeypot is filled.
		 * 
		 * @param	bool	$is_filled Whethter the honeypot is filled.
		 */
		$is_filled = apply_filters( 'form_block_is_honeypot_filled', $is_filled );
		
		return $is_filled;
	}
	
	/**
	 * Unify the $_FILES-formatted array.
	 * 
	 * @param	array	$file_post The $_FILES-formatted array
	 * @return	array The new formatted array
	 */
	private function unify_files_array( array $file_post ): array {
		$file_ary = [];
		$file_count = count( $file_post['name'] );
		$file_keys = array_keys( $file_post );
		
		for ( $i = 0; $i < $file_count; $i++ ) {
			foreach ( $file_keys as $key ) {
				$file_ary[ $i ][ $key ] = $file_post[ $key ][ $i ];
			}
		}
		
		return $file_ary;
	}
	
	/**
	 * Validate all POST fields.
	 * 
	 * @return	array The validated fields
	 */
	private function validate_fields(): array {
		$form_data = get_option( 'form_block_data_' . $this->form_id, [] );
		$validated = [];
		
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		foreach ( $_POST as $key => $value ) {
			// sanitize_key() but with support for uppercase
			$key = preg_replace( '/[^A-Za-z0-9_\-]/', '', wp_unslash( $key ) );
			
			// iterate through an array to sanitize its fields
			if ( is_array( $value ) ) {
				foreach ( $value as $item_key => &$item ) {
					// if it's not a string, die with an error message
					if ( ! is_string( $item ) ) {
						/* translators: 1: the value name, 2: the field name */
						wp_die( sprintf( esc_html__( 'Wrong item format of value %1$s in field %2$s.', 'form-block' ), esc_html( $item_key ), esc_html( $this->get_field_title_by_name( $key, $form_data['fields'] ) ) ) );
					}
					
					$item = sanitize_textarea_field( wp_unslash( $item ) );
				}
			}
			else {
				// if it's not a string, die with an error message
				if ( ! is_string( $value ) ) {
					/* translators: the field name */
					wp_die( sprintf( esc_html__( 'Wrong item format in field %s.', 'form-block' ), esc_html( $this->get_field_title_by_name( $key, $form_data['fields'] ) ) ) );
				}
				
				$value = sanitize_textarea_field( wp_unslash( $value ) );
			}
			
			$validated[ $key ] = $value;
		}
		// phpcs:enable
		
		unset( $validated['_form_id'], $validated['action'], $validated['_town'] );
		
		// remove empty fields
		foreach ( $validated as $key => $value ) {
			if ( ! empty( $value ) ) {
				continue;
			}
			
			unset( $validated[ $key ] );
		}
		
		// TODO: validate according to attributes
		
		/**
		 * Filter the validated fields.
		 * 
		 * @param	array	$validated The validated fields
		 * @param	string	$form_id The form ID
		 * @param	array	$form_data The form data
		 */
		$validated = apply_filters( 'form_block_validated_fields', $validated, $this->form_id, $form_data );
		
		$required_fields = $this->get_required_fields( $this->form_id );
		
		// check all required fields
		$missing_fields = [];
		
		// iterate through all required
		foreach ( $required_fields as $field_name ) {
			// check if a field with this identifier is empty
			// and if it's not a file upload
			if (
				(
					empty( $_FILES[ $field_name ]['tmp_name'] )
					|| is_array( $_FILES[ $field_name ]['tmp_name'] ) && empty( array_filter( $_FILES[ $field_name ]['tmp_name'] ) ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash
				)
				&& empty( $validated[ $field_name ] )
			) {
				$missing_fields[] = $this->get_field_title_by_name( $field_name, $form_data['fields'] );
			}
		}
		
		// output error if there are missing fields
		if ( ! empty( $missing_fields ) ) {
			wp_die(
				sprintf(
					/* translators: missing fields */
					esc_html( _n( 'The following field is missing: %s', 'The following fields are missing: %s', count( $missing_fields ), 'form-block' ) ),
					esc_html( implode( ', ', $missing_fields ) )
				)
			);
		}
		
		return $validated;
	}
	
	/**
	 * Validate all files.
	 * 
	 * @return	array The validated files
	 */
	private function validate_files(): array {
		$validated = [];
		
		if ( empty( $_FILES ) ) {
			return $validated;
		}
		
		foreach ( $_FILES as $files ) {
			if ( is_array( $files['name'] ) ) {
				// if multiple files, resort
				$files = $this->unify_files_array( $files );
				
				foreach ( $files as $file ) {
					if ( empty( $file['tmp_name'] ) ) {
						continue;
					}
					
					// phpcs:disable WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
					$file_content = file_get_contents( $file['tmp_name'] );
					// phpcs:enable
					$validated[] = [
						'name' => $file['name'],
						// phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
						'content' => base64_encode( $file_content ),
						// phpcs:enable
					];
				}
			}
			else if ( ! empty( $files['tmp_name'] ) ) {
				// phpcs:disable WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
				$file_content = file_get_contents( $files['tmp_name'] );
				// phpcs:enable
				$validated[] = [
					'name' => $files['name'],
					// phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
					'content' => base64_encode( $file_content ),
					// phpcs:enable
				];
			}
		}
		
		return $validated;
	}
}

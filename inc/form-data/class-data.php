<?php
namespace epiphyt\Form_Block\form_data;

use epiphyt\Form_Block\Form_Block;
use epiphyt\Form_Block\submissions\methods\Email;

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
	private string $form_id = '';
	
	/**
	 * @var		\epiphyt\Form_Block\form_data\Data
	 */
	public static ?self $instance = null;
	
	/**
	 * Initialize the class.
	 */
	public function init(): void {
		\add_action( 'wp_ajax_form-block-create-nonce', [ $this, 'create_nonce' ] );
		\add_action( 'wp_ajax_form-block-submit', [ $this, 'get_request' ] );
		\add_action( 'wp_ajax_nopriv_form-block-create-nonce', [ $this, 'create_nonce' ] );
		\add_action( 'wp_ajax_nopriv_form-block-submit', [ $this, 'get_request' ] );
	}
	
	/**
	 * Create a nonce via Ajax.
	 * 
	 * @since	1.0.2
	 */
	public function create_nonce(): void {
		if ( empty( $_POST['form_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			\wp_send_json_error(
				[
					'message' => \__( 'The form could not be prepared to submit requests. Please reload the page.', 'form-block' ),
				]
			);
		}
		
		$id = \sanitize_text_field( \wp_unslash( $_POST['form_id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		
		if ( ! $this->is_valid_form_id( $id ) ) {
			\wp_send_json_error();
		}
		
		\wp_send_json_success(
			[
				'nonce' => \wp_create_nonce( 'form_block_submit_' . $id ),
			],
			201
		);
	}
	
	/**
	 * Get form data.
	 * 
	 * @param	string	$form_id Current form ID
	 * @return	array<string, mixed> Form data
	 */
	public function get( string $form_id = '' ): array {
		if ( ! $form_id ) {
			$form_id = $this->form_id;
		}
		
		if ( ! $form_id ) {
			return [];
		}
		
		return (array) \get_option( 'form_block_data_' . $form_id, [] );
	}
	
	/**
	 * Get the field data of a list of fields by its name.
	 * 
	 * @deprecated	1.5.0 Use epiphyt\Form_Block\form_data\Field::get_by_name() instead
	 * 
	 * @param	string	$name The name to search for
	 * @param	array	$fields The fields to search in
	 * @return	array The field data
	 */
	public function get_field_data_by_name( string $name, array $fields ): array {
		\_doing_it_wrong(
			__METHOD__,
			\sprintf(
				/* translators: alternative method */
				\esc_html__( 'Use %s instead', 'form-block' ),
				'epiphyt\Form_Block\form_data\Field::get_by_name()'
			),
			'1.5.0'
		);
		
		return Field::get_by_name( $name, $fields );
	}
	
	/**
	 * Get the field title of a list of fields by its name.
	 * 
	 * @deprecated	1.5.0 Use epiphyt\Form_Block\form_data\Field::get_title_by_name() instead
	 * 
	 * @param	string	$name The name to search for
	 * @param	array	$fields The fields to search in
	 * @param	bool	$reset_name_attributes Whether to reset the block name attributes
	 * @return	string The field title or the field name, if title cannot be found
	 */
	public function get_field_title_by_name( string $name, array $fields, bool $reset_name_attributes = true ): string {
		\_doing_it_wrong(
			__METHOD__,
			\sprintf(
				/* translators: alternative method */
				\esc_html__( 'Use %s instead', 'form-block' ),
				'epiphyt\Form_Block\form_data\Field::get_title_by_name()'
			),
			'1.5.0'
		);
		
		return Field::get_title_by_name( $name, $fields, $reset_name_attributes );
	}
	
	/**
	 * Get the form ID.
	 * 
	 * @return	string The form ID
	 */
	public function get_form_id(): string {
		return $this->form_id;
	}
	
	/**
	 * Get a list of registered form IDs.
	 * 
	 * @return	string[] List of form IDs
	 */
	public static function get_form_ids(): array {
		$form_ids = \get_option( 'form_block_form_ids', [] );
		
		if ( ! \is_array( $form_ids ) ) {
			$form_ids = [];
		}
		
		return \array_keys( $form_ids );
	}
	
	/**
	 * Get a unique instance of the class.
	 * 
	 * @return	\epiphyt\Form_Block\form_data\Data The single instance of this class
	 */
	public static function get_instance(): self {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * Get the request data.
	 */
	public function get_request(): void {
		if ( empty( $_POST['_wpnonce'] ) ) {
			/**
			 * Fires after verifying that the nonce is empty or absent.
			 */
			\do_action( 'form_block_empty_nonce' );
			
			// explicitly return success so that bad actors cannot learn
			\wp_send_json_success();
		}
		
		if ( ! isset( $_POST['_form_id'] ) || ! isset( $_POST['_town'] ) ) {
			/**
			 * Fires after a request is considered invalid.
			 */
			\do_action( 'form_block_invalid_data' );
			
			// explicitly return success so that bots cannot learn
			\wp_send_json_success();
		}
		
		$this->form_id = \sanitize_text_field( \wp_unslash( $_POST['_form_id'] ) );
		
		if ( ! \wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_POST['_wpnonce'] ) ), 'form_block_submit_' . $this->form_id ) ) {
			/**
			 * Fires after a request has an invalid nonce.
			 */
			\do_action( 'form_block_invalid_nonce' );
			
			// explicitly return success so that bad actors cannot learn
			\wp_send_json_success();
		}
		
		if ( $this->is_honeypot_filled() ) {
			/**
			 * Fires after a request is considered invalid due to a filled honeypot.
			 */
			\do_action( 'form_block_is_honeypot_filled' );
			
			// explicitly return success so that bots cannot learn
			\wp_send_json_success();
		}
		
		/**
		 * Fires before data has been validated.
		 * 
		 * @param	string	$form_id The form ID
		 */
		\do_action( 'form_block_pre_validated_data', $this->form_id );
		
		$fields = Validation::get_instance()->fields();
		$validated_files = Validation::get_instance()->files();
		$files = [
			'local' => File::save_local( $this->form_id, $validated_files ),
			'validated' => $validated_files,
		];
		
		/**
		 * Fires after data has been validated.
		 * 
		 * @since	1.6.0 Added parameter $local_files
		 * 
		 * @param	string	$form_id The form ID
		 * @param	array	$fields Validated fields
		 * @param	array	$validated_files Validated files
		 * @param	array	$local_files Local files data
		 */
		\do_action( 'form_block_validated_data', $this->form_id, $fields, $validated_files, $files['local'] );
		
		/**
		 * Fires when form data is ready to be submitted.
		 * 
		 * @since	1.6.0
		 * 
		 * @param	bool[]	$success A list of successful or failed submission methods
		 * @param	string		$form_id The form ID
		 * @param	array		$fields Validated fields
		 * @param	array		$files Files data
		 */
		$success = (array) \apply_filters( 'form_block_submit_data', [], $this->form_id, $fields, $files );
		
		if ( \in_array( false, \array_values( $success ), true ) ) {
			\wp_send_json_error( [
				'message' => \esc_html__( 'Form submission failed for at least one recipient.', 'form-block' ),
			] );
		}
		
		/**
		 * Filter the submit success data.
		 * 
		 * @since	1.0.3
		 * 
		 * @param	array|null	$data Current data
		 * @param	string		$form_id Current form ID
		 */
		$data = \apply_filters( 'form_block_submit_success_data', null, $this->form_id );
		
		\wp_send_json_success( $data );
	}
	
	/**
	 * Get all required fields of a form.
	 * 
	 * @param	string	$form_id Current form ID
	 * @param	array	$post_fields Submitted form fields
	 * @param	array	$fields_to_check Optional fields to check
	 * @return	array List of required field names
	 */
	public function get_required_fields( string $form_id = '', array $post_fields = [], array $fields_to_check = [] ): array {
		if ( ! $form_id ) {
			$form_id = $this->form_id;
		}
		
		if ( ! $form_id ) {
			return [];
		}
		
		Form_Block::get_instance()->reset_block_name_attributes();
		
		$data = $this->get( $form_id );
		
		if ( empty( $fields_to_check ) ) {
			$fields_to_check = $data['fields'];
		}
		
		$required = [];
		
		foreach ( $fields_to_check as $field ) {
			if ( ! empty( $field['fields'] ) ) {
				$required = \array_merge( $required, $this->get_required_fields( $form_id, $post_fields, $field['fields'] ) );
			}
			
			if ( ! isset( $field['required'] ) ) {
				continue;
			}
			
			$required[] = Form_Block::get_instance()->get_block_name_attribute( $field );
		}
		
		/**
		 * Filter the required fields.
		 * 
		 * @since	1.3.0
		 * 
		 * @param	array	$required Required fields
		 * @param	array	$data Form data
		 * @param	string	$form_id Form ID
		 * @param	array	$post_fields POST fields
		 */
		$required = (array) \apply_filters( 'form_block_required_fields', $required, $data, $form_id, $post_fields );
		
		return $required;
	}
	
	/**
	 * Get the submit object data of submitted data.
	 * Requires _object_type and _object_id to be set in the data.
	 * 
	 * @param	mixed[]	$fields List of fields
	 * @return	array{title: string, url: string} URL where the data has been submitted
	 */
	public static function get_submit_object_data( array $fields ): array {
		$default = [
			'title' => '',
			'url' => '',
		];
		
		if ( ! isset( $fields['_object_id'] ) || ! isset( $fields['_object_type'] ) ) {
			return $default;
		}
		
		if ( ! \is_numeric( $fields['_object_id'] ) || ! \is_string( $fields['_object_type'] ) ) {
			return $default;
		}
		
		switch ( $fields['_object_type'] ) {
			case 'WP_Post':
			case 'WP_Post_Type':
				return [
					'title' => \get_post_field( 'post_title', $fields['_object_id'] ),
					'url' => \get_permalink( $fields['_object_id'] ),
				];
			case 'WP_Term':
				return [
					'title' => \get_term_field( 'taxonomy', $fields['_object_id'] ),
					'url' => \get_term_link( $fields['_object_id'] ),
				];
			default:
				return $default;
		}
	}
	
	/**
	 * Check whether the honeypot is filled.
	 * 
	 * @return	bool Wether the honeypot is filled
	 */
	private function is_honeypot_filled(): bool {
		$honeypot_key = '_town';
		$is_filled = false;
		
		/**
		 * Filter the honeypot key.
		 * 
		 * @param	string	$honeypot_key The default key '_town'
		 */
		$honeypot_key = \apply_filters( 'form_block_honeypot_key', $honeypot_key );
		
		$is_filled = ! empty( $_POST[ $honeypot_key ] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		
		/**
		 * Filter whether the honeypot is filled.
		 * 
		 * @param	bool	$is_filled Whether the honeypot is filled.
		 */
		$is_filled = \apply_filters( 'form_block_is_honeypot_filled', $is_filled );
		
		return $is_filled;
	}
	
	/**
	 * Check, whether a form ID is valid. That means, there are form fields stored.
	 * 
	 * @since	1.0.2
	 * 
	 * @param	string	$form_id The form ID to check
	 * @return	bool Whether a form ID is valid
	 */
	public function is_valid_form_id( string $form_id ): bool {
		$maybe_data = (array) \get_option( 'form_block_data_' . $form_id, [] );
		
		return ! empty( $maybe_data['fields'] );
	}
	
	/**
	 * Send form submission to the recipients.
	 * 
	 * @deprecated	1.6.0 Use epiphyt\Form_Block\submissions\methods\Email::send() instead
	 * 
	 * @param	array	$fields The validated fields
	 * @param	array	$files The local and validated files
	 */
	public function send( array $fields, array $files ): void {
		\_doing_it_wrong(
			__METHOD__,
			\sprintf(
				/* translators: alternative method */
				\esc_html__( 'Use %s instead', 'form-block' ),
				'epiphyt\Form_Block\submissions\methods\Email::send()'
			),
			'1.6.0'
		);
		
		Email::send( [], $this->form_id, $fields, $files );
	}
	
	/**
	 * Set static output value for checkboxes and radio buttons.
	 * 
	 * @since		1.1.0
	 * @deprecated	1.5.0
	 * 
	 * @param	string	$output The field output
	 * @param	string	$name The field name
	 * @param	mixed	$value The field value
	 * @param	array	$form_data The form data
	 * @return	string The updated field output
	 */
	public function get_static_value_output( string $output, string $name, mixed $value, array $form_data ): string {
		\_doing_it_wrong(
			__METHOD__,
			\esc_html__( 'This method is outdated and will be removed in the future.', 'form-block' ),
			'1.5.0'
		);
		
		return Field::get_static_value_output( $output, $name, $value, $form_data, 0, 'plain' );
	}
	
	/**
	 * Unify the $_FILES-formatted array.
	 * 
	 * @param	array	$file_post The $_FILES-formatted array
	 * @return	array The new formatted array
	 */
	public function unify_files_array( array $file_post ): array {
		$file_ary = [];
		$file_count = \count( $file_post['name'] );
		$file_keys = \array_keys( $file_post );
		
		for ( $i = 0; $i < $file_count; $i++ ) {
			foreach ( $file_keys as $key ) {
				$file_ary[ $i ][ $key ] = $file_post[ $key ][ $i ];
			}
		}
		
		return $file_ary;
	}
}

<?php
namespace epiphyt\Form_Block\form_data;

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
		\add_filter( 'form_block_output_field_output', [ Field::class, 'get_static_value_output' ], 10, 5 );
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
	 * @return	array Form data
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
	 * Get Reply-to email address.
	 * 
	 * @param	array	$data Form data
	 * @param	array	$fields Form fields
	 * @return	string Reply-to email address
	 */
	private static function get_reply_to( array $data, array $fields ): string {
		// reverse since the latest reply to field is the most important one
		$reverse_fields = \array_reverse( $fields );
		
		foreach ( \array_reverse( $data ) as $name => $value ) {
			$label = Field::get_title_by_name( $name, $fields );
			$key = \array_search( $label, \array_column( \array_reverse( $fields ), 'label' ), true );
			
			if ( $key === false ) {
				continue;
			}
			
			if ( ! empty( $reverse_fields[ $key ]['is_reply_to'] ) ) {
				/**
				 * Filter the reply to address.
				 * 
				 * @since	1.1.0
				 * 
				 * @param	mixed	$value The field value
				 * @param	array	$data The POST data
				 * @param	array	$fields The form fields
				 */
				$value = \apply_filters( 'form_block_reply_to', $value, $data, $fields );
				
				return $value;
			}
		}
		
		/**
		 * This filter is described in epiphyt\Form_Block\form_data\Data::get_reply_to().
		 */
		return \apply_filters( 'form_block_reply_to', '', $data, $fields );
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
		$files = Validation::get_instance()->files();
		
		/**
		 * Fires after data has been validated.
		 * 
		 * @param	string	$form_id The form ID
		 * @param	array	$fields Validated fields
		 * @param	array	$files Validated files
		 */
		\do_action( 'form_block_validated_data', $this->form_id, $fields, $files );
		
		$this->send( $fields, $files );
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
	 * @param	array	$fields The validated fields
	 * @param	array	$files The validated files
	 */
	public function send( array $fields, array $files ): void {
		$recipients = [
			\get_option( 'admin_email' ),
		];
		
		/**
		 * Filter the form recipients.
		 * 
		 * @param	array	$recipients The recipients
		 * @param	int		$form_id The form ID
		 * @param	array	$fields The validated fields
		 * @param	array	$files The validated files
		 */
		$recipients = \apply_filters( 'form_block_recipients', $recipients, $this->form_id, $fields, $files );
		
		$form_data = $this->get( $this->form_id );
		$field_output = [
			\trim( Field::get_instance()->get_output( $form_data['fields'], $fields ) ),
		];
		
		$attachments = [];
		
		if ( ! empty( $files ) ) {
			foreach ( $files as $file ) {
				$file_data = Field::get_by_name( $file['field_name'], $form_data['fields'] );
				$new_path = \sys_get_temp_dir() . $file['name'];
				
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
				
				\move_uploaded_file( $file['path'], $new_path );
				
				/**
				 * Fires after the file has been moved.
				 * 
				 * @param	array	$file Uploaded file information array
				 * @param	array	$file_data Form field data for this file
				 */
				\do_action( 'form_field_attachment_after_moved', $file, $file_data );
				
				/**
				 * Filter the file output.
				 * 
				 * @since	1.4.1
				 * 
				 * @param	string	$output The field output
				 * @param	string	$name The field name
				 * @param	mixed	$new_path File path
				 * @param	array	$file data File data array
				 */
				$output = \apply_filters( 'form_block_output_file_output', '', $file['field_name'], $new_path, $file_data );
				
				/**
				 * This filter is documented in inc/form-data/class-data.php
				 * 
				 * @since	1.4.1 Added filter for file inputs
				 */
				$output = \apply_filters( 'form_block_output_field_output', $output, $file['field_name'], $new_path, $form_data, 0 );
				
				if ( ! empty( $output ) ) {
					$field_output[] = $output;
				}
			}
		}
		
		$headers = [];
		$reply_to = self::get_reply_to( $fields, $form_data['fields'] );
		
		if ( ! empty( $reply_to ) ) {
			if ( \str_contains( $reply_to, ' ' ) ) {
				$reply_to = \explode( ' ', $reply_to );
			}
			else {
				$reply_to = (array) $reply_to;
			}
			
			$headers[] = 'Reply-To: ' . \trim( \implode( ',', $reply_to ), ' ' );
		}
		
		$email_text = \sprintf(
			/* translators: 1: blog title, 2: form fields */
			\__( 'Hello,

you have just received a new form submission with the following data from "%1$s":

%2$s

Your "%1$s" WordPress', 'form-block' ),
			\get_bloginfo( 'name' ),
			\implode( \PHP_EOL, $field_output )
		);
		
		/**
		 * Filter the email text.
		 * 
		 * @param	string	$email_text The email text
		 * @param	string	$field_output The field text output
		 * @param	string	$form_id The form ID
		 * @param	array	$fields The validated fields
		 */
		$email_text = \apply_filters( 'form_block_email_text', $email_text, $field_output, $this->form_id, $fields );
		
		$success = [];
		
		if ( ! empty( $form_data['subject'] ) ) {
			$subject = $form_data['subject'];
		}
		else {
			/* translators: blog name */
			$subject = \sprintf( \__( 'New form submission via "%s"', 'form-block' ), \get_bloginfo( 'name' ) );
		}
		
		/**
		 * Filter the email subject.
		 * 
		 * @param	string	$subject The email subject
		 */
		$subject = \apply_filters( 'form_block_mail_subject', $subject );
		
		foreach ( $recipients as $recipient ) {
			if ( ! \filter_var( $recipient, \FILTER_VALIDATE_EMAIL ) ) {
				continue;
			}
			
			$sent = \wp_mail( $recipient, $subject, $email_text, $headers, $attachments );
			
			$success[ $recipient ] = $sent;
		}
		
		/**
		 * Runs after sending emails with a status per recipient.
		 * If status is true, the email was sent.
		 * 
		 * @param	array	$success List of emails and whether they were sent
		 * @param	string	$email_text The sent email text
		 * @param	array	$attachments The sent attachments
		 */
		\do_action( 'form_block_sent_emails', $success, $email_text, $attachments );
		
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
	 * Set static output value for checkboxes and radio buttons.
	 * 
	 * @since		1.1.0
	 * @deprecated	1.5.0 Use epiphyt\Form_Block\form_data\Field::get_static_value_output() instead
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
			\sprintf(
				/* translators: alternative method */
				\esc_html__( 'Use %s instead', 'form-block' ),
				'epiphyt\Form_Block\form_data\Field::get_static_value_output()'
			),
			'1.5.0'
		);
		
		return Field::get_static_value_output( $output, $name, $value, $form_data, 0 );
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

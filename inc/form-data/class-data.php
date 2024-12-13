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
	private $form_id = '';
	
	/**
	 * @var		\epiphyt\Form_Block\form_data\Data
	 */
	public static $instance;
	
	/**
	 * Initialize the class.
	 */
	public function init(): void {
		add_action( 'wp_ajax_form-block-create-nonce', [ $this, 'create_nonce' ] );
		add_action( 'wp_ajax_form-block-submit', [ $this, 'get_request' ] );
		add_action( 'wp_ajax_nopriv_form-block-create-nonce', [ $this, 'create_nonce' ] );
		add_action( 'wp_ajax_nopriv_form-block-submit', [ $this, 'get_request' ] );
		
		add_filter( 'form_block_output_field_output', [ $this, 'set_static_value_output' ], 10, 4 );
	}
	
	/**
	 * Filter an array recursively.
	 * 
	 * @param	mixed[]	$input Input array
	 * @return	mixed[] Filtered array
	 */
	private static function array_filter_recursive( array $input ): array {
		foreach ( $input as $key => &$value ) {
			if ( \is_array( $value ) ) {
				$value = self::array_filter_recursive( $value );
				
				if ( empty( $value ) ) {
					unset( $input[ $key ] );
				}
			}
			else if ( empty( $value ) ) {
				unset( $input[ $key ] );
			}
		}
		
		return $input;
	}
	
	/**
	 * Create a nonce via Ajax.
	 * 
	 * @since	1.0.2
	 */
	public function create_nonce(): void {
		if ( empty( $_POST['form_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			wp_send_json_error(
				[
					'message' => __( 'The form could not be prepared to submit requests. Please reload the page.', 'form-block' ),
				]
			);
		}
		
		$id = sanitize_text_field( wp_unslash( $_POST['form_id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		
		if ( ! $this->is_valid_form_id( $id ) ) {
			wp_send_json_error();
		}
		
		wp_send_json_success(
			[
				'nonce' => wp_create_nonce( 'form_block_submit_' . $id ),
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
		
		return (array) get_option( 'form_block_data_' . $form_id, [] );
	}
	
	/**
	 * Get the field data of a list of fields by its name.
	 * 
	 * @param	string	$name The name to search for
	 * @param	array	$fields The fields to search in
	 * @return	array The field data
	 */
	public function get_field_data_by_name( string $name, array $fields ): array {
		Form_Block::get_instance()->reset_block_name_attributes();
		
		foreach ( $fields as $field ) {
			if ( $name !== Form_Block::get_instance()->get_block_name_attribute( $field ) ) {
				continue;
			}
			
			return $field;
		}
		
		return [];
	}
	
	/**
	 * Get a valid name by its label.
	 *
	 * @param	string	$label The original label
	 * @param	bool	$to_lowercase Whether the name should be lowercase
	 * @return	string The valid name
	 */
	public static function get_field_name_by_label( string $label, bool $to_lowercase = true ): string {
		if ( $to_lowercase ) {
			$label = \mb_strtolower( $label );
		}
		
		/**
		 * Filter the label before generating a name out of it.
		 * 
		 * @param	string	$label The original label
		 * @param	bool	$to_lowercase Whether the name should be lowercase
		 * @return	string The updated label
		 */
		$label = \apply_filters( 'form_block_pre_get_name_by_label', $label, $to_lowercase );
		
		$regex = '/[^A-Za-z0-9\-_\[\]]/';
		$replace = [ 'ae', 'oe', 'ue', 'ss', '-' ];
		$search = [ 'ä', 'ö', 'ü', 'ß', ' ' ];
		$name = \preg_replace( $regex, '', \str_replace( $search, $replace, $label ) );
		
		/**
		 * Filter the generated name from a label.
		 * 
		 * @param	string	$name The generated name
		 * @param	string	$label The original label
		 * @param	bool	$to_lowercase Whether the name should be lowercase
		 * @return	string The updated name
		 */
		$name = \apply_filters( 'form_block_get_name_by_label', $name, $label, $to_lowercase );
		
		return $name;
	}
	
	/**
	 * Get field output.
	 * 
	 * @param	mixed[]		$field Current field data
	 * @param	mixed[]		$form_data Form data
	 * @param	string[]	$post_fields POST fields
	 * @param	int			$level Indentation level
	 * @return	string Field output
	 */
	public function get_field_output( array $field, array $form_data, array $post_fields, int $level = 0 ): string {
		$output = '';
		
		if ( ! isset( $field['name'] ) && isset( $field['label'] ) ) {
			$field['name'] = self::get_field_name_by_label( $field['label'] );
		}
		
		if (
			isset( $field['name'] )
			&& ( isset( $post_fields[ $field['name'] ] ) || Validation::is_field_submitted( $field['name'], $post_fields ) )
		) {
			$post_field = [
				'name' => $field['name'],
				'value' => self::get_post_field_value( $field['name'], $post_fields ),
			];
			
			if ( \is_array( $post_field['value'] ) ) {
				$post_field['value'] = self::array_filter_recursive( $post_field['value'] );
			}
			
			if ( ! empty( $post_field['value'] ) ) {
				$output = $this->get_raw_field_output( $post_field, $form_data, $level );
			}
		}
		else if ( isset( $field['legend']['textContent'] ) ) {
			/**
			 * Filter the fieldset legend text.
			 * 
			 * @since	1.5.0
			 * 
			 * @param	string		$legend Current legend text
			 * @param	mixed[]		$field Form field data
			 * @param	mixed[]		$form_data Form data
			 * @param	string[]	$post_fields POST fields
			 */
			$legend = \apply_filters( 'form_block_output_fieldset_legend', $field['legend']['textContent'], $field, $form_data, $post_fields );
			$output = $legend . ':' . \PHP_EOL;
		}
		
		if ( ! empty( $field['fields'] ) ) {
			$subfields_output = '';
			++$level;
			
			foreach ( $field['fields'] as $sub_field ) {
				$subfields_output .= $this->get_field_output( $sub_field, $form_data, $post_fields, $level );
			}
			
			// don't output fieldset legend if no fields in the fieldset are available
			if ( empty( $subfields_output ) && isset( $field['legend']['textContent'] ) ) {
				$output = '';
			}
		}
		
		return $output;
	}
	
	/**
	 * Get the field title of a list of fields by its name.
	 * 
	 * @param	string	$name The name to search for
	 * @param	array	$fields The fields to search in
	 * @param	bool	$reset_name_attributes Whether to reset the block name attributes
	 * @return	string The field title or the field name, if title cannot be found
	 */
	public function get_field_title_by_name( string $name, array $fields, bool $reset_name_attributes = true ): string {
		if ( $reset_name_attributes ) {
			Form_Block::get_instance()->reset_block_name_attributes();
		}
		
		foreach ( $fields as $field ) {
			$field_name = Form_Block::get_instance()->get_block_name_attribute( $field );
			
			if ( $field_name === $name || preg_match( '/' . preg_quote( $field_name, '/' ) . '-\d+/', $name ) ) {
				return $field['label'] ?? $name;
			}
			
			if ( ! empty( $field['fields'] ) ) {
				return $this->get_field_title_by_name( $name, $field['fields'], false );
			}
		}
		
		return $name;
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
	public static function get_instance(): Data {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * Get field indentation by a defined level.
	 * 
	 * @param	mixed[]	$field Field data
	 * @param	int		$level Indentation level
	 * @return	string Field output with indentation
	 */
	private static function get_field_indentation( array $field, int $level ): string {
		$output = '';
		
		foreach ( $field as $key => $item ) {
			$output .= \str_repeat( ' ', $level * 2 );
			/* translators: list item key */
			$output .= \sprintf( \_x( '- %s:', 'list element in plaintext email', 'form-block' ), $key );
			
			if ( \is_array( $item ) ) {
				++$level;
				$output .= \PHP_EOL;
				$output .= self::get_field_indentation( $item, $level );
				--$level;
			}
			else {
				$output .= ' ' . $item;
			}
		}
		
		return $output;
	}
	
	/**
	 * Get the value of a (nested) post field.
	 * 
	 * @param	string	$field_name Field name
	 * @param	array	$post_fields POST fields
	 * @return	mixed Post field value
	 */
	private static function get_post_field_value( string $field_name, array $post_fields ): mixed {
		if ( \preg_match_all( '/([^\[\]]+)/', $field_name, $matches ) ) {
			$keys = $matches[1];
		}
		else {
			$keys = $field_name;
		}
		
		if ( ! empty( $keys ) ) {
			$key = \reset( $keys );
			
			return $post_fields[ $key ] ?? '';
		}
		
		$current_fields = $post_fields;
		
		foreach ( $keys as $key ) {
			if ( ! \is_array( $current_fields ) || ! \array_key_exists( $key, $current_fields ) ) {
				return '';
			}
			
			$current_fields = $current_fields[ $key ];
			
			if ( \is_string( $current_fields ) ) {
				return $current_fields;
			}
			else if ( isset( $current_fields[0] ) ) {
				if ( \is_string( $current_fields[0] ) ) {
					return $current_fields[0];
				}
				
				$current_fields = $current_fields[0];
			}
		}
		
		return '';
	}
	
	/**
	 * Get raw field output.
	 * 
	 * @param	array{name: string, value: string}	$field POST field data
	 * @param	array								$form_data Form data
	 * @param	int									$level Indentation level
	 * @return	string Raw field output
	 */
	private function get_raw_field_output( array $field, array $form_data, int $level = 0 ): string {
		/**
		 * Filter whether to omit the field from output.
		 * 
		 * @since	1.0.3
		 * 
		 * @param	bool	$omit_field Whether to omit the field from output
		 * @param	string	$name Field name
		 * @param	mixed	$value Field value
		 * @param	array	$form_data Form data
		 */
		$omit_field = \apply_filters( 'form_block_output_field_omit', false, $field['name'], $field['value'], $form_data );
		
		if ( $omit_field ) {
			return '';
		}
		
		$output = $this->get_field_title_by_name( $field['name'], $form_data['fields'] ) . ': ';
		
		/**
		 * Filter the field value in the output.
		 * 
		 * @since	1.0.3
		 * 
		 * @param	mixed	$value Field value
		 * @param	string	$name Field name
		 * @param	array	$form_data Form data
		 */
		$value = \apply_filters( 'form_block_output_field_value', $field['value'], $field['name'], $form_data );
		
		if ( \is_string( $value ) && \str_contains( $value, \PHP_EOL ) ) {
			$output .= \PHP_EOL;
		}
		
		if ( ! is_array( $value ) ) {
			$output .= $value;
		}
		else {
			foreach ( $value as $key => $item ) {
				$output .= \PHP_EOL;
				
				if ( \is_string( $item ) ) {
					/* translators: 1: list item key, list item value */
					$output .= \sprintf( \_x( '- %1$s: %2$s', 'list element in plaintext email', 'form-block' ), $key, $item );
					
					continue;
				}
				
				$output .= self::get_field_indentation( [ $key => $item ], $level );
			}
		}
		
		if ( $level ) {
			$output = \str_repeat( ' ', $level * 2 ) . $output . \PHP_EOL; // non-breaking space UTF-8 character
		}
		
		/**
		 * Filter the field output.
		 * 
		 * @since	1.1.0
		 * 
		 * @param	string	$output Field output
		 * @param	string	$name Field name
		 * @param	mixed	$value Field value
		 * @param	array	$form_data Form data
		 */
		$output = \apply_filters( 'form_block_output_field_output', $output, $field['name'], $field['value'], $form_data );
		
		return $output;
	}
	
	/**
	 * Get Reply-to email address.
	 * 
	 * @param	array	$data Form data
	 * @param	array	$fields Form fields
	 * @return	string Reply-to email address
	 */
	private function get_reply_to( array $data, array $fields ): string {
		// reverse since the latest reply to field is the most important one
		$reverse_fields = array_reverse( $fields );
		
		foreach ( array_reverse( $data ) as $name => $value ) {
			$label = $this->get_field_title_by_name( $name, $fields );
			$key = array_search( $label, array_column( array_reverse( $fields ), 'label' ), true );
			
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
				$value = apply_filters( 'form_block_reply_to', $value, $data, $fields );
				
				return $value;
			}
		}
		
		/**
		 * This filter is described in epiphyt\Form_Block\form_data\Data::get_reply_to().
		 */
		return apply_filters( 'form_block_reply_to', '', $data, $fields );
	}
	
	/**
	 * Get the request data.
	 */
	public function get_request(): void {
		if ( empty( $_POST['_wpnonce'] ) ) {
			/**
			 * Fires after verifying that the nonce is empty or absent.
			 */
			do_action( 'form_block_empty_nonce' );
			
			// explicitly return success so that bad actors cannot learn
			wp_send_json_success();
		}
		
		if ( ! isset( $_POST['_form_id'] ) || ! isset( $_POST['_town'] ) ) {
			/**
			 * Fires after a request is considered invalid.
			 */
			do_action( 'form_block_invalid_data' );
			
			// explicitly return success so that bots cannot learn
			wp_send_json_success();
		}
		
		$this->form_id = sanitize_text_field( wp_unslash( $_POST['_form_id'] ) );
		
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'form_block_submit_' . $this->form_id ) ) {
			/**
			 * Fires after a request has an invalid nonce.
			 */
			do_action( 'form_block_invalid_nonce' );
			
			// explicitly return success so that bad actors cannot learn
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
		
		/**
		 * Fires before data has been validated.
		 * 
		 * @param	string	$form_id The form ID
		 */
		do_action( 'form_block_pre_validated_data', $this->form_id );
		
		$fields = Validation::get_instance()->fields();
		$files = Validation::get_instance()->files();
		
		/**
		 * Fires after data has been validated.
		 * 
		 * @param	string	$form_id The form ID
		 * @param	array	$fields Validated fields
		 * @param	array	$files Validated files
		 */
		do_action( 'form_block_validated_data', $this->form_id, $fields, $files );
		
		$this->send( $fields, $files );
	}
	
	/**
	 * Get all required fields of a form.
	 * 
	 * @since	1.3.0 Add $fields parameter
	 * 
	 * @param	string	$form_id Current form ID
	 * @param	array	$fields Submitted form fields
	 * @return	array List of required field names
	 */
	public function get_required_fields( string $form_id = '', array $fields = [] ): array {
		if ( ! $form_id ) {
			$form_id = $this->form_id;
		}
		
		if ( ! $form_id ) {
			return [];
		}
		
		Form_Block::get_instance()->reset_block_name_attributes();
		
		$data = $this->get( $form_id );
		$required = [];
		
		foreach ( $data['fields'] as $field ) {
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
		 */
		$required = \apply_filters( 'form_block_required_fields', $required, $data, $form_id, $fields );
		
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
		 * @param	bool	$is_filled Whether the honeypot is filled.
		 */
		$is_filled = apply_filters( 'form_block_is_honeypot_filled', $is_filled );
		
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
		$maybe_data = (array) get_option( 'form_block_data_' . $form_id, [] );
		
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
			get_option( 'admin_email' ),
		];
		
		/**
		 * Filter the form recipients.
		 * 
		 * @param	array	$recipients The recipients
		 * @param	int		$form_id The form ID
		 * @param	array	$fields The validated fields
		 * @param	array	$files The validated files
		 */
		$recipients = apply_filters( 'form_block_recipients', $recipients, $this->form_id, $fields, $files );
		
		$form_data = $this->get( $this->form_id );
		$field_output = [];
		$processed_fields = [];
		
		foreach ( $form_data['fields'] as $field ) {
			$processed_field_name = '';
			
			if ( ! isset( $field['name'] ) && isset( $field['label'] ) ) {
				$field['name'] = self::get_field_name_by_label( $field['label'] );
			}
			
			if ( isset( $field['name'] ) ) {
				$processed_field_name = \substr( $field['name'], 0, \strpos( $field['name'], '[' ) ?: \strlen( $field['name'] ) );
			}
			
			// process nested fields only once
			if ( ! empty( $processed_field_name ) && \in_array( $processed_field_name, $processed_fields, true ) ) {
				continue;
			}
			
			$output = $this->get_field_output( $field, $form_data, $fields );
			
			if ( ! empty( $processed_field_name ) ) {
				$processed_fields[] = $processed_field_name;
			}
			
			if ( ! empty( $output ) ) {
				$field_output[] = $output;
			}
		}
		
		$attachments = [];
		
		if ( ! empty( $files ) ) {
			foreach ( $files as $file ) {
				$file_data = $this->get_field_data_by_name( $file['field_name'], $form_data['fields'] );
				$new_path = sys_get_temp_dir() . $file['name'];
				
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
				
				move_uploaded_file( $file['path'], $new_path );
				
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
				 * This filter is documented in inc/form-data/class.data.php
				 * 
				 * @since	1.4.1 Added filter for file inputs
				 */
				$output = \apply_filters( 'form_block_output_field_output', $output, $file['field_name'], $new_path, $form_data );
				
				if ( ! empty( $output ) ) {
					$field_output[] = $output;
				}
			}
		}
		
		$headers = [];
		$reply_to = $this->get_reply_to( $fields, $form_data['fields'] );
		
		if ( ! empty( $reply_to ) ) {
			if ( str_contains( $reply_to, ' ' ) ) {
				$reply_to = explode( ' ', $reply_to );
			}
			else {
				$reply_to = (array) $reply_to;
			}
			
			$headers[] = 'Reply-To: ' . trim( implode( ',', $reply_to ), ' ' );
		}
		
		$email_text = sprintf(
			/* translators: 1: blog title, 2: form fields */
			__( 'Hello,

you have just received a new form submission with the following data from "%1$s":

%2$s

Your "%1$s" WordPress', 'form-block' ),
			get_bloginfo( 'name' ),
			implode( PHP_EOL, $field_output )
		);
		
		/**
		 * Filter the email text.
		 * 
		 * @param	string	$email_text The email text
		 * @param	string	$field_output The field text output
		 * @param	string	$form_id The form ID
		 * @param	array	$fields The validated fields
		 */
		$email_text = apply_filters( 'form_block_email_text', $email_text, $field_output, $this->form_id, $fields );
		
		$success = [];
		
		if ( ! empty( $form_data['subject'] ) ) {
			$subject = $form_data['subject'];
		}
		else {
			/* translators: blog name */
			$subject = sprintf( __( 'New form submission via "%s"', 'form-block' ), get_bloginfo( 'name' ) );
		}
		
		/**
		 * Filter the email subject.
		 * 
		 * @param	string	$subject The email subject
		 */
		$subject = apply_filters( 'form_block_mail_subject', $subject );
		
		foreach ( $recipients as $recipient ) {
			if ( ! filter_var( $recipient, FILTER_VALIDATE_EMAIL ) ) {
				continue;
			}
			
			$sent = wp_mail( $recipient, $subject, $email_text, $headers, $attachments );
			
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
		do_action( 'form_block_sent_emails', $success, $email_text, $attachments );
		
		if ( in_array( false, array_values( $success ), true ) ) {
			wp_send_json_error( [
				'message' => esc_html__( 'Form submission failed for at least one recipient.', 'form-block' ),
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
		$data = apply_filters( 'form_block_submit_success_data', null, $this->form_id );
		
		wp_send_json_success( $data );
	}
	
	/**
	 * Set static output value for checkboxes and radio buttons.
	 * 
	 * @since	1.1.0
	 * 
	 * @param	string	$output The field output
	 * @param	string	$name The field name
	 * @param	mixed	$value The field value
	 * @param	array	$form_data The form data
	 * @return	string The updated field output
	 */
	public function set_static_value_output( string $output, string $name, $value, array $form_data ): string {
		$field_data = $this->get_field_data_by_name( $name, $form_data['fields'] );
		
		if ( empty( $field_data['type'] ) || ( $field_data['type'] !== 'checkbox' && $field_data['type'] !== 'radio' ) ) {
			return $output;
		}
		
		$label = $this->get_field_title_by_name( $name, $form_data['fields'] );
		
		if ( $field_data['type'] === 'checkbox' ) {
			/* translators: form field title */
			return sprintf( __( 'Checked: %s', 'form-block' ), $label );
		}
		else if ( $field_data['type'] === 'radio' ) {
			if ( $value !== 'on' ) {
				/* translators: form field title or value */
				return sprintf( __( 'Selected: %s', 'form-block' ), $value );
			}
			
			/* translators: form field title or value */
			return sprintf( __( 'Selected: %s', 'form-block' ), $label );
		}
		
		// this should never happen, just in case
		return $output;
	}
	
	/**
	 * Unify the $_FILES-formatted array.
	 * 
	 * @param	array	$file_post The $_FILES-formatted array
	 * @return	array The new formatted array
	 */
	public function unify_files_array( array $file_post ): array {
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
}

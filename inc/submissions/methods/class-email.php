<?php
namespace epiphyt\Form_Block\submissions\methods;

use epiphyt\Form_Block\form_data\Data;
use epiphyt\Form_Block\form_data\Field;
use epiphyt\Form_Block\form_data\File;

/**
 * Action to send submission via email.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Email {
	public const IDENTIFIER = 'email';
	
	/**
	 * Initialize functionality.
	 */
	public static function init(): void {
		\add_filter( 'form_block_submit_data', [ self::class, 'send' ], 10, 4 );
	}
	
	/**
	 * Get Reply-to email address.
	 * 
	 * @param	array	$data Form data
	 * @param	array	$fields Form fields
	 * @return	string Reply-to email address
	 */
	public static function get_reply_to( array $data, array $fields ): string {
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
	 * Send email(s).
	 * 
	 * @param	bool[]	$success A list of successful or failed submission methods
	 * @param	string	$form_id The form ID
	 * @param	array	$fields Validated fields
	 * @param	array	$files Files data
	 * @return	bool[] Whether the submission has been saved successfully
	 */
	public static function send( array $success, string $form_id, array $fields, array $files ): array {
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
		$recipients = \apply_filters( 'form_block_recipients', $recipients, $form_id, $fields, $files['validated'] );
		
		$form_data = Data::get_instance()->get( $form_id );
		$field_output = [
			\trim( Field::get_instance()->get_output( $form_data['fields'], $fields ) ),
		];
		
		$attachments = [];
		
		if ( ! empty( $files['validated'] ) ) {
			foreach ( $files['validated'] as $file_key => $validated_file ) {
				$file = File::get_data( $validated_file, $file_key, $files );
				$output = File::get_output( $file, $form_data, $attachments );
				
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
				$reply_to = \array_map( static function( $email ) {
					if ( \str_ends_with( $email, ',' ) ) {
						$email = \rtrim( $email, ',' );
					}
					
					return $email;
				}, $reply_to );
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
		$email_text = \apply_filters( 'form_block_email_text', $email_text, $field_output, $form_id, $fields );
		
		$email_sent = [];
		
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
			
			$email_sent[ $recipient ] = \wp_mail( $recipient, $subject, $email_text, $headers, $attachments );
		}
		
		/**
		 * Runs after sending emails with a status per recipient.
		 * If status is true, the email was sent.
		 * 
		 * @param	array	$email_sent List of emails and whether they were sent
		 * @param	string	$email_text The sent email text
		 * @param	array	$attachments The sent attachments
		 */
		\do_action( 'form_block_sent_emails', $email_sent, $email_text, $attachments );
		
		$success[ self::IDENTIFIER ] = ! \in_array( false, \array_values( $email_sent ), true );
		
		return $success;
	}
}

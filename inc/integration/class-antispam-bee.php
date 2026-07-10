<?php
declare( strict_types = 1 );

namespace epiphyt\Form_Block\integration;

use AntispamBee\Handlers\Rules;
use epiphyt\Form_Block\Admin;
use epiphyt\Form_Block\Form_Block;
use epiphyt\Form_Block\form_data\Data;
use epiphyt\Form_Block\form_data\Field;
use epiphyt\Form_Block\submissions\methods\Email;
use epiphyt\Form_Block\submissions\methods\Local_Storage;
use epiphyt\Form_Block\submissions\Submission_Handler;

/**
 * Antispam Bee integration.
 * 
 * Checks form submissions for spam via Antispam Bee (version 3 or higher).
 * Spam submissions are silently accepted and – if local storage is enabled for
 * the form – stored flagged as spam for review.
 * 
 * @since	1.8.0
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Antispam_Bee {
	public const OPTION_NAME = 'form_block_antispam_bee_active';
	
	public const REACTION_TYPE = 'form_block_form';
	
	public const SPAM_TYPE = 'spam';
	
	/**
	 * @var		\epiphyt\Form_Block\integration\Antispam_Bee
	 */
	public static ?self $instance = null;
	
	/**
	 * @var		bool Whether the submission currently being stored is spam
	 */
	private static bool $is_spam = false;
	
	/**
	 * @var		string[] Spam reasons of the submission currently being stored
	 */
	private static array $spam_reasons = [];
	
	/**
	 * Initialize the class.
	 */
	public function init(): void {
		\add_action( 'admin_init', [ $this, 'register_settings' ] );
		\add_action( 'plugins_loaded', [ $this, 'integrate' ] );
		\add_filter( 'antispam_bee_disallow_ajax_calls', [ $this, 'allow_ajax_init' ] );
	}
	
	/**
	 * Add the custom reaction type to the supported types of the content rules.
	 * 
	 * @param	array	$supported_types Currently supported reaction types
	 * @param	string	$slug Rule slug
	 * @return	array Updated supported reaction types
	 */
	public function add_supported_type( array $supported_types, string $slug ): array {
		if ( \in_array( $slug, self::get_rule_slugs(), true ) ) {
			$supported_types[] = self::get_reaction_type();
		}
		
		return $supported_types;
	}
	
	/**
	 * Allow Antispam Bee to initialize during the form submission Ajax request.
	 * 
	 * By default, Antispam Bee skips its module initialization – and therefore
	 * the rule registration – during Ajax requests. Since form submissions are
	 * processed via Ajax, the initialization needs to be allowed for them.
	 * 
	 * @param	bool	$disallow Whether Ajax initialization is disallowed
	 * @return	bool Whether Ajax initialization is disallowed
	 */
	public function allow_ajax_init( bool $disallow ): bool {
		if ( ! self::is_enabled() ) {
			return $disallow;
		}
		
		$action = \sanitize_key( \wp_unslash( $_REQUEST['action'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		
		if ( $action === 'form-block-submit' ) {
			return false;
		}
		
		return $disallow;
	}
	
	/**
	 * Check a validated submission for spam.
	 * 
	 * @param	string	$form_id The form ID
	 * @param	array	$fields Validated fields
	 * @param	array	$validated_files Validated files
	 * @param	array	$local_files Local files data
	 */
	public function check( string $form_id, array $fields, array $validated_files, array $local_files ): void {
		$form_data = Data::get_instance()->get( $form_id );
		$fields_config = $form_data['fields'] ?? [];
		$content = \trim( Field::get_instance()->get_output( $fields_config, $fields ) );
		$email = Email::get_reply_to( $fields, $fields_config );
		
		// nothing to evaluate
		if ( $content === '' && $email === '' ) {
			return;
		}
		
		$reaction_type = self::get_reaction_type();
		$item = [
			'comment_agent' => \sanitize_text_field( \wp_unslash( $_SERVER['HTTP_USER_AGENT'] ?? '' ) ),
			'comment_author' => '',
			'comment_author_email' => $email,
			'comment_author_IP' => \sanitize_text_field( \wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) ),
			'comment_author_url' => $this->get_url_field_value( $fields, $fields_config ),
			'comment_content' => $content,
			'comment_type' => $reaction_type,
			'reaction_type' => $reaction_type,
		];
		
		/**
		 * Filter the item that is passed to Antispam Bee.
		 * 
		 * @since	1.8.0
		 * 
		 * @param	array	$item The mapped item with comment-shaped keys
		 * @param	array	$fields Validated fields
		 * @param	string	$form_id The form ID
		 */
		$item = (array) \apply_filters( 'form_block_antispam_bee_item', $item, $fields, $form_id );
		
		$rules = new Rules( $reaction_type ); // @phpstan-ignore class.notFound
		
		/**
		 * Filter whether the submission is considered spam.
		 * 
		 * @since	1.8.0
		 * 
		 * @param	bool	$is_spam Whether the submission is spam
		 * @param	array	$item The mapped item
		 * @param	string	$form_id The form ID
		 */
		$is_spam = (bool) \apply_filters( 'form_block_antispam_bee_is_spam', $rules->apply( $item ), $item, $form_id ); // @phpstan-ignore class.notFound
		
		if ( ! $is_spam ) {
			return;
		}
		
		$reasons = $rules->get_spam_reasons(); // @phpstan-ignore class.notFound
		
		/**
		 * Fires when a submission is detected as spam.
		 * 
		 * @since	1.8.0
		 * 
		 * @param	string	$form_id The form ID
		 * @param	array	$fields Validated fields
		 * @param	array	$reasons Slugs of the rules that flagged the submission
		 * @param	array	$item The mapped item
		 */
		\do_action( 'form_block_antispam_bee_spam_detected', $form_id, $fields, $reasons, $item );
		
		// keep the submission for review if local storage is enabled for this form
		if ( Local_Storage::is_savable( $form_id ) ) {
			self::$is_spam = true;
			self::$spam_reasons = $reasons;
			
			Submission_Handler::create_submission(
				$form_id,
				$fields,
				[
					'local' => $local_files,
					'validated' => $validated_files,
				]
			);
			
			self::$is_spam = false;
			self::$spam_reasons = [];
		}
		
		// silently accept so bad actors cannot learn they were blocked
		\wp_send_json_success();
	}
	
	/**
	 * Flag the submission currently being stored as spam.
	 * 
	 * @param	array	$submission_data Submission data
	 * @return	array Updated submission data
	 */
	public function flag_submission( array $submission_data ): array {
		if ( ! self::$is_spam ) {
			return $submission_data;
		}
		
		$types = $submission_data['types'] ?? [];
		
		if ( ! \in_array( self::SPAM_TYPE, $types, true ) ) {
			$types[] = self::SPAM_TYPE;
		}
		
		$submission_data['types'] = $types;
		$submission_data['spam_reasons'] = self::$spam_reasons;
		
		return $submission_data;
	}
	
	/**
	 * Get a unique instance of the class.
	 * 
	 * @return	\epiphyt\Form_Block\integration\Antispam_Bee The single instance of this class
	 */
	public static function get_instance(): self {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * Get the reaction type used for form submissions.
	 * 
	 * @return	string Reaction type
	 */
	public static function get_reaction_type(): string {
		/**
		 * Filter the reaction type used for form submissions.
		 * 
		 * @since	1.8.0
		 * 
		 * @param	string	$reaction_type The reaction type
		 */
		return (string) \apply_filters( 'form_block_antispam_bee_reaction_type', self::REACTION_TYPE );
	}
	
	/**
	 * Get the Antispam Bee rule slugs that should check form submissions.
	 * 
	 * @return	string[] List of rule slugs
	 */
	public static function get_rule_slugs(): array {
		/**
		 * Filter the Antispam Bee rule slugs opted into the form reaction type.
		 * 
		 * @since	1.8.0
		 * 
		 * @param	string[]	$slugs List of rule slugs
		 */
		return (array) \apply_filters(
			'form_block_antispam_bee_rule_slugs',
			[
				'asb-bbcode',
				'asb-country-spam',
				'asb-lang-spam',
				'asb-regexp',
			]
		);
	}
	
	/**
	 * Get the value of the first URL field of a form.
	 * 
	 * @param	array	$fields Validated fields
	 * @param	array	$fields_config Form field configuration
	 * @return	string URL field value, or an empty string
	 */
	private function get_url_field_value( array $fields, array $fields_config ): string {
		Form_Block::get_instance()->reset_block_name_attributes();
		$url = '';
		
		foreach ( $fields_config as $field ) {
			if ( ! \is_array( $field ) ) {
				continue;
			}
			
			$name = Form_Block::get_instance()->get_block_name_attribute( $field );
			
			if ( ( $field['type'] ?? '' ) !== 'url' ) {
				continue;
			}
			
			if ( ! empty( $fields[ $name ] ) && \is_string( $fields[ $name ] ) ) {
				$url = $fields[ $name ];
			}
		}
		
		return $url;
	}
	
	/**
	 * Set up the integration once all plugins are loaded.
	 */
	public function integrate(): void {
		if ( ! self::is_available() ) {
			return;
		}
		
		\add_filter( 'antispam_bee_reaction_types', [ $this, 'register_reaction_type' ] );
		\add_filter( 'antispam_bee_rule_supported_types', [ $this, 'add_supported_type' ], 10, 2 );
		\add_filter( 'form_block_submission', [ $this, 'flag_submission' ] );
		\add_filter( 'form_block_submission_types', [ $this, 'register_submission_type' ] );
		
		if ( self::is_enabled() ) {
			\add_action( 'form_block_validated_data', [ $this, 'check' ], 10, 4 );
		}
	}
	
	/**
	 * Check whether the Antispam Bee API is available.
	 * 
	 * @return	bool Whether Antispam Bee (version 3 or higher) is active
	 */
	public static function is_available(): bool {
		return \class_exists( '\AntispamBee\Handlers\Rules' );
	}
	
	/**
	 * Check whether the spam check is enabled.
	 * 
	 * @return	bool Whether the spam check is enabled
	 */
	public static function is_enabled(): bool {
		return self::is_available() && \get_option( self::OPTION_NAME, 'yes' ) === 'yes';
	}
	
	/**
	 * Register the custom reaction type with Antispam Bee.
	 * 
	 * @param	array	$types Reaction type names
	 * @return	array Updated reaction type names
	 */
	public function register_reaction_type( array $types ): array {
		$types[ self::get_reaction_type() ] = \__( 'Form Block', 'form-block' );
		
		return $types;
	}
	
	/**
	 * Register the settings field.
	 */
	public function register_settings(): void {
		\add_settings_field(
			self::OPTION_NAME,
			\__( 'Spam protection', 'form-block' ),
			[ $this, 'render_setting' ],
			'form-block',
			'form_block_general'
		);
		\register_setting(
			'form-block',
			self::OPTION_NAME,
			[
				'sanitize_callback' => [ Admin::class, 'validate_checkbox' ],
				'type' => 'string',
			]
		);
	}
	
	/**
	 * Register the spam submission type.
	 * 
	 * @param	array	$types Registered submission types
	 * @return	array Updated submission types
	 */
	public function register_submission_type( array $types ): array {
		$types[ self::SPAM_TYPE ] = [
			'action_add' => \__( 'Spam', 'form-block' ),
			'action_remove' => \__( 'Not spam', 'form-block' ),
			'hide_from_default' => true,
			'label' => \__( 'Spam', 'form-block' ),
		];
		
		return $types;
	}
	
	/**
	 * Render the settings field.
	 */
	public function render_setting(): void {
		if ( ! self::is_available() ) {
			echo '<p class="description">' . \esc_html__( 'Install and activate Antispam Bee (version 3 or higher) to check form submissions for spam.', 'form-block' ) . '</p>';
			
			return;
		}
		
		$value = \get_option( self::OPTION_NAME, 'yes' );
		?>
		<label for="<?php echo \esc_attr( self::OPTION_NAME ); ?>">
			<input type="checkbox" name="<?php echo \esc_attr( self::OPTION_NAME ); ?>" id="<?php echo \esc_attr( self::OPTION_NAME ); ?>" value="yes"<?php \checked( $value, 'yes' ); ?>>
			<?php \esc_html_e( 'Check form submissions for spam via Antispam Bee', 'form-block' ); ?>
		</label>
		<?php
	}
}

<?php
declare( strict_types = 1 );

namespace epiphyt\Form_Block\modules;

use epiphyt\Form_Block\Admin;
use epiphyt\Form_Block\form_data\Data;

/**
 * Time-based honeypot module.
 * 
 * Records when a form page has been loaded and when the form is submitted.
 * Submissions that are sent implausibly fast or that match a specific pattern
 * of many spam bots are rejected.
 * 
 * @since	1.8.0
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Time_Honeypot {
	public const FIELD_LOAD_TIME = '_load_time';
	public const FIELD_PAGE_LOAD = '_page_load';
	public const FIELD_SUBMIT_TIME = '_submit_time';
	public const OPTION_NAME = 'form_block_time_honeypot';
	public const SUSPICIOUS_SECONDS = 30;
	public const TOLERANCE_SECONDS = 2;
	
	/**
	 * @var		bool Whether a form has been rendered on the current page
	 */
	private static bool $has_form = false;
	
	/**
	 * Initialize the class.
	 */
	public function init(): void {
		\add_action( 'admin_init', [ self::class, 'register_settings' ] );
		\add_action( 'form_block_pre_validated_data', [ self::class, 'check' ] );
		\add_action( 'wp_footer', [ self::class, 'print_script' ] );
		\add_filter( 'form_block_system_field_names', [ self::class, 'add_system_field_names' ] );
		\add_filter( 'render_block_form-block/form', [ self::class, 'add_fields' ], 10, 2 );
	}
	
	/**
	 * Register the honeypot fields as system field names.
	 * 
	 * @param	string[]	$field_names List of field names used by the system
	 * @return	string[] Updated list of field names used by the system
	 */
	public static function add_system_field_names( array $field_names ): array {
		$field_names[] = self::FIELD_LOAD_TIME;
		$field_names[] = self::FIELD_PAGE_LOAD;
		$field_names[] = self::FIELD_SUBMIT_TIME;
		
		return $field_names;
	}
	
	/**
	 * Add the time-based honeypot fields to the form.
	 * 
	 * @param	string	$block_content The block content
	 * @param	array	$block Block attributes
	 * @return	string The updated block content
	 */
	public static function add_fields( string $block_content, array $block ): string {
		if ( ! self::is_enabled() ) {
			return $block_content;
		}
		
		self::$has_form = true;
		
		$fields = '<input type="hidden" name="' . self::FIELD_LOAD_TIME . '" value="" />'
			. '<input type="hidden" name="' . self::FIELD_PAGE_LOAD . '" value="" />'
			. '<input type="hidden" name="' . self::FIELD_SUBMIT_TIME . '" value="" />';
			
		/**
		 * Filter the time-based honeypot fields.
		 * 
		 * @param	string	$fields The honeypot fields
		 * @param	string	$block_content The block content
		 * @param	array	$block Block attributes
		 */
		$fields = (string) \apply_filters( 'form_block_time_honeypot_fields', $fields, $block_content, $block );
		
		return \str_replace( '</form>', $fields . '</form>', $block_content );
	}
	
	/**
	 * Check whether the current submission is valid regarding its timing.
	 * 
	 * @param	string	$form_id The form ID
	 */
	public static function check( string $form_id ): void {
		if ( ! self::is_enabled() ) {
			return;
		}
		
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$load_time = isset( $_POST[ self::FIELD_LOAD_TIME ] ) ? \sanitize_text_field( \wp_unslash( $_POST[ self::FIELD_LOAD_TIME ] ) ) : '';
		$page_load = isset( $_POST[ self::FIELD_PAGE_LOAD ] ) ? \sanitize_text_field( \wp_unslash( $_POST[ self::FIELD_PAGE_LOAD ] ) ) : '';
		$submit_time = isset( $_POST[ self::FIELD_SUBMIT_TIME ] ) ? \sanitize_text_field( \wp_unslash( $_POST[ self::FIELD_SUBMIT_TIME ] ) ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Missing
		
		// timestamps not available: ignore silently
		if ( ! \is_numeric( $load_time ) || ! \is_numeric( $page_load ) || ! \is_numeric( $submit_time ) ) {
			\wp_send_json_success();
		}
		
		$elapsed = (float) $submit_time - (float) $load_time;
		
		// timestamps do not differ: ignore silently
		if ( $elapsed <= 0 ) {
			\wp_send_json_success();
		}
		
		// suspicious ~30 seconds window (30 seconds + page loading time, ± tolerance)
		$suspicious_center = self::SUSPICIOUS_SECONDS * 1000 + (float) $page_load;
		$tolerance = self::TOLERANCE_SECONDS * 1000;
		
		if ( \abs( $elapsed - $suspicious_center ) <= $tolerance ) {
			/**
			 * Fires when a submission is flagged as suspicious by the time-based honeypot.
			 * 
			 * @since	1.8.0
			 * 
			 * @param	string	$form_id The form ID
			 */
			\do_action( 'form_block_time_honeypot_triggered', $form_id );
			
			\wp_send_json_error( [
				'message' => \esc_html__( 'Your submission was flagged as suspicious activity. Please try submitting again.', 'form-block' ),
			] );
		}
		
		// minimum time: 2 seconds per required field
		$minimum = \count( Data::get_instance()->get_required_fields( $form_id ) ) * 2000;
		
		// submitted too fast: ignore silently
		if ( $elapsed < $minimum ) {
			\wp_send_json_success();
		}
	}
	
	/**
	 * Get the inline JavaScript that sets the load timestamps.
	 * The script must not contain any dynamic values so its hash stays stable.
	 * 
	 * @return	string The JavaScript code
	 */
	public static function get_script(): string {
		$script = '( function () {
	var loadTime = ( window.performance && performance.timeOrigin ) ? Math.round( performance.timeOrigin ) : Date.now();

	function setTimes() {
		var pageLoad = ( window.performance && typeof performance.now === "function" ) ? Math.round( performance.now() ) : 0;
		var loadFields = document.querySelectorAll( \'input[name="' . self::FIELD_LOAD_TIME . '"]\' );
		var pageLoadFields = document.querySelectorAll( \'input[name="' . self::FIELD_PAGE_LOAD . '"]\' );

		for ( var i = 0; i < loadFields.length; i++ ) {
			loadFields[ i ].value = loadTime;
		}

		for ( var j = 0; j < pageLoadFields.length; j++ ) {
			pageLoadFields[ j ].value = pageLoad;
		}
	}

	if ( document.readyState === "loading" ) {
		document.addEventListener( "DOMContentLoaded", setTimes );
	}
	else {
		setTimes();
	}
} )();';
		
		/**
		 * Filter the time-based honeypot inline script.
		 * 
		 * @since	1.8.0
		 * 
		 * @param	string	$script The JavaScript code
		 */
		return (string) \apply_filters( 'form_block_time_honeypot_script', $script );
	}
	
	/**
	 * Check whether the time-based honeypot is enabled.
	 * 
	 * @return	bool Whether the honeypot is enabled
	 */
	public static function is_enabled(): bool {
		return \get_option( self::OPTION_NAME, 'yes' ) !== '';
	}
	
	/**
	 * Print the inline script that sets the load timestamps.
	 */
	public static function print_script(): void {
		if ( ! self::$has_form || ! self::is_enabled() ) {
			return;
		}
		
		echo \wp_get_inline_script_tag( self::get_script() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	
	/**
	 * Register the settings field.
	 */
	public static function register_settings(): void {
		\add_settings_field(
			self::OPTION_NAME,
			\__( 'Spam protection', 'form-block' ),
			[ self::class, 'render_setting' ],
			'form-block',
			'form_block_general'
		);
		\register_setting(
			'form-block',
			self::OPTION_NAME,
			[
				'default' => 'yes',
				'sanitize_callback' => [ self::class, 'validate' ],
				'type' => 'string',
			]
		);
	}
	
	/**
	 * Render the settings field.
	 */
	public static function render_setting(): void {
		$value = \get_option( self::OPTION_NAME, 'yes' );
		?>
		<label for="<?php echo \esc_attr( self::OPTION_NAME ); ?>">
			<input type="checkbox" id="<?php echo \esc_attr( self::OPTION_NAME ); ?>" name="<?php echo \esc_attr( self::OPTION_NAME ); ?>" value="yes"<?php \checked( $value, 'yes' ); ?> aria-describedby="<?php echo \esc_attr( self::OPTION_NAME ); ?>_description" />
			<?php \esc_html_e( 'Enable time-based spam protection', 'form-block' ); ?>
		</label>
		<p id="<?php echo \esc_attr( self::OPTION_NAME ); ?>_description"><?php \esc_html_e( 'Reject form submissions that are sent implausibly fast or that match common spam bot timing patterns.', 'form-block' ); ?></p>
		<?php
	}
	
	/**
	 * Validate the time-based honeypot setting.
	 * 
	 * @param	string|null	$value The saved value
	 * @return	string The validated value
	 */
	public static function validate( ?string $value ): string {
		return Admin::validate_checkbox( $value, self::OPTION_NAME, \__( 'Spam protection', 'form-block' ) );
	}
}

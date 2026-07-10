<?php
declare( strict_types = 1 );

namespace epiphyt\Form_Block\modules;

/**
 * Flood control module.
 * 
 * Prevents the same visitor from re-submitting any form within a configurable
 * time frame. A visitor is identified by a combination of IP address and
 * user agent.
 * 
 * @since	1.8.0
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Flood_Control {
	public const DEFAULT_INTERVAL = 30;
	public const OPTION_NAME = 'form_block_flood_control_interval';
	public const TRANSIENT_PREFIX = 'form_block_flood_';
	
	/**
	 * Initialize the class.
	 */
	public function init(): void {
		\add_action( 'admin_init', [ self::class, 'register_settings' ] );
		\add_action( 'form_block_pre_validated_data', [ self::class, 'check' ] );
		\add_filter( 'form_block_submit_success_data', [ self::class, 'record' ] );
	}
	
	/**
	 * Check whether the current visitor is submitting too quickly.
	 * 
	 * @param	string	$form_id The form ID
	 */
	public static function check( string $form_id ): void {
		$interval = self::get_interval();
		
		if ( $interval === 0 ) {
			return;
		}
		
		$key = self::get_transient_key();
		
		if ( $key === '' ) {
			return;
		}
		
		$last_submission = \get_transient( $key );
		
		if ( $last_submission === false ) {
			return;
		}
		
		$remaining = \max( 1, (int) $last_submission + $interval - \time() );
		
		/**
		 * Fires when a submission is blocked by flood control.
		 * 
		 * @since	1.8.0
		 * 
		 * @param	string	$form_id The form ID
		 * @param	int		$remaining Remaining seconds until a new submission is allowed
		 */
		\do_action( 'form_block_flood_control_triggered', $form_id, $remaining );
		
		\wp_send_json_error( [
			'message' => \sprintf(
				/* translators: number of seconds to wait */
				\_n(
					'Please wait %d second before submitting again.',
					'Please wait %d seconds before submitting again.',
					$remaining,
					'form-block'
				),
				$remaining
			),
		] );
	}
	
	/**
	 * Get the flood control interval in seconds.
	 * 
	 * @return	int The interval in seconds, or 0 if disabled
	 */
	public static function get_interval(): int {
		$interval = \get_option( self::OPTION_NAME, self::DEFAULT_INTERVAL );
		
		if ( ! \is_numeric( $interval ) || (int) $interval < 0 ) {
			return self::DEFAULT_INTERVAL;
		}
		
		return (int) $interval;
	}
	
	/**
	 * Get the transient key identifying the current visitor.
	 * 
	 * @return	string The transient key, or an empty string if the visitor cannot be identified
	 */
	private static function get_transient_key(): string {
		$ip_address = \sanitize_text_field( \wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) );
		$user_agent = \sanitize_text_field( \wp_unslash( $_SERVER['HTTP_USER_AGENT'] ?? '' ) );
		
		if ( $ip_address === '' && $user_agent === '' ) {
			return '';
		}
		
		return self::TRANSIENT_PREFIX . \md5( $ip_address . '|' . $user_agent );
	}
	
	/**
	 * Record a successful submission to start the flood control window.
	 * 
	 * @param	mixed	$data Current success data
	 * @return	mixed Unchanged success data
	 */
	public static function record( mixed $data ): mixed {
		$interval = self::get_interval();
		
		if ( $interval === 0 ) {
			return $data;
		}
		
		$key = self::get_transient_key();
		
		if ( $key !== '' ) {
			\set_transient( $key, \time(), $interval );
		}
		
		return $data;
	}
	
	/**
	 * Register the settings field.
	 */
	public static function register_settings(): void {
		\add_settings_field(
			self::OPTION_NAME,
			\__( 'Flood control', 'form-block' ),
			[ self::class, 'render_setting' ],
			'form-block',
			'form_block_general'
		);
		\register_setting(
			'form-block',
			self::OPTION_NAME,
			[
				'default' => self::DEFAULT_INTERVAL,
				'sanitize_callback' => [ self::class, 'validate_interval' ],
				'type' => 'number',
			]
		);
	}
	
	/**
	 * Render the settings field.
	 */
	public static function render_setting(): void {
		$value = self::get_interval();
		?>
		<label for="<?php echo \esc_attr( self::OPTION_NAME ); ?>">
			<?php \esc_html_e( 'Prevent visitors from re-submitting any form within', 'form-block' ); ?>
			<input type="number" id="<?php echo \esc_attr( self::OPTION_NAME ); ?>" name="<?php echo \esc_attr( self::OPTION_NAME ); ?>" class="small-text" value="<?php echo \esc_attr( (string) $value ); ?>" min="0" aria-describedby="<?php echo \esc_attr( self::OPTION_NAME ); ?>_description">
			<?php \esc_html_e( 'seconds', 'form-block' ); ?>
		</label>
		<p id="<?php echo \esc_attr( self::OPTION_NAME ); ?>_description"><?php \esc_html_e( 'Visitors are identified by a combination of IP address and user agent. Set to 0 to disable flood control.', 'form-block' ); ?></p>
		<?php
	}
	
	/**
	 * Validate the flood control interval setting.
	 * 
	 * @param	string|null	$value The saved value
	 * @return	int The validated value
	 */
	public static function validate_interval( ?string $value ): int {
		if ( \is_numeric( $value ) && (int) $value >= 0 ) {
			return (int) $value;
		}
		
		return self::DEFAULT_INTERVAL;
	}
}

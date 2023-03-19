<?php
/** @noinspection PhpMissingFieldTypeInspection */
namespace epiphyt\Form_Block;

use function add_action;
use function plugin_dir_path;
use function plugin_dir_url;
use function wp_enqueue_script;
use function wp_enqueue_style;
use const EPI_FORM_BLOCK_FILE;

/**
 * Form Block admin class.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Admin {
	/**
	 * @var		\epiphyt\Form_Block\Admin
	 */
	public static $instance;
	
	/**
	 * Initialize the class.
	 */
	public function init(): void {
		add_action( 'admin_init', [ $this, 'register_options' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'block_assets' ] );
	}
	
	/**
	 * Enqueue block assets in the block editor.
	 */
	public function block_assets(): void {
		$asset_file = include plugin_dir_path( EPI_FORM_BLOCK_FILE ) . 'build/index.asset.php';
		
		wp_enqueue_script( 'form-block-editor', plugin_dir_url( EPI_FORM_BLOCK_FILE ) . 'build/index.js', $asset_file['dependencies'], $asset_file['version'] );
		wp_enqueue_style( 'form-block', plugin_dir_url( EPI_FORM_BLOCK_FILE ) . 'build/style-index.css', [], $asset_file['version'] );
	}
	
	/**
	 * Get a unique instance of the class.
	 * 
	 * @return	\epiphyt\Form_Block\Admin The single instance of this class
	 */
	public static function get_instance(): Admin {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * Get the input for maximum upload size option.
	 */
	public function get_maximum_upload_size_input(): void {
		$maximum_upload_size = floor( wp_max_upload_size() / 1024 / 1024 * 100 ) / 100;
		$option_value = get_option( 'form_block_maximum_upload_size' );
		?>
		<input type="number" id="form_block_maximum_upload_size" name="form_block_maximum_upload_size" value="<?php echo esc_attr( $option_value ); ?>" step=".01" min="0" max="<?php echo esc_attr( $maximum_upload_size ); ?>" class="small-text" /> <?php esc_html_e( 'MiB', 'form-block' ); ?>
		<p>
			<?php
			/* translators: upload size limit */
			printf( esc_html__( 'Your server is capable of uploads in size of %s MiB. Please keep in mind that your mail server could have other (lower) limits.', 'form-block' ), esc_html( number_format_i18n( $maximum_upload_size, 2 ) ) );
			?>
		</p>
		<?php
	}
	
	/**
	 * Get the input for preserve data on uninstall option.
	 */
	public function get_preserve_data_on_uninstall_input(): void {
		$option_value = get_option( 'form_block_preserve_data_on_uninstall' );
		?>
		<label>
			<input type="checkbox" id="form_block_preserve_data_on_uninstall" name="form_block_preserve_data_on_uninstall" value="yes"<?php checked( $option_value, 'yes' ) ?>/>
			<?php esc_html_e( 'Preserve data on uninstall', 'form-block' ); ?>
		</label>
		<p><?php esc_html_e( 'By enabling this option, all plugin data is preserved on uninstall.', 'form-block' ); ?></p>
		<?php
	}
	
	/**
	 * Register options.
	 */
	public function register_options(): void {
		add_settings_section(
			'form_block',
			esc_html__( 'Form Block', 'form-block' ),
			null,
			'writing',
		);
		add_settings_field(
			'form_block_maximum_upload_size',
			__( 'Maximum form upload size', 'form-block' ),
			[ $this, 'get_maximum_upload_size_input' ],
			'writing',
			'form_block',
			[
				'label_for' => 'form_block_maximum_upload_size',
			]
		);
		register_setting(
			'writing',
			'form_block_maximum_upload_size',
			[
				'sanitize_callback' => [ $this, 'validate_maximum_upload_size' ],
				'type' => 'number',
			]
		);
		add_settings_field(
			'form_block_preserve_data_on_uninstall',
			__( 'Data handling', 'form-block' ),
			[ $this, 'get_preserve_data_on_uninstall_input' ],
			'writing',
			'form_block',
		);
		register_setting(
			'writing',
			'form_block_preserve_data_on_uninstall',
			[
				'sanitize_callback' => [ $this, 'validate_preserve_data_on_uninstall' ],
				'type' => 'string',
			]
		);
	}
	
	/**
	 * Validate maximum upload size setting.
	 * 
	 * @param	string	$value The saved value
	 * @return	string The validated value
	 */
	public function validate_maximum_upload_size( string $value ): string {
		// allow empty value to reset
		if ( empty( $value ) ) {
			return '';
		}
		
		if ( ! is_numeric( $value ) ) {
			add_settings_error(
				'form_block_maximum_upload_size',
				'invalid_value',
				/* translators: setting name */
				sprintf( esc_html__( '%s: The entered value is invalid.', 'form-block' ), esc_html__( 'Maximum form upload size', 'form-block' ),
				)
			);
			
			return '';
		}
		
		$byte_value = floor( $value * 1024 * 1024 );
		
		if ( $byte_value > wp_max_upload_size() ) {
			add_settings_error(
				'form_block_maximum_upload_size',
				'invalid_value',
				/* translators: setting name */
				sprintf( esc_html__( '%s: The value must not be greater than the maximum upload size the server is capable of.', 'form-block' ), esc_html__( 'Maximum form upload size', 'form-block' ),
				)
			);
			
			return '';
		}
		
		return $value;
	}
	
	/**
	 * Validate preserve data on uninstall setting.
	 * 
	 * @param	null|string	$value The saved value
	 * @return	string The validated value
	 */
	public function validate_preserve_data_on_uninstall( ?string $value ): string {
		// allow empty value to reset
		if ( empty( $value ) ) {
			return '';
		}
		
		if ( $value !== 'yes' ) {
			add_settings_error(
				'form_block_preserve_data_on_uninstall',
				'invalid_value',
				/* translators: setting name */
				sprintf( esc_html__( '%s: The value is invalid.', 'form-block' ), esc_html__( 'Preserve data on uninstall', 'form-block' ),
				)
			);
			
			return '';
		}
		
		return $value;
	}
}

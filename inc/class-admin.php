<?php
namespace epiphyt\Form_Block;

use epiphyt\Form_Block\submissions\Submission_List_Table;

/**
 * Form Block admin class.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Admin {
	public const PAGE_NAME = 'form-block';
	
	/**
	 * @var		\epiphyt\Form_Block\Admin
	 */
	public static ?self $instance = null;
	
	/**
	 * Initialize the class.
	 */
	public function init(): void {
		\add_action( 'admin_enqueue_scripts', [ self::class, 'enqueue_assets' ] );
		\add_action( 'admin_init', [ $this, 'register_options' ] );
		\add_action( 'admin_menu', [ self::class, 'register_options_page' ] );
		\add_action( 'enqueue_block_editor_assets', [ $this, 'block_assets' ] );
		\add_action( 'load-settings_page_' . self::PAGE_NAME, [ self::class, 'register_screen_options' ] );
		\add_filter( 'set_screen_option_submissions_per_page', [ self::class, 'save_per_page_screen_option' ], 10, 3 );
		\add_filter( 'wp_script_attributes', [ self::class, 'set_script_attributes' ] );
	}
	
	/**
	 * Enqueue block assets in the block editor.
	 */
	public function block_assets(): void {
		\wp_set_script_translations( 'form-block-editor', 'form-block' );
	}
	
	/**
	 * Enqueue admin assets.
	 */
	public static function enqueue_assets(): void {
		$screen = \get_current_screen();
		
		if ( ! $screen instanceof \WP_Screen ) {
			return;
		}
		
		$is_debug = ( \defined( 'WP_DEBUG' ) && \WP_DEBUG ) || ( \defined( 'SCRIPT_DEBUG' ) && \SCRIPT_DEBUG );
		$suffix = $is_debug ? '' : '.min';
		
		if ( $screen->id === 'settings_page_form-block' ) {
			$asset_path = \EPI_FORM_BLOCK_BASE . 'assets/js/' . ( $is_debug ? '' : 'build/' ) . 'snackbar' . $suffix . '.js';
			$asset_url = \EPI_FORM_BLOCK_URL . 'assets/js/' . ( $is_debug ? '' : 'build/' ) . 'snackbar' . $suffix . '.js';
			$version = $is_debug ? (string) \filemtime( $asset_path ) : \FORM_BLOCK_VERSION;
			
			\wp_enqueue_script( 'form-block-admin-snackbar', $asset_url, [], $version, [ 'strategy' => 'defer' ] );
			
			$asset_path = \EPI_FORM_BLOCK_BASE . 'assets/js/' . ( $is_debug ? '' : 'build/' ) . 'admin' . $suffix . '.js';
			$asset_url = \EPI_FORM_BLOCK_URL . 'assets/js/' . ( $is_debug ? '' : 'build/' ) . 'admin' . $suffix . '.js';
			$version = $is_debug ? (string) \filemtime( $asset_path ) : \FORM_BLOCK_VERSION;
			
			\wp_enqueue_script( 'form-block-admin', $asset_url, [], $version, [ 'strategy' => 'defer' ] );
			\wp_localize_script(
				'form-block-admin',
				'formBlockAdmin',
				[
					'nonce' => \wp_create_nonce( 'wp_rest' ),
					'restRootUrl' => \esc_url( \rest_url() ),
					'submissionRemovedError' => \__( 'Submission could not be removed.', 'form-block' ),
					'submissionRemovedSuccess' => \__( 'Submission removed successfully.', 'form-block' ),
				]
			);
			
			$asset_path = \EPI_FORM_BLOCK_BASE . 'assets/style/build/admin' . $suffix . '.css';
			$asset_url = \EPI_FORM_BLOCK_URL . 'assets/style/build/admin' . $suffix . '.css';
			$version = $is_debug ? (string) \filemtime( $asset_path ) : \FORM_BLOCK_VERSION;
			
			\wp_enqueue_style( 'form-block-admin', $asset_url, [], $version );
		}
	}
	
	/**
	 * Get a unique instance of the class.
	 * 
	 * @return	\epiphyt\Form_Block\Admin The single instance of this class
	 */
	public static function get_instance(): self {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * Get the input for maximum upload size option.
	 */
	public function get_maximum_upload_size_input(): void {
		$maximum_upload_size = \floor( \wp_max_upload_size() / 1024 / 1024 * 100 ) / 100;
		$option_value = \get_option( 'form_block_maximum_upload_size' );
		?>
		<input type="number" id="form_block_maximum_upload_size" name="form_block_maximum_upload_size" value="<?php echo \esc_attr( $option_value ); ?>" step=".01" min="0" max="<?php echo \esc_attr( $maximum_upload_size ); ?>" class="small-text" /> <?php \esc_html_e( 'MiB', 'form-block' ); ?>
		<p>
			<?php
			/* translators: upload size limit */
			\printf( \esc_html__( 'Your server is capable of uploads in size of %s MiB. Please keep in mind that your mail server could have other (lower) limits.', 'form-block' ), \esc_html( \number_format_i18n( $maximum_upload_size, 2 ) ) );
			?>
		</p>
		<?php
	}
	
	/**
	 * Get options page HTML.
	 */
	public static function get_options_page_html(): void {
		$table = new Submission_List_Table();
		$table->init();
		
		echo '<div class="wrap">';
		echo '<h1>' . \esc_html__( 'Form Block', 'form-block' ) . '</h1>';
		echo '<form method="post">';
		$table->prepare_items();
		$table->search_box( \__( 'Search Submissions', 'form-block' ), 'search_id' );
		$table->display();
		echo '</form';
		echo '</div>'; // .wrap
	}
	
	/**
	 * Get the input for preserve data on uninstall option.
	 */
	public function get_preserve_data_on_uninstall_input(): void {
		$option_value = \get_option( 'form_block_preserve_data_on_uninstall' );
		?>
		<label>
			<input type="checkbox" id="form_block_preserve_data_on_uninstall" name="form_block_preserve_data_on_uninstall" value="yes"<?php \checked( $option_value, 'yes' ) ?>/>
			<?php \esc_html_e( 'Preserve data on uninstall', 'form-block' ); ?>
		</label>
		<p><?php \esc_html_e( 'By enabling this option, all plugin data is preserved on uninstall.', 'form-block' ); ?></p>
		<?php
	}
	
	/**
	 * Get the input for preserve data on uninstall option.
	 */
	public function get_save_submissions_input(): void {
		$option_value = \get_option( 'form_block_save_submissions' );
		?>
		<label>
			<input type="checkbox" id="form_block_save_submissions" name="form_block_save_submissions" value="yes"<?php \checked( $option_value, 'yes' ) ?>/>
			<?php \esc_html_e( 'Save submissions', 'form-block' ); ?>
		</label>
		<p><?php \esc_html_e( 'By enabling this option, all submissions will be saved in WordPress as well.', 'form-block' ); ?></p>
		<?php
	}
	
	/**
	 * Register options.
	 */
	public function register_options(): void {
		\add_settings_section(
			'form_block',
			\esc_html__( 'Form Block', 'form-block' ),
			null,
			'writing'
		);
		\add_settings_field(
			'form_block_maximum_upload_size',
			\__( 'Maximum form upload size', 'form-block' ),
			[ $this, 'get_maximum_upload_size_input' ],
			'writing',
			'form_block',
			[
				'label_for' => 'form_block_maximum_upload_size',
			]
		);
		\register_setting(
			'writing',
			'form_block_maximum_upload_size',
			[
				'sanitize_callback' => [ $this, 'validate_maximum_upload_size' ],
				'type' => 'number',
			]
		);
		\add_settings_field(
			'form_block_save_submissions',
			\__( 'Data handling', 'form-block' ),
			[ $this, 'get_save_submissions_input' ],
			'writing',
			'form_block'
		);
		\register_setting(
			'writing',
			'form_block_save_submissions',
			[
				'sanitize_callback' => [ $this, 'validate_save_submissions' ],
				'type' => 'string',
			]
		);
		\add_settings_field(
			'form_block_preserve_data_on_uninstall',
			'',
			[ $this, 'get_preserve_data_on_uninstall_input' ],
			'writing',
			'form_block'
		);
		\register_setting(
			'writing',
			'form_block_preserve_data_on_uninstall',
			[
				'sanitize_callback' => [ $this, 'validate_preserve_data_on_uninstall' ],
				'type' => 'string',
			]
		);
	}
	
	/**
	 * Register options page.
	 */
	public static function register_options_page(): void {
		\add_submenu_page(
			'options-general.php',
			\__( 'Form Block', 'form-block' ),
			\__( 'Form Block', 'form-block' ),
			'manage_options',
			self::PAGE_NAME,
			[ self::class, 'get_options_page_html' ]
		);
	}
	
	/**
	 * Register screen options.
	 */
	public static function register_screen_options(): void {
		$screen = \get_current_screen();
		
		if ( ! $screen instanceof \WP_Screen || $screen->id !== 'settings_page_' . self::PAGE_NAME ) {
			return;
		}
		
		\add_screen_option(
			'per_page',
			[
				'default' => 20,
				'label' => \__( 'Submissions per page', 'form-block' ),
				'option' => 'submissions_per_page',
			]
		);
	}
	
	/**
	 * Save 'per page' screen option.
	 * 
	 * @param	mixed	$screen_option Value to save instead of the option value
	 * @param	string	$option Option name
	 * @param	int		$value Option value
	 * @return	int Option value
	 */
	public static function save_per_page_screen_option( mixed $screen_option, string $option, int $value ): int {
		return $value;
	}
	
	/**
	 * Set script attribute 'module'.
	 * 
	 * @param	string[]	$attributes List of attributes to set
	 * @return	string[] Updated attributes
	 */
	public static function set_script_attributes( array $attributes ): array {
		if ( ! empty( $attributes['id'] ) && \str_starts_with( $attributes['id'], 'form-block-admin' ) ) {
			$attributes['type'] = 'module';
		}
		
		return $attributes;
	}
	
	/**
	 * Validate a checkbox setting.
	 * 
	 * @param	string|null	$value Saved value
	 * @param	string		$option Option name
	 * @param	string		$title Option title
	 * @return	string Validated value
	 */
	public static function validate_checkbox( ?string $value, string $option, string $title ): string {
		// allow empty value to reset
		if ( empty( $value ) ) {
			return '';
		}
		
		if ( $value !== 'yes' ) {
			\add_settings_error(
				$option,
				'invalid_value',
				/* translators: setting name */
				\sprintf( \esc_html__( '%s: The value is invalid.', 'form-block' ), \esc_html( $title ) )
			);
			
			return '';
		}
		
		return $value;
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
		
		if ( ! \is_numeric( $value ) ) {
			\add_settings_error(
				'form_block_maximum_upload_size',
				'invalid_value',
				/* translators: setting name */
				\sprintf( \esc_html__( '%s: The entered value is invalid.', 'form-block' ), \esc_html__( 'Maximum form upload size', 'form-block' )
				)
			);
			
			return '';
		}
		
		$byte_value = \floor( $value * 1024 * 1024 );
		
		if ( $byte_value > \wp_max_upload_size() ) {
			\add_settings_error(
				'form_block_maximum_upload_size',
				'invalid_value',
				/* translators: setting name */
				\sprintf( \esc_html__( '%s: The value must not be greater than the maximum upload size the server is capable of.', 'form-block' ), \esc_html__( 'Maximum form upload size', 'form-block' )
				)
			);
			
			return '';
		}
		
		return $value;
	}
	
	/**
	 * Validate preserve data on uninstall setting.
	 * 
	 * @param	string|null	$value The saved value
	 * @return	string The validated value
	 */
	public function validate_preserve_data_on_uninstall( ?string $value ): string {
		return self::validate_checkbox( $value, 'form_block_preserve_data_on_uninstall', \__( 'Preserve data on uninstall', 'form-block' ) );
	}
	
	/**
	 * Validate save submissions setting.
	 * 
	 * @param	string|null	$value The saved value
	 * @return	string The validated value
	 */
	public function validate_save_submissions( ?string $value ): string {
		return self::validate_checkbox( $value, 'form_block_save_submissions', \__( 'Save submissions', 'form-block' ) );
	}
}

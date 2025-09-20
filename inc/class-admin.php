<?php
namespace epiphyt\Form_Block;

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
		
		if ( $screen->id === 'settings_page_form-block' || $screen->id === 'tools_page_form-block-submissions' ) {
			$asset_path = \EPI_FORM_BLOCK_BASE . 'assets/style/build/admin' . $suffix . '.css';
			$asset_url = \EPI_FORM_BLOCK_URL . 'assets/style/build/admin' . $suffix . '.css';
			$version = $is_debug ? (string) \filemtime( $asset_path ) : \FORM_BLOCK_VERSION;
			
			\wp_enqueue_style( 'form-block-admin', $asset_url, [], $version );
		}
		
		if ( $screen->id === 'settings_page_form-block' ) {
			$asset_path = \EPI_FORM_BLOCK_BASE . 'assets/js/' . ( $is_debug ? '' : 'build/' ) . 'tabs' . $suffix . '.js';
			$asset_url = \EPI_FORM_BLOCK_URL . 'assets/js/' . ( $is_debug ? '' : 'build/' ) . 'tabs' . $suffix . '.js';
			$version = $is_debug ? (string) \filemtime( $asset_path ) : \FORM_BLOCK_VERSION;
			
			\wp_enqueue_script( 'form-block-admin-tabs', $asset_url, [], $version, [ 'strategy' => 'defer' ] );
		}
		
		if ( $screen->id === 'tools_page_form-block-submissions' ) {
			$asset_path = \EPI_FORM_BLOCK_BASE . 'assets/js/' . ( $is_debug ? '' : 'build/' ) . 'snackbar' . $suffix . '.js';
			$asset_url = \EPI_FORM_BLOCK_URL . 'assets/js/' . ( $is_debug ? '' : 'build/' ) . 'snackbar' . $suffix . '.js';
			$version = $is_debug ? (string) \filemtime( $asset_path ) : \FORM_BLOCK_VERSION;
			
			\wp_enqueue_script( 'form-block-admin-snackbar', $asset_url, [], $version, [ 'strategy' => 'defer' ] );
			
			$asset_path = \EPI_FORM_BLOCK_BASE . 'assets/js/' . ( $is_debug ? '' : 'build/' ) . 'submissions' . $suffix . '.js';
			$asset_url = \EPI_FORM_BLOCK_URL . 'assets/js/' . ( $is_debug ? '' : 'build/' ) . 'submissions' . $suffix . '.js';
			$version = $is_debug ? (string) \filemtime( $asset_path ) : \FORM_BLOCK_VERSION;
			
			\wp_enqueue_script( 'form-block-admin-submissions', $asset_url, [], $version, [ 'strategy' => 'defer' ] );
			\wp_localize_script(
				'form-block-admin-submissions',
				'formBlockSubmissions',
				[
					'nonce' => \wp_create_nonce( 'wp_rest' ),
					'restRootUrl' => \esc_url( \rest_url() ),
					'submissionRemovedError' => \__( 'Submission could not be removed.', 'form-block' ),
					'submissionRemovedSuccess' => \__( 'Submission removed successfully.', 'form-block' ),
				]
			);
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
		// check user capabilities
		if ( ! \current_user_can( 'manage_options' ) ) {
			return;
		}
		
		// show error/update messages
		\settings_errors( 'form_block_messages' );
		
		$tabs = self::get_options_tabs();
		
		/**
		 * Filter the default tab.
		 * 
		 * @param	string	$default_tab The default tab
		 */
		$default_tab = \apply_filters( 'form_block_admin_options_default_tab', 'general' );
		
		// get current tab
		$current_tab = isset( $_GET['tab'] ) ? \sanitize_text_field( \wp_unslash( $_GET['tab'] ) ) : $default_tab; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		
		if ( empty( $tabs[ $current_tab ] ) ) {
			$current_tab = $default_tab;
		}
		
		echo '<div class="wrap form-block__settings">';
		echo '<h1>' . \esc_html__( 'Form Block', 'form-block' ) . '</h1>';
		echo '<form action="' . \esc_url( \admin_url( 'options.php' ) ) . '" method="post">';
		echo '<input type="hidden" name="option_page" value="form-block" />';
		echo '<input type="hidden" name="action" value="update" />';
		
		\wp_nonce_field( 'form-block-options', '_wpnonce', false );
		
		$referer = \remove_query_arg( '_wp_http_referer' );
		
		if ( ! \str_contains( $referer, '&tab=' ) && $current_tab !== $default_tab ) {
			$referer .= '&tab=' . $current_tab;
		}
		
		echo '<input type="hidden" name="_wp_http_referer" value="' . \esc_url( $referer ) . '" />';
		echo '<div class="nav-tab-wrapper" role="tablist">';
		
		foreach ( $tabs as $tab ) {
			if ( empty( $tab['name'] ) || empty( $tab['title'] ) ) {
				continue;
			}
			
			$is_active_tab = $current_tab === $tab['name'];
			
			echo '<button type="button" id="tab-' . \esc_attr( $tab['name'] ) . '" data-tab="' . \esc_attr( $tab['name'] ) . '" class="nav-tab' . ( $is_active_tab ? ' nav-tab-active' : '' ) . '" role="tab" aria-selected="' . ( $is_active_tab ? 'true' : 'false' ) . '" data-slug="' . \esc_attr( $tab['name'] ) . '" tabindex="' . ( $is_active_tab ? '0' : '-1' ) . '">' . \esc_html( $tab['title'] ) . '</button>'; // @phpstan-ignore Generic.Strings.UnnecessaryStringConcat.Found
		}
		
		echo '</div>'; // .nav-tab-wrapper
		echo '<div class="form-block__content-wrapper">';
		
		foreach ( $tabs as $tab ) {
			$is_active_tab = $current_tab === $tab['name'];
			
			if ( \is_callable( $tab['callback'] ) ) {
				echo '<div id="nav-tab__content--' . \esc_attr( $tab['name'] ) . '" class="nav-tab__content" role="tabpanel" data-tab="' . \esc_attr( $tab['name'] ) . '" aria-labelledby="tab-' . \esc_attr( $tab['name'] ) . '"' . ( ! $is_active_tab ? ' hidden' : '' ) . ' tabindex="' . ( $is_active_tab ? '0' : '-1' ) . '">';
				echo \call_user_func( $tab['callback'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, NeutronStandard.Functions.DisallowCallUserFunc.CallUserFunc
				echo '</div>';
			}
		}
		
		echo '</div>'; // .form-block__content-wrapper
		
		\submit_button( \esc_html__( 'Save Settings', 'form-block' ) );
		
		echo '</form>';
		echo '</div>'; // .wrap
	}
	
	/**
	 * Get all options tabs.
	 * 
	 * @return	array<string, array{callback: callable, name: string, title: string}> Admin options tabs
	 */
	public static function get_options_tabs(): array {
		$tabs = [
			'general' => [
				'callback' => [ self::class, 'get_settings_tab_html' ],
				'name' => 'general',
				'title' => \__( 'Settings', 'form-block' ),
			],
			'pro' => [
				'callback' => [ self::class, 'get_pro_tab_html' ],
				'name' => 'pro',
				'title' => \__( 'Get Pro', 'form-block' ),
			],
		];
		
		/**
		 * Filter admin options tabs.
		 * 
		 * @param	array<string, array{callback: callable, name: string, title: string}>	$tabs Admin options tabs
		 */
		$tabs = (array) \apply_filters( 'form_block_admin_options_tabs', $tabs );
		
		return $tabs;
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
	 * Get Pro tab HTML.
	 * 
	 * @return	string Pro tab HTML markup
	 */
	public static function get_pro_tab_html(): string {
		\ob_start();
		?>
		<h2><?php \esc_html_e( 'You want more? Check out Form Block Pro!', 'form-block' ); ?></h2>
		<p><?php \esc_html_e( 'Form Block can handle basic (contact) forms. If you have special needs or need particular features, you can find more of them in Form Block Pro with many additional features and enhancements for Form Block.', 'form-block' ); ?></p>
		
		<h3><?php \esc_html_e( 'Go Pro to support development', 'form-block' ); ?></h3>
		<p>
			<?php
			/* translators: commercial plugin name */
			\printf( \esc_html__( 'Even as a private website owner you can upgrade to %s anytime. Every single Pro user means the world to us, since it\'s those users who support our ongoing work on both the free and paid version. In addition, we\'ll continue to add even more nifty features to Pro.', 'form-block' ), \esc_html__( 'Form Block Pro', 'form-block' ) );
			?>
		</p>
		<p><a href="<?php echo \esc_url( \__( 'https://formblock.pro/en/', 'form-block' ) ); ?>" class="button button-primary button-hero"><?php \esc_html_e( 'Get Form Block Pro now', 'form-block' ); ?></a></p>
		
		<h3><?php \esc_html_e( 'Compare now', 'form-block' ); ?></h3>
		<table class="wp-list-table widefat striped form-block__compare-table">
			<tbody>
				<thead>
					<th><strong><?php \esc_html_e( 'Feature', 'form-block' ); ?></strong></th>
					<th><strong><?php \esc_html_e( 'Form Block', 'form-block' ); ?></strong></th>
					<th><strong><?php \esc_html_e( 'Form Block Pro', 'form-block' ); ?></strong></th>
				</thead>
				<tr>
					<td><?php \esc_html_e( 'Accessible forms', 'form-block' ); ?></td>
					<td><span class="green"><span class="dashicons dashicons-yes" aria-hidden="true"></span> <?php \esc_html_e( 'Yes', 'form-block' ); ?></span></td>
					<td><span class="green"><span class="dashicons dashicons-yes" aria-hidden="true"></span> <?php \esc_html_e( 'Yes', 'form-block' ); ?></span></td>
				</tr>
				<tr>
					<td><?php \esc_html_e( 'Seamless block editor integration', 'form-block' ); ?></td>
					<td><span class="green"><span class="dashicons dashicons-yes" aria-hidden="true"></span> <?php \esc_html_e( 'Yes', 'form-block' ); ?></span></td>
					<td><span class="green"><span class="dashicons dashicons-yes" aria-hidden="true"></span> <?php \esc_html_e( 'Yes', 'form-block' ); ?></span></td>
				</tr>
				<tr>
					<td><?php \esc_html_e( 'Integrated honeypot', 'form-block' ); ?></td>
					<td><span class="green"><span class="dashicons dashicons-yes" aria-hidden="true"></span> <?php \esc_html_e( 'Yes', 'form-block' ); ?></span></td>
					<td><span class="green"><span class="dashicons dashicons-yes" aria-hidden="true"></span> <?php \esc_html_e( 'Yes', 'form-block' ); ?></span></td>
				</tr>
				<tr>
					<td><?php \esc_html_e( 'Manage submissions within WordPress', 'form-block' ); ?></td>
					<td><span class="green"><span class="dashicons dashicons-yes" aria-hidden="true"></span> <?php \esc_html_e( 'Yes', 'form-block' ); ?></span></td>
					<td><span class="green"><span class="dashicons dashicons-yes" aria-hidden="true"></span> <?php \esc_html_e( 'Yes', 'form-block' ); ?></span></td>
				</tr>
				<tr>
					<td><?php \esc_html_e( 'Knowledge base', 'form-block' ); ?></td>
					<td><span class="green"><span class="dashicons dashicons-yes" aria-hidden="true"></span> <?php \esc_html_e( 'Yes', 'form-block' ); ?></span></td>
					<td><span class="green"><span class="dashicons dashicons-yes" aria-hidden="true"></span> <?php \esc_html_e( 'Yes', 'form-block' ); ?></span></td>
				</tr>
				<tr>
					<td><?php \esc_html_e( 'Server-side validation checks', 'form-block' ); ?></td>
					<td>
						<span class="grey"><span class="dashicons dashicons-marker" aria-hidden="true"></span> <?php \esc_html_e( 'Some', 'form-block' ); ?></span><br>
						<?php \esc_html_e( '(basic checks)', 'form-block' ); ?>
					</td>
					<td>
						<span class="green"><span class="dashicons dashicons-yes" aria-hidden="true"></span> <?php \esc_html_e( 'Many', 'form-block' ); ?></span><br>
						<?php \esc_html_e( '(enhanced checks for each field attribute)', 'form-block' ); ?>
					</td>
				</tr>
				<tr>
					<td><?php \esc_html_e( 'Advanced accessibility functionality', 'form-block' ); ?></td>
					<td><span class="red"><span class="dashicons dashicons-no" aria-hidden="true"></span> <?php \esc_html_e( 'No', 'form-block' ); ?></span></td>
					<td><span class="green"><span class="dashicons dashicons-yes" aria-hidden="true"></span> <?php \esc_html_e( 'Yes', 'form-block' ); ?></span></td>
				</tr>
				<tr>
					<td><?php \esc_html_e( 'Multiple recipients', 'form-block' ); ?></td>
					<td><span class="red"><span class="dashicons dashicons-no" aria-hidden="true"></span> <?php \esc_html_e( 'No', 'form-block' ); ?></span></td>
					<td><span class="green"><span class="dashicons dashicons-yes" aria-hidden="true"></span> <?php \esc_html_e( 'Yes', 'form-block' ); ?></span></td>
				</tr>
				<tr>
					<td><?php \esc_html_e( 'Field dependencies', 'form-block' ); ?></td>
					<td><span class="red"><span class="dashicons dashicons-no" aria-hidden="true"></span> <?php \esc_html_e( 'No', 'form-block' ); ?></span></td>
					<td><span class="green"><span class="dashicons dashicons-yes" aria-hidden="true"></span> <?php \esc_html_e( 'Yes', 'form-block' ); ?></span></td>
				</tr>
				<tr>
					<td><?php \esc_html_e( 'Drag-and-drop upload zone', 'form-block' ); ?></td>
					<td><span class="red"><span class="dashicons dashicons-no" aria-hidden="true"></span> <?php \esc_html_e( 'No', 'form-block' ); ?></span></td>
					<td><span class="green"><span class="dashicons dashicons-yes" aria-hidden="true"></span> <?php \esc_html_e( 'Yes', 'form-block' ); ?></span></td>
				</tr>
				<tr>
					<td><?php \esc_html_e( 'Maximum upload size per file/form', 'form-block' ); ?></td>
					<td><span class="red"><span class="dashicons dashicons-no" aria-hidden="true"></span> <?php \esc_html_e( 'No', 'form-block' ); ?></span></td>
					<td><span class="green"><span class="dashicons dashicons-yes" aria-hidden="true"></span> <?php \esc_html_e( 'Yes', 'form-block' ); ?></span></td>
				</tr>
				<tr>
					<td><?php \esc_html_e( 'Local file uploads', 'form-block' ); ?></td>
					<td><span class="red"><span class="dashicons dashicons-no" aria-hidden="true"></span> <?php \esc_html_e( 'No', 'form-block' ); ?></span></td>
					<td><span class="green"><span class="dashicons dashicons-yes" aria-hidden="true"></span> <?php \esc_html_e( 'Yes', 'form-block' ); ?></span></td>
				</tr>
				<tr>
					<td><?php \esc_html_e( 'Custom submission messages', 'form-block' ); ?></td>
					<td><span class="red"><span class="dashicons dashicons-no" aria-hidden="true"></span> <?php \esc_html_e( 'No', 'form-block' ); ?></span></td>
					<td><span class="green"><span class="dashicons dashicons-yes" aria-hidden="true"></span> <?php \esc_html_e( 'Yes', 'form-block' ); ?></span></td>
				</tr>
				<tr>
					<td><?php \esc_html_e( 'Custom redirect after submission', 'form-block' ); ?></td>
					<td><span class="red"><span class="dashicons dashicons-no" aria-hidden="true"></span> <?php \esc_html_e( 'No', 'form-block' ); ?></span></td>
					<td><span class="green"><span class="dashicons dashicons-yes" aria-hidden="true"></span> <?php \esc_html_e( 'Yes', 'form-block' ); ?></span></td>
				</tr>
				<tr>
					<td><br></td>
					<td></td>
					<td>
						<a href="<?php echo \esc_url( \__( 'https://epiph.yt/en/?add-to-cart=372', 'form-block' ) ); ?>" class="button button-primary"><?php \esc_html_e( 'Purchase', 'form-block' ); ?> <span class="screen-reader-text"><?php \esc_html_e( 'Form Block Pro', 'form-block' ); ?></span></a>
						<a href="<?php echo \esc_url( \__( 'https://formblock.pro/en/', 'form-block' ) ); ?>" class="button button-secondary"><?php \esc_html_e( 'More information', 'form-block' ); ?> <span class="screen-reader-text"><?php echo \esc_html_x( 'about Form Block Pro', 'more information about the plugin', 'form-block' ); ?></a>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
		return (string) \ob_get_clean();
	}
	
	/**
	 * Get the input for submissions auto-delete option.
	 */
	public function get_submissions_auto_delete_input(): void {
		$option_value = \get_option( 'form_block_submissions_auto_delete', 30 );
		?>
		<label for="form_block_submissions_auto_delete">
			<?php \esc_html_e( 'Delete locally stored submissions after', 'form-block' ); ?>
			<input type="number" id="form_block_submissions_auto_delete" name="form_block_submissions_auto_delete" class="small-text" value="<?php echo \esc_attr( $option_value ); ?>" min="0" aria-describedby="form_block_submissions_auto_delete_description">
			<?php \esc_html_e( 'Days', 'form-block' ); ?>
		</label>
		<p id="form_block_submissions_auto_delete_description"><?php \esc_html_e( 'Set the days after which form submissions are deleted automatically. Set to 0 to disable automatic deletion.', 'form-block' ); ?></p>
		<?php
	}
	
	/**
	 * Get settings tab HTML.
	 * 
	 * @return	string Settings tab HTML markup
	 */
	public static function get_settings_tab_html(): string {
		\ob_start();
		
		echo '<h2>' . \esc_html__( 'Form Block Settings', 'form-block' ) . '</h2>';
		echo '<table class="form-table" role="presentation">';
		
		\do_settings_fields( 'form-block', 'form_block_general' );
		
		/**
		 * Fires after the general Form Block settings in the settings tab.
		 */
		\do_action( 'form_block_settings_page' );
		
		echo '</table>';
		
		return (string) \ob_get_clean();
	}
	
	/**
	 * Register options.
	 */
	public function register_options(): void {
		\add_settings_section(
			'form_block_general',
			\esc_html__( 'Form Block', 'form-block' ),
			'__return_empty_string',
			'form-block'
		);
		\add_settings_field(
			'form_block_maximum_upload_size',
			\__( 'Maximum form upload size', 'form-block' ),
			[ $this, 'get_maximum_upload_size_input' ],
			'form-block',
			'form_block_general',
			[
				'label_for' => 'form_block_maximum_upload_size',
			]
		);
		\register_setting(
			'form-block',
			'form_block_maximum_upload_size',
			[
				'sanitize_callback' => [ $this, 'validate_maximum_upload_size' ],
				'type' => 'number',
			]
		);
		\add_settings_field(
			'form_block_submissions_auto_delete',
			\__( 'Data handling', 'form-block' ),
			[ $this, 'get_submissions_auto_delete_input' ],
			'form-block',
			'form_block_general'
		);
		\register_setting(
			'form-block',
			'form_block_submissions_auto_delete',
			[
				'sanitize_callback' => [ $this, 'validate_submissions_auto_delete' ],
				'type' => 'number',
			]
		);
		\add_settings_field(
			'form_block_preserve_data_on_uninstall',
			'',
			[ $this, 'get_preserve_data_on_uninstall_input' ],
			'form-block',
			'form_block_general'
		);
		\register_setting(
			'form-block',
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
		if (
			! empty( $attributes['id'] )
			&& (
				\str_starts_with( $attributes['id'], 'form-block-admin-snackbar' )
				|| \str_starts_with( $attributes['id'], 'form-block-admin-submissions' )
				|| \str_starts_with( $attributes['id'], 'form-block-admin-tabs' )
			)
		) {
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
	 * Validate submissions auto-delete setting.
	 * 
	 * @param	string|null	$value The saved value
	 * @return	int The validated value
	 */
	public function validate_submissions_auto_delete( ?string $value ): string {
		if ( \is_numeric( $value ) && (int) $value >= 0 ) {
			return (int) $value;
		}
		
		return 0;
	}
}

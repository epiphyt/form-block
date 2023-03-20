<?php
namespace epiphyt\Form_Block\blocks;

use epiphyt\Form_Block\Form_Block;

/**
 * Form block class.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Form {
	const MAX_INT = 2147483647;
	
	/**
	 * @var		\epiphyt\Form_Block\blocks\Form
	 */
	public static $instance;
	
	/**
	 * Initialize the class.
	 */
	public function init(): void {
		add_action( 'init', [ $this, 'enqueue_block_styles' ] );
		add_action( 'init', [ $this, 'register_frontend_assets' ] );
		add_action( 'render_block_form-block/form', [ $this, 'add_action' ], 10, 2 );
		add_action( 'render_block_form-block/form', [ $this, 'add_form_id_input' ], 10, 2 );
		add_action( 'render_block_form-block/form', [ $this, 'add_honeypot' ], 10, 2 );
		add_action( 'render_block_form-block/form', [ $this, 'add_method' ], 10, 2 );
		
		register_block_type(
			'form-block/form',
			[
				'view_script' => 'form-block-form', // WP 5.9
				'view_script_handles' => [ // since WP 6.1
					'form-block-form',
				],
			],
		);
	}
	
	/**
	 * Add the form action.
	 *
	 * @param	string	$block_content The block content
	 * @param	array	$block Block attributes
	 * @return	string Updated block content
	 */
	public function add_action( string $block_content, array $block ): string {
		$url = Form_Block::get_instance()->get_current_request_url();
		
		/**
		 * Filter the form action URL.
		 * 
		 * @param	string	$url The action URL
		 * @param	string	$block_content The block content
		 * @param	array	$block Block attributes
		 */
		$url = apply_filters( 'form_block_form_action', $url, $block_content, $block );
		
		return str_replace( '<form', '<form action="' . esc_url( $url ) . '"', $block_content );
	}
	
	/**
	 * Add the form ID input field.
	 *
	 * @param	string	$block_content The block content
	 * @param	array	$block Block attributes
	 * @return	string Updated block content
	 */
	public function add_form_id_input( string $block_content, array $block ): string {
		if ( empty( $block['attrs']['formId'] ) ) {
			return $block_content;
		}
		
		/**
		 * Filter the form ID.
		 * 
		 * @param	string	$form_id The form ID
		 * @param	string	$block_content The block content
		 * @param	array	$block Block attributes
		 */
		$form_id = apply_filters( 'form_block_form_form_id', $block['attrs']['formId'], $block_content, $block );
		
		$form_id_input = '<input type="hidden" name="_form_id" value="' . esc_attr( $form_id ) . '" />';
		
		/**
		 * Filter the form ID input.
		 * 
		 * @param	string	$form_id_input The form ID input
		 * @param	string	$form_id The form ID
		 * @param	string	$block_content The block content
		 * @param	array	$block Block attributes
		 */
		$form_id_input = apply_filters( 'form_block_form_id_input', $form_id_input, $form_id, $block_content, $block );
		
		return str_replace( 'enctype="multipart/form-data" novalidate>', 'enctype="multipart/form-data" novalidate>' . PHP_EOL . $form_id_input, $block_content );
	}
	
	/**
	 * Add the honeypot code.
	 *
	 * @param	string	$block_content The block content
	 * @param	array	$block Block attributes
	 * @return	string Updated block content
	 */
	public function add_honeypot( string $block_content, array $block ): string {
		$honeypot = '<div class="wp-block-form-block-input form-block__element"><input name="_town" id="id-_town" type="text"aria-hidden="true" autocomplete="new-password" style="padding: 0; clip: rect(1px, 1px, 1px, 1px); position: absolute !important; white-space: nowrap; height: 1px; width: 1px; overflow: hidden;" tabindex="-1" /></div>';
		
		/**
		 * Filter the honeypot code.
		 * 
		 * @param	string	$honeypot The honeypot code
		 * @param	string	$block_content The block content
		 * @param	array	$block Block attributes
		 */
		$honeypot = apply_filters( 'form_block_honeypot_code', $honeypot, $block_content, $block );
		
		return str_replace( '</form>', $honeypot . '</form>', $block_content );
	}
	
	/**
	 * Add the form method.
	 *
	 * @param	string	$block_content The block content
	 * @param	array	$block Block attributes
	 * @return	string Updated block content
	 */
	public function add_method( string $block_content, array $block ): string {
		/**
		 * Filter the form method.
		 * 
		 * @param	string	$method The form method
		 * @param	string	$block_content The block content
		 * @param	array	$block Block attributes
		 */
		$method = apply_filters( 'form_block_form_method', 'POST', $block_content, $block );
		
		return str_replace( '<form', '<form method="' . esc_attr( $method ) . '"', $block_content );
	}
	
	/**
	 * Enqueue block styles.
	 */
	public function enqueue_block_styles(): void {
		$is_debug = defined( 'WP_DEBUG' ) && WP_DEBUG;
		$suffix = ( $is_debug ? '' : '.min' );
		
		wp_enqueue_block_style(
			'form-block/form',
			[
				'handle' => 'form-block',
				'src' => plugin_dir_url( EPI_FORM_BLOCK_FILE ) . 'assets/style/build/form' . $suffix . '.css',
				'deps' => [],
				'ver' => $is_debug ? filemtime( plugin_dir_path( EPI_FORM_BLOCK_FILE ) . 'assets/style/build/form' . $suffix . '.css' ) : FORM_BLOCK_VERSION,
			]
		);
	}
	
	/**
	 * Get a unique instance of the class.
	 * 
	 * @return	\epiphyt\Form_Block\blocks\Form The single instance of this class
	 */
	public static function get_instance(): Form {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * Register frontend assets.
	 */
	public function register_frontend_assets(): void {
		$is_debug = defined( 'WP_DEBUG' ) && WP_DEBUG;
		$suffix = ( $is_debug ? '' : '.min' );
		$file_path = plugin_dir_path( EPI_FORM_BLOCK_FILE ) . 'assets/js/' . ( $is_debug ? '' : 'build/' ) . 'validator' . $suffix . '.js';
		$file_url = plugin_dir_url( EPI_FORM_BLOCK_FILE ) . 'assets/js/' . ( $is_debug ? '' : 'build/' ) . 'validator' . $suffix . '.js';
		
		wp_register_script( 'form-block-validator', $file_url, [], $is_debug ? filemtime( $file_path ) : FORM_BLOCK_VERSION, true );
		
		$file_path = plugin_dir_path( EPI_FORM_BLOCK_FILE ) . 'assets/js/' . ( $is_debug ? '' : 'build/' ) . 'validation' . $suffix . '.js';
		$file_url = plugin_dir_url( EPI_FORM_BLOCK_FILE ) . 'assets/js/' . ( $is_debug ? '' : 'build/' ) . 'validation' . $suffix . '.js';
		$maximum_upload_size = (float) get_option( 'form_block_maximum_upload_size', self::MAX_INT );
		
		if ( $maximum_upload_size && $maximum_upload_size !== self::MAX_INT ) {
			$maximum_upload_size = floor( (float) $maximum_upload_size * 1024 * 1024 );
		}
		else {
			$maximum_upload_size = self::MAX_INT;
		}
		
		wp_register_script( 'form-block-validation', $file_url, [ 'form-block-validator' ], $is_debug ? filemtime( $file_path ) : FORM_BLOCK_VERSION, true );
		wp_localize_script( 'form-block-validation', 'formBlockValidationData', [
			'validatorAllFilesTooBig' => esc_js( __( 'The uploaded files are too big.', 'form-block' ) ),
			'validatorChecked' => esc_js( __( 'This field must be checked.', 'form-block' ) ),
			'validatorDate' => esc_js( __( 'This field has an invalid date.', 'form-block' ) ),
			'validatorEmail' => esc_js( __( 'This email address is invalid.', 'form-block' ) ),
			'validatorEmpty' => esc_js( __( 'This field must not be empty.', 'form-block' ) ),
			'validatorFileTooBig' => esc_js( __( 'The uploaded file is too big.', 'form-block' ) ),
			'validatorInvalid' => esc_js( __( 'This field is invalid.', 'form-block' ) ),
			'validatorLong' => esc_js( __( 'This field is too long.', 'form-block' ) ),
			'validatorMaxFilesize' => esc_js( min( wp_max_upload_size(), $maximum_upload_size ) ),
			'validatorMaxFilesizePerFile' => esc_js( min( wp_max_upload_size(), $maximum_upload_size ) ),
			'validatorNumber' => esc_js( __( 'This field does not contain a number.', 'form-block' ) ),
			'validatorNumberMax' => esc_js( __( 'This value is too low.', 'form-block' ) ),
			'validatorNumberMin' => esc_js( __( 'This value is too high.', 'form-block' ) ),
			'validatorOneFileTooBig' => esc_js( __( 'At least one of the uploaded files is too big.', 'form-block' ) ),
			'validatorRadio' => esc_js( __( 'One option must be selected.', 'form-block' ) ),
			'validatorShort' => esc_js( __( 'This field is too short.', 'form-block' ) ),
			'validatorSelect' => esc_js( __( 'You must select an option.', 'form-block' ) ),
			'validatorTime' => esc_js( __( 'This field has an invalid time.', 'form-block' ) ),
			'validatorUrl' => esc_js( __( 'This field has an invalid URL.', 'form-block' ) ),
		] );
		
		$file_path = plugin_dir_path( EPI_FORM_BLOCK_FILE ) . 'assets/js/' . ( $is_debug ? '' : 'build/' ) . 'form' . $suffix . '.js';
		$file_url = plugin_dir_url( EPI_FORM_BLOCK_FILE ) . 'assets/js/' . ( $is_debug ? '' : 'build/' ) . 'form' . $suffix . '.js';
		
		wp_register_script( 'form-block-form', $file_url, [ 'form-block-validator', 'form-block-validation' ], $is_debug ? filemtime( $file_path ) : FORM_BLOCK_VERSION, true );
		wp_localize_script( 'form-block-form', 'formBlockData', [
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'i18n' => [
				'backendError' => esc_js( __( 'There was a problem with the backend. Please contact the administrator otherwise.', 'form-block' ) ),
				'isLoading' => esc_js( __( 'Loading …', 'form-block' ) ),
				'requestError' => esc_js( __( 'There was a problem with your request. Please try again.', 'form-block' ) ),
				'requestSuccess' => esc_js( __( 'The form has been submitted successfully.', 'form-block' ) ),
			],
		] );
	}
}

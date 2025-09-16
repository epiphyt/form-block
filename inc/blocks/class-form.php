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
	/**
	 * @var		\epiphyt\Form_Block\blocks\Form
	 */
	public static ?self $instance = null;
	
	/**
	 * Initialize the class.
	 */
	public function init(): void {
		\add_action( 'enqueue_block_editor_assets', [ self::class, 'enqueue_block_assets' ] );
		\add_action( 'init', [ $this, 'enqueue_block_styles' ] );
		\add_action( 'init', [ $this, 'register_block' ] );
		\add_action( 'init', [ $this, 'register_frontend_assets' ] );
		\add_filter( 'block_type_metadata', [ self::class, 'register_block_metadata' ] );
		\add_filter( 'render_block_form-block/form', [ $this, 'add_action' ], 10, 2 );
		\add_filter( 'render_block_form-block/form', [ $this, 'add_form_id_input' ], 10, 2 );
		\add_filter( 'render_block_form-block/form', [ $this, 'add_honeypot' ], 10, 2 );
		\add_filter( 'render_block_form-block/form', [ $this, 'add_label' ], 20, 2 );
		\add_filter( 'render_block_form-block/form', [ $this, 'add_maximum_upload_sizes' ], 10, 2 );
		\add_filter( 'render_block_form-block/form', [ $this, 'add_method' ], 10, 2 );
		\add_filter( 'render_block_form-block/form', [ $this, 'add_object_inputs' ], 10, 2 );
		\add_filter( 'render_block_form-block/form', [ $this, 'add_required_notice' ], 10, 2 );
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
		$url = \apply_filters( 'form_block_form_action', $url, $block_content, $block );
		
		return \str_replace( '<form', '<form action="' . \esc_url( $url ) . '"', $block_content );
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
		$form_id = \apply_filters( 'form_block_form_form_id', $block['attrs']['formId'], $block_content, $block );
		
		$form_id_input = '<input type="hidden" name="_form_id" value="' . \esc_attr( $form_id ) . '" />';
		
		/**
		 * Filter the form ID input.
		 * 
		 * @param	string	$form_id_input The form ID input
		 * @param	string	$form_id The form ID
		 * @param	string	$block_content The block content
		 * @param	array	$block Block attributes
		 */
		$form_id_input = \apply_filters( 'form_block_form_id_input', $form_id_input, $form_id, $block_content, $block );
		
		return \str_replace( 'enctype="multipart/form-data" novalidate>', 'enctype="multipart/form-data" novalidate>' . \PHP_EOL . $form_id_input, $block_content );
	}
	
	/**
	 * Add the object data input fields.
	 * 
	 * @param	string	$block_content The block content
	 * @param	array	$block Block attributes
	 * @return	string Updated block content
	 */
	public function add_object_inputs( string $block_content, array $block ): string {
		$queried_object = \get_queried_object();
		$object_inputs = '<input type="hidden" name="_object_id" value="' . \esc_attr( \get_queried_object_id() ) . '" />' . \PHP_EOL;
		$object_inputs .= '<input type="hidden" name="_object_type" value="' . \esc_attr( $queried_object ? $queried_object::class : '' ) . '" />';
		
		/**
		 * Filter the object inputs.
		 * 
		 * @param	string	$object_inputs The object inputs
		 * @param	string	$block_content The block content
		 * @param	array	$block Block attributes
		 */
		$object_inputs = \apply_filters( 'form_block_object_inputs', $object_inputs, $block_content, $block );
		
		return \str_replace( 'enctype="multipart/form-data" novalidate>', 'enctype="multipart/form-data" novalidate>' . \PHP_EOL . $object_inputs, $block_content );
	}
	
	/**
	 * Add the honeypot code.
	 * 
	 * @param	string	$block_content The block content
	 * @param	array	$block Block attributes
	 * @return	string Updated block content
	 */
	public function add_honeypot( string $block_content, array $block ): string {
		$honeypot = '<div class="wp-block-form-block-input form-block__element is-type-system"><input name="_town" type="text" aria-hidden="true" autocomplete="new-password" style="padding: 0; clip: rect(1px, 1px, 1px, 1px); position: absolute !important; white-space: nowrap; height: 1px; width: 1px; overflow: hidden;" tabindex="-1" /></div>';
		
		/**
		 * Filter the honeypot code.
		 * 
		 * @param	string	$honeypot The honeypot code
		 * @param	string	$block_content The block content
		 * @param	array	$block Block attributes
		 */
		$honeypot = \apply_filters( 'form_block_honeypot_code', $honeypot, $block_content, $block );
		
		return \str_replace( '</form>', $honeypot . '</form>', $block_content );
	}
	
	/**
	 * Add the form label.
	 * 
	 * @since	1.5.0
	 * 
	 * @param	string	$block_content Block content
	 * @param	array	$block Block attributes
	 * @return	string Updated block content
	 */
	public function add_label( string $block_content, array $block ): string {
		if ( empty( $block['attrs']['label'] ) || empty( $block['attrs']['formId'] ) ) {
			return $block_content;
		}
		
		$label_id = $block['attrs']['formId'] . '-label';
		$block_content = \str_replace( '<form', '<form aria-labelledby="' . $label_id . '"', $block_content );
		
		/**
		 * Filter the form label.
		 * 
		 * @since	1.5.0
		 * 
		 * @param	string	$label Form label
		 * @param	string	$block_content Block content
		 * @param	array	$block Block attributes
		 */
		$label = (string) \apply_filters( 'form_block_form_label', (string) $block['attrs']['label'], $block_content, $block );
		
		return \str_replace(
			'enctype="multipart/form-data" novalidate>',
			'enctype="multipart/form-data" novalidate>' . \PHP_EOL . '<span class="screen-reader-text" id="' . \esc_attr( $label_id ) . '">' . \esc_html( $label ) . '</span>',
			$block_content
		);
	}
	
	/**
	 * Add the form maximum upload sizes.
	 * 
	 * @since	1.0.3
	 * 
	 * @param	string	$block_content The block content
	 * @param	array	$block Block attributes
	 * @return	string Updated block content
	 */
	public function add_maximum_upload_sizes( string $block_content, array $block ): string {
		$maximum = Form_Block::get_instance()->get_maximum_upload_size();
		
		/**
		 * Filter the form maximum upload size.
		 * 
		 * @param	int		$maximum_upload_size Current maximum upload size
		 * @param	string	$block_content The block content
		 * @param	array	$block Block attributes
		 */
		$maximum_upload_size = \apply_filters( 'form_block_form_maximum_upload_size', $maximum, $block_content, $block );
		
		/**
		 * Filter the form maximum upload size per file.
		 * 
		 * @param	int		$maximum_upload_size Current maximum upload size per file
		 * @param	string	$block_content The block content
		 * @param	array	$block Block attributes
		 */
		$maximum_upload_size_per_file = \apply_filters( 'form_block_form_maximum_upload_size_per_file', $maximum, $block_content, $block );
		
		return \str_replace( '<form', '<form data-max-upload="' . \esc_attr( $maximum_upload_size ) . '" data-max-upload-file="' . \esc_attr( $maximum_upload_size_per_file ) . '"', $block_content );
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
		$method = \apply_filters( 'form_block_form_method', 'POST', $block_content, $block );
		
		return \str_replace( '<form', '<form method="' . \esc_attr( $method ) . '"', $block_content );
	}
	
	/**
	 * Add a required notice.
	 * 
	 * @param	string	$block_content The block content
	 * @param	array	$block Block attributes
	 * @return	string Updated block content
	 */
	public function add_required_notice( string $block_content, array $block ): string {
		/* translators: an asterisk sign */
		$notice = '<p class="form-block__required-notice" aria-hidden="true">' . \sprintf( \esc_html__( 'Required fields are marked with %s', 'form-block' ), '<span class="is-required" aria-hidden="true">*</span>' ) . '</p>';
		
		/**
		 * Filter the form required notice.
		 * 
		 * @param	string	$notice The form required notice
		 * @param	string	$block_content The block content
		 * @param	array	$block Block attributes
		 */
		$notice = \apply_filters( 'form_block_form_required_notice', $notice, $block_content, $block );
		
		return \str_replace( '<form', $notice . '<form', $block_content );
	}
	
	/**
	 * Enqueue block assets.
	 */
	public static function enqueue_block_assets(): void {
		\wp_localize_script(
			'form-block-form-editor-script',
			'formBlockFormData',
			[
				'saveSubmissions' => \get_option( 'form_block_save_submissions' ) === 'yes',
				'submissionListTableLink' => \admin_url( 'tools.php?page=form-block-submissions' ),
			]
		);
	}
	
	/**
	 * Enqueue block styles.
	 */
	public function enqueue_block_styles(): void {
		$is_debug = \defined( 'WP_DEBUG' ) && \WP_DEBUG;
		$suffix = ( $is_debug ? '' : '.min' );
		
		\wp_enqueue_block_style(
			'form-block/form',
			[
				'deps' => [],
				'handle' => 'form-block',
				'src' => \plugin_dir_url( \EPI_FORM_BLOCK_FILE ) . 'assets/style/build/form' . $suffix . '.css',
				'ver' => $is_debug ? \filemtime( \plugin_dir_path( \EPI_FORM_BLOCK_FILE ) . 'assets/style/build/form' . $suffix . '.css' ) : \FORM_BLOCK_VERSION,
			]
		);
	}
	
	/**
	 * Get a unique instance of the class.
	 * 
	 * @return	\epiphyt\Form_Block\blocks\Form The single instance of this class
	 */
	public static function get_instance(): self {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * Register additional block metadata.
	 * 
	 * @since	1.3.0
	 * 
	 * @param	array	$metadata Block metadata
	 * @return	array Updated block metadata
	 */
	public static function register_block_metadata( array $metadata ): array {
		if ( $metadata['name'] !== 'form-block/form' ) {
			return $metadata;
		}
		
		/**
		 * Filter form block style before register the block type.
		 * 
		 * @since	1.0.1
		 * @since	1.3.0 Value is an array by default
		 * 
		 * @param	array	$styles Current block styles
		 */
		$metadata['style'] = \apply_filters( 'form_block_form_style', [ 'form-block' ] );
		
		$metadata['viewScript'][] = 'form-block-validator';
		$metadata['viewScript'][] = 'form-block-validation';
		
		return $metadata;
	}
	
	/**
	 * Register block.
	 * 
	 * @since	1.3.0
	 */
	public static function register_block(): void {
		\register_block_type( \EPI_FORM_BLOCK_BASE . '/build/form' );
	}
	
	/**
	 * Register frontend assets.
	 */
	public function register_frontend_assets(): void {
		$is_debug = \defined( 'WP_DEBUG' ) && \WP_DEBUG;
		$suffix = ( $is_debug ? '' : '.min' );
		$file_path = \plugin_dir_path( \EPI_FORM_BLOCK_FILE ) . 'assets/js/' . ( $is_debug ? '' : 'build/' ) . 'multi-field' . $suffix . '.js';
		$file_url = \plugin_dir_url( \EPI_FORM_BLOCK_FILE ) . 'assets/js/' . ( $is_debug ? '' : 'build/' ) . 'multi-field' . $suffix . '.js';
		
		\wp_register_script( 'form-block-multi-field', $file_url, [ 'form-block-form' ], $is_debug ? \filemtime( $file_path ) : \FORM_BLOCK_VERSION, true );
		
		$file_path = \plugin_dir_path( \EPI_FORM_BLOCK_FILE ) . 'assets/js/vendor/validator' . $suffix . '.js';
		$file_url = \plugin_dir_url( \EPI_FORM_BLOCK_FILE ) . 'assets/js/vendor/validator' . $suffix . '.js';
		
		\wp_register_script( 'form-block-validator', $file_url, [], $is_debug ? \filemtime( $file_path ) : \FORM_BLOCK_VERSION, true );
		
		$file_path = \plugin_dir_path( \EPI_FORM_BLOCK_FILE ) . 'assets/js/' . ( $is_debug ? '' : 'build/' ) . 'validation' . $suffix . '.js';
		$file_url = \plugin_dir_url( \EPI_FORM_BLOCK_FILE ) . 'assets/js/' . ( $is_debug ? '' : 'build/' ) . 'validation' . $suffix . '.js';
		
		\wp_register_script( 'form-block-validation', $file_url, [ 'form-block-validator' ], $is_debug ? \filemtime( $file_path ) : \FORM_BLOCK_VERSION, true );
		\wp_localize_script( 'form-block-validation', 'formBlockValidationData', [
			/* translators: invalid field count */
			'validationInvalidFieldNotice' => \esc_js( \__( 'Could not submit form because %d fields are invalid.', 'form-block' ) ),
			'validatorAllFilesTooBig' => \esc_js( \__( 'The uploaded files are too big.', 'form-block' ) ),
			'validatorChecked' => \esc_js( \__( 'This field must be checked.', 'form-block' ) ),
			'validatorDate' => \esc_js( \__( 'This field has an invalid date.', 'form-block' ) ),
			'validatorEmail' => \esc_js( \__( 'This email address is invalid.', 'form-block' ) ),
			'validatorEmpty' => \esc_js( \__( 'This field must not be empty.', 'form-block' ) ),
			'validatorFileTooBig' => \esc_js( \__( 'The file is to big to be uploaded.', 'form-block' ) ),
			'validatorInvalid' => \esc_js( \__( 'This field is invalid.', 'form-block' ) ),
			'validatorLong' => \esc_js( \__( 'This field is too long.', 'form-block' ) ),
			'validatorMaxFilesize' => \esc_js( Form_Block::get_instance()->get_maximum_upload_size() ),
			'validatorMaxFilesizePerFile' => \esc_js( Form_Block::get_instance()->get_maximum_upload_size() ),
			'validatorNumber' => \esc_js( \__( 'This field does not contain a number.', 'form-block' ) ),
			'validatorNumberMax' => \esc_js( \__( 'This value is too low.', 'form-block' ) ),
			'validatorNumberMin' => \esc_js( \__( 'This value is too high.', 'form-block' ) ),
			'validatorOneFileTooBig' => \esc_js( \__( 'At least one of the uploaded files is too big.', 'form-block' ) ),
			'validatorRadio' => \esc_js( \__( 'One option must be selected.', 'form-block' ) ),
			'validatorSelect' => \esc_js( \__( 'You must select an option.', 'form-block' ) ),
			'validatorShort' => \esc_js( \__( 'This field is too short.', 'form-block' ) ),
			'validatorTime' => \esc_js( \__( 'This field has an invalid time.', 'form-block' ) ),
			'validatorUrl' => \esc_js( \__( 'This field has an invalid URL.', 'form-block' ) ),
		] );
		\wp_add_inline_script( 'form-block-validation', 'let formBlockIsValidated = false;', 'before' );
		
		$file_path = \plugin_dir_path( \EPI_FORM_BLOCK_FILE ) . 'assets/js/' . ( $is_debug ? '' : 'build/' ) . 'form' . $suffix . '.js';
		$file_url = \plugin_dir_url( \EPI_FORM_BLOCK_FILE ) . 'assets/js/' . ( $is_debug ? '' : 'build/' ) . 'form' . $suffix . '.js';
		
		\wp_register_script( 'form-block-form', $file_url, [ 'form-block-validator', 'form-block-validation' ], $is_debug ? \filemtime( $file_path ) : \FORM_BLOCK_VERSION, true );
		\wp_localize_script( 'form-block-form', 'formBlockData', [
			'ajaxUrl' => \admin_url( 'admin-ajax.php' ),
			'i18n' => [
				'backendError' => \esc_js( \__( 'There was a problem with the backend. Please contact the administrator otherwise.', 'form-block' ) ),
				'isLoading' => \esc_js( \__( 'Loading …', 'form-block' ) ),
				'requestError' => \esc_js( \__( 'There was a problem with your request. Please try again.', 'form-block' ) ),
				'requestSuccess' => \esc_js( \__( 'The form has been submitted successfully.', 'form-block' ) ),
				'requestSuccessRedirect' => \esc_js( \__( 'The form has been submitted successfully. Redirecting …', 'form-block' ) ),
			],
			'requestUrl' => \esc_js( Form_Block::get_instance()->get_current_request_url() ),
		] );
	}
}

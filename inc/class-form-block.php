<?php
namespace epiphyt\Form_Block;

use DOMDocument;
use epiphyt\Form_Block\api\Submission;
use epiphyt\Form_Block\block_data\Data as Block_Data_Data;
use epiphyt\Form_Block\blocks\Fieldset;
use epiphyt\Form_Block\blocks\Form;
use epiphyt\Form_Block\blocks\Input;
use epiphyt\Form_Block\blocks\Select;
use epiphyt\Form_Block\blocks\Textarea;
use epiphyt\Form_Block\form_data\Data as Form_Data_Data;
use epiphyt\Form_Block\form_data\Field;
use epiphyt\Form_Block\form_data\File;
use epiphyt\Form_Block\modules\Custom_Date;
use epiphyt\Form_Block\submissions\Submission_Handler;

/**
 * Form Block main class.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Form_Block {
	public const MAX_INT = 2147483647;
	
	/**
	 * @var		array Registered APIs
	 */
	public array $apis = [
		Submission::class,
	];
	
	/**
	 * @var		array Registered Modules
	 */
	public array $modules = [
		Custom_Date::class,
	];
	
	/**
	 * @var		array List of block name attributes
	 */
	private array $block_name_attributes = [
		'_town',
	];
	
	/**
	 * @var		\epiphyt\Form_Block\Form_Block
	 */
	public static ?self $instance = null;
	
	/**
	 * Initialize the class.
	 */
	public function init(): void {
		\add_action( 'admin_init', [ self::class, 'register_cron' ] );
		\add_filter( 'wp_kses_allowed_html', [ $this, 'set_allow_tags' ], 10, 2 );
		\register_activation_hook( \EPI_FORM_BLOCK_FILE, [ self::class, 'activate' ] );
		\register_deactivation_hook( \EPI_FORM_BLOCK_FILE, [ self::class, 'deactivate' ] );
		
		Admin::get_instance()->init();
		Block_Data_Data::get_instance()->init();
		// initialize before any block
		Theme_Styles::get_instance()->init();
		Fieldset::init();
		File::init();
		Form::get_instance()->init();
		Form_Data_Data::get_instance()->init();
		Input::get_instance()->init();
		Select::get_instance()->init();
		Submission_Handler::init();
		Textarea::get_instance()->init();
		
		foreach ( $this->apis as $key => $api ) {
			$this->apis[ $key ] = new $api(); // phpcs:ignore NeutronStandard.Functions.VariableFunctions.VariableFunction
			$this->apis[ $key ]->init();
		}
		
		foreach ( $this->modules as $key => $module ) {
			$this->modules[ $key ] = new $module(); // phpcs:ignore NeutronStandard.Functions.VariableFunctions.VariableFunction
			$this->modules[ $key ]->init();
		}
	}
	
	/**
	 * Tasks to do on plugin activation.
	 */
	public static function activate(): void {
		self::register_cron();
	}
	
	/**
	 * Add attributes for name, id and label for.
	 * 
	 * @param	string	$block_content The block content
	 * @param	array	$block Block attributes
	 * @return	string Updated block content
	 */
	public function add_attributes( string $block_content, array $block ): string {
		$dom = new DOMDocument();
		$element_type = \str_replace( 'form-block/', '', $block['blockName'] );
		$label = '';
		$multiple_regex = '/<input([^>]+)\s+multiple\s*/';
		$name_regex = '/name="(?<attribute>[^"]*)"/';
		$type_regex = '/type="(?<attribute>[^"]*)"/';
		
		$dom->loadHTML(
			'<html><meta charset="UTF-8">' . $block_content . '</html>',
			\LIBXML_HTML_NOIMPLIED | \LIBXML_HTML_NODEFDTD
		);
		
		// get label content
		/** @var	\DOMElement $element */
		foreach ( $dom->getElementsByTagName( 'span' ) as $element ) {
			if ( ! \str_contains( $element->getAttribute( 'class' ), 'form-block__label-content' ) ) {
				continue;
			}
			
			$label = $element->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		}
		
		// get multiple attribute
		\preg_match( $multiple_regex, $block_content, $multiple_matches );
		// get name attribute
		\preg_match( $name_regex, $block_content, $name_matches );
		
		// get type attribute only if not yet set
		if ( empty( $block['attrs']['type'] ) ) {
			\preg_match( $type_regex, $block_content, $type_matches );
			
			$block['attrs']['type'] = $type_matches['attribute'] ?? '';
		}
		
		$block['attrs']['label'] = $label;
		$block['attrs']['name'] = $name_matches['attribute'] ?? '';
		$name = $this->get_block_name_attribute( $block, 'non-unique' );
		$name_unique = $this->get_block_name_attribute( $block );
		$value_as_array = ! empty( $multiple_matches ) && ( empty( $block['attrs']['type'] ) || $block['attrs']['type'] !== 'email' );
		$attribute_replacement = 'name="' . \esc_attr( $name ) . ( $value_as_array ? '[]' : '' ) . '" id="id-' . \esc_attr( $name_unique ) . '"';
		
		if ( \preg_match( $name_regex, $block_content ) ) {
			$block_content = \preg_replace( $name_regex, $attribute_replacement, $block_content );
		}
		else {
			$block_content = \str_replace( '<' . $element_type, '<' . $element_type . ' ' . $attribute_replacement, $block_content );
		}
		
		$block_content = \str_replace( '<label', '<label for="id-' . \esc_attr( $name_unique ) . '"', $block_content );
		
		$dom->loadHTML(
			'<html><meta charset="UTF-8">' . $block_content . '</html>',
			\LIBXML_HTML_NOIMPLIED | \LIBXML_HTML_NODEFDTD
		);
		
		$element = $dom->getElementById( 'id-' . \esc_attr( $name_unique ) );
		$element->setAttribute( 'class', \trim( $element->getAttribute( 'class' ) . ' form-block__source' ) );
		
		if (
			! $element->hasAttribute( 'required' )
			&& ! $element->hasAttribute( 'disabled' )
			&& ! $element->hasAttribute( 'readonly' )
			&& $element->getAttribute( 'type' ) !== 'checkbox'
			&& $element->getAttribute( 'type' ) !== 'radio'
		) {
			$element->setAttribute( 'class', \trim( $element->getAttribute( 'class' ) . ' optional' ) );
		}
		
		if ( $element->hasAttribute( 'autocomplete' ) && ! empty( $block['attrs']['autoCompleteSection'] ) ) {
			$element->setAttribute( 'autocomplete', \esc_attr( $block['attrs']['autoCompleteSection'] . ' ' . $element->getAttribute( 'autocomplete' ) ) );
		}
		
		if ( $element->hasAttribute( 'capture' ) && empty( $element->getAttribute( 'capture' ) ) ) {
			$element->removeAttribute( 'capture' );
		}
		
		// make sure staring and ending slashes are available
		if ( $element->hasAttribute( 'pattern' ) ) {
			$element->setAttribute( 'pattern', '/' . \trim( $element->getAttribute( 'pattern' ) ) . '/' );
		}
		
		if ( $element->hasAttribute( 'min' ) && empty( $element->getAttribute( 'min' ) ) ) {
			$element->removeAttribute( 'min' );
		}
		
		if ( $element->hasAttribute( 'nax' ) && empty( $element->getAttribute( 'nax' ) ) ) {
			$element->removeAttribute( 'nax' );
		}
		
		if ( $element->hasAttribute( 'minlength' ) && empty( $element->getAttribute( 'minlength' ) ) ) {
			$element->removeAttribute( 'minlength' );
		}
		
		if ( $element->hasAttribute( 'maxlength' ) && empty( $element->getAttribute( 'maxlength' ) ) ) {
			$element->removeAttribute( 'maxlength' );
		}
		
		if ( $element->hasAttribute( 'min' ) || $element->hasAttribute( 'max' ) ) {
			$value = '';
			
			if ( $element->hasAttribute( 'min' ) && ! empty( $element->getAttribute( 'min' ) ) ) {
				$value .= $element->getAttribute( 'min' );
			}
			
			if ( $element->hasAttribute( 'max' ) && ! empty( $element->getAttribute( 'max' ) ) ) {
				$value .= ',' . $element->getAttribute( 'max' );
			}
			
			if ( $value ) {
				$element->setAttribute( 'data-validate-minmax', $value );
			}
		}
		
		if ( $element->hasAttribute( 'minlength' ) || $element->hasAttribute( 'maxlength' ) ) {
			$value = '';
			
			if ( $element->hasAttribute( 'minlength' ) && ! empty( $element->getAttribute( 'minlength' ) ) ) {
				$value .= $element->getAttribute( 'minlength' );
			}
			
			if ( $element->hasAttribute( 'maxlength' ) && ! empty( $element->getAttribute( 'maxlength' ) ) ) {
				$value .= ',' . $element->getAttribute( 'maxlength' );
			}
			
			if ( $value ) {
				$element->setAttribute( 'data-validate-length-range', $value );
			}
		}
		
		return \str_replace( [ '<html><meta charset="UTF-8">', '</html>' ], '', $dom->saveHTML( $dom->documentElement ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	}
	
	/**
	 * Tasks to do on plugin deactivation.
	 */
	public static function deactivate(): void {
		self::unregister_cron();
	}
	
	/**
	 * Get a valid name attribute of a form element.
	 * 
	 * @param	array	$block Block attributes
	 * @param	string	$uniqueness 'unique' or 'non-unique'
	 * @return	string A valid name attribute
	 */
	public function get_block_name_attribute( array $block, string $uniqueness = 'unique' ): string {
		// if we only have field data, there is no 'attrs' key
		if ( ! isset( $block['attrs'] ) ) {
			$block = [
				'attrs' => $block,
			];
		}
		
		if ( ! empty( $block['attrs']['name'] ) ) {
			return $this->get_unique_block_name_attribute( $block['attrs']['name'], $uniqueness );
		}
		
		if ( ! empty( $block['attrs']['label'] ) ) {
			return $this->get_unique_block_name_attribute( Field::get_name_by_label( $block['attrs']['label'] ), $uniqueness );
		}
		
		return $this->get_unique_block_name_attribute( 'unknown', $uniqueness );
	}
	
	/**
	 * Get the current request URL.
	 * 
	 * @return	string The current request URL
	 */
	public function get_current_request_url(): string {
		$request_uri = \sanitize_text_field( \wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) );
		$current_path = $request_uri ?: '/';
		$home_path = \home_url( '', 'relative' );
		$home_path_position = \strpos( $current_path, $home_path );
		
		if ( $home_path_position !== false ) {
			$current_path = \substr_replace( $current_path, '', $home_path_position, \strlen( $home_path ) );
		}
		
		return \home_url( $current_path );
	}
	
	/**
	 * Get a unique instance of the class.
	 * 
	 * @return	\epiphyt\Form_Block\Form_Block The single instance of this class
	 */
	public static function get_instance(): self {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * Get the maximum upload size.
	 * 
	 * @since	1.0.3
	 * 
	 * @return	int The maximum upload size
	 */
	public function get_maximum_upload_size(): int {
		$maximum_upload_size = (float) \get_option( 'form_block_maximum_upload_size', self::MAX_INT );
		
		if ( $maximum_upload_size && $maximum_upload_size !== self::MAX_INT ) {
			$maximum_upload_size = \floor( (float) $maximum_upload_size * 1024 * 1024 );
		}
		else {
			$maximum_upload_size = self::MAX_INT;
		}
		
		return \min( \wp_max_upload_size(), $maximum_upload_size );
	}
	
	/**
	 * Get a unique name attribute.
	 * Similar to wp_unique_post(), which has been the inspiration for this. 
	 * 
	 * @param	string	$block_name The block name
	 * @param	string	$uniqueness 'unique' or 'non-unique'
	 * @return	string A unique name attribute
	 */
	public function get_unique_block_name_attribute( string $block_name, string $uniqueness = 'unique' ): string {
		$block_name_check = \in_array( $block_name, $this->block_name_attributes, true );
		
		if ( $uniqueness === 'non-unique' ) {
			$block_name_check = false;
		}
		
		if ( ! $block_name_check ) {
			$this->block_name_attributes[] = $block_name;
			
			return $block_name;
		}
		
		$suffix = 2;
		
		do {
			$new_block_name = $block_name . '-' . $suffix;
			$block_name_check = \in_array( $new_block_name, $this->block_name_attributes, true );
			++$suffix;
		} while ( $block_name_check );
		
		$this->block_name_attributes[] = $new_block_name;
		
		return $new_block_name;
	}
	
	/**
	 * Get the directory and URL.
	 * 
	 * @param	string	$sub_dir Optional sub-directory
	 * @return	string[] Thumbnail directory and URL
	 */
	public static function get_upload_directory( string $sub_dir = '' ): array {
		$upload_dir = \wp_get_upload_dir();
		
		if ( ! $upload_dir || $upload_dir['error'] !== false ) {
			return [
				'base_dir' => '',
				'base_url' => '',
			];
		}
		
		$path = $upload_dir['basedir'] . '/form-block' . ( ! empty( $sub_dir ) ? '/' . \ltrim( $sub_dir ) : '' );
		$url = $upload_dir['baseurl'] . '/form-block' . ( ! empty( $sub_dir ) ? '/' . \ltrim( $sub_dir ) : '' );
		
		if ( ! \file_exists( $path ) ) {
			\wp_mkdir_p( $path );
		}
		
		return [
			'base_dir' => $path,
			'base_url' => $url,
		];
	}
	
	/**
	 * Register cron job.
	 */
	public static function register_cron(): void {
		if ( ! \wp_next_scheduled( 'form_block_cleanup' ) ) {
			$date = new \DateTime( 'tomorrow 00:00:00', \wp_timezone() );
			$date->add( new \DateInterval( 'PT' . $date->format( 'Z' ) . 'S' ) );
			
			\wp_schedule_event( $date->getTimestamp(), 'daily', 'form_block_cleanup' );
		}
	}
	
	/**
	 * Reset the block name attributes.
	 */
	public function reset_block_name_attributes(): void {
		$this->block_name_attributes = [
			'_town',
		];
	}
	
	/**
	 * Add some used HTML elements to the allowed tags.
	 * 
	 * @param	array[]|string	$tags The allowed HTML tags
	 * @param	string			$context The context
	 * @return	array[]|string The updated allowed tags
	 */
	public function set_allow_tags( array|string $tags, string $context ) { // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
		if ( $context !== 'post' ) {
			return $tags;
		}
		
		$tags['form'] = [
			'accept' => true,
			'accept-charset' => true,
			'action' => true,
			'class' => true,
			'data-*' => true,
			'enctype' => true,
			'id' => true,
			'method' => true,
			'name' => true,
			'target' => true,
		];
		$tags['input'] = [
			'autocomplete' => true,
			'class' => true,
			'data-*' => true,
			'disabled' => true,
			'id' => true,
			'max' => true,
			'maxlength' => true,
			'min' => true,
			'minlength' => true,
			'multiple' => true,
			'name' => true,
			'pattern' => true,
			'placeholder' => true,
			'readonly' => true,
			'required' => true,
			'size' => true,
			'spellcheck' => true,
			'step' => true,
			'type' => true,
			'value' => true,
		];
		$tags['label'] = [
			'class' => true,
			'data-*' => true,
			'for' => true,
		];
		$tags['option'] = [
			'class' => true,
			'data-*' => true,
			'disabled' => true,
			'name' => true,
			'selected' => true,
			'value' => true,
		];
		$tags['select'] = [
			'autocomplete' => true,
			'class' => true,
			'data-*' => true,
			'disabled' => true,
			'id' => true,
			'multiple' => true,
			'name' => true,
			'readonly' => true,
			'required' => true,
			'size' => true,
		];
		$tags['span'] = [
			'class' => true,
			'data-*' => true,
			'id' => true,
		];
		$tags['textarea'] = [
			'autocomplete' => true,
			'class' => true,
			'cols' => true,
			'data-*' => true,
			'disabled' => true,
			'id' => true,
			'label' => true,
			'maxlength' => true,
			'minlength' => true,
			'name' => true,
			'placeholder' => true,
			'readonly' => true,
			'required' => true,
			'rows' => true,
			'spellcheck' => true,
			'type' => true,
			'value' => true,
			'wrap' => true,
		];
		
		return $tags;
	}
	
	/**
	 * Unregister cron job.
	 */
	public static function unregister_cron(): void {
		if ( \wp_next_scheduled( 'form_block_cleanup' ) ) {
			\wp_clear_scheduled_hook( 'form_block_cleanup' );
		}
	}
}

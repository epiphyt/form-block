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
 * Form Block main class.
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
	public function init() {
		add_action( 'enqueue_block_editor_assets', [ $this, 'block_assets' ] );
	}
	
	/**
	 * Enqueue block assets in the block editor.
	 */
	public function block_assets(): void {
		$asset_file = include plugin_dir_path( EPI_FORM_BLOCK_FILE ) . 'build/index.asset.php';
		
		wp_enqueue_script( 'form-block', plugin_dir_url( EPI_FORM_BLOCK_FILE ) . '/build/index.js', $asset_file['dependencies'], $asset_file['version'] );
		wp_enqueue_style( 'form-block', plugin_dir_url( EPI_FORM_BLOCK_FILE ) . '/build/style-index.css', [], $asset_file['version'] );
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
}

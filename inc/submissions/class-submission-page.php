<?php
namespace epiphyt\Form_Block\submissions;

/**
 * Form submission page.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Submission_Page {
	public const NAME = 'form-block-submissions';
	
	/**
	 * Initialize functionality.
	 */
	public static function init(): void {
		\add_action( 'admin_menu', [ self::class, 'register_options_page' ] );
	}
	
	/**
	 * Register options page.
	 */
	public static function register_options_page(): void {
		\add_submenu_page(
			'tools.php',
			\__( 'Form Submissions', 'form-block' ),
			\__( 'Form Submissions', 'form-block' ),
			'manage_options',
			self::NAME,
			[ self::class, 'get_options_page_html' ]
		);
	}
	
	/**
	 * Get options page HTML.
	 */
	public static function get_options_page_html(): void {
		// check user capabilities
		if ( ! \current_user_can( 'manage_options' ) ) {
			return;
		}
		
		echo '<div class="wrap form-block__submissions">';
		echo '<h2>' . \esc_html__( 'Form submissions', 'form-block' ) . '</h2>';
		echo '<form method="post">';
		
		$table = new Submission_List_Table();
		$table->init();
		
		$table->prepare_items();
		$table->search_box( \__( 'Search Submissions', 'form-block' ), 'search_id' );
		$table->display();
		
		echo '</form>';
		echo '</div>'; // .wrap
	}
}

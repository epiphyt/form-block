<?php
namespace epiphyt\Form_Block\Tests;

use Brain\Monkey\Functions;
use epiphyt\Form_Block\Admin;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass( Admin::class )]
final class AdminTest extends TestCase {
	public function test_get_instance_returns_singleton(): void {
		$this->assertSame( Admin::get_instance(), Admin::get_instance() );
	}

	public function test_validate_checkbox_accepts_yes(): void {
		$this->assertSame( 'yes', Admin::validate_checkbox( 'yes', 'option', 'Title' ) );
	}

	public function test_validate_checkbox_returns_empty_for_empty(): void {
		$this->assertSame( '', Admin::validate_checkbox( '', 'option', 'Title' ) );
	}

	public function test_validate_checkbox_rejects_invalid_value(): void {
		Functions\when( 'add_settings_error' )->justReturn( null );

		$this->assertSame( '', Admin::validate_checkbox( 'no', 'option', 'Title' ) );
	}

	public function test_validate_submissions_auto_delete_accepts_number(): void {
		$this->assertSame( 30, Admin::get_instance()->validate_submissions_auto_delete( '30' ) );
	}

	public function test_validate_submissions_auto_delete_rejects_negative(): void {
		$this->assertSame( 0, Admin::get_instance()->validate_submissions_auto_delete( '-5' ) );
	}

	public function test_validate_submissions_auto_delete_rejects_non_numeric(): void {
		$this->assertSame( 0, Admin::get_instance()->validate_submissions_auto_delete( 'abc' ) );
	}

	public function test_validate_maximum_upload_size_returns_empty_for_empty(): void {
		$this->assertSame( '', Admin::get_instance()->validate_maximum_upload_size( '' ) );
	}

	public function test_validate_maximum_upload_size_rejects_non_numeric(): void {
		Functions\when( 'add_settings_error' )->justReturn( null );

		$this->assertSame( '', Admin::get_instance()->validate_maximum_upload_size( 'abc' ) );
	}

	public function test_validate_maximum_upload_size_accepts_valid_size(): void {
		Functions\when( 'wp_max_upload_size' )->justReturn( 100 * 1024 * 1024 );

		$this->assertSame( '5', Admin::get_instance()->validate_maximum_upload_size( '5' ) );
	}

	public function test_validate_maximum_upload_size_rejects_too_large(): void {
		Functions\when( 'wp_max_upload_size' )->justReturn( 1024 );
		Functions\when( 'add_settings_error' )->justReturn( null );

		$this->assertSame( '', Admin::get_instance()->validate_maximum_upload_size( '5' ) );
	}

	public function test_validate_preserve_data_on_uninstall_accepts_yes(): void {
		$this->assertSame( 'yes', Admin::get_instance()->validate_preserve_data_on_uninstall( 'yes' ) );
	}

	public function test_set_script_attributes_adds_module_type_for_admin_scripts(): void {
		$attributes = Admin::set_script_attributes( [ 'id' => 'form-block-admin-tabs-js' ] );

		$this->assertSame( 'module', $attributes['type'] );
	}

	public function test_set_script_attributes_leaves_other_scripts_untouched(): void {
		$attributes = Admin::set_script_attributes( [ 'id' => 'some-other-script' ] );

		$this->assertArrayNotHasKey( 'type', $attributes );
	}

	public function test_get_options_tabs_contains_default_tabs(): void {
		$tabs = Admin::get_options_tabs();

		$this->assertArrayHasKey( 'general', $tabs );
		$this->assertArrayHasKey( 'pro', $tabs );
	}

	public function test_save_per_page_screen_option_returns_value(): void {
		$this->assertSame( 25, Admin::save_per_page_screen_option( false, 'submissions_per_page', 25 ) );
	}

	public function test_render_plugin_documentation_link_ignores_other_plugins(): void {
		$input = [ 'existing-link' ];

		$this->assertSame( $input, Admin::render_plugin_documentation_link( $input, 'other-plugin/other.php' ) );
	}

	public function test_render_plugin_documentation_link_adds_link_for_plugin(): void {
		Functions\when( 'get_plugin_data' )->justReturn( [ 'Version' => '1.8.0' ] );

		$result = Admin::render_plugin_documentation_link( [ 'existing-link' ], 'form-block.php' );

		$this->assertCount( 2, $result );
		$this->assertStringContainsString( 'Documentation', $result[1] );
	}
}

<?php
namespace epiphyt\Form_Block\Tests\modules;

use Brain\Monkey\Functions;
use epiphyt\Form_Block\Admin;
use epiphyt\Form_Block\modules\Time_Honeypot;
use epiphyt\Form_Block\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass( Time_Honeypot::class )]
#[UsesClass( Admin::class )]
final class TimeHoneypotTest extends TestCase {
	public function test_add_system_field_names_appends_honeypot_fields(): void {
		$names = Time_Honeypot::add_system_field_names( [ 'existing' ] );

		$this->assertSame(
			[ 'existing', Time_Honeypot::FIELD_LOAD_TIME, Time_Honeypot::FIELD_PAGE_LOAD, Time_Honeypot::FIELD_SUBMIT_TIME ],
			$names
		);
	}

	public function test_check_does_nothing_when_disabled(): void {
		Functions\when( 'get_option' )->justReturn( '' );
		Functions\when( 'wp_send_json_error' )->alias( static function() {
			throw new \RuntimeException( 'rejected' );
		} );

		Time_Honeypot::check( 'form-1' );

		$this->assertTrue( true, 'a disabled honeypot must not reject a submission' );
	}

	public function test_check_rejects_instead_of_reporting_success(): void {
		Functions\when( 'get_option' )->justReturn( 'yes' );
		Functions\when( 'wp_send_json_error' )->alias( static function() {
			throw new \RuntimeException( 'rejected' );
		} );
		// a silent success would discard the submission without telling the visitor
		Functions\when( 'wp_send_json_success' )->alias( static function() {
			throw new \LogicException( 'submission was silently discarded' );
		} );

		$_POST = [];

		$this->expectException( \RuntimeException::class );

		Time_Honeypot::check( 'form-1' );
	}

	public function test_is_enabled_true_by_default(): void {
		Functions\when( 'get_option' )->justReturn( 'yes' );

		$this->assertTrue( Time_Honeypot::is_enabled() );
	}

	public function test_is_enabled_false_when_empty(): void {
		Functions\when( 'get_option' )->justReturn( '' );

		$this->assertFalse( Time_Honeypot::is_enabled() );
	}

	public function test_get_script_contains_field_names(): void {
		$script = Time_Honeypot::get_script();

		$this->assertStringContainsString( Time_Honeypot::FIELD_LOAD_TIME, $script );
		$this->assertStringContainsString( Time_Honeypot::FIELD_PAGE_LOAD, $script );
	}

	public function test_add_fields_returns_content_unchanged_when_disabled(): void {
		Functions\when( 'get_option' )->justReturn( '' );

		$content = '<form></form>';

		$this->assertSame( $content, Time_Honeypot::add_fields( $content, [] ) );
	}

	public function test_add_fields_injects_hidden_inputs_when_enabled(): void {
		Functions\when( 'get_option' )->justReturn( 'yes' );

		$result = Time_Honeypot::add_fields( '<form></form>', [] );

		$this->assertStringContainsString( 'name="' . Time_Honeypot::FIELD_LOAD_TIME . '"', $result );
		$this->assertStringContainsString( 'name="' . Time_Honeypot::FIELD_SUBMIT_TIME . '"', $result );
		$this->assertStringContainsString( '</form>', $result );
	}

	public function test_validate_returns_yes_for_valid_value(): void {
		$this->assertSame( 'yes', Time_Honeypot::validate( 'yes' ) );
	}

	public function test_validate_returns_empty_for_null(): void {
		$this->assertSame( '', Time_Honeypot::validate( null ) );
	}
}

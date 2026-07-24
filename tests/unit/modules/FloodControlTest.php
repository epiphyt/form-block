<?php
namespace epiphyt\Form_Block\Tests\modules;

use Brain\Monkey\Functions;
use epiphyt\Form_Block\modules\Flood_Control;
use epiphyt\Form_Block\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass( Flood_Control::class )]
final class FloodControlTest extends TestCase {
	public function test_get_interval_returns_option_value(): void {
		Functions\when( 'get_option' )->justReturn( 15 );

		$this->assertSame( 15, Flood_Control::get_interval() );
	}

	public function test_get_interval_returns_default_for_non_numeric(): void {
		Functions\when( 'get_option' )->justReturn( 'invalid' );

		$this->assertSame( Flood_Control::DEFAULT_INTERVAL, Flood_Control::get_interval() );
	}

	public function test_get_interval_returns_default_for_negative(): void {
		Functions\when( 'get_option' )->justReturn( -5 );

		$this->assertSame( Flood_Control::DEFAULT_INTERVAL, Flood_Control::get_interval() );
	}

	public function test_get_interval_allows_zero(): void {
		Functions\when( 'get_option' )->justReturn( 0 );

		$this->assertSame( 0, Flood_Control::get_interval() );
	}

	public function test_validate_interval_accepts_numeric(): void {
		$this->assertSame( 42, Flood_Control::validate_interval( '42' ) );
	}

	public function test_validate_interval_accepts_zero(): void {
		$this->assertSame( 0, Flood_Control::validate_interval( '0' ) );
	}

	public function test_validate_interval_rejects_non_numeric(): void {
		$this->assertSame( Flood_Control::DEFAULT_INTERVAL, Flood_Control::validate_interval( 'abc' ) );
	}

	public function test_validate_interval_rejects_negative(): void {
		$this->assertSame( Flood_Control::DEFAULT_INTERVAL, Flood_Control::validate_interval( '-1' ) );
	}

	public function test_validate_interval_rejects_null(): void {
		$this->assertSame( Flood_Control::DEFAULT_INTERVAL, Flood_Control::validate_interval( null ) );
	}
}

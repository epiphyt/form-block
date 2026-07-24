<?php
namespace epiphyt\Form_Block\Tests\submissions\methods;

use Brain\Monkey\Functions;
use epiphyt\Form_Block\form_data\Data;
use epiphyt\Form_Block\submissions\methods\Local_Storage;
use epiphyt\Form_Block\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass( Local_Storage::class )]
#[UsesClass( Data::class )]
final class LocalStorageTest extends TestCase {
	public function test_is_savable_true_by_default(): void {
		Functions\when( 'get_option' )->justReturn( [ 'fields' => [] ] );

		$this->assertTrue( Local_Storage::is_savable( 'form-1' ) );
	}

	public function test_is_savable_respects_disabled_flag(): void {
		Functions\when( 'get_option' )->justReturn( [ 'localStorage' => false ] );

		$this->assertFalse( Local_Storage::is_savable( 'form-1' ) );
	}

	public function test_is_savable_respects_enabled_flag(): void {
		Functions\when( 'get_option' )->justReturn( [ 'localStorage' => true ] );

		$this->assertTrue( Local_Storage::is_savable( 'form-1' ) );
	}

	public function test_update_form_data_defaults_to_true(): void {
		$this->assertSame(
			[ 'localStorage' => true ],
			Local_Storage::update_form_data( [], [ 'attrs' => [] ] )
		);
	}

	public function test_update_form_data_uses_block_attribute(): void {
		$this->assertSame(
			[ 'localStorage' => false ],
			Local_Storage::update_form_data( [], [ 'attrs' => [ 'methods' => [ 'localStorage' => false ] ] ] )
		);
	}

	public function test_has_submissions_true_when_submissions_exist(): void {
		Functions\when( 'get_option' )->alias( static function( $key, $default = false ) {
			if ( $key === 'form_block_form_ids' ) {
				return [ 'form-1' => [ 1 ] ];
			}

			return [ [ 'field' => 'value' ] ];
		} );

		$this->assertTrue( Local_Storage::has_submissions() );
	}

	public function test_has_submissions_false_when_none_exist(): void {
		Functions\when( 'get_option' )->alias( static function( $key, $default = false ) {
			if ( $key === 'form_block_form_ids' ) {
				return [ 'form-1' => [ 1 ] ];
			}

			return [];
		} );

		$this->assertFalse( Local_Storage::has_submissions() );
	}
}

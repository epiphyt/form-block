<?php
namespace epiphyt\Form_Block\Tests\modules;

use epiphyt\Form_Block\form_data\Field;
use epiphyt\Form_Block\Form_Block;
use epiphyt\Form_Block\modules\Custom_Date;
use epiphyt\Form_Block\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass( Custom_Date::class )]
#[UsesClass( Field::class )]
#[UsesClass( Form_Block::class )]
final class CustomDateTest extends TestCase {
	public function test_get_field_data_returns_all_fields(): void {
		$fields = Custom_Date::get_field_data();

		$this->assertSame(
			[ 'day', 'hour', 'minute', 'month', 'week', 'year' ],
			\array_keys( $fields )
		);
		$this->assertSame( 31, $fields['day']['validation']['max'] );
	}

	public function test_get_field_data_reorders_by_given_order(): void {
		$fields = Custom_Date::get_field_data( [ 'year', 'month', 'day' ] );

		$this->assertSame( [ 'year', 'month', 'day' ], \array_slice( \array_keys( $fields ), 0, 3 ) );
	}

	public function test_get_field_order_for_date_custom(): void {
		$this->assertSame( [ 'month', 'day', 'year' ], Custom_Date::get_field_order( 'date-custom' ) );
	}

	public function test_get_field_order_for_time_custom(): void {
		$this->assertSame( [ 'hour', 'minute' ], Custom_Date::get_field_order( 'time-custom' ) );
	}

	public function test_get_field_order_unknown_type_returns_empty(): void {
		$this->assertSame( [], Custom_Date::get_field_order( 'unknown' ) );
	}

	public function test_set_output_format_returns_value_for_non_custom_field(): void {
		$fields = [ [ 'name' => 'plain', 'label' => 'Plain', 'type' => 'text' ] ];

		$this->assertSame( 'unchanged', Custom_Date::set_output_format( 'unchanged', 'plain', $fields ) );
	}

	public function test_set_output_format_joins_date_parts(): void {
		$fields = [ [ 'name' => 'birthday', 'label' => 'Birthday', 'type' => 'date-custom' ] ];
		$value = [ 'month' => '12', 'day' => '31', 'year' => '2020' ];

		$this->assertSame( '12/31/2020', Custom_Date::set_output_format( $value, 'birthday', $fields ) );
	}

	public function test_validate_completeness_ignores_non_custom_field(): void {
		$errors = Custom_Date::validate_completeness( [], [ 'value' => 'x' ], [ 'type' => 'text' ] );

		$this->assertSame( [], $errors );
	}

	public function test_validate_completeness_ignores_empty_field(): void {
		$attributes = [ 'type' => 'date-custom' ];
		$value = [ 'month' => '', 'day' => '', 'year' => '' ];

		$this->assertSame( [], Custom_Date::validate_completeness( [], $value, $attributes ) );
	}

	public function test_validate_completeness_reports_partial_field(): void {
		$attributes = [ 'type' => 'date-custom' ];
		$value = [ 'month' => '12', 'day' => '', 'year' => '' ];

		$errors = Custom_Date::validate_completeness( [], $value, $attributes );

		$this->assertCount( 1, $errors );
		$this->assertSame( 'custom-date', $errors[0]['type'] );
	}

	public function test_validate_completeness_accepts_complete_field(): void {
		$attributes = [ 'type' => 'date-custom' ];
		$value = [ 'month' => '12', 'day' => '31', 'year' => '2020' ];

		$this->assertSame( [], Custom_Date::validate_completeness( [], $value, $attributes ) );
	}
}

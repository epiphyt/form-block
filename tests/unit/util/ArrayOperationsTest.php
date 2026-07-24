<?php
namespace epiphyt\Form_Block\Tests\util;

use epiphyt\Form_Block\Tests\TestCase;
use epiphyt\Form_Block\util\Array_Operations;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass( Array_Operations::class )]
final class ArrayOperationsTest extends TestCase {
	public function test_filter_recursive_removes_empty_values(): void {
		$input = [
			'a' => 'value',
			'b' => '',
			'c' => 0,
			'd' => null,
		];

		$this->assertSame( [ 'a' => 'value' ], Array_Operations::filter_recursive( $input ) );
	}

	public function test_filter_recursive_removes_nested_empty_arrays(): void {
		$input = [
			'keep' => [ 'inner' => 'value' ],
			'drop' => [ 'inner' => '' ],
			'empty' => [],
		];

		$this->assertSame( [ 'keep' => [ 'inner' => 'value' ] ], Array_Operations::filter_recursive( $input ) );
	}

	public function test_filter_recursive_keeps_populated_nested_values(): void {
		$input = [
			'level1' => [
				'level2' => [
					'value' => 'deep',
					'empty' => '',
				],
			],
		];

		$this->assertSame(
			[ 'level1' => [ 'level2' => [ 'value' => 'deep' ] ] ],
			Array_Operations::filter_recursive( $input )
		);
	}

	public function test_filter_recursive_returns_empty_array_for_empty_input(): void {
		$this->assertSame( [], Array_Operations::filter_recursive( [] ) );
	}

	public function test_get_most_nested_value_returns_deepest_value(): void {
		$input = [
			'a' => [
				'b' => [
					'c' => 'deepest',
				],
			],
		];

		$this->assertSame( 'deepest', Array_Operations::get_most_nested_value( $input ) );
	}

	public function test_get_most_nested_value_returns_last_end_value(): void {
		$input = [
			'first' => 'one',
			'second' => 'two',
		];

		$this->assertSame( 'two', Array_Operations::get_most_nested_value( $input ) );
	}

	public function test_get_most_nested_value_follows_last_element(): void {
		$input = [
			'list' => [ 'a', 'b' ],
			'value' => 'scalar',
		];

		$this->assertSame( 'scalar', Array_Operations::get_most_nested_value( $input ) );
	}
}

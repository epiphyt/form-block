<?php
namespace epiphyt\Form_Block\Tests\block_data;

use Brain\Monkey\Functions;
use epiphyt\Form_Block\block_data\Data;
use epiphyt\Form_Block\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass( Data::class )]
final class DataTest extends TestCase {
	public function test_get_instance_returns_singleton(): void {
		$this->assertSame( Data::get_instance(), Data::get_instance() );
	}

	public function test_get_block_context_strips_prefix(): void {
		$this->assertSame( 'input', Data::get_block_context( [ 'blockName' => 'form-block/input' ] ) );
	}

	public function test_get_block_context_strips_pro_prefix(): void {
		$this->assertSame( 'input', Data::get_block_context( [ 'blockName' => 'form-block-pro/input' ] ) );
	}

	public function test_is_current_context_true_for_matching_block(): void {
		$this->assertTrue( Data::is_current_context( [ 'blockName' => 'form-block/form' ], 'form' ) );
	}

	public function test_is_current_context_false_for_other_block(): void {
		$this->assertFalse( Data::is_current_context( [ 'blockName' => 'form-block/input' ], 'form' ) );
	}

	public function test_get_attributes_extracts_input_attributes_and_label(): void {
		Functions\when( 'wp_strip_all_tags' )->alias( static function( $value ) {
			return \trim( \strip_tags( (string) $value ) );
		} );

		$html = '<label class="form-block__label"><span class="form-block__label-content">My Label</span></label>'
			. '<input class="form-block__input" name="field" type="text" />';

		$attributes = Data::get_instance()->get_attributes( $html, 'input' );

		$this->assertSame( 'field', $attributes['name'] );
		$this->assertSame( 'text', $attributes['type'] );
		$this->assertSame( 'form-block__input', $attributes['class'] );
		$this->assertSame( 'My Label', $attributes['label'] );
	}
}

<?php
namespace epiphyt\Form_Block\Tests\blocks;

use epiphyt\Form_Block\blocks\Form;
use epiphyt\Form_Block\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass( Form::class )]
final class FormTest extends TestCase {
	public function test_get_instance_returns_singleton(): void {
		$this->assertSame( Form::get_instance(), Form::get_instance() );
	}

	public function test_clear_empty_block_returns_empty_without_inner_blocks(): void {
		$this->assertSame( '', Form::clear_empty_block( '<form></form>', [ 'innerBlocks' => [] ] ) );
	}

	public function test_clear_empty_block_keeps_content_with_inner_blocks(): void {
		$content = '<form><input></form>';

		$this->assertSame(
			$content,
			Form::clear_empty_block( $content, [ 'innerBlocks' => [ [ 'blockName' => 'form-block/input' ] ] ] )
		);
	}
}

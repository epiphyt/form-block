<?php
namespace epiphyt\Form_Block\Tests\block_data;

use Brain\Monkey\Functions;
use epiphyt\Form_Block\block_data\Util;
use epiphyt\Form_Block\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass( Util::class )]
final class UtilTest extends TestCase {
	public function test_has_block_in_content_true_for_direct_block(): void {
		$content = '<!-- wp:form-block/form {"formId":"abc"} --><!-- /wp:form-block/form -->';

		$this->assertTrue( Util::has_block_in_content( 'form-block/form', $content ) );
	}

	public function test_has_block_in_content_false_without_block(): void {
		$this->assertFalse( Util::has_block_in_content( 'form-block/form', '<p>Just a paragraph.</p>' ) );
	}

	public function test_has_block_in_content_false_when_reusable_has_no_match(): void {
		Functions\when( 'parse_blocks' )->justReturn( [] );

		$content = '<!-- wp:block {"ref":5} /-->';

		$this->assertFalse( Util::has_block_in_content( 'form-block/form', $content ) );
	}

	public function test_has_block_true_when_wp_has_block(): void {
		Functions\when( 'has_block' )->justReturn( true );

		$this->assertTrue( Util::has_block( 'form-block/form', '<!-- wp:form-block/form -->' ) );
	}

	public function test_has_block_false_when_no_block_present(): void {
		Functions\when( 'has_block' )->justReturn( false );

		$this->assertFalse( Util::has_block( 'form-block/form', '<p>Nothing here.</p>' ) );
	}
}

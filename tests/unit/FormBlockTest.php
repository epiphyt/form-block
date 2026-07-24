<?php
namespace epiphyt\Form_Block\Tests;

use Brain\Monkey\Functions;
use epiphyt\Form_Block\form_data\Field;
use epiphyt\Form_Block\Form_Block;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass( Form_Block::class )]
#[UsesClass( Field::class )]
final class FormBlockTest extends TestCase {
	public function test_get_instance_returns_singleton(): void {
		$this->assertSame( Form_Block::get_instance(), Form_Block::get_instance() );
	}

	public function test_get_unique_block_name_attribute_returns_name_first_time(): void {
		$block = Form_Block::get_instance();
		$block->reset_block_name_attributes();

		$this->assertSame( 'email', $block->get_unique_block_name_attribute( 'email' ) );
	}

	public function test_get_unique_block_name_attribute_appends_suffix_on_collision(): void {
		$block = Form_Block::get_instance();
		$block->reset_block_name_attributes();

		$block->get_unique_block_name_attribute( 'email' );

		$this->assertSame( 'email-2', $block->get_unique_block_name_attribute( 'email' ) );
	}

	public function test_get_unique_block_name_attribute_non_unique_mode_keeps_name(): void {
		$block = Form_Block::get_instance();
		$block->reset_block_name_attributes();

		$block->get_unique_block_name_attribute( 'email', 'non-unique' );

		$this->assertSame( 'email', $block->get_unique_block_name_attribute( 'email', 'non-unique' ) );
	}

	public function test_reset_block_name_attributes_clears_collisions(): void {
		$block = Form_Block::get_instance();
		$block->reset_block_name_attributes();
		$block->get_unique_block_name_attribute( 'email' );
		$block->reset_block_name_attributes();

		$this->assertSame( 'email', $block->get_unique_block_name_attribute( 'email' ) );
	}

	public function test_get_block_name_attribute_uses_name(): void {
		$block = Form_Block::get_instance();
		$block->reset_block_name_attributes();

		$this->assertSame( 'field', $block->get_block_name_attribute( [ 'attrs' => [ 'name' => 'field' ] ] ) );
	}

	public function test_get_block_name_attribute_falls_back_to_label(): void {
		$block = Form_Block::get_instance();
		$block->reset_block_name_attributes();

		$this->assertSame( 'my-label', $block->get_block_name_attribute( [ 'attrs' => [ 'label' => 'My Label' ] ] ) );
	}

	public function test_get_block_name_attribute_defaults_to_unknown(): void {
		$block = Form_Block::get_instance();
		$block->reset_block_name_attributes();

		$this->assertSame( 'unknown', $block->get_block_name_attribute( [ 'attrs' => [] ] ) );
	}

	public function test_get_block_name_attribute_wraps_field_only_data(): void {
		$block = Form_Block::get_instance();
		$block->reset_block_name_attributes();

		$this->assertSame( 'field', $block->get_block_name_attribute( [ 'name' => 'field' ] ) );
	}

	public function test_set_allow_tags_ignores_non_post_context(): void {
		$tags = [ 'existing' => true ];

		$this->assertSame( $tags, Form_Block::get_instance()->set_allow_tags( $tags, 'comment' ) );
	}

	public function test_set_allow_tags_adds_form_tags_for_post_context(): void {
		$tags = Form_Block::get_instance()->set_allow_tags( [], 'post' );

		$this->assertArrayHasKey( 'form', $tags );
		$this->assertArrayHasKey( 'input', $tags );
		$this->assertArrayHasKey( 'select', $tags );
		$this->assertArrayHasKey( 'textarea', $tags );
	}

	public function test_get_maximum_upload_size_returns_server_limit_when_unset(): void {
		Functions\when( 'get_option' )->justReturn( Form_Block::MAX_INT );
		Functions\when( 'wp_max_upload_size' )->justReturn( 1000 );

		$this->assertSame( 1000, Form_Block::get_instance()->get_maximum_upload_size() );
	}

	public function test_get_maximum_upload_size_converts_mib_to_bytes(): void {
		Functions\when( 'get_option' )->justReturn( '5' );
		Functions\when( 'wp_max_upload_size' )->justReturn( 100 * 1024 * 1024 );

		$this->assertSame( 5 * 1024 * 1024, Form_Block::get_instance()->get_maximum_upload_size() );
	}

	public function test_get_current_request_url_builds_from_request_uri(): void {
		$_SERVER['REQUEST_URI'] = '/contact';
		Functions\when( 'home_url' )->alias( static function( $path = '', $scheme = null ) {
			return $scheme === 'relative' ? '' : 'https://example.org' . $path;
		} );

		$this->assertSame( 'https://example.org/contact', Form_Block::get_instance()->get_current_request_url() );

		unset( $_SERVER['REQUEST_URI'] );
	}
}

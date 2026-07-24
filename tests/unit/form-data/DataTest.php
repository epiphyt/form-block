<?php
namespace epiphyt\Form_Block\Tests\form_data;

use Brain\Monkey\Functions;
use epiphyt\Form_Block\form_data\Data;
use epiphyt\Form_Block\Form_Block;
use epiphyt\Form_Block\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass( Data::class )]
#[UsesClass( Form_Block::class )]
final class DataTest extends TestCase {
	public function test_get_instance_returns_singleton(): void {
		$this->assertSame( Data::get_instance(), Data::get_instance() );
	}

	public function test_unify_files_array_resorts_multiple_files(): void {
		$file_post = [
			'name' => [ 'a.txt', 'b.txt' ],
			'type' => [ 'text/plain', 'text/plain' ],
			'tmp_name' => [ '/tmp/a', '/tmp/b' ],
			'error' => [ 0, 0 ],
			'size' => [ 10, 20 ],
		];

		$this->assertSame(
			[
				[ 'name' => 'a.txt', 'type' => 'text/plain', 'tmp_name' => '/tmp/a', 'error' => 0, 'size' => 10 ],
				[ 'name' => 'b.txt', 'type' => 'text/plain', 'tmp_name' => '/tmp/b', 'error' => 0, 'size' => 20 ],
			],
			Data::get_instance()->unify_files_array( $file_post )
		);
	}

	public function test_get_submit_object_data_returns_default_without_keys(): void {
		$this->assertSame(
			[ 'title' => '', 'url' => '' ],
			Data::get_submit_object_data( [] )
		);
	}

	public function test_get_submit_object_data_returns_default_for_invalid_id(): void {
		$this->assertSame(
			[ 'title' => '', 'url' => '' ],
			Data::get_submit_object_data( [ '_object_id' => 'not-numeric', '_object_type' => 'WP_Post' ] )
		);
	}

	public function test_get_submit_object_data_for_post(): void {
		Functions\when( 'get_post_field' )->justReturn( 'Post title' );
		Functions\when( 'get_permalink' )->justReturn( 'https://example.org/post' );

		$this->assertSame(
			[ 'title' => 'Post title', 'url' => 'https://example.org/post' ],
			Data::get_submit_object_data( [ '_object_id' => 5, '_object_type' => 'WP_Post' ] )
		);
	}

	public function test_get_submit_object_data_for_term(): void {
		Functions\when( 'get_term_field' )->justReturn( 'category' );
		Functions\when( 'get_term_link' )->justReturn( 'https://example.org/term' );

		$this->assertSame(
			[ 'title' => 'category', 'url' => 'https://example.org/term' ],
			Data::get_submit_object_data( [ '_object_id' => 7, '_object_type' => 'WP_Term' ] )
		);
	}

	public function test_get_submit_object_data_returns_default_for_unknown_type(): void {
		$this->assertSame(
			[ 'title' => '', 'url' => '' ],
			Data::get_submit_object_data( [ '_object_id' => 1, '_object_type' => 'Unknown' ] )
		);
	}

	public function test_is_valid_form_id_true_with_fields(): void {
		Functions\when( 'get_option' )->justReturn( [ 'fields' => [ [ 'name' => 'email' ] ] ] );

		$this->assertTrue( Data::get_instance()->is_valid_form_id( 'form-1' ) );
	}

	public function test_is_valid_form_id_false_without_fields(): void {
		Functions\when( 'get_option' )->justReturn( [] );

		$this->assertFalse( Data::get_instance()->is_valid_form_id( 'form-1' ) );
	}

	public function test_get_form_ids_returns_keys(): void {
		Functions\when( 'get_option' )->justReturn( [ 'form-1' => [ 1 ], 'form-2' => [ 2 ] ] );

		$this->assertSame( [ 'form-1', 'form-2' ], Data::get_form_ids() );
	}

	public function test_get_form_ids_returns_empty_for_non_array(): void {
		Functions\when( 'get_option' )->justReturn( false );

		$this->assertSame( [], Data::get_form_ids() );
	}

	public function test_get_returns_empty_array_without_form_id(): void {
		$this->assertSame( [], Data::get_instance()->get() );
	}

	public function test_get_returns_option_data(): void {
		Functions\when( 'get_option' )->justReturn( [ 'fields' => [] ] );

		$this->assertSame( [ 'fields' => [] ], Data::get_instance()->get( 'form-1' ) );
	}

	public function test_get_required_fields_returns_required_field_names(): void {
		Functions\when( 'get_option' )->justReturn( [
			'fields' => [
				[ 'name' => 'email', 'label' => 'Email', 'required' => true ],
				[ 'name' => 'message', 'label' => 'Message' ],
			],
		] );

		$this->assertSame( [ 'email' ], Data::get_instance()->get_required_fields( 'form-1' ) );
	}

	public function test_get_required_fields_returns_empty_without_form_id(): void {
		$this->assertSame( [], Data::get_instance()->get_required_fields() );
	}
}

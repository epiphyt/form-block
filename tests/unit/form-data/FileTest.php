<?php
namespace epiphyt\Form_Block\Tests\form_data;

use Brain\Monkey\Functions;
use epiphyt\Form_Block\form_data\Field;
use epiphyt\Form_Block\form_data\File;
use epiphyt\Form_Block\Form_Block;
use epiphyt\Form_Block\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass( File::class )]
#[UsesClass( Field::class )]
#[UsesClass( Form_Block::class )]
final class FileTest extends TestCase {
	public function test_get_data_combines_local_and_validated(): void {
		$validated = [ 'field_name' => 'upload', 'name' => 'a.txt' ];
		$files = [ 'local' => [ 0 => [ 'hash' => 'abc' ] ] ];

		$this->assertSame(
			[
				'local' => [ 'hash' => 'abc' ],
				'validated' => $validated,
			],
			File::get_data( $validated, 0, $files )
		);
	}

	public function test_get_data_defaults_local_to_empty_array(): void {
		$validated = [ 'field_name' => 'upload' ];

		$this->assertSame(
			[ 'local' => [], 'validated' => $validated ],
			File::get_data( $validated, 3, [ 'local' => [] ] )
		);
	}

	public function test_is_saved_locally_true_when_flag_set(): void {
		$this->assertTrue( File::is_saved_locally( [ 'localFiles' => true ] ) );
	}

	public function test_is_saved_locally_false_by_default(): void {
		$this->assertFalse( File::is_saved_locally( [] ) );
	}

	public function test_set_hashed_filename_format(): void {
		$filename = File::set_hashed_filename( 'document.pdf' );

		$this->assertMatchesRegularExpression( '/^[a-f0-9]{40}\.bin$/', $filename );
	}

	public function test_set_hashed_filename_is_unique(): void {
		$this->assertNotSame(
			File::set_hashed_filename( 'a.txt' ),
			File::set_hashed_filename( 'a.txt' )
		);
	}

	public function test_set_local_file_path_returns_original_when_not_local(): void {
		$this->assertSame( '/tmp/original', File::set_local_file_path( '/tmp/original', [], [] ) );
	}

	public function test_set_local_file_path_returns_original_without_local_path(): void {
		$this->assertSame(
			'/tmp/original',
			File::set_local_file_path( '/tmp/original', [ 'local' => [] ], [ 'localFiles' => true ] )
		);
	}

	public function test_set_local_file_path_returns_local_path(): void {
		$this->assertSame(
			'/local/path.bin',
			File::set_local_file_path(
				'/tmp/original',
				[ 'local' => [ 'path' => '/local/path.bin' ] ],
				[ 'localFiles' => true ]
			)
		);
	}

	public function test_set_local_file_output_returns_original_when_not_local(): void {
		$this->assertSame( 'original', File::set_local_file_output( 'original', 'upload', '/path', [], [], 'plain' ) );
	}

	public function test_set_local_file_output_returns_original_without_local_file(): void {
		$this->assertSame(
			'original',
			File::set_local_file_output( 'original', 'upload', '/path', [ 'localFiles' => true ], [ 'local' => [] ], 'plain' )
		);
	}

	public function test_set_local_file_output_plain_format(): void {
		$field_data = [ 'name' => 'upload', 'label' => 'Upload', 'localFiles' => true ];
		$file = [ 'local' => [ 'url' => 'https://example.org/file' ], 'validated' => [ 'name' => 'a.txt' ] ];

		$this->assertSame(
			'Upload: https://example.org/file',
			File::set_local_file_output( '', 'upload', '/path', $field_data, $file, 'plain' )
		);
	}

	public function test_get_hash_map_returns_option(): void {
		Functions\when( 'get_option' )->justReturn( [ 'hash1' => [ 'path' => '/x' ] ] );

		$this->assertSame( [ 'hash1' => [ 'path' => '/x' ] ], File::get_hash_map() );
	}

	public function test_delete_hash_returns_false_for_unknown_hash(): void {
		Functions\when( 'get_option' )->justReturn( [ 'known' => [] ] );

		$this->assertFalse( File::delete_hash( 'unknown' ) );
	}

	public function test_delete_hash_removes_existing_hash(): void {
		Functions\when( 'get_option' )->justReturn( [ 'known' => [ 'path' => '/x' ] ] );
		Functions\expect( 'update_option' )
			->once()
			->with( 'form_block_local_file_map', [] )
			->andReturn( true );

		$this->assertTrue( File::delete_hash( 'known' ) );
	}
}

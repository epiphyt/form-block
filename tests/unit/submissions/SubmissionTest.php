<?php
namespace epiphyt\Form_Block\Tests\submissions;

use Brain\Monkey\Functions;
use DateTimeImmutable;
use epiphyt\Form_Block\submissions\Submission;
use epiphyt\Form_Block\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass( Submission::class )]
final class SubmissionTest extends TestCase {
	private function make_submission( array $fields = [], array $files = [], array $files_local = [] ): Submission {
		Functions\when( 'wp_timezone' )->justReturn( new \DateTimeZone( 'UTC' ) );

		return new Submission( 'form-1', [
			'fields' => $fields,
			'files' => $files,
			'files_local' => $files_local,
		] );
	}

	public function test_get_data_returns_form_id(): void {
		$submission = $this->make_submission();

		$this->assertSame( 'form-1', $submission->get_data( 'form_id' ) );
	}

	public function test_get_data_returns_field_value(): void {
		$submission = $this->make_submission( [ 'email' => 'john@example.org' ] );

		$this->assertSame( 'john@example.org', $submission->get_data( 'email', 'fields' ) );
	}

	public function test_get_data_returns_null_for_missing_field(): void {
		$submission = $this->make_submission( [ 'email' => 'john@example.org' ] );

		$this->assertNull( $submission->get_data( 'missing', 'fields' ) );
	}

	public function test_get_data_returns_files_local(): void {
		$submission = $this->make_submission( [], [], [ [ 'hash' => 'abc' ] ] );

		$this->assertSame( [ [ 'hash' => 'abc' ] ], $submission->get_data( 'files_local' ) );
	}

	public function test_get_date_object_returns_immutable_date(): void {
		$submission = $this->make_submission();

		$this->assertInstanceOf( DateTimeImmutable::class, $submission->get_date_object() );
	}

	public function test_get_date_uses_provided_format(): void {
		Functions\when( 'wp_date' )->alias( static function( $format, $timestamp ) {
			return \gmdate( $format, $timestamp );
		} );

		$submission = $this->make_submission();
		$timestamp = $submission->get_date_object()->getTimestamp();

		$this->assertSame( \gmdate( 'Y-m-d', $timestamp ), $submission->get_date( 'Y-m-d' ) );
	}

	public function test_get_raw_returns_post_value(): void {
		$_POST['name'] = 'John';

		$submission = $this->make_submission();

		$this->assertSame( 'John', $submission->get_raw( 'name', 'fields' ) );
	}

	public function test_get_raw_returns_empty_array_for_unknown_type(): void {
		$submission = $this->make_submission();

		$this->assertSame( [], $submission->get_raw( 'name', 'unknown' ) );
	}

	public function test_search_finds_term_in_fields(): void {
		$submission = $this->make_submission( [ 'message' => 'hello world' ] );

		$this->assertTrue( $submission->search( 'world' ) );
	}

	public function test_search_finds_term_in_nested_fields(): void {
		$submission = $this->make_submission( [ 'group' => [ 'inner' => 'nested value' ] ] );

		$this->assertTrue( $submission->search( 'nested' ) );
	}

	public function test_search_returns_false_when_not_found(): void {
		$submission = $this->make_submission( [ 'message' => 'hello world' ] );

		$this->assertFalse( $submission->search( 'missing' ) );
	}
}

<?php
namespace epiphyt\Form_Block\Tests\submissions;

use Brain\Monkey\Functions;
use epiphyt\Form_Block\form_data\Data;
use epiphyt\Form_Block\submissions\Submission;
use epiphyt\Form_Block\submissions\Submission_Handler;
use epiphyt\Form_Block\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass( Submission_Handler::class )]
#[UsesClass( Data::class )]
#[UsesClass( Submission::class )]
final class SubmissionHandlerTest extends TestCase {
	private function make_submission(): Submission {
		Functions\when( 'wp_timezone' )->justReturn( new \DateTimeZone( 'UTC' ) );

		return new Submission( 'form-1', [ 'fields' => [], 'files' => [], 'files_local' => [] ] );
	}

	private function stub_options( array $form_ids, array $submissions ): void {
		Functions\when( 'get_option' )->alias( static function( $key, $default = false ) use ( $form_ids, $submissions ) {
			if ( $key === 'form_block_form_ids' ) {
				return $form_ids;
			}

			if ( \str_starts_with( $key, Submission_Handler::OPTION_KEY_PREFIX . '_' ) ) {
				$form_id = \substr( $key, \strlen( Submission_Handler::OPTION_KEY_PREFIX . '_' ) );

				return $submissions[ $form_id ] ?? [];
			}

			return $default;
		} );
	}

	public function test_get_submissions_returns_form_submissions(): void {
		$first = $this->make_submission();
		$second = $this->make_submission();
		$this->stub_options(
			[ 'form-1' => [ 1 ] ],
			[ 'form-1' => [ $first, $second ] ]
		);

		$this->assertSame( [ $first, $second ], Submission_Handler::get_submissions( 'form-1' ) );
	}

	public function test_get_submissions_returns_empty_for_form_without_data(): void {
		$this->stub_options( [ 'form-1' => [ 1 ] ], [] );

		$this->assertSame( [], Submission_Handler::get_submissions( 'form-1' ) );
	}

	public function test_get_submission_returns_single_entry(): void {
		$first = $this->make_submission();
		$second = $this->make_submission();
		$this->stub_options(
			[ 'form-1' => [ 1 ] ],
			[ 'form-1' => [ $first, $second ] ]
		);

		$this->assertSame( $second, Submission_Handler::get_submission( 'form-1', 1 ) );
	}
}

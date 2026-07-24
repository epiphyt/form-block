<?php
namespace epiphyt\Form_Block\Tests\form_data;

use epiphyt\Form_Block\form_data\Validation;
use epiphyt\Form_Block\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass( Validation::class )]
final class ValidationTest extends TestCase {
	public function test_get_instance_returns_singleton(): void {
		$this->assertSame( Validation::get_instance(), Validation::get_instance() );
	}

	public function test_get_system_field_names_contains_defaults(): void {
		$names = Validation::get_instance()->get_system_field_names();

		$this->assertContains( '_form_id', $names );
		$this->assertContains( '_wpnonce', $names );
		$this->assertContains( 'action', $names );
		$this->assertContains( '_town', $names );
	}

	public function test_get_errors_returns_empty_without_fields(): void {
		$this->assertSame( [], Validation::get_instance()->get_errors( [ 'x' => 'y' ], [] ) );
	}

	public function test_get_errors_returns_empty_for_valid_value(): void {
		$form_data = [
			'fields' => [
				[ 'name' => 'message', 'label' => 'Message', 'block_type' => 'input' ],
			],
		];

		$this->assertSame(
			[],
			Validation::get_instance()->get_errors( [ 'message' => 'clean value' ], $form_data )
		);
	}

	public function test_get_errors_detects_invalid_sanitized_value(): void {
		$form_data = [
			'fields' => [
				[ 'name' => 'message', 'label' => 'Message', 'block_type' => 'input' ],
			],
		];

		$errors = Validation::get_instance()->get_errors( [ 'message' => '<script>x</script>' ], $form_data );

		$this->assertArrayHasKey( 'message', $errors );
		$this->assertSame( 'Message', $errors['message']['field_title'] );
		$this->assertSame( 'block_type', $errors['message']['errors'][0]['type'] );
	}

	public function test_get_errors_detects_changed_disabled_value(): void {
		$form_data = [
			'fields' => [
				[ 'name' => 'fixed', 'label' => 'Fixed', 'disabled' => true, 'value' => 'expected' ],
			],
		];

		$errors = Validation::get_instance()->get_errors( [ 'fixed' => 'changed' ], $form_data );

		$this->assertArrayHasKey( 'fixed', $errors );
		$this->assertSame( 'disabled', $errors['fixed']['errors'][0]['type'] );
	}
}

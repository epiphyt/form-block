<?php
namespace epiphyt\Form_Block\Tests\submissions\methods;

use epiphyt\Form_Block\form_data\Field;
use epiphyt\Form_Block\Form_Block;
use epiphyt\Form_Block\submissions\methods\Email;
use epiphyt\Form_Block\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass( Email::class )]
#[UsesClass( Field::class )]
#[UsesClass( Form_Block::class )]
final class EmailTest extends TestCase {
	public function test_get_reply_to_returns_reply_to_field_value(): void {
		$fields = [
			[ 'name' => 'name', 'label' => 'Name' ],
			[ 'name' => 'email', 'label' => 'Email', 'is_reply_to' => true ],
		];
		$data = [
			'name' => 'John',
			'email' => 'john@example.org',
		];

		$this->assertSame( 'john@example.org', Email::get_reply_to( $data, $fields ) );
	}

	public function test_get_reply_to_returns_empty_without_reply_to_field(): void {
		$fields = [
			[ 'name' => 'name', 'label' => 'Name' ],
			[ 'name' => 'email', 'label' => 'Email' ],
		];
		$data = [
			'name' => 'John',
			'email' => 'john@example.org',
		];

		$this->assertSame( '', Email::get_reply_to( $data, $fields ) );
	}
}

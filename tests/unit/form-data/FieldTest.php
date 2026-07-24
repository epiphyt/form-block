<?php
namespace epiphyt\Form_Block\Tests\form_data;

use epiphyt\Form_Block\form_data\Field;
use epiphyt\Form_Block\Form_Block;
use epiphyt\Form_Block\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass( Field::class )]
#[UsesClass( Form_Block::class )]
final class FieldTest extends TestCase {
	public function test_get_instance_returns_singleton(): void {
		$this->assertSame( Field::get_instance(), Field::get_instance() );
	}

	public function test_strip_special_characters_replaces_umlauts_and_spaces(): void {
		$this->assertSame( 'muelheim-an-der-ruhr', Field::strip_special_characters( 'Mülheim an der Ruhr' ) );
	}

	public function test_strip_special_characters_handles_sharp_s(): void {
		$this->assertSame( 'strasse', Field::strip_special_characters( 'Straße' ) );
	}

	public function test_strip_special_characters_removes_html_tags(): void {
		$this->assertSame( 'bold', Field::strip_special_characters( '<b>Bold</b>' ) );
	}

	public function test_strip_special_characters_keeps_allowed_characters(): void {
		$this->assertSame( 'field_name-1[0].test', Field::strip_special_characters( 'field_name-1[0].test' ) );
	}

	public function test_strip_special_characters_strips_disallowed_characters(): void {
		$this->assertSame( 'abc', Field::strip_special_characters( 'a!b@c#' ) );
	}

	public function test_get_name_by_label_generates_valid_name(): void {
		$this->assertSame( 'first-name', Field::get_name_by_label( 'First Name', true ) );
	}

	public function test_get_by_name_returns_matching_field(): void {
		$fields = [
			[ 'name' => 'email', 'label' => 'Email' ],
			[ 'name' => 'message', 'label' => 'Message' ],
		];

		$this->assertSame( $fields[1], Field::get_by_name( 'message', $fields ) );
	}

	public function test_get_by_name_returns_empty_array_when_not_found(): void {
		$fields = [
			[ 'name' => 'email', 'label' => 'Email' ],
		];

		$this->assertSame( [], Field::get_by_name( 'missing', $fields ) );
	}

	public function test_get_by_name_skips_non_array_fields(): void {
		$fields = [
			'not-an-array',
			[ 'name' => 'email', 'label' => 'Email' ],
		];

		$this->assertSame( $fields[1], Field::get_by_name( 'email', $fields ) );
	}

	public function test_get_by_name_all_mode_returns_list(): void {
		$fields = [
			[ 'name' => 'choice', 'label' => 'Choice A' ],
			[ 'name' => 'choice', 'label' => 'Choice B' ],
		];

		$result = Field::get_by_name( 'choice', $fields, 'all' );

		$this->assertCount( 2, $result );
	}

	public function test_get_title_by_name_returns_label(): void {
		$fields = [
			[ 'name' => 'email', 'label' => 'Email address' ],
		];

		$this->assertSame( 'Email address', Field::get_title_by_name( 'email', $fields ) );
	}

	public function test_get_title_by_name_returns_name_when_not_found(): void {
		$fields = [
			[ 'name' => 'email', 'label' => 'Email address' ],
		];

		$this->assertSame( 'missing', Field::get_title_by_name( 'missing', $fields ) );
	}

	public function test_get_title_by_name_searches_nested_fields(): void {
		$fields = [
			[
				'name' => 'group',
				'label' => 'Group',
				'fields' => [
					[ 'name' => 'inner', 'label' => 'Inner label' ],
				],
			],
		];

		$this->assertSame( 'Inner label', Field::get_title_by_name( 'inner', $fields ) );
	}

	public function test_get_title_by_field_returns_label_for_matching_field(): void {
		$field = [ 'name' => 'email', 'label' => 'Email' ];
		$fields = [ $field ];

		$this->assertSame( 'Email', Field::get_title_by_field( $field, $fields ) );
	}

	public function test_get_title_by_field_returns_empty_when_not_found(): void {
		$field = [ 'name' => 'email', 'label' => 'Email' ];
		$fields = [ [ 'name' => 'other', 'label' => 'Other' ] ];

		$this->assertSame( '', Field::get_title_by_field( $field, $fields ) );
	}
}

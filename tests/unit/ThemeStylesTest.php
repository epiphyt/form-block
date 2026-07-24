<?php
namespace epiphyt\Form_Block\Tests;

use Brain\Monkey\Functions;
use epiphyt\Form_Block\Theme_Styles;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass( Theme_Styles::class )]
final class ThemeStylesTest extends TestCase {
	/**
	 * Stub wp_get_theme() to report a given theme name and template.
	 */
	private function stub_theme( string $name, string $template = '' ): void {
		$theme = new class( $name, $template ) {
			private string $name;

			private string $template;

			public function __construct( string $name, string $template ) {
				$this->name = $name;
				$this->template = $template;
			}

			public function get( string $key ): string {
				return $key === 'Template' ? $this->template : $this->name;
			}
		};

		Functions\when( 'wp_get_theme' )->justReturn( $theme );
	}

	public function test_get_instance_returns_singleton(): void {
		$this->assertSame( Theme_Styles::get_instance(), Theme_Styles::get_instance() );
	}

	public function test_is_theme_matches_by_name(): void {
		$this->stub_theme( 'Twenty Twenty-Five' );

		$this->assertTrue( Theme_Styles::get_instance()->is_theme( 'Twenty Twenty-Five' ) );
	}

	public function test_is_theme_matches_by_template(): void {
		$this->stub_theme( 'Child Theme', 'twentytwentyfour' );

		$this->assertTrue( Theme_Styles::get_instance()->is_theme( 'twentytwentyfour' ) );
	}

	public function test_is_theme_returns_false_for_other_theme(): void {
		$this->stub_theme( 'Some Theme' );

		$this->assertFalse( Theme_Styles::get_instance()->is_theme( 'Twenty Twenty-Five' ) );
	}

	public function test_register_block_styles_adds_matching_theme_style(): void {
		$this->stub_theme( 'Twenty Twenty-Four' );

		$this->assertSame(
			[ 'form-block-twenty-twenty-four' ],
			Theme_Styles::get_instance()->register_block_styles( [] )
		);
	}

	public function test_register_block_styles_leaves_styles_unchanged_for_other_theme(): void {
		$this->stub_theme( 'Unknown Theme' );

		$this->assertSame( [ 'existing' ], Theme_Styles::get_instance()->register_block_styles( [ 'existing' ] ) );
	}
}

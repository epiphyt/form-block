<?php
namespace epiphyt\Form_Block\Tests;

use epiphyt\Form_Block\Assets;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass( Assets::class )]
final class AssetsTest extends TestCase {
	public function test_get_asset_builds_url_from_constant(): void {
		$asset = Assets::get_asset( 'assets/style/build/admin.css' );

		$this->assertSame( \EPI_FORM_BLOCK_URL . 'assets/style/build/admin.css', $asset['url'] );
	}

	public function test_get_asset_uses_version_constant_when_not_debug(): void {
		// no debug constants defined in the test environment
		$asset = Assets::get_asset( 'assets/style/build/admin.css' );

		$this->assertSame( \FORM_BLOCK_VERSION, $asset['version'] );
	}

	public function test_is_debug_returns_false_without_debug_constants(): void {
		$this->assertFalse( Assets::is_debug() );
	}
}

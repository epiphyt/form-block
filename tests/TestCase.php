<?php
namespace epiphyt\Form_Block\Tests;

use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase as PHPUnit_TestCase;

/**
 * Base test case wiring up Brain Monkey and common WordPress stubs.
 */
abstract class TestCase extends PHPUnit_TestCase {
	protected function setUp(): void {
		parent::setUp();

		Monkey\setUp();

		Functions\stubTranslationFunctions();
		Functions\stubEscapeFunctions();

		// commonly used WordPress helpers with a predictable implementation
		Functions\when( 'wp_unslash' )->returnArg();
		Functions\when( 'sanitize_text_field' )->alias( static function( $value ) {
			return \is_string( $value ) ? \trim( \preg_replace( '/[\r\n\t ]+/', ' ', \strip_tags( $value ) ) ) : $value;
		} );
		Functions\when( 'sanitize_textarea_field' )->alias( static function( $value ) {
			return \is_string( $value ) ? \trim( \strip_tags( $value ) ) : $value;
		} );
		Functions\when( 'sanitize_key' )->alias( static function( $value ) {
			return \preg_replace( '/[^a-z0-9_\-]/', '', \strtolower( (string) $value ) );
		} );
		Functions\when( 'wp_parse_args' )->alias( static function( $args, $defaults = [] ) {
			return \array_merge( (array) $defaults, (array) $args );
		} );
	}

	protected function tearDown(): void {
		Monkey\tearDown();

		$_POST = [];
		$_GET = [];
		$_FILES = [];

		parent::tearDown();
	}
}

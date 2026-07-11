<?php
namespace epiphyt\Form_Block;

/**
 * Asset helper class.
 * 
 * @since	1.8.0
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Assets {
	/**
	 * Get URL and version of a plugin asset.
	 * 
	 * @param	string	$relative_path Asset path relative to the plugin root
	 * @return	array{url: string, version: string} Asset URL and version
	 */
	public static function get_asset( string $relative_path ): array {
		return [
			'url' => \EPI_FORM_BLOCK_URL . $relative_path, // @phpstan-ignore constant.notFound
			'version' => self::is_debug() ? (string) \filemtime( \EPI_FORM_BLOCK_BASE . $relative_path ) : \FORM_BLOCK_VERSION,
		];
	}
	
	/**
	 * Whether assets should be served unminified.
	 * 
	 * @return	bool Whether debug mode is enabled
	 */
	public static function is_debug(): bool {
		return ( \defined( 'WP_DEBUG' ) && \WP_DEBUG ) || ( \defined( 'SCRIPT_DEBUG' ) && \SCRIPT_DEBUG );
	}
}

<?php
namespace epiphyt\Form_Block\block_data;

use WP_Post;

/**
 * Block data util class.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Form_Block
 */
final class Util {
	/**
	 * Check if a block is inside a post, even if it's a reusable one.
	 * 
	 * @link	https://github.com/WordPress/gutenberg/issues/18272#issuecomment-566179633
	 * 
	 * @param	string			$block_name Full block type to look for
	 * @param	int|string|null	$id The post ID or post content to look for
	 * @return	bool Whether the post contains this block
	 */
	public static function has_block( string $block_name, int|string|null $id = null ): bool {
		$id = ! $id ? \get_post() : $id;
		
		if ( $id instanceof WP_Post ) {
			$id = $id->post_content;
		}
		
		if ( \has_block( $block_name, $id ) ) {
			return true;
		}
		
		if ( \has_block( 'block', $id ) ) {
			// check reusable blocks
			$content = \is_numeric( $id ) ? \get_post_field( 'post_content', $id ) : $id;
			$blocks = \parse_blocks( $content );
			
			if ( empty( $blocks ) ) {
				return false;
			}
			
			foreach ( $blocks as $block ) {
				if ( self::has_block_recursive( $block_name, $block ) ) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Check if a block is inside a content string.
	 * 
	 * @param	string	$block_name The name of the block you're looking for
	 * @param	string	$content The content to search for the block in
	 * @return	bool Whether the content contains the block
	 */
	public static function has_block_in_content( string $block_name, string $content ): bool {
		if ( \strpos( $content, '<!-- wp:' . $block_name . ' ' ) !== false ) {
			return true;
		}
		
		if ( \strpos( $content, '<!-- wp:block ' ) === false ) {
			return false;
		}
		
		$blocks = \parse_blocks( $content );
		
		if ( empty( $blocks ) ) {
			return false;
		}
		
		foreach ( $blocks as $block ) {
			if ( self::has_block_recursive( $block_name, $block ) ) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Check if a block is inside widget areas.
	 * 
	 * @param	string	$block_name Full block type to look for
	 * @return	bool Whether a widget area contains the block
	 */
	public static function has_block_in_widgets( string $block_name ): bool {
		$widget_block = \get_option( 'widget_block' );
		
		foreach ( \wp_get_sidebars_widgets() as $sidebar => $widgets ) {
			// ignore inactive widgets
			if ( $sidebar === 'wp_inactive_widgets' ) {
				continue;
			}
			
			if ( empty( $widgets ) || ! \is_array( $widgets ) ) {
				continue;
			}
			
			foreach ( $widgets as $widget ) {
				// ignore any widgets that are no block widgets
				if ( \strpos( $widget, 'block-' ) !== 0 ) {
					continue;
				}
				
				// block ID is part of the widget ID
				$block_id = (string) \str_replace( 'block-', '', $widget );
				$block = $widget_block[ $block_id ];
				
				if ( empty( $block['content'] ) ) {
					continue;
				}
				
				// don't use self::has_block() since this only allows to pass
				// an ID and not the content directly
				if ( \has_block( $block_name, $block['content'] ) ) {
					return true;
				}
				
				// search in reusable blocks
				$parsed_blocks = \parse_blocks( $block['content'] );
				
				if ( empty( $parsed_blocks ) ) {
					continue;
				}
				
				foreach ( $parsed_blocks as $parsed_block ) {
					if ( self::has_block_recursive( $block_name, $parsed_block ) ) {
						return true;
					}
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Check recursive if a block contains a specific block by name.
	 * 
	 * @param	string	$block_name Full block type to look for
	 * @param	array	$block Current block's data
	 * @return	bool Whether the post contains this block
	 */
	private static function has_block_recursive( string $block_name, array $block ): bool {
		if ( ! empty( $block['attrs']['ref'] ) && \has_block( $block_name, $block['attrs']['ref'] ) ) {
			return true;
		}
		
		if ( ! empty( $block['innerBlocks'] ) ) {
			foreach ( $block['innerBlocks'] as $inner_block ) {
				if ( self::has_block_recursive( $block_name, $inner_block ) ) {
					return true;
				}
			}
		}
		
		// check reusable blocks inside reusable blocks
		if ( ! empty( $block['attrs']['ref'] ) ) {
			$content = \get_post_field( 'post_content', $block['attrs']['ref'] );
			$blocks = \parse_blocks( $content );
			
			foreach ( $blocks as $block ) {
				if ( self::has_block_recursive( $block_name, $block ) ) {
					return true;
				}
			}
		}
		
		return false;
	}
}

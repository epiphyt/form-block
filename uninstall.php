<?php
namespace epiphyt\Form_Block;

// if uninstall.php is not called by WordPress, die
if ( ! \defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$GLOBALS['options'] = [
	'form_block_form_ids',
	'form_block_maximum_upload_size',
	'form_block_preserve_data_on_uninstall',
];

if ( \is_multisite() ) {
	$sites = \get_sites( [ 'number' => 99999 ] );
	
	foreach ( $sites as $site ) {
		\switch_to_blog( $site->blog_id );
		
		// do nothing if option says so
		if ( \get_option( 'form_block_preserve_data_on_uninstall' ) ) {
			continue;
		}
		
		\epiphyt\Form_Block\delete_data();
		\restore_current_blog();
	}
}
else if ( ! \get_option( 'form_block_preserve_data_on_uninstall' ) ) {
	\epiphyt\Form_Block\delete_data();
}

/**
 * Delete all data.
 */
function delete_data(): void {
	global $options;
	
	foreach ( $options as $option ) {
		if ( $option === 'form_block_form_ids' ) {
			$form_ids = \get_option( $option, [] );
			
			foreach ( \array_keys( $form_ids ) as $form_id ) {
				\delete_option( 'form_block_data_' . $form_id );
			}
		}
		
		\delete_option( $option );
	}
}

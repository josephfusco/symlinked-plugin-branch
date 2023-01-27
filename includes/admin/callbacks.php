<?php
/**
 * Various admin related callbacks used through the Faust plugin.
 *
 * @package Symlinked_Plugin_Branch
 */

namespace Symlinked_Plugin_Branch\Admin;

use function Symlinked_Plugin_Branch\Utilities\current_git_branch;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue the plugin's admin styles.
 *
 * @param string $hook
 * @return void
 */
function enqueue_admin_styles( $hook ) {
	// Exit if not on plugins page.
	if ( 'plugins.php' !== $hook ) {
		return;
	}

	wp_enqueue_style( 'symlinked-plugin-branch', plugins_url( '/css/style.css' , SYMLINKED_PLUGIN_BRANCH_FILE ) );
}
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\enqueue_admin_styles' );

/**
 * Filters the column headers for a list table on a specific screen.
 *
 * The dynamic portion of the hook name, `$screen->id`, refers to the
 * ID of a specific screen. For example, the screen ID for the Posts
 * list table is edit-post, so the filter for that screen would be
 * manage_edit-post_columns.
 *
 * @param string[] $columns The column header labels keyed by column ID.
 */
function add_git_info_column( $columns ) {
	$columns['git'] = __( 'Symlinked Branch', 'symlinked-plugin-branch' );

	return $columns;
}
add_filter( 'manage_plugins_columns', __NAMESPACE__ . '\add_git_info_column' );

/**
 * Fires inside each custom column of the Plugins list table.
 *
 * @param string $column_name Name of the column.
 * @param string $plugin_file Path to the plugin file relative to the plugins directory.
 * @param array  $plugin_data An array of plugin data. See `get_plugin_data()`
 *                            and the {@see 'plugin_row_meta'} filter for the list
 *                            of possible values.
 */
function plugin_row_column_content( $column_name, $plugin_file, $plugin_data ) {
	switch ( $column_name ) {
		case "git": display_column_content( $plugin_file, $plugin_data ); break;
	}
}
add_action( 'manage_plugins_custom_column', __NAMESPACE__ . '\plugin_row_column_content', 10, 3 );

/**
 * Displays the column's content.
 *
 * @param string $plugin_file Path to the plugin file relative to the plugins directory.
 * @param array  $plugin_data An array of plugin data. See `get_plugin_data()`
 *                            and the {@see 'plugin_row_meta'} filter for the list
 *                            of possible values.
 * @return void
 */
function display_column_content( $plugin_file, $plugin_data ) {
	$plugin_path = dirname( trailingslashit( WP_PLUGIN_DIR ) . $plugin_file );

	// Bail if there is no symbolic link for this plugin directory.
	if ( ! is_link( $plugin_path ) ) {
		return;
	}

	$target_path = readlink( $plugin_path );
	$branch      = current_git_branch( $target_path );

	echo "
		<div class='spb-root'>
			<div class='spb-row'>
				<i class='spb-icon'></i>
				$branch
			</div>
			<div class='spb-row spb-row-dark'>
				<code>$target_path</code>
			</div>
		</div>
	";
}

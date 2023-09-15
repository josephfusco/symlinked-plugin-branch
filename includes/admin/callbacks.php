<?php
/**
 * Various admin related callbacks used through the plugin.
 *
 * @package Symlinked_Plugin_Branch
 */

namespace Symlinked_Plugin_Branch\Admin;

use function Symlinked_Plugin_Branch\Utilities\current_git_branch;
use function Symlinked_Plugin_Branch\Utilities\get_plugins_with_symlinks;
use function Symlinked_Plugin_Branch\Utilities\replace_home_with_tilde;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\enqueue_admin_styles_and_scripts' );
/**
 * Enqueue the plugin's admin styles.
 *
 * @param string $hook The current admin page.
 */
function enqueue_admin_styles_and_scripts( $hook ) {
    // Exit if not on plugins or network plugins page.
    if ( 'plugins.php' !== $hook && 'plugins-network.php' !== $hook ) {
        return;
    }

    if ( ! function_exists( 'get_plugin_data' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    }

    $plugin_data    = get_plugin_data( SYMLINKED_PLUGIN_BRANCH_FILE );
    $plugin_version = $plugin_data['Version'];

    wp_enqueue_style( 'symlinked-plugin-branch', plugins_url( '/assets/css/style.css' , SYMLINKED_PLUGIN_BRANCH_FILE ), $plugin_version );
}

add_filter( 'manage_plugins-network_columns', __NAMESPACE__ . '\add_git_info_column' );
add_filter( 'manage_plugins_columns', __NAMESPACE__ . '\add_git_info_column' );
/**
 * Filters the column headers for a list table on a specific screen.
 *
 * @param string[] $columns An array of column header labels keyed by column ID.
 *
 * @return string[] The modified array of column header labels.
 */
function add_git_info_column( $columns ) {
	$columns['git'] = __( 'Symlinked Branch', 'symlinked-plugin-branch' );
	return $columns;
}

add_action( 'manage_network_plugins_custom_column', __NAMESPACE__ . '\plugin_row_column_content', 10, 3 );
add_action( 'manage_plugins_custom_column', __NAMESPACE__ . '\plugin_row_column_content', 10, 3 );
/**
 * Fires inside each custom column of the Plugins list table.
 *
 * @param string $column_name The name of the custom column.
 * @param string $plugin_file The plugin file.
 * @param array  $plugin_data An array of plugin data.
 */
function plugin_row_column_content( $column_name, $plugin_file, $plugin_data ) {
	if ( 'git' === $column_name ) {
		display_column_content( $plugin_file, $plugin_data );
	}
}

/**
 * Displays the content of the custom column in the plugins list table.
 *
 * @param string $plugin_file The plugin file.
 * @param array  $plugin_data An array of plugin data.
 */
function display_column_content( $plugin_file, $plugin_data ) {
	$plugin_path = dirname( trailingslashit( WP_PLUGIN_DIR ) . $plugin_file );
	if ( ! is_link( $plugin_path ) ) {
		return;
	}

	$target_path = replace_home_with_tilde( readlink( $plugin_path ) );
	$branch = current_git_branch( $target_path );

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

add_action( 'admin_bar_menu', __NAMESPACE__ . '\add_admin_bar_menu', 100 );
/**
 * Adds an admin menu bar node.
 *
 * @param WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance, passed by reference.
 */
function add_admin_bar_menu( $wp_admin_bar ) {
    $symlinked_plugins = get_plugins_with_symlinks();
    if ( empty( $symlinked_plugins ) ) {
        return;
    }

    $wp_admin_bar->add_node(
        [
            'id'     => 'symlinked-plugins',
            'title'  => __( 'Symlinked Plugins', 'symlinked-plugin-branch' ),
        ]
    );

    foreach ( $symlinked_plugins as $plugin ) {
		$slug = sanitize_title( $plugin['name'] );

        $wp_admin_bar->add_node(
            [
                'id'     => sanitize_key( 'symlinked-plugin-' . $slug ),
                'title'  => sprintf( '%s (%s)', $slug, $plugin['currentBranch'] ),
                'parent' => 'symlinked-plugins',
                'href'   => is_multisite() ? network_admin_url( 'plugins.php#' . $slug ) : admin_url( 'plugins.php#' . $slug ),
            ]
        );
    }
}

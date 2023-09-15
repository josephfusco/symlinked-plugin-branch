<?php
/**
 * Various utility functions used through the plugin.
 *
 * @package Symlinked_Plugin_Branch
 */

namespace Symlinked_Plugin_Branch\Utilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the git branch of the given directory.
 *
 * @param string $plugin_path The plugin directory path.
 *
 * @return string The git branch name.
 */
function current_git_branch( $plugin_path ) {
	$branch = shell_exec( 'cd ' . esc_html( $plugin_path ) . ' && git rev-parse --abbrev-ref HEAD 2>&1' );
	return trim( $branch );
}

/**
 * Get a list of all plugins with their meta data and symlink information.
 *
 * @return array Associative array of plugin data.
 */
function get_plugins_with_symlinks() {
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$home_path = getenv( 'HOME' ) ?: getenv( 'USERPROFILE' );
	$all_plugins = get_plugins();
	$symlinked_plugins = [];

	foreach ( $all_plugins as $plugin_file => $plugin_details ) {
		$plugin_path = dirname( trailingslashit( WP_PLUGIN_DIR ) . $plugin_file );
		$is_symlink = is_link( $plugin_path );
		$symlink_path = $is_symlink ? readlink( $plugin_path ) : null;

		$symlink_path = replace_home_with_tilde( $symlink_path );

		$symlinked_plugins[] = [
			'name'          => $plugin_details['Name'],
			'version'       => $plugin_details['Version'],
			'symlinkPath'   => $symlink_path,
			'currentBranch' => $is_symlink ? current_git_branch( $symlink_path ) : null,
		];
	}

	return $symlinked_plugins;
}

/**
 * Replace the home directory path with a tilde (~) in a given path.
 *
 * @param string $path The full path to shorten.
 *
 * @return string The path with the home directory replaced by a tilde.
 */
function replace_home_with_tilde( $path ) {
	$home_path = getenv( 'HOME' ) ?: getenv( 'USERPROFILE' );

	if ( $path && strpos( $path, $home_path ) === 0 ) {
		return '~' . substr( $path, strlen( $home_path ) );
	}

	return $path;
}

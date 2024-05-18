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
 * @param string $plugin_path The path of the plugin directory.
 *
 * @return string The git branch name or a message indicating git is not found.
 */
function current_git_branch( $plugin_path ) {
    // Ensure the plugin path is an absolute path and resolve symlinks
    $resolved_path = realpath( $plugin_path );

    if ( ! $resolved_path || ! is_dir( $resolved_path ) ) {
        return 'Invalid plugin path: ' . htmlspecialchars( $resolved_path );
    }

    $git_path = get_git_path();
    if ( ! $git_path ) {
        return 'Git is not installed or not in the PATH';
    }

    // Check for .git directory
    $git_dir_exists = file_exists( $resolved_path . '/.git' );

    if ( !$git_dir_exists ) {
        return 'No .git directory found in resolved path: ' . htmlspecialchars( $resolved_path );
    }

    // Find the root of the repository
    $repo_root = shell_exec( 'cd ' . escapeshellarg( $resolved_path ) . ' && ' . escapeshellcmd( $git_path ) . ' rev-parse --show-toplevel 2>&1' );
    $repo_root = trim( $repo_root );

    if ( empty( $repo_root ) || !is_dir( $repo_root ) ) {
        return 'Not a Git repository or unable to find repository root: ' . htmlspecialchars( $repo_root );
    }

    // Check if the directory is a Git repository
    $is_git_repo = shell_exec( 'cd ' . escapeshellarg( $repo_root ) . ' && ' . escapeshellcmd( $git_path ) . ' rev-parse --is-inside-work-tree 2>&1' );

    if ( trim( $is_git_repo ) !== 'true' ) {
        return 'Not a Git repository: ' . htmlspecialchars( $repo_root );
    }

    // Execute the command and capture output and errors
    $command = 'cd ' . escapeshellarg( $repo_root ) . ' && ' . escapeshellcmd( $git_path ) . ' rev-parse --abbrev-ref HEAD 2>&1';
    $branch = shell_exec( $command );

    if ( empty( $branch ) ) {
        return 'Error retrieving Git branch. Debug output: Command: ' . htmlspecialchars( $command ) . ' Output: ' . htmlspecialchars( $branch );
    }

    return trim( $branch );
}

/**
 * Determines the path to the Git binary by checking common locations.
 *
 * @return string|bool The Git binary path or false if not found.
 */
function get_git_path() {
    $common_paths = [
        '/usr/bin/git',
        '/usr/local/bin/git',
        '/bin/git',
        '/opt/bin/git'
    ];

    foreach ( $common_paths as $path ) {
        if ( file_exists( $path ) && is_executable( $path ) ) {
            return $path;
        }
    }

    return false;
}

/**
 * Get a list of all plugins with their meta data and symlink information.
 *
 * @return array Associative array of plugin data.
 */
function get_plugins_with_symlinks() {
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
            'currentBranch' => $is_symlink ? current_git_branch( $plugin_path ) : null,
        ];
    }

    // Debug output
    error_log( print_r( $symlinked_plugins, true ) );

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

add_filter( 'plugin_git_directory', __NAMESPACE__ . '\\my_plugin_git_directory' );
/**
 * Filter callback to determine the Git directory for the plugin.
 *
 * @param string $plugin_path The default plugin directory path.
 *
 * @return string The plugin directory path.
 */
function my_plugin_git_directory( $plugin_path ) {
    // Default plugin path
    $default_path = plugin_dir_path( __FILE__ );
    
    // Allow customization of the plugin path
    $custom_path = apply_filters( 'custom_plugin_git_directory', $default_path );
    
    return $custom_path;
}
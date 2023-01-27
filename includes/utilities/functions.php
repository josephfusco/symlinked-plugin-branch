<?php
/**
 * Various utility functions used through the  plugin.
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
 * @param string $plugin_path
 * @return string
 */
function current_git_branch( $plugin_path ) {
	return shell_exec( 'cd ' . esc_html( $plugin_path ) . ' && git rev-parse --abbrev-ref HEAD 2>&1' );
}

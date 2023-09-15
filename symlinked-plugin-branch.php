<?php
/**
 * Plugin Name:       Symlinked Plugin Branch
 * Plugin URI:        https://github.com/josephfusco/symlinked-plugin-branch/
 * GitHub Plugin URI: https://github.com/wp-graphql/wp-graphql
 * Description:       Easily identify the current git branch of your symlinked WordPress plugins.
 * Version:           1.2.0
 * Author:            Joseph Fusco
 * Author URI:        https://josephfus.co/
 * License:           MIT
 * Text Domain:       symlinked-plugin-branch
 *
 * @package Symlinked_Plugin_Branch
 */

namespace Symlinked_Plugin_Branch;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SYMLINKED_PLUGIN_BRANCH_FILE', __FILE__ );
define( 'SYMLINKED_PLUGIN_BRANCH_DIR', dirname( __FILE__ ) );
define( 'SYMLINKED_PLUGIN_BRANCH_URL', plugin_dir_url( __FILE__ ) );
define( 'SYMLINKED_PLUGIN_BRANCH_PATH', plugin_basename( SYMLINKED_PLUGIN_BRANCH_FILE ) );
define( 'SYMLINKED_PLUGIN_BRANCH_SLUG', dirname( plugin_basename( SYMLINKED_PLUGIN_BRANCH_FILE ) ) );

require SYMLINKED_PLUGIN_BRANCH_DIR . '/includes/utilities/functions.php';
require SYMLINKED_PLUGIN_BRANCH_DIR . '/includes/admin/callbacks.php';
require SYMLINKED_PLUGIN_BRANCH_DIR . '/includes/graphql/callbacks.php';

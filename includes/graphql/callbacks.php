<?php
/**
 * GraphQL related callbacks
 *
 * @package Symlinked_Plugin_Branch
 */

namespace Symlinked_Plugin_Branch\GraphQL;

use function Symlinked_Plugin_Branch\Utilities\current_git_branch;
use function Symlinked_Plugin_Branch\Utilities\get_plugins_with_symlinks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the "symlinkedPluginBranch" type.
 */
function register_symlinked_plugin_branch_type() {
    register_graphql_object_type( 'SymlinkedPlugin', [
        'description' => __( 'WordPress Plugin Details', 'symlinked-plugin-branch' ),
        'fields' => [
            'name' => [
                'type' => 'String',
                'description' => __( 'Plugin Name', 'symlinked-plugin-branch' ),
            ],
            'version' => [
                'type' => 'String',
                'description' => __( 'Plugin Version', 'symlinked-plugin-branch' ),
            ],
            'symlinkPath' => [
                'type' => 'String',
                'description' => __( 'Symlink Path', 'symlinked-plugin-branch' ),
            ],
            'currentBranch' => [
                'type' => 'String',
                'description' => __( 'Current Git Branch', 'symlinked-plugin-branch' ),
            ],
        ],
    ]);

    register_graphql_field( 'RootQuery', 'symlinkedPlugins', [
        'type' => [
            'list_of' => 'SymlinkedPlugin',
        ],
        'description' => __( 'An array of all installed plugins with symlink information.', 'symlinked-plugin-branch' ),
        'resolve'     => function() {
            if ( ! current_user_can( 'manage_plugins') ) {
                return null;
            };

            return get_plugins_with_symlinks();
        },
    ]);
}
add_action( 'graphql_register_types', __NAMESPACE__ . '\register_symlinked_plugin_branch_type' );
